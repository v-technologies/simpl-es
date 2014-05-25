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
	public function set($key = null, $data = null) {
		if (is_string($key)) {
			$this->_data[$key] = $data ;
		} else {
			$this->_check($key) ;
			$this->_data = $key ;
		}

		return $this ;
	}
	
	/**
	 * Check if response data is valid.
	 * 
	 * @param array $data	Response data.
	 * @throws Simples_Response_Exception 
	 */
	protected function _check(array $data) {
		// Intercepted ES error
		if (isset($data['body']['error'])) {
			throw new Simples_Response_Exception($data['body']) ;
		}
		// Shard failure
		if (!empty($data['body']['_shards']['failed'])) {
			if (empty($data['body']['_shards']['failures'])) {
				throw new Simples_Response_Exception('An error has occured on a shard during request parsing') ;
			} else {
				$errors = array() ;
				foreach($data['body']['_shards']['failures'] as $failure) {
					if (!empty($failure['reason'])) {
						$errors[] = $failure['reason'] ;
					}
				}
				throw new Simples_Response_Exception('Some errors have occured on a shard during request parsing : ' . implode($errors)) ;
			}
		}
		//TODO use http codes
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
		
		$get = function(array $path) {
			$data = $this->_data;
			foreach ($path as $value) {
				if (!isset($data[$value])) {
					return null;
				}

				$data = $data[$value];
			}

			if (is_array($data)) {
				return new self($data);
			}
			return $data;
		};

		$direct = $get(array($path));

		if (!is_null($direct)) {
			return $direct;
		}

		$direct = $get(array($path));

		if (!is_null($direct)) {
			return $direct;
		}

		$body = $get(array('body', $path));

		if (!is_null($body)) {
			return $body;
		}

		return $get(array('http', $path));
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
