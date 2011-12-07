<?php

/**
 * Get.
 * 
 * @author Sébastien Charrier <scharrier@gmail.com>
 * @package	Simples
 * @subpackage Request
 */
class Simples_Request_Get extends Simples_Request {
	
	/**
	 * Call method.
	 * 
	 * @var string
	 */
	protected $_method = self::GET ;
	
	protected $_required = array(
		'index', 'type', 'id'
	) ;
	
	protected $_body = array(
		'index' => null,
		'type' => null,
		'id' => null
	);
	
	/**
	 * Default param.
	 * 
	 * @var string
	 */
	protected $_default = 'id' ;
	
	/**
	 * Path : id management.
	 * 
	 * @return string	API path
	 */
	public function path() {
		$path = parent::path() ;
		
		// Object id transmited : we had it to the url.
		$path .= $this->_body['id'] . '/' ;
		
		return $path ;
	}
}