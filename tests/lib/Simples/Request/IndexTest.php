<?php

require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'bootstrap.php');

class Simples_Request_IndexTest extends PHPUnit_Framework_TestCase {

	public function testIndex() {
		try {
			$request = new Simples_Request_Index(new Simples_Transport_Http());
			$this->fail();
		} catch (Exception $e) {
			
		}

		$client = new Simples_Transport_Http();

		$request = new Simples_Request_Index($client, array(
			'index' => 'twitter',
			'type' => 'tweet',
			'id' => 1,
			'data' => array(
				'user' => 'scharrier',
				'fullname' => 'Sébastien Charrier'
			)
		));
		$this->assertEquals('/twitter/tweet/1/', $request->path());

		$this->assertTrue($request->ok);
		$this->assertEquals(1, $request->_id);

		$request = new Simples_Request_Index($client,array(
			'index' => 'twitter',
			'type' => 'tweet',
			'id' => 2,
			'data' => array(
				'user' => 'vtechnologies',
			)
		)) ;
		$this->assertEquals('/twitter/tweet/2/', $request->path());
		$this->assertEquals(2, $request->_id);
	}

}