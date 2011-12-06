<?php
require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'bootstrap.php') ;

class Simples_Request_IndexTest extends PHPUnit_Framework_TestCase {

   public function testIndex() {
	   try {
		 $request = new Simples_Request_Index(new Simples_Transport_Http()) ;
		 $this->fail() ;
	   } catch (Exception $e) {}
	   
	   $client = new Simples_Transport_Http() ;
	   
	   $request = new Simples_Request_Index($client, 'twitter', 'tweet') ;
	   $this->assertEquals('/twitter/tweet/', $request->path()) ;
	   
	   $request->properties(array(
		   '_id' => 1,
		   'user' => 'scharrier',
		   'fullname' => 'Sébastien Charrier'
	   )) ;
	   $this->assertEquals('/twitter/tweet/1/', $request->path()) ;
	   $this->assertTrue($request->ok) ;
	   $this->assertEquals(1, $request->_id) ;
	   
	   $request = new Simples_Request_Index($client, 'twitter', 'tweet', array(
		   '_id' => 2,
		   'user' => 'v-technologies'
	   )) ;
	   $this->assertEquals('/twitter/tweet/2/', $request->path()) ;
	   $this->assertEquals(2, $request->_id) ;
   }
}