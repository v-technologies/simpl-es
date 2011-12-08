<?php

require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'bootstrap.php');

class Simples_Request_DefinitionTest extends PHPUnit_Framework_TestCase {

	public function testDefinition() {
		$definition = new Simples_Request_Definition(array('method' => 'GET')) ;
		$this->assertEquals('GET', $definition->method()) ;
		$this->assertNull($definition->path()) ;
		
		$definition = new Simples_Request_Definition(array(
			'method' => Simples_Request::GET,
			'required' => array(
				'body' => array('id')
			)
		)) ;
		
		$this->assertEquals(Simples_Request::GET, $definition->method()) ;
		$this->assertEquals(array('id'), $definition->required('body')) ;
		$this->assertEquals(array(), $definition->required('options')) ;
	}

}