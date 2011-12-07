<?php

/**
 * Delete.
 * 
 * Can be used to delete :
 * - an index
 * - a type
 * - an object
 * 
 * @author Sébastien Charrier <scharrier@gmail.com>
 * @package	Simples
 * @subpackage Request
 */
class Simples_Request_Delete extends Simples_Request {
	
	/**
	 * Call method.
	 * 
	 * @var string
	 */
	protected $_method = self::DELETE ;
	
	protected $_body = array(
		'index' => null,
		'type' => null,
		'id' => null
	) ;
	
	
	/**
	 * Path : id management.
	 * 
	 * @return string	API path
	 */
	public function path() {
		$path = parent::path() ;
		
		// Object id transmited : we had it to the url.
		if (isset($this->_body['id'])) {
			$path .= $this->_body['id'] . '/' ;
		}
		
		return $path ;
	}
}