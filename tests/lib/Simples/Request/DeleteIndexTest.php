<?php

require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'bootstrap.php');

use PHPUnit\Framework\TestCase;

class Simples_Request_DeleteIndexTest extends TestCase {

	public function testDelete() {
		$client = new Simples_Transport_Http();		
		$request = $client->deleteIndex('twitter') ;
		$this->assertEquals(Simples_Request::DELETE, $request->method()) ;
		$this->assertEquals('/twitter/', (string) $request->path()) ;
	}

}