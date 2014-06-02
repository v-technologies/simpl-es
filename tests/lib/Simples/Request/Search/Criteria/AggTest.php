<?php

require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) . DIRECTORY_SEPARATOR . 'bootstrap.php');

class Simples_Request_Search_Criteria_AggTest extends PHPUnit_Framework_TestCase {

	public function testBase() {
		$aggregate = new Simples_Request_Search_Criteria_Agg('interests');
		$this->assertEquals('terms',$aggregate->type());
		$this->assertEquals('interests',$aggregate->get('in'));
	}

	public function testIn() {
		$aggregate = new Simples_Request_Search_Criteria_Agg('interests');
		$expected = array(
			'interests' => array(
				'terms' => array(
					'field' => 'interests',
				)
			)
		);
		$this->assertEquals($expected,$aggregate->to('array'));

		$aggregate = new Simples_Request_Search_Criteria_Agg(array('in' => 'interests'));
		$expected = array(
			'interests' => array(
				'terms' => array(
					'field' => 'interests',
				)
			)
		);
		$this->assertEquals($expected,$aggregate->to('array'));

		$aggregate = new Simples_Request_Search_Criteria_Agg(array('field' => 'interests'));
		$expected = array(
			'interests' => array(
				'terms' => array(
					'field' => 'interests',
				)
			)
		);
		$this->assertEquals($expected,$aggregate->to('array'));

		$aggregate = new Simples_Request_Search_Criteria_Agg(array('name' => 'all_interests','field' => 'interests'));
		$expected = array(
			'all_interests' => array(
				'terms' => array(
					'field' => 'interests',
				)
			)
		);
		$this->assertEquals($expected,$aggregate->to('array'));

		$aggregate = new Simples_Request_Search_Criteria_Agg(array('field' => 'interests'), array('type' => 'avg'));
		$expected = array(
			'interests' => array(
				'avg' => array(
					'field' => 'interests',
				)
			)
		);
		$this->assertEquals($expected,$aggregate->to('array'));

		$aggregate = new Simples_Request_Search_Criteria_Agg('interests', array('type' => 'avg'));
		$expected = array(
			'interests' => array(
				'avg' => array(
					'field' => 'interests',
				)
			)
		);
		$this->assertEquals($expected,$aggregate->to('array'));
	}

	/**
	 * @expectedException Simples_Request_Exception
	 */
	public function testWrongType() {
		$aggregate = new Simples_Request_Search_Criteria_Agg('interests', array('type' => 'wrongType'));
	}

	public function testSubAggregation() {
		$aggregate = new Simples_Request_Search_Criteria_Agg('interests');
		$aggregate->agg('name');
		$expected = array(
			'interests' => array(
				'terms' => array(
					'field' => 'interests',
				),
				'aggs' => array(
					'name' => array(
						'terms' => array(
							'field' => 'name',
						)
					)
				)
			)
		);
		$this->assertEquals($expected,$aggregate->to('array'));
	}

	public function testMultipleSubAggregation() {
		$aggregate = new Simples_Request_Search_Criteria_Agg('interests');
		$aggregate->aggs(array('name', array('age', array('type' => 'avg'))));
		$expected = array(
			'interests' => array(
				'terms' => array(
					'field' => 'interests',
				),
				'aggs' => array(
					'name' => array(
						'terms' => array(
							'field' => 'name',
						)
					),
					'age' => array(
						'avg' => array(
							'field' => 'age',
						)
					)
				)
			)
		);
		$this->assertEquals($expected,$aggregate->to('array'));
	}

	public function testDeepSubAggregation() {
		$aggregate = new Simples_Request_Search_Criteria_Agg('interests', array(
			'aggs' => array(
				array(
					'name',
					array(
						'aggs' => array(
							'age'
						)
					)
				)
			)
		));
		$expected = array(
			'interests' => array(
				'terms' => array(
					'field' => 'interests',
				),
				'aggs' => array(
					'name' => array(
						'terms' => array(
							'field' => 'name',
						),
						'aggs' => array(
							'age' => array(
								'terms' => array(
									'field' => 'age',
								)
							)
						)
					)
				)
			)
		);
		$this->assertEquals($expected,$aggregate->to('array'));
	}

	/**
	 * @expectedException Simples_Request_Exception
	 */
	public function testWrongSubAggregation() {
		$aggregate = new Simples_Request_Search_Criteria_Agg('interests', array('type' => 'avg'));
		$aggregate->agg('name');
	}
}
