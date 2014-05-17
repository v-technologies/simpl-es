<?php
require_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'bootstrap.php') ;

class Simples_RequestTest extends PHPUnit_Framework_TestCase {
	
	public function testConstruct() {
		$request = new Simples_Request_Custom() ;
		$this->assertTrue($request instanceof Simples_Request) ;
	}
	
	public function testDefinition() {
		$request = new Simples_Request_Custom() ;
		$this->assertTrue($request->definition() instanceof Simples_Request_Definition) ;
		$this->assertEquals('_status', $request->definition()->path()) ;
	}
	
	public function testPath() {
		$request = new Simples_Request_Custom() ;
		$this->assertEquals('/_status/', $request->path()) ;
		
	}
	
	public function testMethod() {
		$request = new Simples_Request_Custom() ;
		$this->assertEquals(Simples_Request::GET, $request->method()) ;
		
	}
	
	public function testExecute() {
		$request = new Simples_Request_Custom() ;
		$res = $request->execute() ;
		$this->assertTrue($request->execute() instanceof Simples_Response) ;
		
		$res = $request->client(new Simples_Transport_Http(array('host' => ES_HOST)))->execute() ;
		$this->assertTrue($res->get('ok') === true) ;
	}
	
	public function testTo() {
		$request = new Simples_Request_Custom() ;
		$this->assertTrue(is_string($request->to('json'))) ;
		
		
		$request->body(array(
			'hey' => 'ho'
		)) ;
		$res = $request->to('array') ;
		$this->assertTrue(is_array($res)) ;
		$this->assertEquals('ho', $res['hey']) ;
		
		try {
			$request->to('somethingbad') ;
			$this->fail('No exception !') ;
		} catch (Exception $e) {
		}
	}
	
	public function testIndicesTypes() {
		$request = new Simples_Request_Custom(null, array(
			'index' => 'twitter',
			'type' => 'tweet'
		)) ;
		$this->assertEquals('twitter', $request->index()) ;
		$this->assertEquals('tweet', $request->type()) ;
		
		$request->options(array('type' => array(
			'tweet','user'
		))) ;
		$this->assertEquals('tweet,user', $request->type()) ;
		
		$this->assertEquals('/twitter/tweet,user/_status/', (string) $request->path()) ;
		
	}
}

class Simples_Request_Custom extends Simples_Request {
	
	protected $_path = '/_status' ;
	
	protected $_method = Simples_Request::GET ;
	
	protected $_definition = array(
		'method' => Simples_Request::GET,
		'path' => '_status'
	) ;
}