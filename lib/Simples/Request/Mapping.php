<?php

/**
 * Mapping (/_mapping)
 * 
 * Get or update a mapping.
 * 
 * @author Sébastien Charrier <scharrier@gmail.com>
 * @package	Simples
 * @subpackage Request
 */
class Simples_Request_Mapping extends Simples_Request {
	
	/**
	 * Definition
	 * 
	 * @var array
	 */
	protected $_definition = array(
		'method' => self::GET,
		'path' => '_mapping',
		'magic' => 'raw',
		'inject' => array(
			'params' => array('ignore_conflicts')
		)
	) ;
	
	/**
	 * Request body 
	 */
	protected $_body = array() ;
	
	/**
	 * Default options 
	 */
	protected $_options = array(
		'ignore_conflicts' => null
	);
	
	/**
	 * Switch method if we're in getter or setter mode.
	 * 
	 * @return string
	 */
	public function method() {
		return (!$this->body()) ? self::GET : self::PUT ;
	}
	
	/**
	 * Works fine with a raw mapping.
	 * 
	 * @param array $data	Body data.
	 * @return array 
	 */
	protected function _toArray($data, array $options = array()) {
		if (isset($data['raw'])) {
			return json_decode($data['raw'], true) ;
		}
		return $data ;
	}
	
	/**
	 * Works fine with a raw mapping.
	 * 
	 * @param array $data	Body data.
	 * @return array 
	 */
	protected function _toJson($data, array $options = array()) {
		if (isset($data['raw'])) {
			return $data['raw'] ;
		}
		return json_encode($data) ;
	}
}