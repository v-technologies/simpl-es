<?php

require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'bootstrap.php');

class Simples_Request_IndexTest extends PHPUnit_Framework_TestCase {

	public function testIndex() {
		try {
			$request = new Simples_Request_Index(new Simples_Transport_Http());
			$this->fail();
		} catch (Exception $e) {
			
		}

		$client = new Simples_Transport_Http(array(
				'index' => 'twitter',
				'type' => 'tweet'
		));

		$request = $client->index(array(
				'user' => 'scharrier',
				'fullname' => 'Sébastien Charrier'
		), array('id' => 1));
		$this->assertEquals('/twitter/tweet/1/', (string) $request->path());

		$this->assertTrue($request->ok);
		$this->assertEquals(1, $request->_id);

		$request = $client->index(array(
				'user' => 'vtechnologies',
		), array('id' => 2)) ;
		$this->assertEquals('/twitter/tweet/2/', (string) $request->path());
		$this->assertEquals(2, $request->_id);
		
		
		$request = $client->index(array(
			'content' => 'First',
			'user' => 'scharrier'
		), array('id' => 1, 'refresh' => true));
		$this->assertEquals('/twitter/tweet/1/?refresh=1', (string) $request->path()) ;
	
	}

}