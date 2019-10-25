<?php

require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'bootstrap.php');

use PHPUnit\Framework\TestCase;

class Simples_Request_DeleteTypeTest extends TestCase {
	
	public $client ;
	
	protected function setUp() : void {
		$this->client = new Simples_Transport_Http(array('index' => 'test_delete', 'type' => 'test_delete_type'));			
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