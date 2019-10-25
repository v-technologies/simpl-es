<?php

require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'bootstrap.php');

use PHPUnit\Framework\TestCase;

class Simples_Response_BulkTest extends TestCase {
	
	/**
	 * Good response. 
	 */
	public function testNoException() {
		$response = new Simples_Response_Bulk(array()) ;
		$this->assertTrue($response instanceof Simples_Response_Bulk) ;
	}
	
	/**
	 */
	public function testException() {
		$this->expectException(Simples_Response_Exception::class);
		$response = new Simples_Response_Bulk(array(
			'took' => '1',
			'items' => array(					
				'index' => array(
					array('error' => 'This is an error. Oops.')
				)
			)
		)) ;
	} 
}