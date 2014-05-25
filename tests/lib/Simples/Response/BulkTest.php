<?php

require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'bootstrap.php');

class Simples_Response_BulkTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * Good response. 
	 */
	public function testNoException() {
		$response = new Simples_Response_Bulk(array()) ;
		$this->assertTrue($response instanceof Simples_Response_Bulk) ;
	}
	
	/**
	 *@expectedException Simples_Response_Exception
	 */
	public function testException() {
		$response = new Simples_Response_Bulk(array(
			'body' => array(
				'took' => '1',
				'items' => array(					
					'index' => array(
						array('error' => 'This is an error. Oops.')
					)
				)
			)
		)) ;
	} 
}
