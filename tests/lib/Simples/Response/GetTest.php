<?php

require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'bootstrap.php');

class Simples_Response_GetTest extends PHPUnit_Framework_TestCase {
	
	public function testDocument() {
		$response = new Simples_Response_Get(array(
			'_id' => 1,
			'_source' => array(
				'some' => 'data'
			)
		));
		
		$this->assertTrue($response->document() instanceof Simples_Document) ;
		$this->assertEquals(1, $response->document()->properties()->id) ;
		$this->assertEquals('data', $response->document()->some) ;
	}
}