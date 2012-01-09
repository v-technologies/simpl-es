<?php

/**
 * Search. Oh yea, here it is.
 * 
 * @author Sébastien Charrier <scharrier@gmail.com>
 * @package	Simples
 * @subpackage Request
 */
class Simples_Request_Search extends Simples_Request {
	
	/**
	 * Do nothing with highlights 
	 */
	const HIGHLIGHT_DO_NOTHING = false ;
	
	/**
	 * Replace highlighted values in the source 
	 */
	const HIGHLIGHT_REPLACE = true ;
	
	/**
	 * Definition
	 * 
	 * @var array
	 */
	protected $_definition = array(
		'method' => self::POST,
		'path' => '_search',
		'magic' => 'query',
	) ;
	
	
	/**
	 * Default body values.
	 * 
	 * @var array
	 */
	protected $_body = array(
		'index' => null,
		'type' => null,
		'query' => null,
		'filter' => null,
		'facets' => null,
		'from' => null,
		'size' => null,
		'sort' => null,
		'highlight' => null,
		'fields' => null,
		'script_fields' => null,
		'explain' => false,
		'version' => null,
		'min_score' => null
	);
	
	/**
	 * Current subobject working.
	 * 
	 * @var string
	 */
	protected $_current = 'query' ;
	
	/**
	 * Query builder.
	 * 
	 * @var Simples_Request_Search_Builder_Query
	 */
	protected $_query ;
	
	/**
	 * Filter builder.
	 * 
	 * @var Simples_Request_Search_Builder_Filter
	 */
	protected $_filters ;
	
	/**
	 * Facet builder.
	 * 
	 * @var Simples_Request_Search_Builder_Facet
	 */
	protected $_facet ;
	
	/**
	 * Request options.
	 * 
	 * @var array
	 */
	protected $_options = array(
		'index' => null,
		'type' => null,
		'highlight' => Simples_Request_Search::HIGHLIGHT_DO_NOTHING
	) ;
	
	/**
	 * Constructor.
	 * 
	 * @param mixed		$body				Request body
	 * @param array		$options			Array of options
	 * @param Simples_Transport $transport	ES client instance
	 */
	public function __construct($body = null, $options = null, Simples_Transport $transport = null) {
		// Builders
		$this->_query = new Simples_Request_Search_Builder_Query(null, $this) ;
		$this->_filters = new Simples_Request_Search_Builder_Filters(null, $this) ;
		$this->_facets = new Simples_Request_Search_Builder_Facets(null, $this) ;
		
		// Simple query_string search : give it to builder.
		if (isset($body['query']) && is_string($body['query'])) {
			$this->_query->add($body['query']) ;
			unset($body['query']) ;
		}
		
		parent::__construct($body, $options, $transport);
	}
	
	/**
	 * Body without null values.
	 * 
	 * @param array $body
	 * @return type 
	 */
	public function body(array $body = null) {
		if (isset($body)) {
			return parent::body($body) ;
		}
		
		$body = parent::body() ;
		
		if (empty($body['query'])) {
			// Force a match_all
			$body['query'] = $this->_query->to('array') ;
		}
		
		if (empty($body['filter']) && $this->_filters->count()) {
			$body['filter'] = $this->_filters->to('array') ;
		}
		
		if (empty($body['facets']) && $this->_facets->count()) {
			$body['facets'] = $this->_facets->to('array') ;
		}
		
		// Reformatage des highlights
		if (!empty($body['highlight']['fields'])) {
			$highlight = array() ;
			foreach($body['highlight']['fields'] as $key => $value) {
				if (is_numeric($key)) {
					$highlight[$value] = new stdClass() ;
				} else {
					$highlight[$key] = $value ;
				}
			}
			$body['highlight']['fields'] = $highlight ;
		}
		
		$body = array_filter($body) ;
		
		return $body ;
	}
	
	/**
	 * Query mode switcher.
	 * 
	 * @param mixed		$query			Setter : Query definition.
	 * @return \Simples_Request_Search	This instance
	 */
	public function query($query = null) {
		// Save current subobject
		$this->_current = 'query' ;
		
		if (isset($query)) {
			$this->_query->add($query) ;
		}
		return $this ;
	}
	
	/**
	 * Filter mode switcher.
	 * 
	 * @param mixed		$filter			Setter : Query definition.
	 * @return \Simples_Request_Search	This instance
	 */
	public function filter($filter = null) {
		// Save current subobject
		$this->_current = 'filters' ;
		
		if (isset($filter)) {
			$this->_filters->add($filter) ;
		}
		return $this ;
	}
	
	/**
	 * Add a facet.
	 * 
	 * @param mixed		$facet			Setter : Query definition.
	 * @return \Simples_Request_Search	This instance
	 */
	public function facet($facet , $options = null) {
		// Save current subobject
		$this->_current = 'facets' ;
		
		$this->_facets->add($facet, $options) ;
		
		return $this ;
	}
	
	/**
	 * Add multiples field queries one time. It's a simplified call wich permit to give this kind of array :
	 * $request->queries(array(
	 *		'field' => 'value',
	 *		'other_field' => array('value 1', 'value 2')
	 * ));
	 * 
	 * @param array $queries			List of criteries. Field name in key, search in value.
	 * @return \Simples_Request_Search	This instance.
	 */
	public function queries(array $queries) {
		foreach($queries as $in => $match) {
			$this->_query->add(array('query' => $match, 'in' => $in)) ;
		}
		return $this ;
	}
	
	/**
	 * Add multiples field queries one time. It's a simplified call wich permit to give this kind of array :
	 * $request->queries(array(
	 *		'field' => 'value',
	 *		'other_field' => array('value 1', 'value 2')
	 * ));
	 * 
	 * @param array $filters			List of criteries. Field name in key, search in value.
	 * @return \Simples_Request_Search	This instance.
	 */
	public function filters(array $filters) {
		foreach($filters as $in => $match) {
			$this->_filters->add(array('query' => $match, 'in' => $in)) ;
		}
		return $this ;
	}
	
	/**
	 * Add multiples facets one time. Support simple calls or full definitions calls :
	 * $request->facets(array(
	 *		'field_1',
	 *		'field_2' => array('order' => 'term)
	 * )) ;
	 * 
	 * @param array $facets				Facets definitions.
	 * @return \Simples_Request_Search 
	 */
	public function facets(array $facets) {
		foreach($facets as $key => $value) {
			if (!is_numeric($key)) {
				if (!is_array($value)) {
					$value = array('in' => $value) ;
				}
				$value = array('name' => $key) + $value ;
			}
			$this->_facets->add($value) ;
		}
		return $this ;
	}
	
	/**
	 * Set the from param.
	 * 
	 * @param int	$from		From value
	 * @return \Simples_Request_Search 
	 */
	public function from($from) {
		$this->_body['from'] = $from ;
		return $this ;
	}
	
	/**
	 * Set the size.
	 * 
	 * @param int	$size		Size value
	 * @return \Simples_Request_Search 
	 */
	public function size($size) {
		$this->_body['size'] = $size;
		return $this ;
	}
	
	/**
	 * Set the sort param.
	 * 
	 * @param string	$sort	Sort value
	 * @return \Simples_Request_Search 
	 */
	public function sort($sort) {
		$this->_body['sort'] = $sort;
		return $this ;
	}
	
	/**
	 * Set the highlight param.
	 * 
	 * @param string	$sort	Sort value
	 * @return \Simples_Request_Search 
	 */
	public function highlight($highlight) {
		$this->_body['highlight'] = $highlight;
		return $this ;
	}
	
	/**
	 * Set the script_fields param.
	 * 
	 * @param string	$fields	script_fields value
	 * @return \Simples_Request_Search 
	 */
	public function scriptFields($fields) {
		$this->_body['script_fields'] = $fields;
		return $this ;
	}
	
	/**
	 * Set the explain param.
	 * 
	 * @param bool	$explain	Explain value
	 * @return \Simples_Request_Search 
	 */
	public function explain($explain) {
		$this->_body['explain'] = $explain;
		return $this ;
	}
	
	/**
	 * Set the min_score param.
	 * 
	 * @param string	$sort	Sort value
	 * @return \Simples_Request_Search 
	 */
	public function minScore($min_score) {
		$this->_body['min_score'] = $min_score;
		return $this ;
	}
	
	/**
	 * Set the fields param.
	 * 
	 * @param string	$sort	Sort value
	 * @return \Simples_Request_Search 
	 */
	public function fields($fields) {
		$this->_body['fields'] = $fields;
		return $this ;
	}
	
	/**
	 * Magic call : chain with subobjects.
	 * 
	 * @param string	$name		Method name
	 * @param array		$args		Arguments
	 * @return \Simples_Request_Search 
	 */
	public function __call($name, $args) {
		$object = '_' . $this->_current ;
		call_user_func_array(array($this->{$object}, $name), $args) ;
		return $this ;
	}
	
	/**
	 * Specific response object.
	 * 
	 * @param array		$data		Search request results.
	 * @return \Simples_Response_Search 
	 */
	protected function _response($data) {
		return new Simples_Response_Search($data, $this->options()) ;
	}
}