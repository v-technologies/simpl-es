<?php

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'bootstrap.php');

use PHPUnit\Framework\TestCase;

class Simples_Request_Search_FacetTest extends TestCase {

	public function testBase() {
		$facet = new Simples_Request_Search_Facet('category_id') ;
		$this->assertEquals('terms',$facet->type()) ;
		$this->assertEquals('category_id',$facet->get('in')) ;
	}

	public function testIn() { 
		$facet = new Simples_Request_Search_Facet('category_id') ;
		$res = $facet->to('array') ;
		$expected = array(
			'category_id' => array(
				'terms' => array(
					'field' => 'category_id',
				)
			)
		) ;
		
		$facet = new Simples_Request_Search_Facet(array('in' => 'category_id')) ;
		$res = $facet->to('array') ;
		$expected = array(
			'category_id' => array(
				'terms' => array(
					'field' => 'category_id',
				)
			)
		) ;
		
		$facet = new Simples_Request_Search_Facet(array('field' => 'category_id')) ;
		$res = $facet->to('array') ;
		$expected = array(
			'category_id' => array(
				'terms' => array(
					'field' => 'category_id',
				)
			)
		) ;
	}
	
	public function testMultipleFields() {
		$facet = new Simples_Request_Search_Facet(array('name' => 'category', 'fields' => array('Offers.category_id','Users.category_id'))) ;
		$res = $facet->to('array') ;
		$expected = array(
			'category' => array(
				'terms' => array(
					'fields' => array('Offers.category_id','Users.category_id'),
				)
			)
		) ;
		$this->assertEquals($expected, $res) ;
	}
	
	public function testFluid() {
		$facet = new Simples_Request_Search_Facet('category') ;
		$facet->filtered()->field('type')->match('administrateur') ;
		$res = $facet->to('array') ;
		$this->assertEquals('administrateur', $res['category']['facet_filter']['term']['type']) ;
		
		$facet->filtered(array(
			array('in' => 'status', 'value' => 'validated'),
			array('in' => 'valid', 'value' => true)
		));
		
		$res = $facet->to('array') ;
		$this->assertEquals(3, count($res['category']['facet_filter']['bool']['must'])) ;
	}

	/**
	 */
	public function testTermsException() {
		$this->expectException(Simples_Request_Exception::class);
		$facet = new Simples_Request_Search_Facet(array(
				'name' => 'termsFacet',
				'size' => 20
			), array(
				'type' => 'terms'
			)) ;
		$res = $facet->to('array') ;
	}

	public function testValueField() {
		$facet = new Simples_Request_Search_Facet(array(
			'name' => 'test_value_field','value_field' => 'category_id'
		)) ;
		$res = $facet->to('array') ;
		$expected = array(
			'test_value_field' => array(
				'terms' => array(
					'value_field' => 'category_id',
				)
			)
		) ;

		$this->assertEquals($expected, $res) ;
	}

	public function testFilter() {
		$facet = new Simples_Request_Search_Facet(array(
				'name' => 'today',
				'term' => array('day' => '2015-03-03')
			), array(
				'type' => 'filter'
			)) ;
		$res = $facet->to('array') ;

		$expected = array(
			'today' => array(
				'filter' => array(
					'term' => array('day' => '2015-03-03')
				)
			)
		);

		$this->assertEquals($expected, $res) ;
	}

	public function testQuery() {
		$facet = new Simples_Request_Search_Facet(array(
				'name' => 'today',
				'term' => array('day' => '2015-03-03')
			), array(
				'type' => 'query'
			)) ;
		$res = $facet->to('array') ;

		$expected = array(
			'today' => array(
				'query' => array(
					'term' => array('day' => '2015-03-03')
				)
			)
		);

		$this->assertEquals($expected, $res) ;
	}

	public function testStastistical() {

		$facet = new Simples_Request_Search_Facet(array(
			'name' => 'stat1',
			'script' => 'doc[\'num1\'].value + doc[\'num2\'].value'
		), array('type' => 'statistical')) ;

		$res = $facet->to('array') ;

		$expected = array(
			'stat1' => array(
				'statistical' => array(
					'script' => 'doc[\'num1\'].value + doc[\'num2\'].value'
				)
			)
		);

		$this->assertEquals($expected, $res) ;
	}
}
