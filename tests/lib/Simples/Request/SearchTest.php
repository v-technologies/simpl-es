<?php

require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'bootstrap.php');

class Simples_Request_SearchTest extends PHPUnit_Framework_TestCase {
	
	public $client ;
	
	public function setUp() {
		$this->client = new Simples_Transport_Http(array(
			'index' => 'twitter',
			'type' => 'tweet'
		));
		
		$this->client->delete(array('type' => null))->execute() ;
		
		$res = $this->client->index(array(
			'content' => 'First',
			'user' => 'scharrier',
			'type' => 'tweet',
			'priority' => 1
		), array('id' => 1, 'refresh' => true))->execute();
		
		$res = $this->client->index(array(
			'content' => 'Second',
			'retweet' => 'scharrier',
			'type' => 'retweet',
			'priority' => 2
		), array('id' => 2, 'refresh' => true))->execute();
		
		$res = $this->client->index(array(
			'content' => 'First',
			'user' => 'vtechnologies',
			'type' => 'tweet',
			'priority' => 2
		), array('id' => 3, 'refresh' => true))->execute();
	}

	public function testSearch() {
		$request = $this->client->search('scharrier') ;
		$body = $request->body() ;
		
		// Base search tests
		$this->assertEquals('scharrier', $body['query']['query_string']['query']) ;
		
		$res = $request->execute() ;		
		$this->assertEquals(2, $res->hits->total) ;
		
		$this->assertEquals(1, $this->client->search('retweet:scharrier')->hits->total) ;
		
		$request = $this->client->search(array(
			'query' => array(
				'term' => array('user' => 'scharrier')
			)
		));
		$this->assertEquals(1, $request->hits->total) ;
		
		// Base facet tests
		$request = $this->client->search(array(
			'facets' => array(
				'type' => array('terms' => array('field' => 'type'))
			)
		)) ;
		var_dump($request->to('json')) ;
		$response = $request->execute();
		$facets = $response->facets->type->terms->to('array') ;
		$this->assertEquals(2, $facets[0]['count']) ;
		$this->assertEquals(1, $facets[1]['count']) ;
		
	}
	
	public function testFluid() {
		$request = $this->client->search();
		$request->query('scharrier')
				->from(10)
				->size(5)
				->sort('Client.name desc') ;
		
		$body = $request->body() ;
		$this->assertEquals('query', key($body)) ;
		$this->assertEquals('scharrier', $body['query']['query_string']['query']) ;
		$this->assertEquals(10, $body['from']) ;
		$this->assertEquals(5, $body['size']) ;
		$this->assertEquals('Client.name desc', $body['sort']) ;
	}
	
	public function testQueryBuilder() {
		// Base case
		$request = $this->client->search()->query('scharrier')->in('username') ;
		$res = $request->to('array') ;
		$expected = array(
			'query' => array(
				'term' => array(
					'username' => 'scharrier'
				)
			)
		) ;
		$this->assertTrue($request instanceof Simples_Request) ;
		$this->assertEquals($expected, $res) ;
		
		// Complex
		$request = $this->client->search()
			->query()
				->should()
					->match('scharrier')->in('username')
					->field('type')->match(array('administrator','visitor'))
				->not()
					->field('connected')->match(true)
			->size(5)
			->sort('username asc') ;
		
		$res = $request->to('array') ;
		$this->assertTrue(isset($res['query']['bool']['should'])) ;
		$this->assertEquals(2, count($res['query']['bool']['should'])) ;
		$this->assertTrue(isset($res['query']['bool']['must_not'])) ;
		$this->assertEquals(1, count($res['query']['bool']['must_not'])) ;
		$this->assertEquals(5, $res['size']) ;
		
		// Real call check
		$response = $request->execute() ;
		$this->assertTrue(isset($response->took)) ;
	}
}