<?php

class Simples_Request_UpdateTest extends Simples_HttpTestCase {

	protected function setUp() : void {
		parent::setUp();
		$this->client->config('index', 'twitter');
		$this->client->config('type', 'tweet');
		$this->client->config('log', true);
	}

	public function testUpdate() {
		$request = $this->client->index(array(
				'user' => 'scharrier',
				'fullname' => 'Sébastien Charrier'
		), array('id' => 1))->execute() ;


		$request = $this->client->update(array(
			'fullname' => 'scharrier'
		), array('id' => 1)) ;
		$this->assertEquals('/twitter/tweet/1/_update/', (string) $request->path()) ;
		$request->execute() ;
		$this->assertEquals('scharrier', $this->client->get(1)->execute()->_source->fullname) ;

		// From a document
		$doc = new Simples_Document(array(
			'id' => 1,
			'fullname' => 'Sébastien Charrier'
		)) ;
		$request = $this->client->update($doc) ;
		$this->assertEquals('/twitter/tweet/1/_update/', (string) $request->path()) ;
		$request->execute() ;
		$this->assertEquals('Sébastien Charrier', $this->client->get(1)->execute()->_source->fullname) ;
	}

	public function testBulk() {
		$data = array(
			array('firstname' => 'Jim', 'lastname' => 'Morrison', 'id' => '1'),
			array('firstname' => 'Ray', 'lastname' => 'Manzarek', 'id' => '2')
		);
		$this->client->index($data, array('refresh' => true))->execute() ;

		$data = array(
			array('firstname' => 'Jim', 'lastname' => 'Morrison (Composer, singer)', 'id' => '1'),
			array('firstname' => 'Ray', 'lastname' => 'Manzarek (Keyboardist)', 'id' => '2')
		);
		$request = $this->client->update($data) ;
		$this->assertTrue($request->bulk()) ;
		$this->assertEquals('/_bulk/', (string) $request->path()) ;

		$request->execute() ;
		$this->assertEquals('Morrison (Composer, singer)', $this->client->get(1)->execute()->_source->lastname) ;
	}
}
