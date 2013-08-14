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
	 * - cast (array) : force some types when cleaning the object (clean === true in the to() call)
	 *
 	 * @var array
	 */
	protected $_config = array(
		'source' => null,
		'cast' => array()
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
	public function set($name, $data = null) {
		if (isset($data)) {
			$this->_data[$name] = $this->_import($data) ;
		} else {
			$data = $name ;
			$source = $this->config('source') ;
			if ($source === true || ($source === null && (isset($data['_source']) || isset($data['fields'])))) {
				if (isset($data['_source'])) {
					$key = '_source' ;
				} else {
					$key = 'fields' ;
				}

				if (isset($data[$key])) {
					$this->_data = $this->_import($data[$key]) ;
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
				$this->_data = $this->_import($data) ;
			}
		}

		return $this ;
	}

	/**
	 * Import a data array  and transforms entities into Simples_Document or
	 * Simples_Document_Set.
	 *
	 * @param  array  $data Data to import
	 * @return array        Data ready to work on
	 */
	protected function _import($data) {
		if (is_scalar($data)) {
			return $data ;
		}

		foreach($data as $key => $value) {
			// Prepare the cast config to remove the current key
			$options = $this->_config ;
			if (!empty($options['cast'])) {
				$this->_options($key, $options) ;
			}
			if (!is_scalar($value)) {
				if (Simples_Document_Set::check($value)) {
					$data[$key] = new Simples_Document_Set($value, array('source' => false) + $options) ;
				} elseif (is_array($value) && !is_numeric(key($value))) {
					$data[$key] = new Simples_Document($value, array('source' => false) + $options) ;
				}
			}
		}
		return $data ;
	}

	/**
	 * Export data into an array, transforming recursively objects into arrays.
	 *
	 * @param  array  $data    Data to export
	 * @param  array  $options Export options
	 * @return array           Full data object
	 */
	protected function _export(array $data, array $options) {
		foreach($data as $key => $value) {
			if (!empty($options['cast'])) {
				$this->_options($key, $options) ;
			}
			if ($value instanceof Simples_Base) {
				$data[$key] = $value->to('array', array('source' => false) + $options) ;
			}
		}
		return $data ;
	}

	/**
	 * Prepares an options array, removing actual key from cast paths.
	 *
	 * @param  string $key     Current key
	 * @param  array  $options Options (by reference)
	 */
	protected function _options($key, array & $options) {
		foreach($options['cast'] as $path => $_value) {
			$_path = preg_replace('/^' . $key . '\./', '', $path) ;
			if ($_path !== $path) {
				$options['cast'][$_path] = $_value ;
				unset($options['cast'][$path]) ;
			}
		}
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

		// First level key
		if (isset($this->_data[$path])) {
			return $this->_data[$path] ;
		}

		// Access from a string path
		if (strpos($path, '.') !== false) {
			$path = explode('.', $path) ;
			$current = $this ;
			foreach($path as $level) {
				if (!isset($current->{$level})) {
					return null ;
				}
				$current = $current->{$level} ;
			}
			return $current ;
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

	public function __set($name, $value) {
		$this->set($name, $value) ;
	}

	/**
	 * Unset a key.
	 *
	 * @param string	$path	Path to check
	 * @return bool				Yep, or nope.
	 */
	public function __unset($path) {
		if (isset($this->_data[$path])) {
			unset($this->_data[$path]) ;
		}
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
			'source' => 'auto',
			'cast' => $this->_config['cast']
		);

		$data = parent::_data($options) ;

		if ($options['clean']) {
			$this->_clean($data, $options) ;
		}

		$source = (bool) $options['source'] ;
		if ($options['source'] === 'auto' && !isset($this->_properties)) {
			$source = false ;
		}

		if (!$source) {
			return $this->_export($data, $options) ;
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
			$return['_source'] = $this->_export($data, $options) ;

		}

		return $return ;
	}

	/**
	 * Clean an object : removes all the empty fields and transforms numeric fields
	 * in float.
	 *
	 * @param array		$data	Object (by reference)
	 */
	protected function _clean(& $data, array $options = array(), $path = array()) {
		foreach($data as $key => $value) {
			// Current path calculation
			$_keys = $path ;
			if (!is_numeric($key)) {
				$_keys[] = $key ;
			}
			$_path = implode($_keys, '.') ;
			if (isset($options['cast'][$_path])) {
				// We wanna force the type
				settype($data[$key], $options['cast'][$_path]) ;
			} else {
				if ($value instanceof Simples_Base) {
					//$this->_clean($value, $options, $_keys) ;
				} elseif ((is_scalar($value) && !strlen($value)) || !isset($value)) {
					if ($data instanceof Simples_Base) {
						unset($data->{$key}) ;
					} else {
						unset($data[$key]) ;
					}
				} elseif (is_numeric($value)) {
					$data[$key] = (float) $value ;
				}
			}
		}
	}
}
