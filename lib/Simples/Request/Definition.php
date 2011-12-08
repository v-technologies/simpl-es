<?php

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
		'required' => array(
			'body' => array(),
			'options' => array()
		),
		'inject' => array(
			'directories' => array(),
			'params' => array()
		)
	) ;
	
	public function __construct(array $definition) {
		$this->_definition = $this->_merge(self::$_base, $definition) ;
	}
	
	public function method() {
		return $this->_definition['method'] ;
	}
	
	public function path() {
		return $this->_definition['path'] ;
	}
	
	public function required($name = null) {
		if (isset($name)) {
			return isset($this->_definition['required'][$name]) ? $this->_definition['required'][$name] : array() ;
		}
		return $this->_definition['required'] ;
	}
	
	public function inject($name = null) {
		if (isset($name)) {
			return isset($this->_definition['inject'][$name]) ? $this->_definition['inject'][$name] : array() ;
		}
		return $this->_definition['inject'] ;
	}
	
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