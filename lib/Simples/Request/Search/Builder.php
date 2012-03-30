<?php

/**
 * Search query builder.
 * 
 * @author Sébastien Charrier <scharrier@gmail.com>
 * @package	Simples
 * @subpackage Request.Search
 */
abstract class Simples_Request_Search_Builder extends Simples_Base implements Countable {
	
	/**
	 * Request dependency.
	 * 
	 * @var Simples_Request_Search
	 */
	protected $_request ;
	
	/**
	 * Constructor.
	 * 
	 * @param mixed						$query		Query definition (string/array)
	 * @param Simples_Request_Search	$request	Request calling this query builder.
	 */
	public function __construct(Simples_Request_Search $request = null) {		
		if (isset($request)) {
			$this->_request = $request ;
		}
	}
	
	/**
	 * Add an element to the current builder.
	 * 
	 * @param mixed		$criteria	Element to add.
	 * @return mixed				Current builder instance or current request instance (fluid calls)
	 */
	abstract public function add($element, array $options = array()) ;
	
	
	/**
	 * Fluid interface : returns the request instance if set, or this instance.
	 * 
	 * @return \Simples_Base
	 */
	protected function _fluid() {
		if (isset($this->_request)) {
			return $this->_request ;
		}
		return $this ;
	}
}