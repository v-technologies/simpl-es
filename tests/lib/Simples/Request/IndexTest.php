<?php

require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'bootstrap.php');

class Simples_Request_IndexTest extends PHPUnit_Framework_TestCase {

	public $client ;
	
	public function setUp() {
		$this->client = new Simples_Transport_Http(array(
				'index' => 'twitter',
				'type' => 'tweet'
		));
	}
	
	public function testIndex() {
		try {
			$request = new Simples_Request_Index(new Simples_Transport_Http());
			$this->fail();
		} catch (Exception $e) {
			
		}

		$request = $this->client->index(array(
				'user' => 'scharrier',
				'fullname' => 'Sébastien Charrier'
		), array('id' => 1));
		$this->assertEquals('/twitter/tweet/1/', (string) $request->path());

		$this->assertTrue($request->ok);
		$this->assertEquals(1, $request->_id);

		$request = $this->client->index(array(
				'user' => 'vtechnologies',
		), array('id' => 2)) ;
		$this->assertEquals('/twitter/tweet/2/', (string) $request->path());
		$this->assertEquals(2, $request->_id);
		
		
		$request = $this->client->index(array(
			'content' => 'First',
			'user' => 'scharrier'
		), array('id' => 1, 'refresh' => true, 'parent' => '123'));
		$this->assertEquals('/twitter/tweet/1/?refresh=1&parent=123', (string) $request->path()) ;
		
		
		// From a document
		$doc = new Simples_Document(array(
			'content' => 'Test',
			'user' => 'jmorrison'
		)) ;
		$request = $this->client->index($doc) ;
		$res = $request->to('array') ;
		$this->assertEquals('jmorrison', $res['user']) ;
	}
	
	public function testBulk() {
		$data = array(
			array('firstname' => 'Jim', 'lastname' => 'Morrison'),
			array('firstname' => 'Ray', 'lastname' => 'Manzarek')
		);
		
		$request = $this->client->index($data, array('refresh' => true)) ;
		
		$body = $request->body() ;
		
		$this->assertTrue($request->bulk()) ;
		$this->assertEquals('/_bulk/?refresh=1', $request->path()) ;
		
		$res = $request->to('array') ;
		$this->assertEquals($data, $res) ;
		
		$res = $request->to('json') ;
		$expected = '{"index":{"_index":"twitter","_type":"tweet"}}
{"firstname":"Jim","lastname":"Morrison"}
{"index":{"_index":"twitter","_type":"tweet"}}
{"firstname":"Ray","lastname":"Manzarek"}
';
		$this->assertEquals($expected, $res) ;

	}
	
	public function testClean() {
		$data = array(
			'empty' => '',
			'zero' => '0',
			'float' => '1.2'
		);
		
		$request = $this->client->index($data, array('clean' => false)) ;
		$this->assertEquals($data, $request->to('array')) ;
		
		$request = $this->client->index($data, array('clean' => true)) ;
		$res = $request->to('array') ;
		$this->assertFalse(isset($res['empty'])) ;
		$this->assertTrue($res['zero'] === 0.0) ;
		$this->assertTrue($res['float'] === 1.2) ;
		
		// Bulk
		$data = array(
			$data,
			$data
		) ;
		$request = $this->client->index($data, array('clean' => true)) ;
		$res = $request->to('array') ;
		$this->assertFalse(isset($res[0]['empty'])) ;
		$this->assertTrue($res[0]['zero'] === 0.0) ;
		$this->assertTrue($res[0]['float'] === 1.2) ;
		$this->assertFalse(isset($res[1]['empty'])) ;
		$this->assertTrue($res[1]['zero'] === 0.0) ;
		$this->assertTrue($res[1]['float'] === 1.2) ;

		$request = $this->client->index($data, array('clean' => true, 'cast' => array('zero' => 'string', 'float' => 'integer'))) ;
		$res = $request->to('array') ;
		$this->assertTrue($res[1]['zero'] === '0') ;
		$this->assertTrue($res[1]['float'] === 1) ;

	}
}