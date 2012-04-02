<?php

require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) . DIRECTORY_SEPARATOR . 'bootstrap.php');

class Simples_Request_Search_Criteria_QueryTest extends PHPUnit_Framework_TestCase {

	public function testType() {
		$query = new Simples_Request_Search_Criteria_Query() ;
		$this->assertEquals('match_all', $query->type()) ;
		
		$query = new Simples_Request_Search_Criteria_Query('scharrier') ;
		$this->assertEquals('query_string', $query->type()) ;
		
		$query = new Simples_Request_Search_Criteria_Query(array('query' => 'scharrier', 'in' => 'name')) ;
		$this->assertEquals('term', $query->type()) ;
		$query = new Simples_Request_Search_Criteria_Query(array('query' => 'scharrier 123', 'in' => 'name')) ;
		$this->assertEquals('term', $query->type()) ;
		$query = new Simples_Request_Search_Criteria_Query(array('query' => 'scharrier AND 123', 'in' => 'name')) ;
		$this->assertEquals('query_string', $query->type()) ;
		
		$query = new Simples_Request_Search_Criteria_Query('*char*') ;
		$this->assertEquals('query_string', $query->type()) ;
		$query = new Simples_Request_Search_Criteria_Query(array('query' => '*char*', 'in' => 'name')) ;
		$this->assertEquals('query_string', $query->type()) ;
		$query = new Simples_Request_Search_Criteria_Query('user:scharrier*') ;
		$this->assertEquals('query_string', $query->type()) ;
		$query = new Simples_Request_Search_Criteria_Query(array('query' => 'user:scharrier*')) ;
		$this->assertEquals('query_string', $query->type()) ;
		
		$query = new Simples_Request_Search_Criteria_Query(array('query' => 'scharrier', 'in' => array('username','retweet'))) ;
		$this->assertEquals('query_string', $query->type()) ;
		
	}
	
	public function testPrepare() {
		// Empty criteria
		$query = new Simples_Request_Search_Criteria_Query() ;
		$res = $query->to('array') ;
		$expected = array(
			'match_all' => new stdClass()
		) ;
		$this->assertEquals($expected, $res) ;
		
		// Simple query_string
		$query = new Simples_Request_Search_Criteria_Query('scharrier') ;
		$res = $query->to('array') ;
		$expected = array(
			'query_string' => array(
				'query' => 'scharrier'
			)
		) ;
		$this->assertEquals($expected, $res) ;
		
		// Simple term
		$query = new Simples_Request_Search_Criteria_Query(array('query' => 'scharrier', 'in' => 'username')) ;
		$res = $query->to('array') ;
		$expected = array(
			'term' => array(
				'username' => 'scharrier'
			)
		) ;
		$this->assertEquals($expected, $res) ;
		
		// Simple term in multiple fields
		$query = new Simples_Request_Search_Criteria_Query(array('query' => 'scharrier', 'in' => array('username','retweet'))) ;
		$res = $query->to('array') ;
		$expected = array(
			'query_string' => array(
				'query' => 'scharrier',
				'fields' => array('username','retweet')
			)
		) ;
		$this->assertEquals($expected, $res) ;
		
		// Multiple terms in multiple fields
		$query = new Simples_Request_Search_Criteria_Query(array(
			'query' => array('sebastien','charrier'), 
			'in' => array('username','retweet')
		)) ;
		$res = $query->to('array') ;
		$expected = array(
			'query_string' => array(
				'query' => 'sebastien AND charrier',
				'fields' => array('username','retweet')
			)
		) ;
		$this->assertEquals($expected, $res) ;
		
		// Standard request (same as previous)
		$query = new Simples_Request_Search_Criteria_Query(array(
			'query' => 'sebastien AND charrier',
			'fields' => array('username','retweet')
		), array('type' => 'query_string')) ;
		$res2 = $query->to('array') ;
		$this->assertEquals($res, $res2) ;
		
		// Same with "or"
		$query = new Simples_Request_Search_Criteria_Query(array(
			'query' => array('sebastien','charrier'), 
			'in' => array('username','retweet')
		), array('mode' => 'or')) ;
		$res = $query->to('array') ;
		$expected = array(
			'query_string' => array(
				'query' => 'sebastien OR charrier',
				'fields' => array('username','retweet')
			)
		) ;
		$this->assertEquals($expected, $res) ;
		
		// Terms
		$query = new Simples_Request_Search_Criteria_Query(array(
			'query' => array('sebastien','charrier'), 
			'in' => 'username'
		)) ;
		$res = $query->to('array') ;
		$expected = array(
			'terms' => array(
				'username' => array('sebastien', 'charrier'),
			)
		) ;
		$this->assertEquals($expected, $res) ;
		
	}

}