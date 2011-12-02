<?php
require_once('bootstrap.php') ;

class SimplesConnectionTest extends PHPUnit_Framework_TestCase {

    public function testConnection() {
		try {
			$connection = new SimplesConnection() ;
			$connection->connect() ;
			
			$this->assertTrue($connection instanceof SimplesConnection) ;
		} catch (Exception $e) {
			$this->markTestSkipped($e->getMessage()) ;
		}
	}
	
	public function testCheck() {
		$connection = new SimplesConnection() ;
		
		$connection->config(array(
			'host' => 'www.google.com',
			'port' => 80
		)) ;
		
		try {
			$connection->connect() ;
		} catch(Exception $e) {
			return ;
		}
		
		$this->fail() ;
	}
	
	public function testUrl() {
		$connection = new SimplesConnection() ;
		$this->assertEquals('http://127.0.0.1/', $connection->url()) ;
		
		$connection->config('host', 'farhost') ;
		$this->assertEquals('http://farhost/', $connection->url()) ;
		
		$this->assertEquals('http://farhost/_status', $connection->url('_status')) ;
		$this->assertEquals('http://farhost/_status', $connection->url('/_status')) ;
	}
}