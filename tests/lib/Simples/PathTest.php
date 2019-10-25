<?php
require_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'bootstrap.php') ;

use PHPUnit\Framework\TestCase;

class Simples_PathTest extends TestCase {
	
	public function testPath() {
		$path = new Simples_Path('/root/') ;
		$this->assertEquals('/root/', (string) $path) ;
		
		$path->directory('sub') ;
		$this->assertEquals('/root/sub/', (string) $path) ;
		
		$path->param('param','value') ;
		$this->assertEquals('/root/sub/?param=value', (string) $path) ;
		
		$path->params(array('other'=>'value')) ;
		$this->assertEquals('/root/sub/?param=value&other=value', (string) $path) ;
		
		$path->directories(array('other','again')) ;
		$this->assertEquals('/root/sub/other/again/?param=value&other=value', (string) $path) ;
	}
}