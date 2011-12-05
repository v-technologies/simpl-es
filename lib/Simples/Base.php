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
}