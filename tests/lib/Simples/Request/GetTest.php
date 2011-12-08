<?php

require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'bootstrap.php');

class Simples_Request_GetTest extends PHPUnit_Framework_TestCase {

	public function testGet() {
		$client = new Simples_Transport_Http(array(
			'index' => 'twitter', 
			'type' => 'tweet'
		));
		
		$this->assertTrue($client->index(array('content' => 'I\'m there.'), array('id' => 'test_get'))->ok) ;

		$this->assertEquals('I\'m there.', $client->get('test_get')->_source->content);
	}

}