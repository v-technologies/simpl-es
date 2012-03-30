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
	 * Add a criteria to the current query.
	 * 
	 * @param  mixed	$criteria	Criteria to add.
	 * @param  array	$options	Options.	
	 * @return mixed				Current query instance or current request instance (fluid calls)
	 */
	public function add($definition, array $options = array()) {
		$facet = new Simples_Request_Search_Facet($definition, $options, $this->_fluid()) ;
		if (count($this->_facets)) {
			$last = $this->_last() ;
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
	 * Magic call : chain with subobjects.
	 * 
	 * @param string	$name		Method name
	 * @param array		$args		Arguments
	 * @return \Simples_Request_Search 
	 */
	public function __call($name, $args) {
		call_user_func_array(array($this->_last(), $name), $args) ;
		return $this->_fluid() ;
	}
	
	/**
	 * Returns the last called.
	 * 
	 * @return type 
	 */
	protected function _last() {
		end($this->_facets) ;
		$last = $this->_facets[key($this->_facets)] ;
		reset($this->_facets) ;
		
		return $last ;
	}
	
	/**
	 * Prepare data.
	 * 
	 * @return array
	 */
	protected function _data(array $options = array()) {
		$return = array() ;
		
		foreach($this->_facets as $facet) {
			$return = array_merge($return, $facet->to('array')) ;
		}
		
		return $return ;
	}
}