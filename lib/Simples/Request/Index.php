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
	 * Definition
	 * 
	 * @var array
	 */
	protected $_definition = array(
		'method' => self::POST,
		'magic' => 'id',
		'required' => array(
			'options' => array('index','type')
		),
		'inject' => array(
			'params' => array('refresh')
		)
	) ;
	
	/**
	 * Path : id management.
	 * 
	 * @return string	API path
	 */
	public function path() {
		$path = parent::path() ;
		
		// Object id transmited : we had it to the url.
		if (isset($this->_options['id'])) {
			$path->directory($this->_options['id']) ;
		}
		
		return $path ;
	}
}