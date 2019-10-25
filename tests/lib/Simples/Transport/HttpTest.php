<?php

class Simples_Transport_HttpTest extends Simples_HttpTestCase {

	public function testConnection() {
		try {
			$this->client->connect() ;
			$this->assertTrue($this->client->connected()) ;
			$this->assertTrue($this->client instanceof Simples_Transport_Http) ;
			
			$this->client->disconnect() ;
			$this->assertFalse($this->client->connected()) ;
		} catch (Exception $e) {
			$this->markTestSkipped($e->getMessage()) ;
		}
	}
		
	/**
	 */
	public function testConnectionException() {
		$this->expectException(\Exception::class);
		$this->client->config('host', 'www.google.com');
		$this->client->config('port', '80');
		$this->client->connect() ;
	}
	
	/**
	 */
	public function testCheck() {
		$this->expectException(\Exception::class);
	
		$this->client->config(array(
			'host' => 'www.google.com',
			'port' => 80
		)) ;
		
		$this->client->connect() ;
	}
	
	public function testUrl() {
		$this->client->config('host', '127.0.0.1');
		$this->assertEquals('http://127.0.0.1/', $this->client->url()) ;
		
		$this->client->config('host', 'farhost');
		$this->assertEquals('http://farhost/', $this->client->url()) ;
		
		$this->assertEquals('http://farhost/_status', $this->client->url('_status')) ;
		$this->assertEquals('http://farhost/_status', $this->client->url('/_status')) ;
	}
	
	public function testCall() {
		$res = $this->client->call() ;
		$this->assertTrue($res['ok']);
		$this->assertTrue(isset($res['version']['number'])) ;
	}

	/**
	 */
	public function testCallCannotJsonDecodeException() {
		$this->expectException(\Exception::class);
		$this->expectExceptionMessage('Cannot JSON decode the response : No handler found for uri [/test] and method [GET]');
		
		$this->client->call('/test');
	}

	/**
	 */
	public function testCallCurlReturnFalse() {
		$this->expectException(\Exception::class);
		$this->expectExceptionMessage('Error during the request (6)');
		$this->client->config(array( 'host' => 'nowhere' ));
		$this->client->call('/test');
	}

	/**
	 */
	public function testCallEmptyResponse() {
		$this->expectException(\Exception::class);
		$this->expectExceptionMessage('The ES server returned an empty response.');

		$this->client->call('/test', 'HEAD');
	}

	/**
	 */
	public function testCallCurlHttpCode() {
		$this->expectException(\Exception::class);
		$this->expectExceptionMessage('Error during the request (HTTP CODE: 400)');

		try {
			$this->client->call('/test', 'DELETE');
			$this->client->call('/test', 'PUT');
		} catch(Exception $e) {}

		$this->client->call('/test', 'POST');
	}

	public function testMagicCall() {
		$status = $this->client->status() ;
		$this->assertTrue($status instanceof Simples_Request_Status) ; 
		$response = $this->client->status()->execute() ;
		$this->assertTrue($response instanceof Simples_Response) ; 
	}
}
