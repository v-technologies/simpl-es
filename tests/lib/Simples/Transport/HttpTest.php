<?php

use PHPUnit\Framework\TestCase;

class Simples_Transport_HttpTest extends TestCase {

    public function testConnection() {
		try {
			$transport = new Simples_Transport_Http() ;
			$transport->connect() ;
			$this->assertTrue($transport->connected()) ;
			$this->assertTrue($transport instanceof Simples_Transport_Http) ;
			
			$transport->disconnect() ;
			$this->assertFalse($transport->connected()) ;
		} catch (Exception $e) {
			$this->markTestSkipped($e->getMessage()) ;
		}
	}
		
	/**
	 */
	public function testConnectionException() {
		$this->expectException(\Exception::class);
		$transport = new Simples_Transport_Http(array('host' => 'www.google.com', 'port' => '80')) ;
		$transport->connect() ;
	}
	
	/**
	 */
	public function testCheck() {
		$this->expectException(\Exception::class);
		$transport = new Simples_Transport_Http() ;
		
		$transport->config(array(
			'host' => 'www.google.com',
			'port' => 80
		)) ;
		
		$transport->connect() ;
	}
	
	public function testUrl() {
		$transport = new Simples_Transport_Http() ;
		$this->assertEquals('http://127.0.0.1/', $transport->url()) ;
		
		$transport->config('host', 'farhost') ;
		$this->assertEquals('http://farhost/', $transport->url()) ;
		
		$this->assertEquals('http://farhost/_status', $transport->url('_status')) ;
		$this->assertEquals('http://farhost/_status', $transport->url('/_status')) ;
	}
	
	public function testCall() {
		$transport = new Simples_Transport_Http() ;
		$res = $transport->call() ;
		$this->assertTrue($res['ok']);
		$this->assertTrue(isset($res['version']['number'])) ;
	}

	/**
	 */
	public function testCallCannotJsonDecodeException() {
		$this->expectException(\Exception::class);
		$this->expectExceptionMessage('Cannot JSON decode the response : No handler found for uri [/test] and method [GET]');
		$transport = new Simples_Transport_Http();
		$transport->call('/test');
	}

	/**
	 */
	public function testCallCurlReturnFalse() {
		$this->expectException(\Exception::class);
		$this->expectExceptionMessage('Error during the request (6)');
		$transport = new Simples_Transport_Http(array( 'host' => 'nowhere' ));
		$transport->call('/test');
	}

	/**
	 */
	public function testCallEmptyResponse() {
		$this->expectException(\Exception::class);
		$this->expectExceptionMessage('The ES server returned an empty response.');
		$transport = new Simples_Transport_Http();
		$transport->call('/test', 'HEAD');
	}

	/**
	 */
	public function testCallCurlHttpCode() {
		$this->expectException(\Exception::class);
		$this->expectExceptionMessage('Error during the request (HTTP CODE: 400)');
		$transport = new Simples_Transport_Http();

		try {
			$transport->call('/test', 'DELETE');
			$transport->call('/test', 'PUT');
		} catch(Exception $e) {}

		$transport->call('/test', 'POST');
	}

	public function testMagicCall() {
		$transport = new Simples_Transport_Http() ;
		$status = $transport->status() ;
		$this->assertTrue($status instanceof Simples_Request_Status) ; 
		$response = $transport->status()->execute() ;
		$this->assertTrue($response instanceof Simples_Response) ; 
	}
}
