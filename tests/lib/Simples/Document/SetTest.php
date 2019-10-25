<?php

require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'bootstrap.php');

use PHPUnit\Framework\TestCase;

class Simples_Document_SetTest extends TestCase {

	public function testConstruct() {
		$set = new Simples_Document_Set() ;
		$this->assertTrue($set instanceof Simples_Document_Set) ;

		try {
			$set = new Simples_Document_Set(array('something', 'bad')) ;
			$this->fail() ;
		} catch (Exception $e) {
		}

		$set = new Simples_Document_Set(new Simples_Document(array('something' => 'good'))) ;
		$this->assertTrue($set instanceof Simples_Document_Set) ;
		$this->assertEquals(1, count($set)) ;

		$set = new Simples_Document_Set(array(new Simples_Document(array('something' => 'good')))) ;
		$this->assertTrue($set instanceof Simples_Document_Set) ;
		$this->assertEquals(1, count($set)) ;
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

	public function testCheck() {
		$data = array('Not','A','Set') ;
		$this->assertFalse(Simples_Document_Set::check($data)) ;

		$data = array(
			array('is' => 'A Simples_Document'),
			array('is' => 'Another Simples_Document')
		) ;
		$this->assertTrue(Simples_Document_Set::check($data)) ;
	}

	public function testTransformation() {
		$data = array(
			array('is' => 'A Simples_Document', 'empty' => ''),
			array('is' => 'Another Simples_Document', 'zero' => '0')
		) ;
		$set = new Simples_Document_Set($data) ;

		$res = $set->to('array') ;
		$this->assertEquals($data, $res) ;

		$res = $set->to('array', array('clean' => true)) ;
		$this->assertFalse(isset($res[0]['empty'])) ;
		$this->assertTrue($res[1]['zero'] === 0.0) ;

		$res = $set->to('json') ;
		$this->assertEquals(json_encode($data), $res) ;
	}

	public function testExtract() {
		$data = array(
			array('id' => 1, 'is' => 'A Simples_Document', 'empty' => '', 'sub' => array('key' => 'value')),
			array('id' => 2, 'is' => 'Another Simples_Document', 'zero' => '0')
		) ;
		$set = new Simples_Document_Set($data) ;

		$res = $set->combine('id','is') ;
		$this->assertEquals(array(
			1 => 'A Simples_Document',
			2 => 'Another Simples_Document'
		), $res) ;

		$res = $set->combine('id','sub.key') ;
		$this->assertEquals(array(
			1 => 'value',
			2 => null
		), $res) ;

		$res = $set->pluck('id') ;
		$this->assertEquals(array(1,2), $res) ;
	}

	public function testSource() {
		$data = array(
			array('_id' => 10)
		) ;
		$set = new Simples_Document_Set($data, array('source' => true)) ;
		$this->assertTrue($set->get(0)->properties() instanceof Simples_Document) ;
		$this->assertEquals(10, $set->get(0)->properties()->id) ;

		$set = new Simples_Document_Set($data, array('source' => false)) ;
		$this->assertFalse($set->get(0)->properties() instanceof Simples_Document) ;
	}

}
