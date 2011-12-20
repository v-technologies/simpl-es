<?php

require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) . DIRECTORY_SEPARATOR . 'bootstrap.php');

class Simples_Request_Search_Builder_FacetsTest extends PHPUnit_Framework_TestCase {

	public function testConstruct() {
		$facets = new Simples_Request_Search_Builder_Facets() ;
		$res = $facets->to('array') ;
		$this->assertTrue(empty($res)) ;
		
		$facets->add('category')->add('user_id') ;
		
		$res = $facets->to('array') ;
		$expected = array('category','user_id') ;
		
		$this->assertEquals(2, count($facets)) ; 
		$this->assertEquals($expected, array_keys($res)) ;
		
		$facets->add(array('order' => 'term')) ;
		$this->assertEquals(2, count($facets)) ; 
		
		$res = $facets->to('array') ;
		$expected = array(
			'terms' => array(
				'field' => 'user_id',
				'order' => 'term'
			)
		) ;
		$this->assertEquals($expected, $res['user_id']) ;
	}
}