<?php

/**
 * Facets builder.
 * 
 * @author Sébastien Charrier <scharrier@gmail.com>
 * @package	Simples
 * @subpackage Request.Search
 */
class Simples_Request_Search_Builder_Facets extends Simples_Request_Search_Builder {
	
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
	 * Add a criteria to the current query.
	 * 
	 * @param mixed		$criteria	Criteria to add.
	 * @return mixed				Current query instance or current request instance (fluid calls)
	 */
	public function add($name) {
		$facet = new Simples_Request_Search_Facet($name) ;
		$this->_data[$name] = $facet ;
		return $this->_fluid() ;
	}
	
	public function count() {
		return count($this->_data) ;
	}
	
}