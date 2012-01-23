<?php

/**
 * Set of Elasticsearch documents.
 * 
 * @author Sébastien Charrier <scharrier@gmail.com>
 * @package	Simples
 * @subpackage Document
 */
class Simples_Document_Set extends Simples_Base implements IteratorAggregate, Countable {
	
	/**
	 * Set data.
	 * 
	 * @var array
	 */
	protected $_data ;
	
	/**
	 * Constructor.
	 * 
	 * @param array $set	Set of documents
	 */
	public function __construct(array $set = null) {
		if (isset($set)) {
			$this->set($set) ;
		}
	}
	
	/**
	 * Sets the documents.
	 * 
	 * @param array $set		Set of documents.
	 * @throws Simples_Document_Exception 
	 */
	public function set(array $set) {
		if (!self::check($set)) {
			throw new Simples_Document_Exception('$set is not a valid Simples_Document_Set set of documents') ;
		}
		foreach($set as $document) {
			$this->_data[] = new Simples_Document($document) ;
		}
		
		return $this ;
	}
	
	/**
	 * IteratorAggregate implementation.
	 * 
	 * @return \ArrayIterator 
	 */
	public function getIterator() {
		return new ArrayIterator($this->_data) ;
	}
	
	/**
	 * Countable implementation : counts the documents.
	 * 
	 * @return int	Docs count.
	 */
	public function count() {
		return count($this->_data) ;
	}
	
	/**
	 * Static callable : check if data can be a Simples_Document_Set.
	 * 
	 * @param mixed		$data		Data to test.
	 * @return boolean
	 */
	static public function check($data) {
		if (is_string($data)) {
			return false ;
		}
		foreach($data as $key => $value) {
			if (!is_numeric($key)) {
				return false ;
			}
			if (!is_array($value)) {
				return false ;
			}
		}
		return true ;
	}
	
	/**
	 * Array transformation.
	 * 
	 * @return array	Array structured docs.
	 */
	protected function _toArray($data, array $options = array()) {
		$return = array() ;
		foreach($data as $doc) {
			$return[] = $doc->to('array') ;
		}
		return $return ;
	}
	
	/**
	 * Json transformation.
	 * 
	 * @return string	Json structured docs.
	 */
	protected function _toJson($data, array $options = array()) {
		$return = array() ;
		foreach($data as $doc) {
			$return[] = $doc->to('array') ;
		}
		return json_encode($return);
	}
}