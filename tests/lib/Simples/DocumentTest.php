<?php
require_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'bootstrap.php') ;

class Simples_DocumentTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * Set up some fixtures. 
	 */
	public function setUp() {
		$this->data['standard'] = array(
			'firstname' => 'Jim',
			'lastname' => 'Morrison',
			'empty' => '',
			'integer' => '9',
			'float' => '1.11',
			'categories' => array('Poet','Composer'),
			'band' => array(
				'name' => 'The doors'
			),
			'friends' => array(
				array('firstname' => 'Ray' , 'lastname' => 'Manzarek'),
				array('firstname' => 'Robbie' , 'lastname' => 'Krieger'),
				array('firstname' => 'John' , 'lastname' => 'Densmore')
			),
			'sub' => array(
				array('integer' => '9', 'string' => 9)
			)
		) ;
		
		$this->data['source'] = array(
			'_index' => 'music',
			'_type' => 'artists',
			'_source' => array(
				'firstname' => 'Jim',
				'lastName' => 'Morrisson'
			)
		);
	}
	
	public function testConstruct() {
		$request = new Simples_Document() ;
		$this->assertTrue($request instanceof Simples_Document) ;
	}
	
	public function testAccessors() {
		$document = new Simples_Document($this->data['standard']) ;
		
		$this->assertEquals('Jim', $document->firstname);
		$this->assertTrue($document->band instanceof Simples_Document) ;
		$this->assertTrue($document->categories instanceof Simples_Document) ;
		$this->assertTrue($document->friends instanceof Simples_Document_Set) ;
		$this->assertNull($document->properties()) ;
		
		$document = new Simples_Document($this->data['source']) ;
		$this->assertEquals('Jim', $document->firstname);
		$this->assertEquals('music', $document->properties()->index) ;
	}
	
	public function testToArray() {
		$document = new Simples_Document($this->data['standard']) ;
		$res = $document->to('array') ;
		$this->assertEquals('Jim', $res['firstname']) ;
		
		$document = new Simples_Document($this->data['source']) ;
		$res = $document->to('array') ;
		$this->assertEquals('Jim', $res['_source']['firstname']) ;
		$this->assertEquals('music', $res['_index']) ;
				
		// Test source
		$document = new Simples_Document($this->data['standard']) ;
		$res = $document->to('array', array('source' => true)) ;
		$this->assertTrue(isset($res['_source'])) ;
		$res = $document->to('array', array('source' => 'auto')) ;
		$this->assertFalse(isset($res['_source'])) ;
		
		$document = new Simples_Document($this->data['source']) ;
		$res = $document->to('array', array('source' => false)) ;
		$this->assertFalse(isset($res['_source'])) ;
		$res = $document->to('array', array('source' => 'auto')) ;
		$this->assertTrue(isset($res['_source'])) ;

		// Force source
		$document = new Simples_Document($this->data['standard'], array('source' => true)) ;
		$res = $document->to('array', array('source' => true)) ;
		$this->assertTrue(isset($res['_firstname'])) ;
		$res = $document->to('array', array('source' => false)) ;
		$this->assertTrue(empty($res)) ;

		$document = new Simples_Document($this->data['standard'], array('source' => false)) ;
		$res = $document->to('array', array('source' => true)) ;
		$this->assertTrue(isset($res['_source']['firstname'])) ;

		$document = new Simples_Document($this->data['source'], array('source' => true)) ;
		$res = $document->to('array', array('source' => true)) ;
		$this->assertTrue(isset($res['_source']['firstname'])) ;

		$document = new Simples_Document($this->data['source'], array('source' => false)) ;
		$res = $document->to('array', array('source' => true)) ;
		$this->assertTrue(isset($res['_source']['_source']['firstname'])) ;
	}

	public function testClean() {
		// Test clean
		$document = new Simples_Document($this->data['standard'], array('mapping' => array(
			'sub.integer' => 'integer',
			'sub.string' => 'string'
		))) ;
		$res = $document->to('array', array('clean' => true)) ;
		$this->assertFalse(isset($res['empty'])) ;
		$this->assertTrue($res['integer'] === 9.0);
		$this->assertTrue($res['float'] === 1.11);
		$this->assertTrue($res['sub'][0]['integer'] === 9);
		$this->assertTrue($res['sub'][0]['string'] === '9');
	}
}