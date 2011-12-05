<?php
require_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'bootstrap.php') ;

class Simples_RequestTest extends PHPUnit_Framework_TestCase {
	
	public function testConstruct() {
		$request = new Simples_Request_Custom() ;
		$this->assertTrue($request instanceof Simples_Request) ;
	}
	
	public function testPath() {
		$request = new Simples_Request_Custom() ;
		$this->assertEquals('/_status', $request->path()) ;
		
	}
	
	public function testMethod() {
		$request = new Simples_Request_Custom() ;
		$this->assertEquals(Simples_Request::GET, $request->method()) ;
		
	}
	
	public function testExecute() {
		$request = new Simples_Request_Custom() ;
		$res = $request->execute() ;
		$this->assertTrue($request->execute() instanceof Simples_Response) ;
		
		$res = $request->client(new Simples_Transport_Http())->execute() ;
		$this->assertTrue($res->get('ok') === true) ;
	}
}

class Simples_Request_Custom extends Simples_Request {
	
	protected $_path = '/_status' ;
	
	protected $_method = Simples_Request::GET ;
}