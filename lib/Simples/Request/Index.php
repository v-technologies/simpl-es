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
	 * Bulk action name.
	 *
	 * @var string
	 */
	protected $_action = 'index' ;

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
			'params' => array('refresh','parent')
		)
	) ;

	/**
	 * Request options :
	 * - index (string) : index name
	 * - type (string) : index type
	 * - id (mixed) : object id, when indexing only one object
	 * - refresh (bool) : should we wait the index refresh before continuing ?
	 * - clean (bool) : should we clean the records before indexing ?
	 * - cast (array) : type casting for specific keys, when cleaning
	 *
	 * @var array
	 */
	protected $_options = array(
		'index' => null,
		'type' => null,
		'id' => null,
		'refresh' => null,
		'parent' => null,
		'clean' => false,
		'cast' => array()
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
			} elseif ($this->_body instanceof Simples_Document) {
				if ($this->_body->id) {
					$path->directory($this->_body->id) ;
				} elseif ($this->_body->properties() && $this->_body->properties()->id) {
					$path->directory($this->_body->properties()->id) ;
				}
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
				$this->_body = $body ;
			} elseif ($body instanceof Simples_Document_Set || Simples_Document_Set::check($body)) {
				if (!$body instanceof Simples_Document_Set) {
					 $body = new Simples_Document_Set($body) ;
				 }
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
					$this->_action => array(
						'_index' => $this->_options['index'],
						'_type' => $this->_options['type']
					)
				) ;
				if (isset($document->id)) {
					// Document without properties, but we specified an id
					$action[$this->_action]['_id'] = $document->id ;
				} elseif ($document->properties() && $document->properties()->id) {
					// Document with properties (directly from ES)
					$action[$this->_action]['_id'] = $document->properties()->id ;
				}

				$doc_content = $this->_jsonDoc($document, array('source' => false)) ;
				if (empty($doc_content)) {
					throw new Simples_Document_Exception('Bulk ' . $this->_action . ' error : empty document in documents set') ;
				}
				$json .= json_encode($action) . "\n" ;
				$json .= $doc_content . "\n" ;
			}
		} else {
			if (!empty($data)) {
				$json = $this->_jsonDoc($data) ;
			}
		}
		return $json ;
	}

	/**
	 * Returns the doc in json (here to be override in Simples_Request_Uopdate)
	 *
	 * @param  Simples_Document $document Doc to index
	 * @return string                     Json string
	 */
	protected function _jsonDoc(Simples_Document $document, array $options = array()) {
		return $document->to('json', $options + $this->_options) ;
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
