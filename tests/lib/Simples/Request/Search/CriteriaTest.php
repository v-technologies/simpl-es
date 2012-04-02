<?php

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'bootstrap.php');

class Simples_Request_Search_CriteriaTest extends PHPUnit_Framework_TestCase {

	public function testNormalize() {
		$criteria = new TestCriteria(array(
			'in' => 'in',
			'value' => 'value'
		)) ;
		$expected = array(
			'in' => 'in',
			'value' => 'value'
		);
		$this->assertEquals($expected, $criteria->get()) ;

		$criteria = new TestCriteria(array(
			'field' => 'in',
			'value' => 'value'
		)) ;
		$expected = array(
			'in' => 'in',
			'value' => 'value'
		);
		$this->assertEquals($expected, $criteria->get()) ;

		$criteria = new TestCriteria(array(
			'in' => 'in',
			'query' => 'value'
		)) ;
		$expected = array(
			'in' => 'in',
			'value' => 'value'
		);
		$this->assertEquals($expected, $criteria->get()) ;

		$criteria = new TestCriteria(array(
			'field' => array('in'),
			'value' => 'value'
		)) ;
		$expected = array(
			'in' => 'in',
			'value' => 'value'
		);
		$this->assertEquals($expected, $criteria->get()) ;
	}

	public function testType() {
		$criteria = new TestCriteria(array(
			'in' => 'in',
			'value' => 'value'
		)) ;
		$this->assertEquals('term', $criteria->type());
		$criteria = new TestCriteria(array(
			'in' => array('in','other'),
			'value' => 'value'
		)) ;
		$this->assertEquals('term', $criteria->type());
		$criteria = new TestCriteria(array(
			'in' => 'in',
			'value' => array('value','other')
		)) ;
		$this->assertEquals('terms', $criteria->type());
		
		$criteria = new TestCriteriaQuery(array(
			'in' => 'in',
			'value' => 'value'
		)) ;
		$this->assertEquals('term', $criteria->type());
	}
}

class TestCriteria extends Simples_Request_Search_Criteria {

}

class TestCriteriaQuery extends Simples_Request_Search_Criteria_Query {

}