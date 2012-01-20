<?php

/**
 * ElasticSearch connection class : connect to a server, check its configuration, and exchange requests
 * and responses with this server.
 * 
 * Actually only HTTP exchange are supported.
 * 
 * @author Sébastien Charrier <scharrier@gmail.com>
 * @package	Simples
 */
abstract class Simples_Transport extends Simples_Base {
	
	protected $_factory ;
	
	/**
	 * Logs.
	 * 
	 * @var array
	 */
	protected $_logs = array() ;
	
	/**
	 * Connection configuration, with defaults.
	 * 
	 * @var array
	 */
	protected $_config = array(
		'index' => null,
		'type' => null,
		'log' => false
	) ;
	
	/**
	 * Current connection.
	 * 
	 * @var mixed
	 */
	protected $_connection ;
	
	/**
	 * Constructor.
	 * 
	 * @param array $config		[optionnal] Connection configuration.
	 */
	public function __construct(array $config = null, Simples_Factory $factory = null) {
		if (isset($config)) {
			$this->config($config) ;
		}
		
		if (isset($factory)) {
			$this->_factory = $factory ;
		} else {
			$this->_factory = new Simples_Factory() ;
		}
	}
	
	/**
	 * Create connection and configure it. 
	 * 
	 * @return \SimplesConnection	Current connection.
	 */
	abstract public function connect() ;
	
	/**
	 * Close the current connection.
	 * 
	 * @return \SSimples_Transport
	 */
	abstract public function disconnect() ;
	
	/**
	 * Call $url with requested $method (an optionnal $data). Return the response.
	 * 
	 * @param string $method	HTTP method
	 * @param string $path		Relative API call
	 * @param mixed	 $data		Optionnal data
	 * @return string			HTTP response to the call, not parsed
	 */
	abstract public function call($path = null, $method = 'GET', $data = null) ;
	
	/**
	 * Check if the instance is currently connected.
	 * 
	 * @return bool		Am I or not ?
	 */
	public function connected() {
		return isset($this->_connection) ;
	}
	
	/**
	 * Returns the current server status.
	 * 
	 * @return Simples_Request_Status
	 */
	public function status() {
		$args = func_get_args() ;
		return $this->__call('status', $args) ;
	}
	
	/**
	 * Returns stats
	 * 
	 * @return Simples_Request_Stats
	 */
	public function stats() {
		$args = func_get_args() ;
		return $this->__call('stats', $args) ;
	}
	
	/**
	 * Index an object
	 * 
	 * @return Simples_Request_Index
	 */
	public function index() {
		$args = func_get_args() ;
		return $this->__call('index', $args) ;
	}
	
	/**
	 * Get an object from its id
	 * 
	 * @return Simples_Request_Get
	 */
	public function get() {
		$args = func_get_args() ;
		return $this->__call('get', $args) ;
	}
	
	/**
	 * Delete something : an object, a type or an index
	 * 
	 * @return Simples_Request_Delete
	 */
	public function delete() {
		$args = func_get_args() ;
		return $this->__call('delete', $args) ;
	}
	
	/**
	 * Search API
	 * 
	 * @return Simples_Request_Search
	 */
	public function search() {
		$args = func_get_args() ;
		return $this->__call('search', $args) ;
	}
	
	/**
	 * Logs some data
	 * @param type $data
	 * @return \Simples_Transport 
	 */
	public function log($path, $method, $data = null) {
		if (is_array($data)) {
			$data = json_encode($data) ;
		}
		$this->_logs[] = array(
			'path' => (string) $path,
			'method' => (string) $method,
			'data' => $data
		);
		return $this ;
	}
	
	/**
	 * Returns the recorded logs.
	 * 
	 * @param	bool	$string	Should we return logs as a string ?
	 * @return	mixed			Logs : as an array or a string.
	 */
	public function logs($string = false) {
		if ($string) {
			$return = '' ;
			foreach($this->_logs as $log) {
				$return .= $log['method'] . ' : ' . $log['path'] ;
				if (!empty($log['data'])) {
					$return .= ' > ' . $log['data'] ;
				}
				$return .= "\n" ;
			}
			return $return ;
		}
		return $this->_logs ;
	}
	
	/**
	 * Here is the magic ! 
	 * 
	 * @param string	$request	Request alias
	 * @param array		$params		Params
	 * @return \Simples_Request 
	 */
	public function __call($request, $params) {
		$path = 'Request.' . $request ;
		if ($this->_factory->valid($path)) {
			// Automatically add index / type if defined
			$options = array(
				'index' => isset($this->_config['index']) ? $this->_config['index'] : null,
				'type' => isset($this->_config['type']) ? $this->_config['type'] : null,
			) ;
			
			if (isset($params[1])) {
				$options = $params[1] + $options ;
			}
			
			// Add request alias + transport instance
			$body = isset($params[0]) ? $params[0] : array() ;
			
			// Magic param
			if (is_scalar($body)) {
				$key = $this->_factory->defaultParam($path) ;
				if (!isset($key)) {
					throw new Simples_Transport_Exception('No default param defined for "' . $path . '". You have to give a full body.') ;
				}
				$body = array($key => $body) ;
			}
			
			return $this->_factory->request($request, $body, $options, $this) ;
		}
	}
}