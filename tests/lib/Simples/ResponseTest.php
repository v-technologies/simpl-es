<?php
require_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'bootstrap.php') ;

class Simples_ResponseTest extends PHPUnit_Framework_TestCase {
	
	public function testConstruct() {
		$request = new Simples_Response() ;
		$this->assertTrue($request instanceof Simples_Response) ;
	}
	
	public function testAccessors() {
		$request = new Simples_Response(array(
			'ok' => true,
			'version' => array(
				'number' => '0.18.5'
			)
		)) ;
		$this->assertEquals(true, $request->ok);
		$this->assertTrue($request->version instanceof Simples_Response) ;
		$this->assertEquals('0.18.5', $request->version->number) ;
	}
}