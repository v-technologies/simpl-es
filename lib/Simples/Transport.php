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
	 * Connection configuration, with defaults.
	 * 
	 * @var array
	 */
	protected $_config = array() ;
	
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
	public function __construct(Simples_Factory $factory = null, array $config = null) {
		if (isset($factory)) {
			$this->_factory = $factory ;
		} else {
			$this->_factory = new Simples_Factory() ;
		}
		
		if (isset($config)) {
			$this->config($config) ;
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
	
	public function __call($request, $params) {
		if ($this->_factory->valid('Request.' . $request)) {
			// Add request alias + transport instance
			$params = array_merge(array($request, $this), $params) ;
			return call_user_func_array(array($this->_factory, 'request'), $params) ;
		}
	}
}