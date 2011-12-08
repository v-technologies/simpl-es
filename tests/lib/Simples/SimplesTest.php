<?php
require_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'bootstrap.php') ;

class SimplesTest extends PHPUnit_Framework_TestCase {
	
	public function testStaticUsage() {
		$client = Simples::connect(array(
			'host' => '127.0.0.1',
		)) ;
		$this->assertTrue(Simples::connected()) ;
		$this->assertTrue($client->connected()) ;
		
		Simples::disconnect() ;
		$this->assertFalse(Simples::connected()) ; 
		
		$client = Simples::connect() ;
		$this->assertEquals('127.0.0.1', $client->config('host')) ;
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
				'id' => 1
			)
		)->execute() ;
		
		$this->assertEquals('Static usage', Simples::current()->get(array(
			'id' => 1
		))->_source->from) ; 
	}
}