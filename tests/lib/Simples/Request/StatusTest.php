<?php
require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'bootstrap.php') ;

class Simples_Request_StatusTest extends PHPUnit_Framework_TestCase {

   public function testStatus() {
	   $request = new Simples_Request_Status(new Simples_Transport_Http()) ;
	   $results = $request->execute() ;
	   $this->assertEquals(true, $results->ok) ;
	   $this->assertTrue(isset($results->_shards->total)) ;
   }
}