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
		
		$client->index(array(
			'id' => '2',
			'data' => array(
				'content' => 'Second',
				'user' => 'scharrier'
			)
		))->execute();
		
		sleep(2) ;
		
		$request = $client->search('first') ;
		$res = $request->execute() ;
		
		//var_dump($request->path()) ;
		//var_dump($request->to('json')) ;
		var_dump($res) ;
		
	}

}