<?php
require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'bootstrap.php') ;

class Simples_Transport_HttpTest extends PHPUnit_Framework_TestCase {

    public function testConnection() {
		$transport = new Simples_Transport_Http(array('host' => ES_HOST)) ;
		$transport->connect() ;
		$this->assertTrue($transport->connected()) ;
		$this->assertTrue($transport instanceof Simples_Transport_Http) ;
		
		$transport->disconnect() ;
		$this->assertFalse($transport->connected()) ;
	}

	/**
	 * @expectedException \Exception
	 */
    public function testConnectionException() {
		$transport = new Simples_Transport_Http(array('host' => 'www.google.com', 'port' => '80')) ;
		$transport->connect() ;
	}
	
	/**
	 * @expectedException \Exception
	 */
	public function testConnectionExceptionOnConfig() {
		$transport = new Simples_Transport_Http(array('host' => ES_HOST)) ;
		
		$transport->config(array(
			'host' => 'www.google.com',
			'port' => 80
		)) ;
		
		$transport->connect() ;
	}
	
	public function testUrl() {
		$transport = new Simples_Transport_Http(array('host' => ES_HOST)) ;
		$this->assertEquals('http://' . ES_HOST . '/', $transport->url()) ;
		
		$transport->config('host', 'farhost') ;
		$this->assertEquals('http://farhost/', $transport->url()) ;
		
		$this->assertEquals('http://farhost/_status', $transport->url('_status')) ;
		$this->assertEquals('http://farhost/_status', $transport->url('/_status')) ;
	}
	
	public function testCall() {
		$transport = new Simples_Transport_Http(array('host' => ES_HOST)) ;
		$res = $transport->call() ;
		$this->assertTrue(isset($res['http']['http_code'])) ;
		$this->assertSame(200, $res['http']['http_code']) ;
		$this->assertTrue(isset($res['body']['status'])) ;
		$this->assertSame(200, $res['body']['status']) ;
		$this->assertTrue(isset($res['body']['version']['number'])) ;
	}
	
	public function testMagicCall() {
		$transport = new Simples_Transport_Http(array('host' => ES_HOST)) ;
		$status = $transport->status() ;
		$this->assertTrue($status instanceof Simples_Request_Status) ; 
		$response = $transport->status()->execute() ;
		$this->assertTrue($response instanceof Simples_Response) ; 
	}
}
