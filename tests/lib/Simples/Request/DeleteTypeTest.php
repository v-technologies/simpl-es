<?php

require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'bootstrap.php');

class Simples_Request_DeleteTypeTest extends PHPUnit_Framework_TestCase {
	
	public $client ;
	
	public function setUp() {
		$this->client = new Simples_Transport_Http(array('host' => ES_HOST, 'index' => 'test_delete', 'type' => 'test_delete_type'));
		$this->client->createIndex()->execute() ;
	}

	public function testDelete() {
		// Fake record
		$this->client->index(array('some'=>'data'), array('refresh' => true))->execute() ;
		
		$request = $this->client->deleteType() ;
		
		$this->assertEquals(Simples_Request::DELETE, $request->method()) ;
		$this->assertEquals('/test_delete/test_delete_type/', (string) $request->path()) ;
		
		$response = $request->execute() ;
		$this->assertTrue($response->ok) ;
	}
	
	public function tearDown() {
		if ($this->client) {
			$this->client->deleteIndex()->execute() ;
		}
	}

}