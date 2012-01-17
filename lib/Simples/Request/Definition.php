<?php

/**
 * Manage a request definition.
 * 
 * @author Sébastien Charrier <scharrier@gmail.com>
 * @package	Simples
 * @subpackage Request
 */
class Simples_Request_Definition {
	
	/**
	 * Current merged definition
	 * 
	 * @var array
	 */
	protected $_definition = array() ;
	
	/**
	 * Base definition 
	 */
	static protected $_base = array(
		'method' => null,
		'path' => null,
		'magic' => null,
		'required' => array(
			'body' => array(),
			'options' => array()
		),
		'inject' => array(
			'directories' => array(),
			'params' => array()
		)
	) ;
	
	/**
	 * Constructor.
	 * 
	 * @param array $definition  Array of definition.
	 */
	public function __construct(array $definition) {
		$this->_definition = $this->_merge(self::$_base, $definition) ;
		
		if (!isset($this->_definition['method'])) {
			throw new Simples_Request_Exception('No method defined (key "method" empty)') ;
		}
	}
	
	/**
	 * Returns the method.
	 * 
	 * @return string
	 */
	public function method() {
		return $this->_definition['method'] ;
	}
	
	/**
	 * Returns the path.
	 * 
	 * @return string
	 */
	public function path() {
		return $this->_definition['path'] ;
	}
	
	/**
	 * Returns default magic param
	 * 
	 * @return string
	 */
	public function magic() {
		return $this->_definition['magic'] ;
	}
	
	/**
	 * Returns the required elements. If $name given, returns the required keys for 
	 * the $name type.
	 * 
	 * @param string	$name		[optionnal] Type name
	 * @return array 
	 */
	public function required($name = null) {
		if (isset($name)) {
			return isset($this->_definition['required'][$name]) ? $this->_definition['required'][$name] : array() ;
		}
		return $this->_definition['required'] ;
	}
	
	/**
	 * Returns the to inject elements. If $name given, returns the keys for 
	 * the $name type only.
	 * 
	 * @param string	$name		[optionnal] Type name
	 * @return array 
	 */
	public function inject($name = null) {
		if (isset($name)) {
			return isset($this->_definition['inject'][$name]) ? $this->_definition['inject'][$name] : array() ;
		}
		return $this->_definition['inject'] ;
	}
	
	/**
	 * Merge two arrays recursivly.
	 * 
	 * @param array $base		Base array
	 * @param array $merge		Array to merge
	 * @return array			Merged.
	 */
	protected function _merge(array $base, array $merge) {
		foreach ($merge as $key => $val)	 {
			if (is_array($val) && isset($base[$key]) && is_array($base[$key])) {
				$base[$key] = $this->_merge($base[$key], $val);
			} elseif (is_int($key)) {
				$base[] = $val;
			} else {
				$base[$key] = $val;
			}
		}
		return $base ;
	}
}