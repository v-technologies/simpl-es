<?php

require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'bootstrap.php');

use PHPUnit\Framework\TestCase;

class Simples_Response_ExceptionTest extends TestCase {
	
	public function testUsage() {
		// String
		$exception = new Simples_Response_Exception('Test string message') ;
		$this->assertEquals($exception->getMessage(), $exception->error) ;
		
		// Array
		$exception = new Simples_Response_Exception(array('error' => 'Test array message')) ;
		$this->assertEquals('Test array message', $exception->error) ;
		
		// Array
		$exception = new Simples_Response_Exception(array('badkey' => 'Test array message')) ;
		$this->assertEquals('An error has occured but cannot be decoded', $exception->error) ;
		$this->assertEquals('Test array message', $exception->badkey) ;
	}
}