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

	public function testPrepare() {
		// Term
		$criteria = new TestCriteria(array(
			'in' => 'in',
			'value' => 'value'
		), array('type' => 'term')) ;
		$res = $criteria->to('array') ;
		$expected = array(
			'term' => array(
				'in' => 'value'
			)
		);
		$this->assertEquals($expected, $res) ;

		// Terms
		$criteria = new TestCriteria(array(
			'in' => 'in',
			'values' => array('value1','value2')
		), array('type' => 'terms')) ;
		$res = $criteria->to('array') ;
		$expected = array(
			'terms' => array(
				'in' => array('value1','value2')
			)
		);
		$this->assertEquals($expected, $res) ;

		// Match
		$criteria = new TestCriteria(array(
			'in' => 'field1',
			'value' => 'value1'
		), array('type' => 'match')) ;
		$res = $criteria->to('array') ;
		$expected = array(
			'match' => array(
				'field1' => 'value1'
			),
		);
		$this->assertEquals($expected, $res) ;

		$criteria = new TestCriteria(array(
			'in' => array('field1', 'field2'),
			'value' => 'value1'
		), array('type' => 'match'));
		$res = $criteria->to('array');
		$expected = array(
			'bool' => array(
				'should' => array(
					array(
						'match' => array(
							'field1' => 'value1',
						)
					),
					array(
						'match' => array(
							'field2' => 'value1',
						),
					)
				),
			),
		);
		$this->assertEquals($expected, $res);

		// Missing
		$criteria = new TestCriteria(array(
			'field' => 'my_field'
		), array('type' => 'missing')) ;
		$res = $criteria->to('array') ;
		$expected = array(
			'missing' => array(
				'field' => 'my_field'
			)
		);
		$this->assertEquals($expected, $res) ;

		// Exists
		$criteria = new TestCriteria(array(
			'field' => 'my_field'
		), array('type' => 'exists')) ;
		$res = $criteria->to('array') ;
		$expected = array(
			'exists' => array(
				'field' => 'my_field'
			)
		);
		$this->assertEquals($expected, $res) ;

		// Ids
		$criteria = new TestCriteria(array(
			'values' => array('1','2','3'),
			'type' => 'my_type'
		), array('type' => 'ids')) ;
		$res = $criteria->to('array') ;
		$expected = array(
			'ids' => array(
				'values' => array('1','2','3'),
				'type' => "my_type"
			)
		);
		$this->assertEquals($expected, $res) ;

		// Range
		$criteria = new TestCriteria(array(
			'field' => 'in',
			'from' => 1,
			'to' => 10
		), array('type' => 'range')) ;
		$res = $criteria->to('array') ;
		$expected = array(
			'range' => array(
				'in' => array('from' => 1,'to' => 10)
			)
		);
		$this->assertEquals($expected, $res) ;

		// Multiple ranges
		$criteria = new TestCriteria(array(
			'field' => 'in',
			'include_upper' => true,
			'ranges' => array(
				array('from' => 1,'to' => 10),
				array('from' => 11,'to' => 20)
			)
		), array('type' => 'range')) ;
		$res = $criteria->to('array') ;
		$expected = array(
			'bool' => array(
				'should' => array(
					array(
						'range' => array(
							'in' => array('from' => 1,'to' => 10, 'include_upper' => true)
						)
					),
					array(
						'range' => array(
							'in' => array('from' => 11,'to' => 20, 'include_upper' => true)
						)
					)
				)
			)
		);
		$this->assertEquals($expected, $res) ;

		// Geo distance
		$criteria = new TestCriteria(array(
			'field' => 'location',
			'values' => array('lat' => 1, 'lon' => 2),
			'distance' => '10km'
		), array('type' => 'geo_distance')) ;
		$res = $criteria->to('array') ;
		$expected = array(
			'geo_distance' => array(
				'location' => array('lat' => 1, 'lon' => 2) ,
				'distance' => '10km'
			)
		);
		$this->assertEquals($expected, $res) ;
		$criteria = new TestCriteria(array(
			'field' => 'location',
			'lat' => 1, 
			'lon' => 2,
			'distance' => '10km'
		), array('type' => 'geo_distance')) ;
		$res = $criteria->to('array') ;
		$this->assertEquals($expected, $res) ;

	}

	public function testNested() {
		$criteria = new TestCriteria(array(
			'path' => 'nestedPath',
			'query' => array(),
		), array('type' => 'nested'));

		$this->assertEquals(array(
			'nested' => array(
				'path'			=> 'nestedPath',
				'query'			=> array(),
				'score_mode'	=> 'avg'
			)
		), $criteria->to('array'));

		$criteria = new TestCriteria(array(
			'path'			=> 'nestedPath',
			'query'			=> array(),
			'score_mode'	=> 'total',
		), array('type' => 'nested'));

		$this->assertEquals(array(
			'nested' => array(
				'path'			=> 'nestedPath',
				'query'			=> array(),
				'score_mode'	=> 'total'
			)
		), $criteria->to('array'));
	}

	/**
	 * @expectedException Simples_Request_Exception
	 */
	public function testNestedException() {
		$criteria = new TestCriteria(array(
			'path' => 'nestedPath',
			'score_mode' => 'wrong',
		), array('type' => 'nested'));

		$criteria->to('array');
	}
}

class TestCriteria extends Simples_Request_Search_Criteria {

}

class TestCriteriaQuery extends Simples_Request_Search_Criteria_Query {

}
