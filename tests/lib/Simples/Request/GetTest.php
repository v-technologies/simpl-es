<?php

require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'bootstrap.php');

class Simples_Request_GetTest extends PHPUnit_Framework_TestCase {

	public function testGet() {
		$client = new Simples_Transport_Http();
		$this->assertTrue($client->index(array(
			'index' => 'twitter', 
			'type' => 'tweet',
			'id' => 'test_get',
			'data' => array(
				'content' => 'I\'m there.'
			)
		))->ok);
		$this->assertEquals('I\'m there.', $client->get(array(
			'index' => 'twitter', 
			'type' => 'tweet', 
			'id' => 'test_get'
		))->_source->content);
	}

}