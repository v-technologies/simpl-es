<?php

/**
 * A search criteria.
 * 
 * @author Sébastien Charrier <scharrier@gmail.com>
 * @package	Simples
 * @subpackage Request
 */
abstract class Simples_Request_Search_Criteria extends Simples_Base {
	
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
	protected $_data = array(
		'query' => null,
		'in' => null
	) ;
	
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
	 * Returns the criteria options.
	 * 
	 * @return array	Options
	 */
	public function options() {
		return $this->_options ;
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
	 * Returns the data prepared for ES requesting.
	 * 
	 * @return array
	 */
	protected function _data(array $options = array()) {
		$method = '_prepare_' . $this->_type ;
		if (method_exists($this, $method)) {
			return $this->{$method}() ; 
		}
		
		return $this->_prepare_term($this->_type) ;
	}
	
	/**
	 * Prepare for a "term" clause.
	 * 
	 * @return array
	 */
	protected function _prepare_term($type = 'term') {
		$data = $this->_data ;
		$in = $data['in'] ;
		$value = isset($data['query']) ? $data['query'] : null ;
		unset($data['in']) ;
		unset($data['query']) ;
			
		if (!is_array($in)) {
			$return = array(
				$type => array(
					$in => $this->_termIn($type, $value, $data)
				) 
			);
		} else {
			$return = array(
				'bool' => array(
					'should' => array()
				)
			) ;
			foreach($in as $field) {
				$_clause = array(
					$type => array(
						$field => $this->_termIn($type, $value, $data)
					)
				) ;
				$return['bool']['should'][] = $_clause ;
			}
		}
		
		
		return $return ;
	}

	/**
	 * Autodetect the "in" key to use. Not coherent in the ES API, I try to make
	 * some magic here.
	 * 
	 * @param  string $type  Criteria type
	 * @param  string $value Value
	 * @param  [type] $data  [description]
	 * @return [type]        [description]
	 */
	protected function _termIn($type, $value, $data) {
		if (!$data) {
			return $value ;
		}
		if (in_array($type, array('term','terms','prefix'))) {
			$key = 'value' ;
		} else {
			$key = 'query' ;
		}
		return array($key => $value) + $data ;
	}

	/**
	 * Prepare for a range clause.
	 * 
	 * @return array
	 */
	protected function _prepare_range() {
		$data = $this->_data ;
		$in = $data['in'] ;
		unset($data['in']) ;
			
		$return = array(
			'range' => array(
				$in => $data
			) 
		);
		
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
		$options = $criteria->options() ;
		foreach($options as $key => $value) {
			if (isset($this->_options[$key])) {
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