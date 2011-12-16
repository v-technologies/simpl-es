<?php

require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) . DIRECTORY_SEPARATOR . 'bootstrap.php');

class Simples_Request_Search_Criteria_FilterTest extends PHPUnit_Framework_TestCase {

	public function testType() {
		$query = new Simples_Request_Search_Criteria_Filter() ;
		$this->assertEquals(null, $query->type()) ;
		
		$query = new Simples_Request_Search_Criteria_Filter(array('query' => 'scharrier', 'in' => 'name')) ;
		$this->assertEquals('term', $query->type()) ;
		$query = new Simples_Request_Search_Criteria_Filter(array('query' => 'scharrier 123', 'in' => 'name')) ;
		$this->assertEquals('term', $query->type()) ;
		$query = new Simples_Request_Search_Criteria_Filter(array('query' => 'scharrier AND 123', 'in' => 'name')) ;
		$this->assertEquals('term', $query->type()) ;
		

		$query = new Simples_Request_Search_Criteria_Filter(array(
			'query' => array('sebastien','charrier'),
			'in' => 'field'
		)) ;
		$this->assertEquals('terms', $query->type()) ;		
	}
	
	public function testPrepare() {
		// Simple term
		$query = new Simples_Request_Search_Criteria_Filter(array('query' => 'scharrier', 'in' => 'username')) ;
		$res = $query->to('array') ;
		$expected = array(
			'term' => array(
				'username' => 'scharrier'
			)
		) ;
		$this->assertEquals($expected, $res) ;		
	}

}