<?php

/**
 * Search query builder.
 * 
 * @author Sébastien Charrier <scharrier@gmail.com>
 * @package	Simples
 * @subpackage Request.Search
 */
class Simples_Request_Search_Builder_Query extends Simples_Request_Search_Builder_Criteria {

	/**
	 * Returns a new Query criteria.
	 * 
	 * @param type $criteria		Criteria definition.
	 * @return \Simples_Request_Search_Criteria_Filter 
	 */
	protected function _criteria($criteria, array $options = array()) {
		return new Simples_Request_Search_Criteria_Query($criteria, $options) ;
	}
}