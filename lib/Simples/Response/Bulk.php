<?php

/**
 * Bulk action response.
 * 
 * @author Sébastien Charrier <scharrier@gmail.com>
 * @package	Simples
 * @subpackage Response
 */
class Simples_Response_Bulk extends Simples_Response {
	
	/**
	 * Set override : check each bulk item.
	 * 
	 * @param array $data	Bulk data respose
	 * @return \Simples_Response_Bulk 
	 * @todo	Generate an exception containing all the previous exceptions (actually, stop on the first)
	 */
	public function set(array $data) {
		// Bulk response check
		if (isset($data['items'])) {
			foreach($data['items'] as $i => $action) {
				$response = $action[key($action)] ;
				$this->_check($response) ;
			}
		}
		$this->_data = $data ;
		return $this ;
	}
}