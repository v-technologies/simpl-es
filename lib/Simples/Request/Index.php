<?php

/**
 * Index.
 * 
 * Index an object in the index/type defined.
 * 
 * @author Sébastien Charrier <scharrier@gmail.com>
 * @package	Simples
 * @subpackage Request
 */
class Simples_Request_Index extends Simples_Request {
	
	/**
	 * Call method.
	 * 
	 * @var string
	 */
	protected $_method = self::POST ;
	
	/**
	 * Required body keys.
	 * 
	 * @var array
	 */
	protected $_required = array(
		'index','type'
	) ;
	
	/**
	 * Base body values.
	 * 
	 * @var array
	 */
	protected $_body = array(
		'index' => null,
		'type' => null,
		'id' => null,
		'data' => null
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
	
	public function body(array $body = null) {
		if (isset($body)) {
			return parent::body($body) ;
		}
		return $this->_body['data'] ;
	}
}