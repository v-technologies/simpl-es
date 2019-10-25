<?php

class Simples_Request_DeleteIndexTest extends Simples_HttpTestCase {

	public function testDelete() {
		$request = $this->client->deleteIndex('twitter') ;
		$this->assertEquals(Simples_Request::DELETE, $request->method()) ;
		$this->assertEquals('/twitter/', (string) $request->path()) ;
	}

}