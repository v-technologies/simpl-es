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
	 * Request body.
	 * 
	 * @var array
	 */
	protected $_body = array(
		'index' => null,
		'type' => null
	) ;
	
	/**
	 * Current response
	 * 
	 * @var string 
	 */
	protected $_response ;
	
	/**
	 * Request required parameters. Has to be overriden in the requests.
	 * 
	 * @var array 
	 */
	protected $_required = array() ;
	
	
	/**
	 * Default param.
	 * 
	 * @var string
	 */
	protected $_default ;
	
	/**
	 * Method GET 
	 */
	const GET = 'GET' ;
	
	/**
	 * Method POST 
	 */
	const POST = 'POST' ;
	
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
	public function __construct($body = null, Simples_Transport $transport = null) {
		if (isset($body)) {
			$this->body($body) ;
		}
		
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
		$path = array() ;
		$index = $this->index() ;
		if ($index) {
			$path[] = trim($index,'/') ;
			$type = $this->type() ;
			if ($type) {
				$path[] = trim($type, '/') ;
			}
		}
		if ($this->_path) {
			$path[] = trim($this->_path, '/') ;
		}
		
		return '/' . implode('/', $path) . '/' ;
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
	 * Get the current index / indices
	 * 
	 * @param type $index
	 * @return \Simples_Request 
	 */
	public function index() {
		if (is_array($this->_body['index'])) {
			return implode(',', $this->_body['index']) ;
		}
		return $this->_body['index'] ;
	}
	
	/**
	 * Getter / setter : current type(s)
	 * 
	 * @param mixed		$type		Type (string) or types (array)
	 * @return \Simples_Request 
	 */
	public function type() {
		if (is_array($this->_body['type'])) {
			return implode(',', $this->_body['type']) ;
		}
		return $this->_body['type'] ;
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
			$response = $this->_client->call($this->path(), $this->_method, $this->to('json')) ;
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
	 * Getter / setter : body of the request.
	 * 
	 * @param array		$body		Setter : body
	 * @return \Simples_Request|array	Setter : $this . Getter : current body.
	 */
	public function body(array $body = null) {
		if (isset($body)) {
			foreach($this->_required as $required) {
				if (!isset($body[$required])) {
					throw new Simples_Request_Exception('Required param "' . $required . '" missing') ;
				}
			} 
			$this->_body = $body + $this->_body ;
			return $this ;
		}
		return $this->_body ;
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
			return $this->{$method}($this->body()) ;
		}
		
		throw new Simples_Request_Exception('Unsupported request transformation format : "' . $format . '"') ;
	}
	
	/**
	 * Json transformation
	 * 
	 * @return string	Request in json 
	 */
	protected function _toJson(array $body) {
		return json_encode($body) ;
	}
	
	/**
	 * Array transformation
	 * 
	 * @return array 
	 */
	protected function _toArray(array $body) {
		return $body ;
	}
}