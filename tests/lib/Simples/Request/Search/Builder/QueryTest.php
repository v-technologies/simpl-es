<?php

require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) . DIRECTORY_SEPARATOR . 'bootstrap.php');

class Simples_Request_Search_Builder_QueryTest extends PHPUnit_Framework_TestCase {

	public function testConstruct() {
		$query = new Simples_Request_Search_Builder_Query() ;
		$res = $query->to('array') ;
		$this->assertTrue(isset($res['match_all'])) ;
		
		$query->add('scharrier') ;
		$res = $query->to('array') ;
		$expected = array(
			'query_string' => array(
				'query' => 'scharrier'
			)
		) ;
		$this->assertEquals($expected, $res) ;
	}
	
	public function testFluid() {
		$request = new Simples_Request_Search() ;
		$builder = new Simples_Request_Search_Builder_Query($request) ;
		$res = $builder->add('scharrier') ;
		$this->assertTrue($res instanceof Simples_Request_Search) ;
		
		$builder = new Simples_Request_Search_Builder_Query() ;
		$res = $builder->add('scharrier') ;
		$this->assertTrue($res instanceof Simples_Request_Search_Builder_Query) ;
	}
	
	public function testMerged() {
		$query = new Simples_Request_Search_Builder_Query() ;
		$query->match('scharrier')->in('username') ;
		$res = $query->to('array') ;
		$expected = array(
			'term' => array('username' => 'scharrier')
		) ;
		$this->assertEquals($res, $expected) ;
		
		$query = new Simples_Request_Search_Builder_Query() ;
		$query->field('username')->match('scharrier') ;
		$res2 = $query->to('array') ;
		$this->assertEquals($res, $res2) ;
		
		$query = new Simples_Request_Search_Builder_Query() ;
		$query->fields(array('username', 'retweet'))->match('scharrier') ;
		$res = $query->to('array') ;
		$expected = array(
			'query_string' => array(
				'query' => 'scharrier',
				'fields' => array('username','retweet')
			)
		) ;
		$this->assertEquals($res, $expected) ;
		
		$query = new Simples_Request_Search_Builder_Query() ;
		$query->match('scharrier') ;
		$query->in(array('username', 'retweet')) ;
		$res = $query->to('array') ;
		$this->assertEquals($res, $expected) ;
		
		$query = new Simples_Request_Search_Builder_Query() ;
		$query->add(array(
			'query' => 'scharrier',
			'in' => array('username', 'retweet')
		)) ;
		$res = $query->to('array') ;
		$this->assertEquals($res, $expected) ;
	}
	
	public function testNotMerged() {
		$query = new Simples_Request_Search_Builder_Query() ;
		
		$query->must()
				->match('scharrier')->in(array('username','retweet'))
				->field('category_id')->match(array('1','2','3'))
			  ->not()
				->field('type')->match('administreur') ;
		
		$res = $query->to('array') ;
		$this->assertEquals(2, count($res['bool']['must'])) ;
		$this->assertEquals(1, count($res['bool']['must_not'])) ;
	}

}