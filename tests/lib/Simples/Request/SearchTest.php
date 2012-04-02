<?php

require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'bootstrap.php');

class Simples_Request_SearchTest extends PHPUnit_Framework_TestCase {
	
	public $client ;
	
	public function setUp() {
		$this->client = new Simples_Transport_Http(array(
			'index' => 'twitter',
			'type' => 'tweet'
		));
		
		$this->client->deleteIndex()->execute() ;
		
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
		$request = $this->client->search() ;
		$request->match('scharrier')->size(0)->explain(false) ;
		$res = $request->to('array') ;
		
		// Base search tests
		$expected = array(
			'query' => array(
				'query_string' => array(
					'query' => 'scharrier'
				)
			),
                        'size' => 0,
                        'explain' => false
		) ;
		$this->assertEquals($expected, $res) ;
		
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
	
	public function testFilterBuilder() {
		// Base case
		$request = $this->client->search()->filter('scharrier')->in('username') ;
		$res = $request->to('array') ;
		$expected = array(
			'query' => array(
				'match_all' => new stdClass()
			),
			'filter' => array(
				'term' => array(
					'username' => 'scharrier'
				)
			)
		) ;
		$this->assertTrue($request instanceof Simples_Request) ;
		$this->assertEquals($expected, $res) ;

		// Test other filter type
		$request = $this->client->search()->filter(array(
			'in' => 'my_field',
			'ranges' => array(
				array('from' => 2),
				array('from' => 3, 'to ' => 5)
			)
		), array('type' => 'range')) ;
		$res = $request->to('array');
		$expected = array(
			'query' => array(
				'match_all' => new stdClass()
			),
			'filter' => array(
				'bool' => array(
					'should' => array(
						array('range' => array('my_field' => array('from' => 2))),
						array('range' => array('my_field' => array('from' => 3, 'to ' => 5)))
					)
				)
			)
		) ;
		$this->assertEquals($expected, $res) ;
	}
        
    public function testFacetsBuilder() {
		// Base case
		$request = $this->client->search()->facet('username') ;
		$res = $request->to('array') ;
		$expected = array(
			'query' => array(
				'match_all' => new stdClass()
			),
			'facets' => array(
				'username' => array(
					'terms' => array('field' => 'username')
				)
			)
		) ;
		$this->assertEquals($expected, $res) ;

		// Range filtered facet
		$request = $this->client->search()->facet('age')
			->filtered(array('in' => 'age', 'ranges' => array(
				array('from' => '5', 'to' => '10'),
				array('from' => '11', 'to' => '20')
			)), array('type' => 'range')) ;
		$res = $request->to('array') ;

		$expected = array(
			'query' => array(
				'match_all' => new stdClass()
			),
			'facets' => array(
				'age' => array(
					'terms' => array('field' => 'age'),
					'facet_filter' => array(
						'bool' => array(
							'should' => array(
								array('range' => array(
									'age' => array('from' => '5', 'to' => '10'))),
								array('range' => array(
									'age' => array('from' => '11', 'to' => '20')))
							)
						)
					)
				)
			)
		);
		$this->assertEquals($expected, $res) ;
	}
	
	public function testMultipleQueries() {
		$request = $this->client->search()->queries(array(
			'firstname' => 'Sebastien',
			'lastname' => array('Charrier','Morrison')
		)) ;
		$res = $request->to('array') ;
		$this->assertEquals('Sebastien', $res['query']['bool']['must'][0]['term']['firstname']) ;
		$this->assertEquals(array('Charrier','Morrison'), $res['query']['bool']['must'][1]['terms']['lastname']) ;
	}
	
	public function testMultipleFilters() {
		$request = $this->client->search()->filters(array(
			'firstname' => 'Sebastien',
			'lastname' => array('Charrier','Morrison')
		)) ;
		$res = $request->to('array') ;
		$this->assertEquals('Sebastien', $res['filter']['bool']['must'][0]['term']['firstname']) ;
		$this->assertEquals(array('Charrier','Morrison'), $res['filter']['bool']['must'][1]['terms']['lastname']) ;
	}
	
	public function testMultipleFacets() {
		$request = $this->client->search()->facets(array(
			'name' => 'firstname',
			'full' => array('in' => array('firstname','lastname'))
		)) ;
		$res = $request->to('array') ;
		$this->assertEquals('firstname', $res['facets']['name']['terms']['field']) ;
		$this->assertEquals(array('firstname','lastname'), $res['facets']['full']['terms']['fields']) ;
	}
	
	public function testFullRequest() {
		$request = $this->client->search()
			->query()
				->match('Sebastien')
			->filter()
				->field('type')->match('administrator')
			->filters(array('level'=>1))
			->facets(array('type','level'))
			->size(2) ;
		$res = $request->to('array') ;
		
		$this->assertEquals('Sebastien', $res['query']['query_string']['query']) ;
		$this->assertEquals('administrator', $res['filter']['bool']['must'][0]['term']['type']) ;
		$this->assertEquals(1, $res['filter']['bool']['must'][1]['term']['level']) ;
		$this->assertEquals(2, count($res['facets'])) ;
		$this->assertEquals(2, $res['size']) ;
	}
	
	public function testHighlight() {
		$request = $this->client->search()->highlight(array(
			'fields' => array(
				'name',
				'address' => array(
					'fragment_size' => 150
				)
			)
		)) ;
		$res = $request->to('array') ;
		$this->assertTrue(isset($res['highlight']['fields']['name'])) ;
		$this->assertTrue(isset($res['highlight']['fields']['address'])) ;
	}
	
	public function testOptions() {
		$request = $this->client->search() ;
		$request->add(array('query' => 'sebastien','in' => 'name'), array('type' => 'text')) ;
		$request->add(array('boost' => '2','in' => 'lastname', 'value' => array('sebastien','jim')), array('type' => 'terms')) ;
		$res = $request->to('array') ;
		$this->assertEquals(2, count($res['query']['bool']['must'])) ;
		$this->assertEquals('text', key($res['query']['bool']['must'][0])) ;
		$this->assertEquals('terms', key($res['query']['bool']['must'][1])) ;
	}
}