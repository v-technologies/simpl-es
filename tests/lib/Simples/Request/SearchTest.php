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
			'content' => 'First',
			'user' => 'scharrier',
			'type' => 'tweet',
			'priority' => 1
		), array('id' => 1, 'refresh' => true))->execute();
		
		$res = $client->index(array(
			'content' => 'Second',
			'retweet' => 'scharrier',
			'type' => 'retweet',
			'priority' => 2
		), array('id' => 2, 'refresh' => true))->execute();
		
		$res = $client->index(array(
			'content' => 'First',
			'user' => 'vtechnologies',
			'type' => 'tweet',
			'priority' => 2
		), array('id' => 3, 'refresh' => true))->execute();
		
		$request = $client->search('scharrier') ;
		$body = $request->body() ;
		
		// Base search tests
		$this->assertEquals('scharrier', $body['query']['query_string']['query']) ;
		
		$res = $request->execute() ;		
		$this->assertEquals(2, $res->hits->total) ;
		
		$this->assertEquals(1, $client->search('retweet:scharrier')->hits->total) ;
		
		$request = $client->search(array(
			'query' => array(
				'term' => array('user' => 'scharrier')
			)
		));
		$this->assertEquals(1, $request->hits->total) ;
		
		// Base facet tests
		$response = $client->search(array(
			'facets' => array(
				'type' => array('terms' => array('field' => 'type'))
			)
		))->execute();
		$facets = $response->facets->type->terms->to('array') ;
		$this->assertEquals(2, $facets[0]['count']) ;
		$this->assertEquals(1, $facets[1]['count']) ;
		
	}

}