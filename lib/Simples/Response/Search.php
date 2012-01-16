<?php

/**
 * Specific search response.
 * 
 * @author Sébastien Charrier <scharrier@gmail.com>
 * @package	Simples
 * @subpackage Response
 */
class Simples_Response_Search extends Simples_Response {
	
	/**
	 * Response config
	 * 
	 * @var array
	 */
	protected $_config = array(
		'highlight' => Simples_Request_Search::HIGHLIGHT_DO_NOTHING
	) ;
	
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
	 * Returns a traversable hits object. You can "foreach" directly on a query
	 * hits.
	 * 
	 * @return \Simples_Document_Set|null		A set of results.
	 */
	public function hits() {
		if (isset($this->_data['hits']['hits'])) {
			return new Simples_Document_Set($this->_data['hits']['hits']) ;
		}
		return null ;
	}
	
	/**
	 * Re-inject highlighted results in the main results.
	 * 
	 * @param array $data	Response data.
	 * @return array		Response data with highlights. 
	 */
	protected function _replaceHighlights(array &$data) {
		if (isset($data['hits']['hits'])) {
			foreach($data['hits']['hits'] as $i => $record) {
				if (isset($record['_source']) && isset($record['highlight'])) {
					foreach($record['highlight'] as $path => $value) {
						$data['hits']['hits'][$i]['_source'] = $this->_insert($data['hits']['hits'][$i]['_source'], $path, $value) ;
					}
				}
			}
		}
		
		return $data ;
	}
	
	/**
	 * Insert the new value in the given path.
	 * 
	 * Note that this code was extracted from the Set::insert() method, part of the CakePHP project.
	 * Reference : https://github.com/cakephp/cakephp/blob/1.3/cake/libs/set.php
	 * 
	 * @param type $data
	 * @param type $path
	 * @param type $value
	 * @return type 
	 */
	protected function _insert($data, $path, $value) {
		if (!is_array($path)) {
			$path = explode('.', $path);
		}
		
		$_list =& $data;

		foreach ($path as $i => $key) {
			if (is_numeric($key) && intval($key) > 0 || $key === '0') {
				$key = intval($key);
			}
			if ($i === count($path) - 1) {
				// Special case : base value is a string and ES gives us an array with only one value.
				if (is_array($value) && count($value) === 1 && isset($value[0]) && isset($_list[$key]) && is_string($_list[$key])) {
					$value = $value[0] ;
				}
				$_list[$key] = $value;
			} else {
				if (!isset($_list[$key])) {
					$_list[$key] = array();
				}
				$_list =& $_list[$key];
			}
		}
		return $data;
	}
}