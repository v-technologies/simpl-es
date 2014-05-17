<?php

require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'bootstrap.php');

class Simples_Request_CreateIndexTest extends PHPUnit_Framework_TestCase {

	public function setUp() {
		$this->client = new Simples_Transport_Http(array('host' => ES_HOST)) ;
	}
	
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