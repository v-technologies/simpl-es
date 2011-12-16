<?php

/**
 * A search criteria.
 * 
 * @author Sébastien Charrier <scharrier@gmail.com>
 * @package	Simples
 * @subpackage Request
 */
class Simples_Request_Search_Criteria_Query extends Simples_Request_Search_Criteria {
	
	/**
	 * Criteria type.
	 * 
	 * @var string
	 */
	protected $_type = 'match_all' ;
	
	protected $_defaultType = 'query_string' ;
	
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
				if (preg_match('/^[a-z0-9 ]+$/i', $definition['query']) && 
					!preg_match('/(AND|OR)/', $definition['query'])) {
					return 'term' ;
				}
			}
		}
		
		return parent::_type($definition, $options) ;
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
}