<?php

class Simples_Request_DeleteTypeTest extends Simples_HttpTestCase {
	
	public $client ;
	
	protected function setUp() : void {
		parent::setUp();
		$this->client->config('index', 'test_delete');
		$this->client->config('type', 'test_delete_type');
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
	
	protected function tearDown() : void {
		if ($this->client) {
			$this->client->deleteIndex()->execute() ;
		}
	}
}