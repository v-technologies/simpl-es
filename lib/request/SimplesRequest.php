<?php

abstract class SimplesRequest extends SimpleBase {
	
	protected $_connection ;
	
	/**
	 * Base path for the request.
	 * 
	 * @var string
	 */
	protected $_path ;
	
	/**
	 * Constructor.
	 * 
	 * @param SimplesConnection $connection		Connection to use.
	 */
	public function __construct(SimplesConnection $connection = null) {
		if (isset($connection)) {
			$this->_connection = $connection ;
		}
	}
	
	public function execute() {
		return $this->_connection->call($this->_path) ;
	}
}