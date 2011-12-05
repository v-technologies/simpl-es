<?php
require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'bootstrap.php') ;

class Simples_Transport_Http_HttpTest extends PHPUnit_Framework_TestCase {

    public function testConnection() {
		try {
			$transport = new Simples_Transport_Http() ;
			$transport->connect() ;
			
			$this->assertTrue($transport instanceof Simples_Transport_Http) ;
		} catch (Exception $e) {
			$this->markTestSkipped($e->getMessage()) ;
		}
	}
	
	public function testCheck() {
		$transport = new Simples_Transport_Http() ;
		
		$transport->config(array(
			'host' => 'www.google.com',
			'port' => 80
		)) ;
		
		try {
			$transport->connect() ;
		} catch(Exception $e) {
			return ;
		}
		
		$this->fail() ;
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
}