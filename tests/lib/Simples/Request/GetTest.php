<?php

require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'bootstrap.php');

class Simples_Request_GetTest extends PHPUnit_Framework_TestCase {

	public function testGet() {
		$client = new Simples_Transport_Http();
		$this->assertTrue($client->index('twitter', 'tweet', array(
			'_id' => 'test_get',
			'content' => 'I\'m there.'
		))->ok);
		$this->assertEquals('I\'m there.', $client->get('twitter', 'tweet', 'test_get')->_source->content);
	}

}