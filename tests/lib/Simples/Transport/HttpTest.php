<?php

class Simples_Transport_HttpTest extends PHPUnit_Framework_TestCase {

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
	 * @expectedException Exception
	 */
	public function testConnectionException() {
		$transport = new Simples_Transport_Http(array('host' => 'www.google.com', 'port' => '80')) ;
		$transport->connect() ;
	}
	
	/**
	 * @expectedException Exception
	 */
	public function testCheck() {
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
	
	public function testMagicCall() {
		$transport = new Simples_Transport_Http() ;
		$status = $transport->status() ;
		$this->assertTrue($status instanceof Simples_Request_Status) ; 
		$response = $transport->status()->execute() ;
		$this->assertTrue($response instanceof Simples_Response) ; 
	}
}
