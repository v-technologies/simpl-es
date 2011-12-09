<?php

require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'bootstrap.php');

class Simples_Request_DeleteTest extends PHPUnit_Framework_TestCase {

	public function testDelete() {
		$client = new Simples_Transport_Http();
		$this->assertTrue($client->index(
			array(
				'content' => 'Pliz, pliz, delete me !'
			),
			array(
				'index' => 'twitter',
				'type' => 'tweet',
				'id' => 'test_get'
			))->ok);
		
		$delete_index = $client->delete(null, array(
			'index' => 'twitter'
		)) ;
		$this->assertEquals('/twitter/', (string) $delete_index->path()) ;
		
		$delete_type = $client->delete(null, array(
			'index' => 'twitter',
			'type' => 'tweet'
		)) ;
		$this->assertEquals('/twitter/tweet/', (string) $delete_type->path()) ;
		
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
		
		$this->assertEquals(true, $delete_type->ok) ;
		
		$this->assertEquals(true, $delete_index->ok) ;
		
		try {
			$client->status(null, array('index' => 'twitter'))->status ;
			$this->fail() ;
		} catch (Exception $e) {
			
		}
	}

}