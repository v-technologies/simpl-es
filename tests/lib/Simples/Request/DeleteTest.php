<?php

require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'bootstrap.php');

class Simples_Request_DeleteTest extends PHPUnit_Framework_TestCase {

	public function setUp() {
		$this->client = new Simples_Transport_Http(array(
			'host' => ES_HOST,
			'index' => 'twitter',
			'type' => 'tweet',
			'log' => true
		));
		$this->client->createIndex()->execute() ;
	}

	public function tearDown() {
		$this->client->deleteIndex()->execute() ;
	}

	public function testDelete() {
		$response = $this->client->index(
			array(
				'content' => 'Pliz, pliz, delete me !'
			),
			array(
				'id' => 'test-delete'
			))->execute();
		$this->assertEquals(201, $response->http->http_code);
		
		$delete_object = $this->client->delete('test-delete');
		$this->assertEquals('/twitter/tweet/test-delete/', (string) $delete_object->path()) ;

		$this->assertEquals(200, $delete_object->execute()->http->http_code);

		$response = $this->client->get('test-delete')->execute();

		$this->assertFalse($response->body->found);
	}
}