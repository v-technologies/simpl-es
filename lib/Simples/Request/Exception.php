<?php

/**
 * Request exception.
 * 
 * @author Sébastien Charrier <scharrier@gmail.com>
 * @package	Simples
 * @subpackage Transport 
 */
class Simples_Request_Exception extends Exception {

	/**
	 * Constructor.
	 * 
	 * @param string $message Exception message.
	 * @param array  $request    [optional] Additionnal request.
	 */
	public function __construct($message, array $request = array()) {
		// Add the request to the message for better debugging 
		if (!empty($request)) {
			$message .= ' (Request : ' . json_encode($request) . ')' ;
		}
		parent::__construct($message) ;
	}
	
}