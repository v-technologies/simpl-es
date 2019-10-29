<?php
require_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'bootstrap.php') ;

use PHPUnit\Framework\TestCase;

class SimplesTest extends TestCase {
	
	public function testStaticUsage() {
		$host = getenv('host') ?: '127.0.0.1';
		$client = Simples::connect(array(
			'host' => $host,
		)) ;
		$this->assertTrue(Simples::connected()) ;
		$this->assertTrue($client->connected()) ;
		
		Simples::disconnect() ;
		$this->assertFalse(Simples::connected()) ; 
		
		$client = Simples::connect() ;
		$this->assertEquals($host, $client->config('host')) ;
		$this->assertEquals(true, Simples::current()->status()->ok) ;
		
		Simples::current()->config(array(
			'index' => 'twitter',
			'type' => 'tweet'
		));
		
		Simples::current()->index(
			array(
				'from' => 'Static usage'
			),
			array(
				'id' => 1,
				'refresh' => true
			)
		)->execute() ;
		
		$this->assertEquals('Static usage', Simples::current()->get(array(
			'id' => 1
		))->_source->from) ; 
	}
	
	public function testMultiConnect() {
		Simples::connect(array(
			'host' => '127.0.0.1',
			'index' => 'stars'
		)) ;
		Simples::connect(array(
			'host' => '127.0.0.1',
			'index' => 'planets'
		)) ;
		$this->assertEquals('planets', Simples::current()->config('index')) ;
	}
	
	public function testClient() {
		$client = Simples::client(array('host' => 'something')) ;
		$other = Simples::client(array('host' => 'somethingelse')) ;
		$this->assertTrue($client->config('host') !== $other->config('host')) ;
	}
}