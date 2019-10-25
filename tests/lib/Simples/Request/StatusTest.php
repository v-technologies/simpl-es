<?php
require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'bootstrap.php') ;

use PHPUnit\Framework\TestCase;

class Simples_Request_StatusTest extends TestCase {

   public function testStatus() {
	   $client = new Simples_Transport_Http() ;
	   $results = $client->status()->execute() ;
	   $this->assertEquals(true, $results->ok) ;
	   $this->assertTrue(isset($results->_shards->total)) ;
   }
}