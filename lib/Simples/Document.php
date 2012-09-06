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
	 * Configuration:
	 * - source (bool) : force or not if we are working on a document in the ES hit format or in a standard document
	 * - mapping (array) : force some types when cleaning the object (clean === true in the to() call)
	 * 
 	 * @var array
	 */
	protected $_config = array(
		'source' => null,
		'mapping' => array()
	);
	
	/**
	 * Constructor.
	 * 
	 * @param SimplesTransport $transport		Connection to use.
	 */
	public function __construct(array $data = null, array $options = null) {
		if (isset($options)) {
			$this->config($options) ;
		}
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
		$source = $this->config('source') ;
		if ($source === true || ($source === null && (isset($data['_source']) || isset($data['fields'])))) {
			if (isset($data['_source'])) {
				$key = '_source' ;
			} else {
				$key = 'fields' ;
			}

			if (isset($data[$key])) {
				$this->_data = $data[$key] ;
			}
			
			// Renaming properties (for simplified call)
			$properties = array() ;
			foreach($data as $_key => $value) {
				if ($_key !== $key) {
					$_key = preg_replace('/^(_)/','', $_key) ;
					$properties[$_key] = $value ;
				}
			}
			$this->_properties = new Simples_Document($properties) ;
		} else {
			$this->_data = $data ;
		}
		
		return $this ;
	}

	/**
	 * Delete all the data or only the $key field.
	 * 
	 * @param  string $key          Field to delete.
	 * @return Simples_Document 	$this instance (for fluid calls)
	 */
	public function delete($key = null) {
		if (!isset($key)) {
			$this->_data = array() ;
		} elseif (isset($this->_data[$key])) {
			unset($this->_data[$key]) ;
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
	protected function _clean(& $data, $path = array()) {
		foreach($data as $key => $value) {
			// Current path calculation
			$_keys = $path ;
			if (!is_numeric($key)) {
				$_keys[] = $key ;
			}
			$_path = implode($_keys, '.') ;
			if (isset($this->_config['mapping'][$_path])) {
				// We wanna force the type
				settype($data[$key], $this->_config['mapping'][$_path]) ;
			} else {
				if (is_array($value)) {
					$this->_clean($data[$key], $_keys) ;
				} elseif ((is_scalar($value) && !strlen($value)) || !isset($value)) {
					unset($data[$key]) ;
				} elseif (is_numeric($value)) {
					$data[$key] = (float) $value ;
				}
			}
		}
	}
}