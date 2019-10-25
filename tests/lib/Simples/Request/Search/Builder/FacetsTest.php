<?php

require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) . DIRECTORY_SEPARATOR . 'bootstrap.php');

use PHPUnit\Framework\TestCase;

class Simples_Request_Search_Builder_FacetsTest extends TestCase {

	public function testConstruct() {
		$facets = new Simples_Request_Search_Builder_Facets() ;
		$res = $facets->to('array') ;
		$this->assertTrue(empty($res)) ;
		
		$facets->add('category', array('size'=>5))->add('user_id') ;
		
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
	
	public function testFiltered() {
		$facets = new Simples_Request_Search_Builder_Facets() ;
		$facets
			->add('category')
			->add('user_id')
			->add(array('size' => 5))
			->filtered()
				->should()
					->field('status')->match('valid') 
					->field('firstname')->match(array('Jim','Ray')) ;
		$res = $facets->to('array') ;

		$this->assertEquals(array('category','user_id'), array_keys($res)) ;
		$this->assertTrue(isset($res['user_id']['facet_filter'])) ;
		$this->assertTrue(isset($res['user_id']['facet_filter']['bool']['should'][0]['term']['status'])) ;
		$this->assertTrue(isset($res['user_id']['facet_filter']['bool']['should'][1]['terms']['firstname'])) ;
	}
}