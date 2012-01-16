<?php

require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'bootstrap.php');

class Simples_Response_SearchTest extends PHPUnit_Framework_TestCase {
	
	public function testHighlight() {
		$response = new Simples_Response_Search(array(
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
		), array('highlight' => Simples_Request_Search::HIGHLIGHT_REPLACE)) ;
		
		$this->assertEquals('<em>Sebastien<em>', $response->hits->hits->{0}->_source->Utilisateur->name) ;
		
		// Special ES case : highlight returned as an array
		$response = new Simples_Response_Search(array(
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
		), array('highlight' => Simples_Request_Search::HIGHLIGHT_REPLACE)) ;
		
		$this->assertEquals('<em>Sebastien<em>', $response->hits->hits->{0}->_source->Utilisateur->name) ;
		
	}
	
	public function testHits() {
		$response = new Simples_Response_Search(array(
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
		));
		
		$this->assertEquals(2, count($response->hits())) ;
		
		$test = array() ;
		foreach($response->hits() as $document) {
			$test[] = $document->Utilisateur->name ;
		}
		
		$expected = array('Jim Morrison', 'Ray Manzareck') ;
		$this->assertEquals($expected, $test) ;
	}
}