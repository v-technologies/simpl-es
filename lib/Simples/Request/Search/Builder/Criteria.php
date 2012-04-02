<?php

/**
 * Search query builder.
 * 
 * @author Sébastien Charrier <scharrier@gmail.com>
 * @package	Simples
 * @subpackage Request.Search
 */
abstract class Simples_Request_Search_Builder_Criteria extends Simples_Request_Search_Builder {
	
	const MUST = 'must' ;
	
	const SHOULD = 'should' ;
	
	const NOT = 'must_not' ;
	
	/**
	 * Keep all the criteria.
	 * 
	 * @var array
	 */
	protected $_criteria = array(
		self::MUST => array(),
		self::SHOULD => array(),
		self::NOT => array()
	) ;
	
	/**
	 * Current working clause.
	 * 
	 * @var string
	 */
	protected $_clause = self::MUST ;
	
	/**
	 * Criteria type.
	 * 
	 * @var string
	 */
	protected $_criteriaType ;
	
	/**
	 * Generate a criteria. 
	 */
	abstract protected function _criteria($criteria) ;
	
	/**
	 * Add a criteria to the current query.
	 * 
	 * @param mixed		$criteria	Criteria to add.
	 * @return mixed				Current query instance or current request instance (fluid calls)
	 */
	public function add($criteria, array $options = array()) {
		$criteria = $this->_criteria($criteria, $options) ;
		$last = $this->_last() ;
		if ($last) {
			if ($last->mergeable($criteria)) {
				$last->merge($criteria) ;
				return $this ;
			}
		}
		$this->_criteria[$this->_clause][] = $criteria ;
		return $this->_fluid() ;
	}

	/**
	 * Get the last clause added to the builder.
	 * 
	 * @return mixed Criteria or null if nothing.
	 */
	protected function _last() {
		$count = count($this->_criteria[$this->_clause]) ;
		if ($count) {
			$last = $this->_criteria[$this->_clause][$count - 1] ;
			return $last ;
		}
		return null ;
	}
	
	/**
	 * Add a "field" criteria to the current query.
	 * 
	 * @param mixed		$field_name	Field name
	 * @return mixed				Current query instance or current request instance (fluid calls)
	 */
	public function field($field_name) {
		return $this->add(array('in' => $field_name)) ;
	}
	
	/**
	 * Add a "fields" criteria to the current query.
	 * 
	 * @param mixed		$fields_names	Fields names
	 * @return mixed					Current query instance or current request instance (fluid calls)
	 */
	public function fields(array $fields_names) {
		return $this->add(array('in' => $fields_names)) ;
	}
	
	/**
	 * Add a "query" criteria to the current query.
	 * 
	 * @param mixed		$query		Query (value you are looking for)
	 * @return mixed				Current query instance or current request instance (fluid calls)
	 */
	public function match($query) {
		return $this->add(array('value' => $query)) ;
	}
	
	/**
	 * Add a "in" criteria to the current query.
	 * 
	 * @param mixed		$in		Search scope (fields/field)
	 * @return mixed			Current query instance or current request instance (fluid calls)
	 */
	public function in($in) {
		return $this->add(array('in' => $in)) ;
	}
	
	/**
	 * All following clausses will be integrated in a bool/must clause.
	 * 
	 * @return mixed	Current query instance or current request instance (fluid calls) 
	 */
	public function must() {
		$this->_clause = self::MUST ;
		return $this->_fluid() ;
	}
	
	/**
	 * All following clausses will be integrated in a bool/should clause.
	 * 
	 * @return mixed	Current query instance or current request instance (fluid calls) 
	 */
	public function should() {
		$this->_clause = self::SHOULD ;
		return $this->_fluid() ;
	}
	
	/**
	 * All following clausses will be integrated in a bool/must_not clause.
	 * 
	 * @return mixed	Current query instance or current request instance (fluid calls) 
	 */
	public function not() {
		$this->_clause = self::NOT ;
		return $this->_fluid() ;
	}
	
	/**
	 * Prepare data for ES server. Construct a bool request if multiple criteria.
	 * 
	 * @return array	Data prepared for json_encode()
	 */
	protected function _data(array $options = array()) {
		$return = array() ;
		if ($this->count()) {
			if ($this->count(self::MUST) === 1 && !$this->count(self::NOT, self::SHOULD))  {
				$return = $this->_criteria[self::MUST][0]->to('array') ;
			} else {
				foreach($this->_criteria as $clause => $value) {
					foreach($value as $_criteria) {
						$return[$clause][] = $_criteria->to('array') ;
					}
				}
				$return = array('bool' => $return) ;
			}
		} else {
			$return = $this->_criteria(null) ;
			$return = $return->to('array') ;
		}
		return $return ;
	}
	
	/**
	 * Count the number of element for alls the criteria types, or for criterias asked in
	 * param.
	 * 
	 * @return int		Count result.
	 */
	public function count() {
		$test = func_get_args() ;
		if (empty($test)) {
			$test = array_keys($this->_criteria) ;
		}
		
		$total = 0 ;
		foreach($test as $clause) {
			$total += count($this->_criteria[$clause]) ;
		}
		
		return $total ;
	}

	/**
	 * Return the last added clause if exists. Otherwise, this.
	 * 
	 * @return mixed Criteria or this.
	 */
	public function instance() {
		$last = $this->_last() ;
		if ($last) {
			return $last ;
		}

		return $this ;
	}
}