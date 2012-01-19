<?php

/**
 * Standard response class.
 * 
 * @author Sébastien Charrier <scharrier@gmail.com>
 * @package	Simples
 * @subpackage Reponse
 */
class Simples_Response extends Simples_Base {
		
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
	public function __construct(array $data, array $config = null) {
		$this->set($data) ;
		
		if (isset($config)) {
			$this->config($config) ;
		}
	}

	/**
	 * Set response data.
	 * 
	 * @param array $data			Array of data
	 * @return \Simples_Response	Current response
	 */
	public function set(array $data = null) {
		// Intercepted ES error
		if (isset($data['error'])) {
			throw new Simples_Response_Exception($data) ;
		}
		// Shard failure
		if (!empty($data['_shards']['failed'])) {
			if (empty($data['_shards']['failures'])) {
				throw new Simples_Response_Exception('An error has occured on a shard during request parsing') ;
			} else {
				$errors = array() ;
				foreach($data['_shards']['failures'] as $failure) {
					if (!empty($failure['reason'])) {
						$errors[] = $failure['reason'] ;
					}
				}
				throw new Simples_Response_Exception('Some errors have occured on a shard during request parsing : ' . implode($errors)) ;
			}
		}
		$this->_data = $data ;
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
				return new self($this->_data[$path]) ;
			}
			return $this->_data[$path] ;
		}
		
		return null ;
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
}