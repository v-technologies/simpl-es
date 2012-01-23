<?php

/**
 * An ES document.
 * 
 * @author Sébastien Charrier <scharrier@gmail.com>
 * @package	Simples
 */
class Simples_Document extends Simples_Base {
	
	/**
	 * Document properties.
	 * 
	 * @var Simples_Document
	 */
	protected $_properties ;
	
	/**
	 * Data
	 * 
	 * @var array
	 */
	protected $_data = array() ;
	
	/**
	 * Constructor.
	 * 
	 * @param SimplesTransport $transport		Connection to use.
	 */
	public function __construct(array $data = null) {
		if (isset($data)) {
			$this->set($data) ;
		}
	}

	/**
	 * Set response data.
	 * 
	 * @param array $data			Array of data
	 * @return \Simples_Response	Current response
	 */
	public function set(array $data = null) {
		if (isset($data['_source'])) {
			$this->_data = $data['_source'] ;
			
			// Renaming properties (for simplified call)
			$properties = array() ;
			foreach($data as $key => $value) {
				if ($key !== '_source') {
					$key = preg_replace('/^(_)/','', $key) ;
					$properties[$key] = $value ;
				}
			}
			$this->_properties = new Simples_Document($properties) ;
			
		} else {
			$this->_data = $data ;
		}
		
		return $this ;
	}
	
	/**
	 * Get some data. If no passe is given, returns all the $this->_data array. If subdata for $path
	 * if an array, returns a news instance of self.
	 * 
	 * @param string $path	Path we want to get	
	 * @return mixed		The value, another instance or null.
	 */
	public function get($path = null) {
		// We want all our data back !
		if (!isset($path)) {
			return $this->_data ;
		}
		
		if (isset($this->_data[$path])) {
			if (is_array($this->_data[$path])) {
				if (Simples_Document_Set::check($this->_data[$path])) {
					return new Simples_Document_Set($this->_data[$path]) ;
				}
				return new self($this->_data[$path]) ;
			}
			return $this->_data[$path] ;
		}
		
		return null ;
	}
	
	/**
	 * Returns the object properties.
	 * 
	 * @return Simples_Document
	 */
	public function properties() {
		return $this->_properties ;
	}
	
	/**
	 * Magic access. Gives the ability to call :
	 * $response->param->subparam
	 * 
	 * @param string	$path	Path we want to get
	 * @return mixed			The value, another instance or null.
	 */
	public function __get($path) {
		return $this->get($path) ;
	}
	
	/**
	 * Check if a key is set in $this->_data.
	 * 
	 * @param string	$path	Path to check
	 * @return bool				Yep, or nope. 
	 */
	public function __isset($path) {
		return isset($this->_data[$path]) ;
	}
	
	/**
	 * Smart data recovering : with _source and without.
	 * 
	 * Options :
	 * - clean (bool) : remove the empty values and convert numerics to floats
	 * - source (mixed) : false to force not working with "_source", true to force working with it. Else, "auto".
	 * 
	 * @return array	Prepared data.
	 */
	protected function _data(array $options = array()) {
		$options += array(
			'clean' => false,
			'source' => 'auto'
		);
		
		$data = parent::_data($options) ;
		
		if ($options['clean']) {
			$this->_clean($data) ;
		}
		
		$source = (bool) $options['source'] ;
		if ($options['source'] === 'auto' && !isset($this->_properties)) {
			$source = false ;
		}
		
		if (!$source) {
			return $data ;			
		} else {
			$return = array() ;

			// Rename properties
			if (isset($this->_properties)) {
				$properties = $this->_properties->to('array') ;
				foreach($properties as $key => $value) {
					$return['_' . $key] = $value ;
				}
			}

			// Add the source
			$return['_source'] = $data ;
			
		}
		
		return $return ;
	}
	
	/**
	 * Clean an object : removes all the empty fields and transforms numeric fields
	 * in float.
	 * 
	 * @param array		$data	Object (by reference)
	 */
	protected function _clean(& $data) {
		foreach($data as $key => $value) {
			if (is_array($value)) {
				$this->_clean($data[$key]) ;
			} elseif ((is_scalar($value) && !strlen($value)) || !isset($value)) {
				unset($data[$key]) ;
			} elseif (is_numeric($value)) {
				$data[$key] = (float) $value ;
			}
		}
	}
}