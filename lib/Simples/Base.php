<?php

abstract class Simples_Base {
	/**
	 * Configuration .
	 * 
	 * @var array 
	 */
	protected $_config = array() ;
	
	/**
	 * Configuration getter/setter.
	 * 
	 * Multiple call modes :
	 * - setter :
	 *		- multiple keys : give an array, with keys => values. Will be merged with current configuration.
	 *		- simple key : $key is your key, $value the value.
	 * - getter :
	 *		- simple key : returns the value for $key, if exists.
	 *		- all the configuration
	 * 
	 * Exemples of usage :
	 * $object->config('server', 'my.es-server.org') ;	// Configures the key 'server'
	 * $object->config(array(
	 *		'server' => 'my.es-server.org',
	 *		'port' => 9200
	 * ));												// Configures the 'server' and 'port' keys.
	 * $object->config('server') ;						// Returns the 'server' key value
	 * $object->config() ;								// Returns all the config
	 * 
	 * @param mixed			$key		Key or full configuration
	 * @param mixed			$value		Config value, in simple setter mode
	 * @return mixed					Current object in setter mode, value (or full config) in getter mode
	 */
	public function config($key = null, $value = null) {
		if (isset($key)) {
			if (is_array($key)) {
				$this->_config = $key + $this->_config ;
				return $this ;
			} 
			if (is_string($key) && isset($value)) {
				$this->_config[$key] = $value ;
				return $this ;
			}
			if (is_string($key)) {
				return isset($this->_config[$key]) ? $this->_config[$key] : null ;
			}
		}
		return $this->_config ;
	}
	
	/**
	 * Wrapper for format transformation : gives the request in the asked
	 * format.
	 * 
	 * Actually supported : array, json
	 * 
	 * @param string	$format		Asked format
	 * @return mixed				Formated request 
	 */
	public function to($format) {
		$method =  '_to' . ucfirst($format) ;
		if (method_exists($this, $method)) {
			return $this->{$method}($this->_data()) ;
		}
		
		throw new Simples_Request_Exception('Unsupported request transformation format : "' . $format . '"') ;
	}
	
	/**
	 * Base data getter.
	 * 
	 * @return array
	 */
	protected function _data() {
		if (isset($this->_data)) {
			return $this->_data ;
		}
		return array() ;
	}
	
	/**
	 * Json transformation
	 * 
	 * @return string	Request in json 
	 */
	protected function _toJson(array $data) {
		return !empty($data) ? json_encode($data) : '' ;
	}
	
	/**
	 * Array transformation
	 * 
	 * @return array 
	 */
	protected function _toArray(array $data) {
		return $data ;
	}
}