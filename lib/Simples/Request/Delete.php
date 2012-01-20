<?php

/**
 * Delete and object.
 * 
 * @author Sébastien Charrier <scharrier@gmail.com>
 * @package	Simples
 * @subpackage Request
 */
class Simples_Request_Delete extends Simples_Request {
	
	/**
	 * Definition
	 * 
	 * @var array
	 */
	protected $_definition = array(
		'method' => self::DELETE,
		'required' => array(
			'body' => array('id'),
			'options' => array('index','type')
		),
		'magic' => 'id'
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