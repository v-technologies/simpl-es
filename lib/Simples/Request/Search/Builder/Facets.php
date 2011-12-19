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
	 * Current facets.
	 * 
	 * @var array
	 */
	protected $_facets = array() ;
	
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
	public function add($definition, $options = null) {
		$facet = new Simples_Request_Search_Facet($definition, $options, $this->_fluid()) ;
		if (count($this->_facets)) {
			end($this->_facets) ;
			$last = $this->_facets[key($this->_facets)] ;
			reset($this->_facets) ;
			if ($last->mergeable($facet)) {
				$last->merge($facet) ;
				return $this->_fluid() ;
			}
		}
		$this->_facets[$facet->name()] = $facet ;
		return $this->_fluid() ;
	}
	
	/**
	 * Count the number of current facets.
	 * 
	 * @return int
	 */
	public function count() {
		return count($this->_facets) ;
	}
	
	/**
	 * Prepare data.
	 * 
	 * @return array
	 */
	protected function _data() {
		$return = array() ;
		
		foreach($this->_facets as $facet) {
			$return = array_merge($return, $facet->to('array')) ;
		}
		
		return $return ;
	}
	
}