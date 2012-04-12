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
		'value' => null,
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
		
		if (isset($options)) {
			$this->_options = $options ;
		}
	}
	
	/**
	 * Returns the criteria options.
	 * 
	 * @return array	Options
	 */
	public function options(array $options = null) {
		if (isset($options)) {
			$this->_options = $options + $this->_options ;
			return $this ;
		}
		return $this->_options ;
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
		$data = $this->_data ;
		foreach($data as $key => $value) {
			if (!isset($value) || (is_string($value) && !strlen($value))) {
				unset($data[$key]) ;
			}
		}
		return $data ;
	}
	
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

		if (isset($this->_data['in']) && is_string($this->_data['in']) && isset($this->_data['value'])) {
			if (is_string($this->_data['in']) && is_array($this->_data['value'])) {
				return 'terms' ;
			}
		}
		
		return $this->_defaultType ;
	}
	
	/**
	 * Normalize $definition (query / in).
	 * 
	 * @param mixed		$definition		Criteria definition (string/array)
	 * @return array					Normalized definition 
	 */
	protected function _normalize($definition) {
		if (is_string($definition)) {
			$definition = array('value' => $definition) ;
		} elseif (is_array($definition)) {
			$this->_in($definition) ;
			$this->_value($definition) ;
		} else {
			$definition = array() ;
		}
		
		return $definition + array('value' => null, 'in' => null) ;
	}

	/**
	 * Normalize the search scope (fields/field/in).
	 * 
	 * @param array		$definition		Criteria definition
	 */
	protected function _in(&$definition) {
		$aliases = array('field','fields') ;
		foreach($aliases as $alias) {
			if (isset($definition[$alias])) {
				$definition['in'] = $definition[$alias] ;
				unset($definition[$alias]) ;
			}
		}

		// Final wash 
		if (isset($definition['in'])) {
			if (is_array($definition['in'])) {
				if (count($definition['in']) === 1) {
					$definition['in'] =  $definition['in'][0] ;
				}
			}
		}
	}

	/**
	 * Normalize values keys.
	 * 
	 * @param  array &$definition Clause definition
	 */
	protected function _value(&$definition) {
		$aliases = array('query','term','terms','match','values') ;

		foreach($aliases as $alias) {
			if (isset($definition[$alias])) {
				$definition['value'] = $definition[$alias] ;
				unset($definition[$alias]) ;
			}
		}
	}
	
	/**
	 * Returns the data prepared for ES requesting.
	 * 
	 * @return array
	 */
	protected function _data(array $options = array()) {
		$type = $this->type() ;
		$method = '_prepare_' . $type ;
		if (method_exists($this, $method)) {
			return $this->{$method}() ; 
		}
		
		return $this->_prepare_term($type) ;
	}
	
	/**
	 * Prepare for a "term" clause.
	 * 
	 * @return array
	 */
	protected function _prepare_term($type = 'term') {
		$data = $this->get() ;
		
		if (!isset($data['in']) || !isset($data['value'])) {
			throw new Simples_Request_Exception('Key "in" or "value" empty') ;
		}

		$in = $data['in'] ;
		$value = $data['value'] ;
		$data = array_diff_key($data, array('in' => true, 'value' => true)) ;
			
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
	 * Prepare for a "missing" or "exists" clause.
	 * 
	 * @return array
	 */
	protected function _prepare_missing($type = 'missing') {
		$data = $this->get() ;
		
		if (!isset($data['in'])) {
			throw new Simples_Request_Exception('Key "in" is empty') ;
		}

		$in = $data['in'] ;
		unset($data['in']) ;
			
		
		$return = array(
			$type => array(
				'field' => $in
			) + $data
		);		
		
		return $return ;
	}

	/**
	 * Prepare for an "exists" clause.
	 * 
	 * @return array
	 */
	protected function _prepare_exists() {
		return $this->_prepare_missing('exists') ;
	}

	/**
	 * Prepare for a "geo_bounding_box" clause.
	 * 
	 * @return array
	 */
	protected function _prepare_ids() {
		$data = $this->get() ;
		
		if (!isset($data['value'])) {
			throw new Simples_Request_Exception('Key "value" is empty') ;
		}

		$value = $data['value'] ;
		unset($data['value']) ;
			
		
		$return = array(
			'ids' => array(
				'values' => $value
			) + $data
		);		
		
		return $return ;
	}

	/**
	 * Prepare for a "geo_bounding_box" clause.
	 * 
	 * @return array
	 */
	protected function _prepare_geo_bounding_box() {
		$data = $this->get() ;
		
		if (!isset($data['in'])) {
			throw new Simples_Request_Exception('Key "in" is empty') ;
		}

		$in = $data['in'] ;
		unset($data['in']) ;
			
		
		$return = array(
			'geo_bounding_box' => array(
				$in => $data
			)
		);		
		
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
		$data = $this->get() ;
		
		if (!isset($data['in'])) {
			throw new Simples_Request_Exception('Key "in" empty') ;
		}

		$in = $data['in'] ;
		unset($data['in']) ;
			
		if (!isset($data['ranges'])) {
			$return = array(
				'range' => array(
					$in => $data
				) 
			);
		} else {
			$return = array(
				'bool' => array(
					'should' => array()
				)
			) ;
			$_data = $data ;
			unset($_data['ranges']) ;
			foreach($data['ranges'] as $range) {
				$_clause = array(
					'range' => array(
						$in => $range + $_data
					)
				) ;
				$return['bool']['should'][] = $_clause ;
			}
		}
		
		return $return ;
	}

	/**
	 * Prepare for a geo distance clause.
	 * 
	 * @return array
	 */
	protected function _prepare_geo_distance() {
		$data = $this->get() ;
		
		if (!isset($data['in'])) {
			throw new Simples_Request_Exception('Key "in" is empty') ;
		}

		$in = $data['in'] ;
		if (!empty($data['lat']) && !empty($data['lon'])) {
			$values = array('lat' => $data['lat'], 'lon' => $data['lon']) ;
			unset($data['lat']) ;
			unset($data['lon']) ;
		} elseif (!empty($data['value'])) {
			$values = $data['value'] ;
			unset($data['value']) ;
		}
		unset($data['in']) ;

		if (empty($values)) {
			throw new Simples_Request_Exception('Keys "values","lat","lon" are empty') ;
		}
		
			
		$return = array(
			'geo_distance' => array($in => $values) + $data
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
		return $this ;
	}	
}