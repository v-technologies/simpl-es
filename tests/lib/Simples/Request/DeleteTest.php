<?php

class Simples_Request_DeleteTest extends Simples_HttpTestCase {

	public function testDelete() {
		$client = $this->client;
		$this->assertTrue($client->index(
			array(
				'content' => 'Pliz, pliz, delete me !'
			),
			array(
				'index' => 'twitter',
				'type' => 'tweet',
				'id' => 'test_get'
			))->ok);
		
		$delete_object = $client->delete(1, array(
			'index' => 'twitter', 
			'type' => 'tweet', 
		)) ;
		$this->assertEquals('/twitter/tweet/1/', (string) $delete_object->path()) ;
		
		$this->assertEquals(true, $delete_object->ok) ;
		$res = $client->get(1, array(
			'index' => 'twitter',
			'type' => 'tweet',
		)) ;
		$this->assertFalse($res->exists);
	}
}