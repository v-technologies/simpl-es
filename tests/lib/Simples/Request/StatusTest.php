<?php

class Simples_Request_StatusTest extends Simples_HttpTestCase {

   public function testStatus() {
	   $results = $this->client->status()->execute() ;
	   $this->assertEquals(true, $results->ok) ;
	   $this->assertTrue(isset($results->_shards->total)) ;
   }
}