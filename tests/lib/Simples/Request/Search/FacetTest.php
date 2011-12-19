<?php

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'bootstrap.php');

class Simples_Request_Search_FacetTest extends PHPUnit_Framework_TestCase {

	public function testBase() {
		$facet = new Simples_Request_Search_Facet('category_id') ;
		$this->assertEquals('term',$facet->type()) ;
		$this->assertEquals('category_id',$facet->get('in')) ;
	}
	
	public function testSimpleField() {
		$facet = new Simples_Request_Search_Facet('category_id') ;
		$res = $facet->to('array') ;
		$expected = array(
			'category_id' => array(
				'term' => array(
					'field' => 'category_id',
				)
			)
		) ;
		$this->assertEquals($expected, $res) ;
	}
	
	public function testMultipleFields() {
		$facet = new Simples_Request_Search_Facet(array('name' => 'category', 'fields' => array('Offers.category_id','Users.category_id'))) ;
		$res = $facet->to('array') ;
		$expected = array(
			'category' => array(
				'term' => array(
					'fields' => array('Offers.category_id','Users.category_id'),
				)
			)
		) ;
		$this->assertEquals($expected, $res) ;
	}

}