<?php
require_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'bootstrap.php') ;

class Simples_TransportTest extends PHPUnit_Framework_TestCase {
	
	public function testMagic() {
		// Defaults index / type
		$client = new Simples_Transport_Fake(array(
			'index' => 'twitter',
			'type' => 'tweet'
		)) ;
		
		$body = $client->get(array('id' => 1))->body() ;
		$this->assertEquals('twitter',$body['index']) ;
		$this->assertEquals('tweet',$body['type']) ;
		
		$client->config('index', 'facebook') ;
		$body = $client->get(array('id' => 2))->body() ;
		$this->assertEquals('facebook',$body['index']) ;
		
		// Magic params
		$body = $client->get(666)->body() ;
		$this->assertEquals('facebook',$body['index']) ;
		$this->assertEquals('tweet',$body['type']) ;
		$this->assertEquals(666,$body['id']) ;
	}
}

class Simples_Transport_Fake extends Simples_Transport {
	
	protected $_connected ;
	
	public function connect() {
		$this->_connected = true ;
		return $this ;
	}
	
	public function disconnect() {
		$this->_connected = false ;
		return $this ;
	}
	
	public function connected() {
		return $this->_connected ;
	}
	
	public function call($path = null, $method = 'GET', $data = null) {
		return new Simples_Response() ;
	}
}