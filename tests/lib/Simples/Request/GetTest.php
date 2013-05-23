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

	public function testMultiple() {
		$client = new Simples_Transport_Http(array(
			'index' => 'twitter',
			'type' => 'tweet'
		));

		$client->index(array(
			array('id' => '1', 'value' => 'first'),
			array('id' => '2', 'value' => 'second')
		), array('refresh' => true)) ;

		$request = $client->get(array(1,2)) ;
		$this->assertEquals('/_mget/', (string) $request->path()) ;
		$body = $request->body() ;
		$this->assertEquals('1', $body['docs'][0]['_id']) ;

		$res = $request->execute() ;
		$this->assertEquals(2, count($res->documents())) ;
	}

}
