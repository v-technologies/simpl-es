<?php

require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'bootstrap.php');

class Simples_Response_SearchTest extends PHPUnit_Framework_TestCase {
	
	public function testHighlight() {
		$response = new Simples_Response_Search(array(
			'body' => array(
				'hits' => array(
					'hits' => array(
						array(
							'_source' => array(
								'Utilisateur' => array(
									'name' => 'Sebastien'
								)
							),
							'highlight' => array(
								'Utilisateur.name' => '<em>Sebastien<em>'
							)
						)
					)
				)
			)
		), array('highlight' => Simples_Request_Search::HIGHLIGHT_REPLACE)) ;
		
		$this->assertEquals('<em>Sebastien<em>', $response->body->hits->hits->{0}->_source->Utilisateur->name);
		
		// Special ES case : highlight returned as an array
		$response = new Simples_Response_Search(array(
			'body' => array(
				'hits' => array(
					'hits' => array(
						array(
							'_source' => array(
								'Utilisateur' => array(
									'name' => 'Sebastien'
								)
							),
							'highlight' => array(
								'Utilisateur.name' => array('<em>Sebastien<em>')
							)
						)
					)
				)
			)
		), array('highlight' => Simples_Request_Search::HIGHLIGHT_REPLACE)) ;
		
		$this->assertEquals('<em>Sebastien<em>', $response->body->hits->hits->{0}->_source->Utilisateur->name);
		
		// special case for subarray formated data
		$response = new Simples_Response_Search(array(
			'body' => array(
				'hits' => array(
					'hits' => array(
						array(
							'_source' => array(
								'Utilisateur' => array(
									0 => array(
										'name' => array(0 => 'Sebastien')
									)
								)
							),
							'highlight' => array(
								'Utilisateur.name' => array('<em>Sebastien<em>')
							)
						)
					)
				)
			)
		), array('highlight' => Simples_Request_Search::HIGHLIGHT_REPLACE)) ;
		
		$this->assertEquals('<em>Sebastien<em>', $response->body->hits->hits->{0}->_source->Utilisateur->{0}->name->{0});
		
	}
	
	public function testHits() {
		$response = new Simples_Response_Search(array(
			'body' => array(
				'hits' => array(
					'hits' => array(
						array(
							'_source' => array(
								'Utilisateur' => array(
									'name' => 'Jim Morrison'
								)
							)
						),
						array(
							'_source' => array(
								'Utilisateur' => array(
									'name' => 'Ray Manzareck'
								)
							)
						)
					)
				)
			)
		));
		
		$this->assertEquals(2, count($response->hits())) ;
		
		$test = array() ;
		foreach($response->hits() as $document) {
			$test[] = $document->Utilisateur->name ;
		}
		
		$expected = array('Jim Morrison', 'Ray Manzareck') ;
		$this->assertEquals($expected, $test) ;
	}

	public function testFacets() {
		$response = new Simples_Response_Search(array(
			'body' => array(
				'facets' => array(
					'wow_facets' => array(
						'filter' => array(
							'term' => array(
								'field' => 'wow'
							)
						)
					)
				)
			)
		));
		
		$this->assertEquals(1, count($response->facets()));
		
		$this->assertEquals('wow', $response->facets()->wow_facets->filter->term->field);
	}

	public function testAggregations() {
		$response = new Simples_Response_Search(array(
			'body' => array(
				'aggregations' => array(
					'user' => array(
						'buckets' => array(
							array(
								'key' => 'scharrier',
								'doc_count' => 1
							),
							array(
								'key' => 'vtechnologies',
								'doc_count' => 3
							),
						 )
					 )
				)
			)
		));

		$this->assertEquals(1, count($response->aggregations()));
		$this->assertEquals(2, count($response->aggregations()->user->buckets));
	}
}
