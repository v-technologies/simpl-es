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
	 * Definition
	 * 
	 * @var array
	 */
	protected $_definition = array(
		'method' => self::POST,
		'required' => array(
			'options' => array('index','type')
		),
		'inject' => array(
			'params' => array('refresh')
		)
	) ;
	
	/**
	 * Request options.
	 * 
	 * @var array
	 */
	protected $_options = array(
		'index' => null,
		'type' => null,
		'id' => null,
		'refresh' => null,
		'clean' => false
	) ;
	
	/**
	 * Are we in bulk mode ?
	 * 
	 * @var bool
	 */
	protected $_bulk = false ;
	
	/**
	 * Path : id management.
	 * 
	 * @return string	API path
	 */
	public function path() {
		if ($this->bulk()) {
			$path = new Simples_Path('_bulk') ;
			if ($this->definition()->inject('params')) {
				$path->params($this->params()) ;
			}
		} else {
			$path = parent::path() ;

			// Object id transmited : we had it to the url.
			if (isset($this->_options['id'])) {
				$path->directory($this->_options['id']) ;
			}
		}
		
		return $path ;
	}
	
	/**
	 * Overrides body for bulk indexing.
	 * 
	 * @param mixed		$body	Data to index.
	 * @return mixed			Data to index. 
	 */
	public function body($body = null) {
		if (isset($body)) {
			if ($body instanceof Simples_Document_Set || Simples_Document_Set::check($body)) {
				if (!$body instanceof Simples_Document_Set) {
					 $body = new Simples_Document_Set($body) ;
				 }
				 $this->_body = $body ;
			} elseif ($body instanceof Simples_Document) {
				$this->_body = $body ;
			} else {
				$this->_body = new Simples_Document($body) ;
			}
			return $this ;
		}
		
		if (isset($this->_body)) {
			return $this->_body ;
		}
		
		return array() ;
	}
	
	/**
	 * Test if the request is in bulk mode.
	 * 
	 * @return bool
	 */
	public function bulk() {
		return $this->_body instanceof Simples_Document_Set ;
	}
	
	/**
	 * Overrides toArray processing for bulk index.
	 * 
	 * @return array
	 */
	protected function _toArray($data, array $options = array()) {
		return $data->to('array', $this->_options) ; ;
	}
	
	/**
	 * Overrides toJson processing for bulk index.
	 * 
	 * @return string
	 */
	protected function _toJson($data, array $options = array()) {
		$json = '' ;
		if ($this->_body instanceof Simples_Document_Set) {
			$iterator = $data->getIterator() ;
			foreach($iterator as $document) {
				$action = array(
					'index' => array(
						'_index' => $this->_options['index'],
						'_type' => $this->_options['type']
					)
				) ;
				if (isset($document->properties->id)) {
					$action['index']['_id'] = $document->properties->id ;
				}
				$json .= json_encode($action) . "\n" ;
				$json .= $document->to('json', $this->_options) . "\n" ;
			}
		} else {
			if (!empty($data)) {
				$json = $data->to('json', $this->_options) ;
			}
		}
		return $json ;
	}
	
	/**
	 * Specific response object.
	 * 
	 * @param array		$data		Search request results.
	 * @return \Simples_Response_Search 
	 */
	protected function _response($data) {
		return new Simples_Response_Bulk($data, $this->options()) ;
	}
}