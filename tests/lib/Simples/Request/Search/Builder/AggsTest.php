<?php

require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) . DIRECTORY_SEPARATOR . 'bootstrap.php');

class Simples_Request_Search_Builder_AggsTest extends PHPUnit_Framework_TestCase {

	public function testConstruct() {
		$aggregates = new Simples_Request_Search_Builder_Aggs();
		$response = $aggregates->to('array');
		$this->assertTrue(empty($response));

		$aggregates->add('category')->add('user_id');

		$response = $aggregates->to('array');

		$this->assertEquals(2, count($aggregates)) ;
		$this->assertEquals(array('category','user_id'), array_keys($response));
	}

	public function testOutput() {
		$aggregates = new Simples_Request_Search_Builder_Aggs();
		$aggregates->add('interests');

		$this->assertEquals(array(
			'interests' => array(
				'terms' => array (
					'field' => 'interests'
				)
			)
		), $aggregates->to('array'));

		$aggregates = new Simples_Request_Search_Builder_Aggs();
		$aggregates->add('age', array('type' => 'avg'));

		$this->assertEquals(array(
			'age' => array(
				'avg' => array (
					'field' => 'age'
				)
			)
		), $aggregates->to('array'));
	}
}
