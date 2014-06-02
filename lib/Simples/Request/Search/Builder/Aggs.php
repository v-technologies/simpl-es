<?php

/**
 * Aggregates builder.
 *
 * @author Christophe Sicard <sicard.christophe@gmail.com>
 * @package	Simples
 * @subpackage Request.Search
 */
class Simples_Request_Search_Builder_Aggs extends Simples_Request_Search_Builder {

	/**
	 * Current aggregates.
	 *
	 * @var array
	 */
	protected $_aggregates = array();

	/**
	 * Add a criteria to the current query.
	 *
	 * @param  mixed	$criteria	Criteria to add.
	 * @param  array	$options	Options.
	 * @return mixed				Current query instance or current request instance (fluid calls)
	 */
	public function add($definition, array $options = array()) {
		$aggregate = new Simples_Request_Search_Criteria_Agg($definition, $options, $this->_fluid());
		if (count($this->_aggregates)) {
			$last = $this->_last();
			if ($last->mergeable($aggregate)) {
				$last->merge($aggregate);
				return $this->_fluid();
			}
		}
		$this->_aggregates[$aggregate->name()] = $aggregate;
		return $this->_fluid();
	}

	/**
	 * Count the number of current aggregates.
	 *
	 * @return int
	 */
	public function count() {
		return count($this->_aggregates);
	}

	/**
	 * Magic call : chain with subobjects.
	 *
	 * @param string	$name		Method name
	 * @param array		$args		Arguments
	 * @return \Simples_Request_Search
	 */
	public function __call($name, $args) {
		call_user_func_array(array($this->_last(), $name), $args);
		return $this->_fluid();
	}

	/**
	 * Returns the last called.
	 *
	 * @return type
	 */
	protected function _last() {
		end($this->_aggregates);
		$last = $this->_aggregates[key($this->_aggregates)];
		reset($this->_aggregates);

		return $last;
	}

	/**
	 * Prepare data.
	 *
	 * @return array
	 */
	protected function _data(array $options = array()) {
		$return = array();

		foreach($this->_aggregates as $aggregate) {
			$return = array_merge($return, $aggregate->to('array'));
		}

		return $return;
	}
}
