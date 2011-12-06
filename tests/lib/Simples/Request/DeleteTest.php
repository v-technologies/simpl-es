<?php

require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'bootstrap.php');

class Simples_Request_DeleteTest extends PHPUnit_Framework_TestCase {

	public function testDelete() {
		$client = new Simples_Transport_Http();
		$this->assertTrue($client->index('twitter', 'tweet', array(
			'_id' => 'test_get',
			'content' => 'Pliz, pliz, delete me !'
		))->ok);
		
		$delete_index = $client->delete('twitter') ;
		$this->assertEquals('/twitter/', $delete_index->path()) ;
		
		$delete_type = $client->delete('twitter','tweet') ;
		$this->assertEquals('/twitter/tweet/', $delete_type->path()) ;
		
		$delete_object = $client->delete('twitter', 'tweet', 1) ;
		$this->assertEquals('/twitter/tweet/1/', $delete_object->path()) ;
		
		$this->assertEquals(true, $delete_object->ok) ;
		$res = $client->get('twitter','tweet',1)->execute() ;
		$this->assertFalse($client->get('twitter','tweet',1)->exists);
		
		$this->assertEquals(true, $delete_type->ok) ;
		
		$this->assertEquals(true, $delete_index->ok) ;
		$this->assertEquals(404, $client->status('twitter')->status);
		
	}

}