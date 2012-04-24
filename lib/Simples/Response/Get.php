<?php

/**
 * Specific get response.
 * 
 * @author Sébastien Charrier <scharrier@gmail.com>
 * @package	Simples
 * @subpackage Response
 */
class Simples_Response_Get extends Simples_Response {
	
	/**
	 * Constructor overriden : do highlight work.
	 * 
	 * @param array $data		Response data.
	 * @param array $config		Response options.
	 */
	public function __construct(array $data, array $config = null) {
		parent::__construct($data, $config);
		
		if ($this->config('highlight') === Simples_Request_Search::HIGHLIGHT_REPLACE)  {
			$this->set($this->_replaceHighlights($data)) ;
		}
	}

	/**
	 * Returns a Document instance.
	 * 
	 * @return Simples_Document Current object
	 */
	public function document() {
		if (isset($this->_data['_source'])) {
			return new Simples_Document($this->_data, array('source' => true)) ;
		}
		return null ;
	}

}