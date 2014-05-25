<?php
require_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'bootstrap.php') ;

class Simples_ResponseTest extends PHPUnit_Framework_TestCase {
	
	public function testConstruct() {
		$response = new Simples_Response(array()) ;
		$this->assertTrue($response instanceof Simples_Response) ;
	}

	public function testSet() {
		$response = new Simples_Response(array()) ;
		$response->set('field','value') ;
		$this->assertEquals('value', $response->field) ;

		$response->set(array('field' => 'value2')) ;
		$this->assertEquals('value2', $response->field) ;
	}
	
	public function testAccessors() {
		$response = new Simples_Response(array(
			'body' => array(
				'status' => 200,
				'version' => array(
					'number' => '0.18.5'
				)
			),
			'http' => array(
				'http_code' => 200
			)
		)) ;
		$this->assertTrue($response->get('body') instanceof Simples_Response) ;
		$this->assertTrue($response->body instanceof Simples_Response) ;
		$this->assertEquals(200, $response->body->status);
		$this->assertEquals(200, $response->status);
		$this->assertTrue($response->body->version instanceof Simples_Response) ;
		$this->assertEquals('0.18.5', $response->body->version->number) ;
		$this->assertTrue($response->http instanceof Simples_Response) ;
		$this->assertEquals(200, $response->http->http_code);
		$this->assertEquals(200, $response->http_code);
	}
	
	/**
	 * @expectedException \Simples_Response_Exception
	 * @expectedExceptionMessage My error message
	 */
	public function testHttpException() {
		$response = new Simples_Response(array(
			'body' => array(
				'error' => 'My error message',
				'status' => 400
			)
		));
	}

	/**
	 * @expectedException \Simples_Response_Exception
	 * @expectedExceptionMessage An error has occured on a shard during request parsing
	 */
	public function testParsingException() {
		$response = new Simples_Response(array(
			'body' => array(
				'_shards' => array(
					'failed' => 2
				)
			)
		));
	}

	/**
	 * @expectedException \Simples_Response_Exception
	 * @expectedExceptionMessage Some errors have occured on a shard during request parsing : Shard error
	 */
	public function testShardException() {
		$response = new Simples_Response(array(
			'body' => array(
				'_shards' => array(
					'failed' => 2,
					'failures' => array(
						array('reason' => 'Shard error')
					)
				)
			)
		));
	}
}
