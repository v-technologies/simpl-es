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
	public function type() {
		if (isset($this->_options['type'])) {
			return $this->_options['type'] ;
		}

		if (empty($this->_data['in']) && empty($this->_data['value'])) {
			return 'match_all' ;
		}

		if (isset($this->_data['in']) && is_string($this->_data['in']) && isset($this->_data['value'])) {
			if (is_string($this->_data['value'])) {
				if (preg_match('/^[a-z0-9 ]+$/i', $this->_data['value']) && 
					!preg_match('/(AND|OR)/', $this->_data['value'])) {
					return 'term' ;
				}
			}
		}
		
		return parent::type() ;
	}
	
	/**
	 * Prepare for a "match_all" clause.
	 * 
	 * @return array
	 */
	protected function _prepare_match_all() {
		// Force json_encode to create a {} (and not a [] wich causes a crash with facets clause)
		return array('match_all' => new stdClass()) ;
	}
	
	/**
	 * Prepare for a "query_string" clause.
	 * 
	 * @return array
	 * @throws Simples_Request_Exception 
	 */
	protected function _prepare_query_string() {
		$return = $this->get() ;
		if (array_key_exists('value', $return)) {
			$return['query'] = $return['value'] ;
			unset($return['value']) ;
		}
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
		if (array_key_exists('in',$return)) {
			if (isset($return['in'])) {
				if (is_array($return['in'])) {
					$return['fields'] = $return['in'] ;
				} else {
					$return['default_field'] = $return['in'] ;
				}
			}
			unset($return['in']) ;
		}
		
		return array('query_string' => $return) ;
	}
}