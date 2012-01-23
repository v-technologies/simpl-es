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
	protected $_defaultType = 'terms' ;
	
	/**
	 * Facet normalized data.
	 * 
	 * @var array
	 */
	protected $_data = array() ;
	
	/**
	 * Options .
	 * 
	 * @var array
	 */
	protected $_options = array() ;
	
	/**
	 * Facet filters.
	 * 
	 * @var Simples_Request_Search_Builder_Filters
	 */
	protected $_filters ;
	
	/**
	 * Fluid return.
	 * 
	 * @var mixed
	 */
	protected $_fluid ;
	
	/**
	 * Constructor.
	 * 
	 * @param mixed		$definition		Facet definition. String or array.
	 * @param array		$options		Array of options.
	 * @param mixed		$fluid			Fluid object instance.	
	 */
	public function __construct($definition = null, array $options = null, $fluid = null) {
		$this->_filters = new Simples_Request_Search_Builder_Filters() ;
		
		if (isset($definition) || isset($options)) {
			$this->_data = $this->_normalize($definition, $options) ;
		}
		
		if (isset($definition)) {
			$this->_type = $this->_type($this->_data, $options) ;
		}
		
		if (isset($options)) {
			$this->_options = $options ;
		}
		
		if (isset($fluid)) {
			$this->_fluid = $fluid ;
		}
	}
	
	/**
	 * Returns the current facet type.
	 * 
	 * @return string
	 */
	public function type() {
		return $this->_type ;
	}
	
	/**
	 * Get the calculated facet name.
	 * 
	 * @return string
	 */
	public function name() {
		if (!empty($this->_data['name'])) {
			return $this->_data['name'] ;
		}
		if (!empty($this->_data['in'])) {
			return $this->_data['in'] ;
		}
		return null ;
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
	 * Add multiple filters once.
	 * 
	 * @param array $filters		Liste of filters.
	 * @return mixed				Fluid instance. 
	 */
	public function filtered(array $filters = null) {
		if ($filters) {
			foreach($filters as $in => $query ){
				$this->_filters->add(array('query' => $query, 'in' => $in)) ;
			}
		}
		return $this->_fluid() ;
	}
	
	/**
	 * Magic call : chain with subobjects.
	 * 
	 * @param string	$name		Method name
	 * @param array		$args		Arguments
	 * @return \Simples_Request_Search 
	 */
	public function __call($name, $args) {
		call_user_func_array(array($this->_filters, $name), $args) ;
		return $this->_fluid() ;
	}
	
	/**
	 * Returns this instance or object setted in fluid property.
	 * 
	 * @return \Simples_Request_Search_Facet 
	 */
	protected function _fluid() {
		if (isset($this->_fluid)) {
			return $this->_fluid ;
		}
		return $this ;
	}
	
	/**
	 * Detect the facet type. Try to detect it if not explicitly defined.
	 * 
	 * @param array		$definition		Criteria definition
	 * @param array		$options		Criteria options
	 * @return string					Type. 
	 */
	protected function _type(array $definition, array $options = null) {
		if (isset($options['type'])) {
			return $options['type'] ;
		}
		
		return $this->_defaultType ;
	}
	
	/**
	 * Normalize $definition
	 * 
	 * @param mixed		$definition		Criteria definition (string/array)
	 * @return array					Normalized definition 
	 */
	protected function _normalize($definition) {
		if (is_string($definition)) {
			$definition = array('in' => $definition) ;
		} else {
                        $in = $this->_in($definition) ;
                        if (isset($in)) {
                            $definition['in'] = $in ;
                            if (isset($definition['field'])) {
                                    unset($definition['field']) ;
                            }
                            if (isset($definition['fields'])) {
                                    unset($definition['fields']) ;
                            }
                        }
		}
		return $definition ;
	}
	
	/**
	 * Normalize the search scope (fields/field/in).
	 * 
	 * @param array		$definition		Facet definition
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
	 * Prepare data for transformation.
	 * 
	 * @return array
	 * @throws Simples_Request_Exception 
	 */
	protected function _data() {
		$data = $this->_data ;
		if (empty($data['in']) && empty($data['value_field'])) {
			throw new Simples_Request_Exception('Facet error : no scope (keys "field","fields","value_field" and "in" are empty)') ;
		}
		
		// Name
		if (empty($data['name']) && !empty($data['in'])) {
			$name = $data['in'] ;
		} elseif (!empty($data['name'])) {
			$name = $data['name'] ;
			unset($data['name']) ;
		} else {
			throw new Simples_Request_Exception('Facet error : the facet\'s name cannot be determined') ;
		}
		
		// Scope
                if (isset($data['in'])) {
                    if (is_array($data['in'])) {
                            $data['fields'] = $data['in'] ;
                    } else {
                            $data['field'] = $data['in'] ;
                    }
                    unset($data['in']) ;
                }
		
		$return = array($this->type() => $data) ;
		
		// Filters
		if (count($this->_filters)) {
			$return['facet_filter'] = $this->_filters->to('array') ;
		}
		
		return  array($name => $return ) ; 
	}
	
	/**
	 * Test if a criteria is mergeable with the current criteria.
	 * 
	 * @param Simples_Request_Search_Criteria $facet		Criteria to test.
	 * @return boolean										Yes/no ?
	 */
	public function mergeable(Simples_Request_Search_Facet $facet) {
		$data =	$facet->get() ;
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
	 * @param Simples_Request_Search_Criteria $facet		Criteria to merge.
	 * @return \Simples_Request_Search_Criteria				This instance (fluid interface).
	 */
	public function merge(Simples_Request_Search_Facet $facet) {
		$this->_data = array_merge($this->_data, $facet->get()) ;
		unset($facet) ;
		$this->_type = $this->_type($this->_data, $this->_options) ;
		return $this ;
	}	
}