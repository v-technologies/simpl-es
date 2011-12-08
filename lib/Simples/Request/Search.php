<?php

/**
 * Search. Oh yea, here it is.
 * 
 * @author Sébastien Charrier <scharrier@gmail.com>
 * @package	Simples
 * @subpackage Request
 */
class Simples_Request_Search extends Simples_Request {
	
	/**
	 * Definition
	 * 
	 * @var array
	 */
	protected $_definition = array(
		'method' => self::POST,
		'path' => '_search',
		'magic' => 'query',
	) ;
	
	
	/**
	 * Default body values.
	 * 
	 * @var array
	 */
	protected $_body = array(
		'index' => null,
		'type' => null,
		'query' => null,
		'filter' => null,
		'facets' => null,
		'from' => null,
		'size' => null,
		'sort' => null,
		'highlight' => null,
		'fields' => null,
		'script_fields' => null,
		'explain' => false,
		'version' => null,
		'min_score' => null
	);
	
	/**
	 * Body without null values.
	 * 
	 * @param array $body
	 * @return type 
	 */
	public function body(array $body = null) {
		if (isset($body)) {
			if (isset($body['query'])) {
				$this->query($body['query']) ;
			}
			return parent::body($body) ;
		}
		
		$body = array_filter(parent::body()) ;
		$body['query'] = $this->query()->to('array') ;
		
		return $body ;
	}
	
	public function query($query = null) {
		if (!isset($this->_query)) {
			$this->_query = new Simples_Request_Search_Query() ;
		}
		if (isset($query)) {
			$this->_query->set($query) ;
			return $this ;
		}
		return $this->_query ;
	}
	
	public function from($from) {
		$this->_body['from'] = $from ;
		return $this ;
	}
	
	public function size($size) {
		$this->_body['size'] = $size;
		return $this ;
	}
	
	public function sort($sort) {
		$this->_body['sort'] = $sort;
		return $this ;
	}
	
}