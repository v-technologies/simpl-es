<?php

require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'bootstrap.php');

class Simples_Document_SetTest extends PHPUnit_Framework_TestCase {
	
	public function testConstruct() {
		$set = new Simples_Document_Set() ;
		$this->assertTrue($set instanceof Simples_Document_Set) ;
		
		try {
			$set = new Simples_Document_Set(array('something', 'bad')) ;
			$this->fail() ;
		} catch (Exception $e) {
		}
	}
	
	public function testInterfaces() {
		// Traversable (IteratorAggregate)
		$set = new Simples_Document_Set() ;
		$set->set(array(
			array('firstname' => 'Jim'),
			array('firstname' => 'Ray'),
			array('firstname' => 'Robbie')
		));
		
		$test = array() ;
		foreach($set as $document) {
			$test[] = $document->firstname ;
		}
		$this->assertEquals(array('Jim','Ray','Robbie'), $test) ;
		
		// Countable
		$this->assertEquals(3, count($set)) ;
	}

	public function testValid() {
		$data = array('Not','A','Set') ;
		$this->assertFalse(Simples_Document_Set::valid($data)) ;
		
		$data = array(
			array('is' => 'A Simples_Document'),
			array('is' => 'Another Simples_Document')
		) ;
		$this->assertTrue(Simples_Document_Set::valid($data)) ;
	}
	
	public function testTransformation() {
		$data = array(
			array('is' => 'A Simples_Document'),
			array('is' => 'Another Simples_Document')
		) ;
		$set = new Simples_Document_Set($data) ;
		
		$res = $set->to('array') ;
		$this->assertEquals($data, $res) ;
		
		$res = $set->to('json') ;
		$this->assertEquals(json_encode($data), $res) ;
	}

}