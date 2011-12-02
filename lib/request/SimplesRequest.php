<?php

class SimplesRequest extends SimpleBase {
	
	protected $_connection ;
	
	public function __construct(SimplesConnection $connection = null) {
		if (isset($connection)) {
			$this->_connection = $connection ;
		}
	}
}