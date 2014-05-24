<?php

require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'bootstrap.php');

class Simples_Request_GetTest extends PHPUnit_Framework_TestCase {

	public function setUp() {
		$this->client = new Simples_Transport_Http(array(
			'host' => ES_HOST,
			'index' => 'twitter',
			'type' => 'tweet',
			'log' => true
		));
		$this->client->createIndex()->execute();
	}

	public function tearDown() {
		$this->client->deleteIndex()->execute();
	}

	public function testGet() {
		$response = $this->client->index(array('content' => 'I\'m there.'), array('id' => 'test_get'))->execute();
		$this->assertEquals(201, $response->http->http_code);
		$this->assertEquals('I\'m there.', $this->client->get('test_get')->body->_source->content);
	}

	public function testMultiple() {
		$this->client->index(array(
			array('id' => '1', 'value' => 'first'),
			array('id' => '2', 'value' => 'second')
		), array('refresh' => true)) ;

		$request = $this->client->get(array(1,2)) ;
		$this->assertEquals('/_mget/', (string) $request->path()) ;
		$body = $request->body() ;
		$this->assertEquals('1', $body['docs'][0]['_id']) ;

		$res = $request->execute() ;
		$this->assertEquals(2, count($res->documents())) ;
	}
}
