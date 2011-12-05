<?php
require_once(SIMPLES_ROOT . 'lib' . DIRECTORY_SEPARATOR . 'connection' . DIRECTORY_SEPARATOR . 'SimplesConnectionException.php') ;

/**
 * ElasticSearch connection class : connect to a server, check its configuration, and exchange requests
 * and responses with this server.
 * 
 * Actually only HTTP exchange are supported.
 * 
 * @author Sébastien Charrier <scharrier@gmail.com>
 * @package	Simples
 * @subpackage lib.connection 
 */
class SimplesConnection extends SimplesBase {
	
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
		'check' => true
	) ;
	
	/**
	 * Current connection.
	 * 
	 * @var SimplesConnection
	 */
	protected $_connection ;
	
	/**
	 * Constructor.
	 * 
	 * @param array $config		[optionnal] Connection configuration.
	 */
	public function __construct(array $config = null) {
		// Check : curl installed ?
		if (!extension_loaded('curl')) {
			throw new SimplesConnectionException('Curl is not installed (curl_init function doesn\'t exists).') ;
		}
		
		if (isset($config)) {
			$this->config($config) ;
		}
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
			$res = json_decode($this->call('/_status'), true) ;
			if (!isset($res)) {
				throw new SimplesConnectionException('Invalid JSON or empty response') ;
			}
			if (!isset($res['ok']) || (isset($res['ok']) && $res['ok'] !== true)) {
				throw new SimplesConnectionException('Bad response from ElasticSearch server. Are you sure you\'re calling the good guy ?') ;
			}
		}
		
		return $this ;
	}
	
	/**
	 * Close the current connection.
	 * 
	 * @return \SimplesConnection 
	 */
	public function disconnect() {
		curl_close($this->_connection);
		$this->_connection = null ;
		
		return $this ;
	}
	
	/**
	 * Check if the instance is currently connected.
	 * 
	 * @return bool		Am I or not ?
	 */
	public function connected() {
		return isset($this->_connection) ;
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
	public function call($url, $method = 'GET', $data = null) {
		// Autoconnect
		if (!$this->connected()) {
			$this->connect() ;
		}
		
		curl_setopt($this->_connection, CURLOPT_CUSTOMREQUEST, strtoupper($method));
		curl_setopt($this->_connection, CURLOPT_URL, $this->url($url)) ;
		
		if (is_array($data)) {
			$data = json_encode($data) ;
		}
		if (isset($data)) {
			curl_setopt($this->_connection, CURLOPT_POSTFIELDS, $data);
		}
		
		$return = curl_exec($this->_connection);
		
		if ($return === false) {
			throw new SimplesConnectionException(
				'Error during the request (' . curl_errno($this->_connection) . ') : ' .
				curl_error($this->_connection)
			);
		}
		
		return $return ;
	}
}