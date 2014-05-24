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
class Simples_Transport_Http extends Simples_Transport {
	
	/**
	 * Connection configuration, with defaults.
	 * 
	 * @var array
	 */
	protected $_config = array(
		'host' => '127.0.0.1',
		'port' => 9200,
		'protocol' => 'http',
		'timeout' => 1000,
		'check' => true,
		'index' => null,
		'type' => null
	) ;
	
	/**
	 * Current connection.
	 * 
	 * @var Simples_Transport
	 */
	protected $_connection ;
	
	/**
	 * Constructor.
	 * 
	 * @param array $config		[optionnal] Connection configuration.
	 */
	public function __construct(array $config = null, Simples_Factory $factory = null) {
		// Check : curl installed ?
		if (!extension_loaded('curl')) {
			throw new Simples_Transport_Exception('Curl is not installed (curl_init function doesn\'t exists).') ;
		}
		
		return parent::__construct($config, $factory) ;
	}
	
	/**
	 * Create the curl connection and configure it. 
	 * 
	 * @return \SimplesConnection	Current connection.
	 */
	public function connect() {
		$this->_connection = curl_init() ;
		curl_setopt($this->_connection, CURLOPT_PORT, $this->config('port')) ;
		curl_setopt($this->_connection, CURLOPT_CONNECTTIMEOUT, $this->config('timeout')) ;
		curl_setopt($this->_connection, CURLOPT_RETURNTRANSFER, 1) ;
		
		// Check if it's an ES server
		if ($this->config('check')) {
			$res = $this->call('/') ;
			if (!isset($res)) {
				throw new Simples_Transport_Exception('Invalid JSON or empty response') ;
			}
			if (!isset($res['body']['status']) || (isset($res['body']['status']) && $res['body']['status'] !== 200)) {
				throw new Simples_Transport_Exception('Bad response from ElasticSearch server. Are you sure you\'re calling the good guy ?') ;
			}
		}
		
		return $this ;
	}
	
	/**
	 * Close the current connection.
	 * 
	 * @return \SSimples_Transport
	 */
	public function disconnect() {
		curl_close($this->_connection);
		$this->_connection = null ;
		
		return $this ;
	}
		
	/**
	 * Generates a full url.
	 * 
	 * @param string	$call	Api call
	 * @return string			Full url 
	 */
	public function url($call = null) {
		$url = $this->config('protocol') . '://' . $this->config('host') . '/' ;
		
		if (isset($call)) {
			$url .= ltrim($call, '/') ;
		}
		
		return $url ;
	}
	
	/**
	 * Call $url with requested $method (an optionnal $data). Return the response.
	 * 
	 * @param string $method	HTTP method
	 * @param string $url		Relative API call
	 * @param mixed	 $data		Optionnal data
	 * @return string			HTTP response to the call, not parsed
	 */
	public function call($url = null, $method = 'GET', $data = null) {		
		// Autoconnect
		if (!$this->connected()) {
			$this->connect() ;
		}
		
		// Action log
		if ($this->config('log')) {
			$this->log($url, $method, $data) ;
		}
		
		curl_setopt($this->_connection, CURLOPT_CUSTOMREQUEST, strtoupper($method));
		curl_setopt($this->_connection, CURLOPT_URL, $this->url($url)) ;
		curl_setopt($this->_connection, CURLOPT_FORBID_REUSE, 1) ;
		
		
		if (is_array($data) && !empty($data)) {
			$data = json_encode($data) ;
		}
		
		if (!empty($data)) {
			curl_setopt($this->_connection, CURLOPT_POSTFIELDS, $data);
		} else {
			curl_setopt($this->_connection, CURLOPT_POSTFIELDS, '');
		}

		$response = curl_exec($this->_connection);
		
		if ($response === false) {
			throw new Simples_Transport_Exception(
				'Error during the request (' . curl_errno($this->_connection) . ') : ' .
				curl_error($this->_connection)
			);
		}
		if (!strlen($response)) {
			throw new Simples_Transport_Exception('The ES server returned an empty response.') ;
		}
		
		$return = array();
		$return['body'] = json_decode($response, true) ;
		$return['http'] = curl_getinfo($this->_connection);
		
		if ($return === null) {
			throw new Simples_Transport_Exception('Cannot JSON decode the response : ' . $response) ;
		}
		
		return $return ;
	}
}