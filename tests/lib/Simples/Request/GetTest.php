<?php

class Simples_Request_GetTest extends Simples_HttpTestCase {

	protected function setUp() : void
	{
		parent::setUp();
		$this->client->config('index', 'twitter');
		$this->client->config('type', 'tweet');
	}
	
	public function testGet() {
		$this->client->config('index', 'twitter');
		$this->client->config('type', 'tweet');

		$this->assertTrue($this->client->index(array('content' => 'I\'m there.'), array('id' => 'test_get'))->ok) ;
		$this->assertEquals('I\'m there.', $this->client->get('test_get')->_source->content);
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
