<?php
require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'bootstrap.php') ;

class Simples_Request_StatusTest extends PHPUnit_Framework_TestCase {

   public function testStatus() {
	   $client = new Simples_Transport_Http(array('host' => ES_HOST)) ;
	   $results = $client->status()->execute() ;
	   //TODO test HTTP code
	   $this->assertTrue(isset($results->_shards->total)) ;
   }
}