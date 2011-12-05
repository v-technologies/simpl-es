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
	protected $_method ;
	
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
	public function __construct(SimplesTransport $transport = null) {
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
	
	public function client(Simples_Transport $client = null) {
		if (isset($client)) {
			$this->_client = $client ;
			return $this ;
		}
		
		return $this->_client ;
	}
	
	/**
	 * Execute the request and returns the response.
	 * 
	 * @return null 
	 */
	public function execute() {
		if (isset($this->_client)) {
			return $this->_client->call($this->_path, $this->_method) ;
		}
		
		return null ;
	}
}