<?php

class Simples_Request_CreateIndexTest extends Simples_HttpTestCase {

	public function testCreate() {
		$this->client->config('index','test_index') ;
		$request = $this->client->createIndex() ;
		$this->assertEquals(Simples_Request::PUT, $request->method()) ;
		$this->assertEquals('/test_index/', (string) $request->path()) ;
		
		$this->client->config(array('index'=>null)) ;
		$request = $this->client->createIndex('test_index') ;
		$this->assertEquals(Simples_Request::PUT, $request->method()) ;
		$this->assertEquals('/test_index/', (string) $request->path()) ;
		
		$this->client->config(array('index' => 'index','type' => 'type')) ;
		$request = $this->client->createIndex() ;
		$this->assertEquals('/index/', (string) $request->path()) ;
		
		$request = $this->client->createIndex() ;
		try {
			$request->to('array') ;
			$this->fail() ;
		} catch (Exception $e) {}
	}
}