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
	 * Definition
	 * 
	 * @var array
	 */
	protected $_definition = array(
		'method' => self::GET,
		'magic' => 'id',
		'required' => array(
			'body' => array('id'),
			'options' => array('index', 'type')
		)
	) ;
	
	protected $_body = array(
		'index' => null,
		'type' => null,
		'id' => null
	);
	
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

	/**
	 * Specific response object.
	 * 
	 * @param array		$data		Get request results.
	 * @return \Simples_Response_Get 
	 */
	protected function _response($data) {
		return new Simples_Response_Get($data, parent::options()) ;
	}
}