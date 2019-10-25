<?php
require_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'bootstrap.php') ;

use PHPUnit\Framework\TestCase;

class Simples_FactoryTest extends TestCase {
	
	public function testConstruct() {
		$factory = new Simples_Factory() ;
		$this->assertTrue($factory instanceof Simples_Factory) ;
	}
	
	public function testMapping() {
		$factory = new Simples_Factory() ;
		$factory->map('Request.status', 'Ahahah') ;
		$this->assertEquals('Ahahah', $factory->mapping('Request.status')) ;

		$factory->map('Request', array('stats' => 'Burp')) ;
		$mapping = $factory->mapping() ;
		$this->assertEquals('Ahahah',$mapping['Request.status']) ;
		$this->assertEquals('Burp', $mapping['Request.stats']) ;
		
		$this->assertTrue($factory->valid('Request.status')) ;
		$this->assertFalse($factory->valid('Request.somethingbad')) ;
	}
	
	public function testNew() {
		$factory = new Simples_Factory() ;
		$status = $factory->request('status') ;
		$this->assertTrue($status instanceof Simples_Request_Status) ;
	}
}