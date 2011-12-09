<?php

/**
 * Response exception : the ES server has responded something bad.
 * 
 * @author Sébastien Charrier <scharrier@gmail.com>
 * @package	Simples
 * @subpackage Transport 
 */
class Simples_Response_Exception extends Exception {
	
	/**
	 * Exception data.
	 * 
	 * @var array
	 */
	protected $_data = array(
		'error' => null
	);
	
	/**
	 * Construct the data from multiple types.
	 * 
	 * @param mixed		$response_data		ES response data
	 */
	public function __construct($response_data) {
		if (is_array($response_data)) {
			if (isset($response_data['error'])) {
				$message = $response_data['error'] ;
			} else {
				$message = 'An error has occured but cannot be decoded' ;
				$response_data['error'] = $message ;
			}
		} else {
			$message = $response_data ;
			$response_data = array('error' => $message) ;
		}
		
		$this->_data = $response_data ;
		
		parent::__construct($message) ;
	}
	
	/**
	 * Magic calls.
	 * 
	 * @param string	$name		Key
	 * @return mixed				Value 
	 */
	public function __get($name) {
		return isset($this->_data[$name]) ? $this->_data[$name] : null ;
	}
}