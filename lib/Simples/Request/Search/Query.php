<?php

/**
 * The search query.
 * 
 * @author Sébastien Charrier <scharrier@gmail.com>
 * @package	Simples
 * @subpackage Request
 */
class Simples_Request_Search_Query extends Simples_Base {
	
	protected $_query = array() ;
	
	public function __construct($query = null) {
		if (isset($query)) {
			$this->set($query) ;
		}
	}
	
	public function set($query) {
		if (is_string($query)) {
			$query = array('query_string' => array('query' => $query)) ;
		}
		$this->_query = $query ;
		return $this ;
	}
	
	protected function _data() {
		if (!empty($this->_query)) {
			return $this->_query ;
		}
		return array('match_all' => array()) ;
	}
}