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
abstract class Simples_Request extends Simples_Base {
	
	/**
	 * Transport instance.
	 * 
	 * @var SimplesTransport
	 */
	protected $_client ;
	
	/**
	 * Base path for the request.
	 * 
	 * @var string
	 */
	protected $_path ;
	
	/**
	 * Method.
	 * 
	 * @var string 
	 */
	protected $_method = self::GET ;
	
	/**
	 * Response class name
	 * 
	 * @var string 
	 */
	protected $_response ;
	
	/**
	 * Method GET 
	 */
	const GET = 'GET' ;
	
	/**
	 * Method PUT 
	 */
	const PUT = 'PUT' ;
	
	/**
	 * Method DELETE 
	 */
	const DELETE = 'DELETE' ;
	
	/**
	 * Constructor.
	 * 
	 * @param SimplesTransport $transport		Connection to use.
	 */
	public function __construct(Simples_Transport $transport = null) {
		if (isset($transport)) {
			$this->_client = $transport ;
		}
	}
	
	/**
	 * Returns the base path for the current request.
	 * 
	 * @return string 
	 */
	public function path() {
		return $this->_path ;
	}
	
	/**
	 * Returns the method for the current request.
	 * 
	 * @return string
	 */
	public function method() {
		return $this->_method ;
	}
	
	/**
	 * Transport client setter/getter : if $client is given, sets $this->_client. Else,
	 * returns the current $this->_client.
	 * 
	 * This permits chained calls :
	 * $request->client($client)->execute() ;
	 * 
	 * @param Simples_Transport $client		Setter mode : the client
	 * @return \Simples_Request				setter mode : current request. Getter mode : current client.
	 */
	public function client(Simples_Transport $client = null) {
		if (isset($client)) {
			$this->_client = $client ;
			return $this ;
		}
		
		return $this->_client ;
	}
	
	/**
	 * Executes the request and returns the response.
	 * 
	 * @return null 
	 */
	public function execute() {
		$response = array() ;
		
		if (isset($this->_client)) {
			$response = $this->_client->call($this->_path, $this->_method) ;
		}
		
		return new Simples_Response($response) ;
	}
}