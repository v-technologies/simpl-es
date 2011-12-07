<?php

require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'bootstrap.php');

class Simples_Request_DeleteTest extends PHPUnit_Framework_TestCase {

	public function testDelete() {
		$client = new Simples_Transport_Http();
		$this->assertTrue($client->index(array(
			'index' => 'twitter',
			'type' => 'tweet',
			'id' => 'test_get',
			'data' => array(
				'content' => 'Pliz, pliz, delete me !'
			)
		))->ok);
		
		$delete_index = $client->delete(array(
			'index' => 'twitter'
		)) ;
		$this->assertEquals('/twitter/', $delete_index->path()) ;
		
		$delete_type = $client->delete(array(
			'index' => 'twitter',
			'type' => 'tweet'
		)) ;
		$this->assertEquals('/twitter/tweet/', $delete_type->path()) ;
		
		$delete_object = $client->delete(array(
			'index' => 'twitter', 
			'type' => 'tweet', 
			'id' => 1
		)) ;
		$this->assertEquals('/twitter/tweet/1/', $delete_object->path()) ;
		
		$this->assertEquals(true, $delete_object->ok) ;
		$res = $client->get(array(
			'index' => 'twitter',
			'type' => 'tweet',
			'id' => 1
		)) ;
		$this->assertFalse($res->exists);
		
		$this->assertEquals(true, $delete_type->ok) ;
		
		$this->assertEquals(true, $delete_index->ok) ;
		$this->assertEquals(404, $client->status(array(
			'index' => 'twitter'
		))->status);
		
	}

}