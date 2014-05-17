<?php

require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'bootstrap.php');

class Simples_Request_DeleteIndexTest extends PHPUnit_Framework_TestCase {

	public function testDelete() {
		$client = new Simples_Transport_Http(array('host' => ES_HOST));
		$request = $client->deleteIndex('twitter') ;
		$this->assertEquals(Simples_Request::DELETE, $request->method()) ;
		$this->assertEquals('/twitter/', (string) $request->path()) ;
	}

}