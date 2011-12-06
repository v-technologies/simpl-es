<?php

/**
 * Index.
 * 
 * Index an object in the index/type defined.
 * 
 * @author Sébastien Charrier <scharrier@gmail.com>
 * @package	Simples
 * @subpackage Request
 */
class Simples_Request_Index extends Simples_Request {
	
	/**
	 * Call method.
	 * 
	 * @var string
	 */
	protected $_method = self::POST ;
	
	/**
	 * Constructor : forces index and type, as you cannot index an object
	 * without giving this informations to ES.
	 * 
	 * @param Simples_Transport $transport
	 * @param type $index
	 * @param type $type
	 * @param type $data
	 * @throws Simples_Reques_Exception 
	 */
	public function __construct(Simples_Transport $transport = null, $index = null, $type = null, $data = null) {
		if (!isset($index)) {
			throw new Simples_Request_Exception('Index is required ($index is null)') ;
		}
		if (!isset($type)) {
			throw new Simples_Request_Exception('Type is required ($type is null)') ;
		}
		
		$this->index($index) ;
		$this->type($type) ;
		
		if (isset($data)) {
			$this->properties($data) ;
		}
		
		parent::__construct($transport);
	}
	
	/**
	 * Path : id management.
	 * 
	 * @return string	API path
	 */
	public function path() {
		$path = parent::path() ;
		
		// Object id transmited : we had it to the url.
		if (isset($this->_properties['_id'])) {
			$path .= $this->_properties['_id'] . '/' ;
		}
		
		return $path ;
	}
}