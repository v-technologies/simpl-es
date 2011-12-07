<?php

require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'bootstrap.php');

class Simples_Request_SearchTest extends PHPUnit_Framework_TestCase {

	public function testSearch() {
		$client = new Simples_Transport_Http(array(
			'index' => 'twitter',
			'type' => 'tweet'
		));
		$client->delete(array('type' => null))->execute() ;
		
		$res = $client->index(array(
			'id' => '1',
			'data' => array(
				'content' => 'First',
				'user' => 'scharrier'
			)
		))->execute();
		
		$index = $client->index(array(
			'id' => '2',
			'refresh' => true,
			'data' => array(
				'content' => 'Second',
				'user' => 'scharrier'
			)
		)) ;
		var_dump($index->path()) ;
		var_dump($index->to('json')) ;
		$index->execute() ;
		
		//$request = $client->search('scharrier') ;
		//$body = $request->body() ;
		
		//$res = $request->execute() ;		
		
	}

}