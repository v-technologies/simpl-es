<?php

/**
 * Get.
 * 
 * @author Sébastien Charrier <scharrier@gmail.com>
 * @package	Simples
 * @subpackage Request
 */
class Simples_Request_Get extends Simples_Request {
	
	/**
	 * Call method.
	 * 
	 * @var string
	 */
	protected $_method = self::GET ;
	
	/**
	 * Id of the object we want to get.
	 * 
	 * @var string
	 */
	protected $_id ;
	
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
	public function __construct(Simples_Transport $transport = null, $index = null, $type = null, $id = null) {
		if (!isset($index)) {
			throw new Simples_Request_Exception('Index is required ($index is null)') ;
		}
		if (!isset($type)) {
			throw new Simples_Request_Exception('Type is required ($type is null)') ;
		}
		if (!isset($id)) {
			throw new Simples_Request_Exception('Id is required ($id is null)') ;
		}
		
		$this->index($index) ;
		$this->type($type) ;
		
		$this->_id = $id ;
		
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
		$path .= $this->_id . '/' ;
		
		return $path ;
	}
}