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
	 * Index (or indices)
	 * 
	 * @var mixed	String or array
	 */
	protected $_index ;
	
	/**
	 * Current response
	 * 
	 * @var string 
	 */
	protected $_response ;
	
	/**
	 * Request properties
	 * 
	 * @var array 
	 */
	protected $_properties = array() ;
	
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
	public function __construct(Simples_Transport $transport = null, $properties = null) {
		if (isset($transport)) {
			$this->_client = $transport ;
		}
		if (isset($properties)) {
			$this->properties($properties) ;
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
	 * getter / setter : current index/indices.
	 * 
	 * @param type $index
	 * @return \Simples_Request 
	 */
	public function index($index = null) {
		if (isset($index)) {
			if (!is_array($index)) {
				$this->_index = array($index) ;
			} else {
				$this->_index = $index ;
			}
			return $this ;
		}
		return $this->_index ;
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
			$response = $this->_client->call($this->_path, $this->_method, $this->_toJson()) ;
		}

		$this->_response = new Simples_Response($response) ;
		
		return $this->_response ;
	}
	
	/**
	 * Check if the request has been executed.
	 * 
	 * @return bool
	 */
	public function executed() {
		return isset($this->_response) ;
	}
	
	/**
	 * Getter / setter : properties of the request.
	 * 
	 * @param array		$properties		Setter : properties
	 * @return \Simples_Request|array	Setter : $this . Getter : current properties.
	 */
	public function properties(array $properties = null) {
		if (isset($properties)) {
			$this->_properties = $properties + $this->_properties ;
			return $this ;
		}
		return $this->_properties ;
	}
	
	/**
	 * Here the magic happens ! You can directly call a response value from the request. If
	 * the request hasn't been executed, execute it and then asks your value to the response
	 * object.
	 * 
	 * Yea, you can call :
	 * $status->version->number
	 * 
	 * @param string	$name		Var name
	 * @return mixed 
	 */
	public function __get($name) {
		if (!$this->executed()) {
			$this->execute() ;
		}
		return $this->_response->get($name) ;
	}
	
	/**
	 * Wrapper for format transformation : gives the request in the asked
	 * format.
	 * 
	 * Actually supported : array, json
	 * 
	 * @param string	$format		Asked format
	 * @return mixed				Formated request 
	 */
	public function to($format) {
		$method =  '_to' . ucfirst($format) ;
		if (method_exists($this, $method)) {
			return $this->{$method}() ;
		}
		
		throw new Simples_Request_Exception('Unsupported request transformation format : "' . $format . '"') ;
	}
	
	/**
	 * Json transformation
	 * 
	 * @return string	Request in json 
	 */
	protected function _toJson() {
		return json_encode($this->_properties) ;
	}
	
	/**
	 * Array transformation
	 * 
	 * @return array 
	 */
	protected function _toArray() {
		return $this->_properties ;
	}
}