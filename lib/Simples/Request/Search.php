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
	 * Current subobject working.
	 * 
	 * @var string
	 */
	protected $_current = 'query' ;
	
	/**
	 * Query builder.
	 * 
	 * @var Simples_Request_Search_Builder_Query
	 */
	protected $_query ;
	
	/**
	 * Filter builder.
	 * 
	 * @var Simples_Request_Search_Builder_Filter
	 */
	protected $_filter ;
	
	/**
	 * Facet builder.
	 * 
	 * @var Simples_Request_Search_Builder_Facet
	 */
	protected $_facet ;
	
	/**
	 * Constructor.
	 * 
	 * @param mixed		$body				Request body
	 * @param array		$options			Array of options
	 * @param Simples_Transport $transport	ES client instance
	 */
	public function __construct($body = null, $options = null, Simples_Transport $transport = null) {
		// Builders
		$this->_query = new Simples_Request_Search_Builder_Query(null, $this) ;
		$this->_filter = new Simples_Request_Search_Builder_Filter(null, $this) ;
		
		// Simple query_string search : give it to builder.
		if (isset($body['query']) && is_string($body['query'])) {
			$this->_query->add($body['query']) ;
			unset($body['query']) ;
		}
		
		parent::__construct($body, $options, $transport);
	}
	
	/**
	 * Body without null values.
	 * 
	 * @param array $body
	 * @return type 
	 */
	public function body(array $body = null) {
		if (isset($body)) {
			return parent::body($body) ;
		}
		
		$body = parent::body() ;
		
		if (empty($body['query'])) {
			// Force a match_all
			$body['query'] = $this->_query->to('array') ;
		}
		
		if (empty($body['filter']) && $this->_filter->count()) {
			$body['filter'] = $this->_filter->to('array') ;
		}
		
		$body = array_filter($body) ;
		
		return $body ;
	}
	
	/**
	 * Query getter/setter.
	 * 
	 * @param mixed		$query			Setter : Query definition.
	 * @return \Simples_Request_Search	This instance
	 */
	public function query($query = null) {
		// Save current subobject
		$this->_current = 'query' ;
		
		if (isset($query)) {
			$this->_query->add($query) ;
		}
		return $this ;
	}
	
	/**
	 * Query getter/setter.
	 * 
	 * @param mixed		$filter			Setter : Query definition.
	 * @return \Simples_Request_Search	This instance
	 */
	public function filter($filter = null) {
		// Save current subobject
		$this->_current = 'filter' ;
		
		if (isset($filter)) {
			$this->_filter->add($filter) ;
		}
		return $this ;
	}
	
	/**
	 * Add multiples field queries one time. It's a simplified call wich permit to give this kind of array :
	 * $request->queries(array(
	 *		'field' => 'value',
	 *		'other_field' => array('value 1', 'value 2')
	 * ));
	 * 
	 * @param array $queries			List of criteries. Field name in key, search in value.
	 * @return \Simples_Request_Search	This instance.
	 */
	public function queries(array $queries) {
		foreach($queries as $in => $match) {
			$this->_query->add(array('query' => $match, 'in' => $in)) ;
		}
		return $this ;
	}
	
	/**
	 * Add multiples field queries one time. It's a simplified call wich permit to give this kind of array :
	 * $request->queries(array(
	 *		'field' => 'value',
	 *		'other_field' => array('value 1', 'value 2')
	 * ));
	 * 
	 * @param array $filters			List of criteries. Field name in key, search in value.
	 * @return \Simples_Request_Search	This instance.
	 */
	public function filters(array $filters) {
		foreach($filters as $in => $match) {
			$this->_filter->add(array('query' => $match, 'in' => $in)) ;
		}
		return $this ;
	}
	
	/**
	 * Set the from param.
	 * 
	 * @param int	$from		From value
	 * @return \Simples_Request_Search 
	 */
	public function from($from) {
		$this->_body['from'] = $from ;
		return $this ;
	}
	
	/**
	 * Set the size.
	 * 
	 * @param int	$size		Size value
	 * @return \Simples_Request_Search 
	 */
	public function size($size) {
		$this->_body['size'] = $size;
		return $this ;
	}
	
	/**
	 * Set the sort param.
	 * 
	 * @param string	$sort	Sort value
	 * @return \Simples_Request_Search 
	 */
	public function sort($sort) {
		$this->_body['sort'] = $sort;
		return $this ;
	}
	
	/**
	 * Magic call : chain with subobjects.
	 * 
	 * @param string	$name		Method name
	 * @param array		$args		Arguments
	 * @return \Simples_Request_Search 
	 */
	public function __call($name, $args) {
		$object = '_' . $this->_current ;
		call_user_func_array(array($this->{$object}, $name), $args) ;
		return $this ;
	}
}