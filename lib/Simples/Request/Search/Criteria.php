<?php

/**
 * A search criteria.
 * 
 * @author Sébastien Charrier <scharrier@gmail.com>
 * @package	Simples
 * @subpackage Request
 */
class Simples_Request_Search_Criteria extends Simples_Base {
	
	/**
	 * Criteria type.
	 * 
	 * @var string
	 */
	protected $_type = 'match_all' ;
	
	/**
	 * Criteria normalized data.
	 * 
	 * @var array
	 */
	protected $_data = array() ;
	
	/**
	 * Criteria options.
	 * 
	 * @var array
	 */
	protected $_options = array() ;
	
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
			if (is_string($definition['query'])) {
				if (preg_match('/^[a-z0-9 ]+$/i', $definition['query'])) {
					return 'term' ;
				}
			} else if (is_string($in) && is_array($definition['query'])) {
				return 'terms' ;
			}
		}
		
		return 'query_string' ;
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
	 * Returns the data prepared for ES requesting.
	 * 
	 * @return array
	 */
	protected function _data() {
		$method = '_prepare_' . $this->_type ;
		if (method_exists($this, $method)) {
			return array($this->_type => $this->{$method}()) ; 
		}
		
		return array($this->_type => $this->_data) ;
	}
	
	/**
	 * Prepare for a "match_all" clause.
	 * 
	 * @return array
	 */
	protected function _prepare_match_all() {
		// Force json_encode to create a {} (and not a [] wich causes a crash with facets clause)
		return new stdClass() ;
	}
	
	/**
	 * Prepare for a "term" clause.
	 * 
	 * @return array
	 */
	protected function _prepare_term() {
		$data = $this->_data ;
		$return = array(
			$data['in'] => $data['query']
		);
		unset($data['in']) ;
		unset($data['query']) ;
		$return += $data ;
		return $return ;
	}
	
	/**
	 * Prepare for a "terms" clause.
	 * 
	 * @return array
	 */
	protected function _prepare_terms() {
		return $this->_prepare_term() ;
	}
	
	/**
	 * Prepare for a "query_string" clause.
	 * 
	 * @return array
	 * @throws Simples_Request_Exception 
	 */
	protected function _prepare_query_string() {
		$return = $this->_data ;
		
		// Multiple values
		if (isset($return['query']) && is_array($return['query'])) {
			$mode = 'AND' ;
			if (isset($this->_options['mode'])) {
				$mode = strtoupper($this->_options['mode']) ;
			}
			if (!in_array($mode, array('AND','OR'))) {
				throw new Simples_Request_Exception('Bad search criteria mode "' . $mode . '"') ;
			}
			$return['query'] = implode(' ' . $mode . ' ', $return['query']) ;
		}
		
		// Search in field(s)
		if (isset($return['in'])) {
			if (is_array($return['in'])) {
				$return['fields'] = $return['in'] ;
			} else {
				$return['default_field'] = $return['in'] ;
			}
			unset($return['in']) ;
		}
		
		return $return ;
	}
	
	/**
	 * Test if a criteria is mergeable with the current criteria.
	 * 
	 * @param Simples_Request_Search_Criteria $criteria		Criteria to test.
	 * @return boolean										Yes/no ?
	 */
	public function mergeable(Simples_Request_Search_Criteria $criteria) {
		$data =	$criteria->get() ;
		foreach($data as $key => $value) {
			if (isset($this->_data[$key])) {
				return false ;
			}
		}
		return true ;
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