<?php

/**
 * Search filter builder.
 * 
 * @author Sébastien Charrier <scharrier@gmail.com>
 * @package	Simples
 * @subpackage Request.Search
 */
class Simples_Request_Search_Builder_Filters extends Simples_Request_Search_Builder_Criteria {
	
	/**
	 * Returns a new Filter criteria.
	 * 
	 * @param type $criteria		Criteria definition.
	 * @return \Simples_Request_Search_Criteria_Filter 
	 */
	protected function _criteria($criteria) {
		return new Simples_Request_Search_Criteria_Filter($criteria) ;
	}
}