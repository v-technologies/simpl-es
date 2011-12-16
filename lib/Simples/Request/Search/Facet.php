<?php

/**
 * A facet definition
 * 
 * @author Sébastien Charrier <scharrier@gmail.com>
 * @package	Simples
 * @subpackage Request
 */
class Simples_Request_Search_Facet extends Simples_Base {
	
	/**
	 * Criteria type.
	 * 
	 * @var string
	 */
	protected $_type = '' ;
	
	/**
	 * Default type.
	 * 
	 * @var string
	 */
	protected $_defaultType = 'term' ;
	
	/**
	 * Criteria normalized data.
	 * 
	 * @var array
	 */
	protected $_data = array() ;
	
	/**
	 * Constructor.
	 * 
	 * @param mixed		$definition		Criteria definition. String or array.
	 * @param array		$options		Array of options.
	 */
	public function __construct($definition = null, array $options = null) {
		if (isset($definition) || isset($options)) {
			$this->_data = $this->_normalize($definition, $options) ;
		}
		
		if (isset($definition)) {
			$this->_type = $this->_type($this->_data, $options) ;
		}
		
		if (isset($options)) {
			$this->_options = $options ;
		}
	}
	
	/**
	 * Returns the current criteria type.
	 * 
	 * @return string
	 */
	public function type() {
		return $this->_type ;
	}
	
	/**
	 * Returns all the normalized data, or only for a key if $key is given.
	 * 
	 * @param string	$key	[optionnal]	Key to return.
	 * @return mixed			Normalized data 
	 */
	public function get($key = null) {
		if (isset($key)) {
			return isset($this->_data[$key]) ? $this->_data[$key] : null ;
		}
		return array_filter($this->_data) ;
	}
	
	/**
	 * Detect the criteria type. Try to detect it if not explicitly defined.
	 * 
	 * @param array		$definition		Criteria definition
	 * @param array		$options		Criteria options
	 * @return string					Type. 
	 */
	protected function _type(array $definition, array $options = null) {
		
		
		if (isset($options['type'])) {
			return $options['type'] ;
		}
		
		$in = $this->_in($definition) ;

		if (isset($in) && is_string($in) && isset($definition['query'])) {
			if (is_string($in) && is_array($definition['query'])) {
				return 'terms' ;
			}
		}
		
		return $this->_defaultType ;
	}
	
	/**
	 * Normalize the search scope (fields/field/in).
	 * 
	 * @param array		$definition		Criteria definition
	 * @return mixed					Scope (string or array) 
	 */
	protected function _in($definition) {
		if (isset($definition['in'])) {
			if (is_array($definition['in'])) {
				if (count($definition['in']) === 1) {
					return $definition['in'][0] ;
				}
			}
			return $definition['in'] ;
		}
		if (isset($definition['field'])) {
			return $definition['field'] ;
		}
		if (isset($definition['fields'])) {
			return $definition['fields'] ;
		}
		return null ;
	}
	
	/**
	 * Normalize $definition (query / in).
	 * 
	 * @param mixed		$definition		Criteria definition (string/array)
	 * @return array					Normalized definition 
	 */
	protected function _normalize($definition) {
		if (is_string($definition)) {
			$definition = array('query' => $definition) ;
		} else {
			$definition['in'] = $this->_in($definition) ;
			if (isset($definition['field'])) {
				unset($definition['field']) ;
			}
			if (isset($definition['fields'])) {
				unset($definition['fields']) ;
			}
		}
		
		return $definition ;
	}
	
	/**
	 * Test if a criteria is mergeable with the current criteria.
	 * 
	 * @param Simples_Request_Search_Criteria $criteria		Criteria to test.
	 * @return boolean										Yes/no ?
	 */
	public function mergeable(Simples_Request_Search_Criteria $criteria) {
		return false ;
	}
	
	/**
	 * Merge a criteria with current. 
	 * 
	 * @param Simples_Request_Search_Criteria $criteria		Criteria to merge.
	 * @return \Simples_Request_Search_Criteria				This instance (fluid interface).
	 */
	public function merge(Simples_Request_Search_Criteria $criteria) {
		$this->_data = array_merge($this->_data, $criteria->get()) ;
		unset($criteria) ;
		$this->_type = $this->_type($this->_data, $this->_options) ;
		return $this ;
	}	
}