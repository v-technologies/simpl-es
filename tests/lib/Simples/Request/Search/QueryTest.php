<?php

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'bootstrap.php');

class Simples_Request_Search_QueryTest extends PHPUnit_Framework_TestCase {

	public function testConstruct() {
		$query = new Simples_Request_Search_Query() ;
		$res = $query->to('array') ;
		$this->assertTrue(isset($res['match_all'])) ;
		
		$query->set('scharrier') ;
		$res = $query->to('array') ;
		$this->assertEquals('scharrier', $res['query_string']['query']) ;
	}

}