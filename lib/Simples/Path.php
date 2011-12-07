<?php

/**
 * Path management.
 * 
 * @author Sébastien Charrier <scharrier@gmail.com>
 * @package	Simples
 */
class Simples_Path {
	
	/**
	 * Base path.
	 * 
	 * @var string
	 */
	protected $_base = '' ;
	
	/**
	 * Directories (/firs/second/...)
	 * 
	 * @var array
	 */
	protected $_directories = array() ;
	
	/**
	 * Query string params (?first=value&second=value...)
	 * 
	 * @var array
	 */
	protected $_params = array() ;
	
	/**
	 * Constructor.
	 * 
	 * @param string	$base	[optionnal] Base path
	 */
	public function __construct($base = null) {
		if (isset($base)) {
			$this->_base = $base ;
		}
	}
	
	/**
	 * Add a directory
	 * 
	 * @param string	$name	Dir name
	 */
	public function directory($name) {
		$this->_directories[] = $name ;
	}
	
	/**
	 * Add some directories
	 * 
	 * @param array $names		Array of names
	 */
	public function directories(array $names) {
		$this->_directories = array_merge($this->_directories, $names) ;
	}
	
	/**
	 * Add a parameter.
	 * 
	 * @param string	$name	Param name
	 * @param mmixed	$value  Param value
	 */
	public function param($name, $value) {
		$this->_params[$name] = $value ;
	}
	
	/**
	 * Add multiples params.
	 * 
	 * @param array $params		Array of params : $key => $value
	 */
	public function params(array $params) {
		$this->_params = array_merge($this->_params, $params) ;
	}
	
	/**
	 * Generates the string path.
	 * 
	 * @return string			Full path.
	 */
	public function __toString() {
		$path = '/' ;
		
		if (strlen($this->_base)) {
			$path .= trim($this->_base, '/') . '/' ;
		}
		
		if (!empty($this->_directories)) {
			$path .= implode('/', array_map('urlencode', $this->_directories)) . '/' ;
		}
		
		if (!empty($this->_params)) {
			$params = array() ;
			foreach($this->_params as $key => $value) {
				$params[] = $key . '=' . urlencode($value) ;
			}
			$path .= '?' . implode('&', $params) ;
		}
		
		return $path ;
	}
	
}