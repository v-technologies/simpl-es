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
		'refresh' => null
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
			if ($body instanceof Simples_Document) {
				$body = $body->to('array') ;
				return parent::body($body) ;
			} elseif ($body instanceof Simples_Document_Set || Simples_Document_Set::check($body)) {
				$this->_bulk = true ;
				
				 if (!$body instanceof Simples_Document_Set) {
					 $body = new Simples_Document_Set($body) ;
				 }
				 $this->_body = $body ;
				 return $this ;
			}
		}
		
		if ($this->bulk()) {
			return $this->_body ;
		}
		return parent::body($body) ;		
	}
	
	/**
	 * Test if the request is in bulk mode.
	 * 
	 * @return bool
	 */
	public function bulk() {
		return $this->_bulk ;
	}
	
	/**
	 * Overrides toArray processing for bulk index.
	 * 
	 * @return array
	 */
	protected function _toArray($data) {
		if ($data instanceof Simples_Document_Set) {
			$data = $data->to('array') ;
		}
		return $data ;
	}
	
	/**
	 * Overrides toJson processing for bulk index.
	 * 
	 * @return string
	 */
	protected function _toJson($data) {
		$json = '' ;
		if ($this->_bulk) {
			foreach($data as $document) {
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
				$json .= $document->to('json') . "\n" ;
			}
		} else {
			$json = json_encode($data) ;
		}
		return $json ;
	}
}