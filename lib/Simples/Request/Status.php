<?php

/**
 * Status (/_status)
 * 
 * Returns the cluster status.
 * 
 * @author Sébastien Charrier <scharrier@gmail.com>
 * @package	Simples
 */
class Simples_Request_Status extends Simples_Request {
	
	/**
	 * Path.
	 * 
	 * @var string
	 */
	protected $_path = '/_status' ;
	
	/**
	 * Constructor. 
	 * 
	 * @param Simples_Transport $transport		Client
	 * @param string			$index			Index name
	 */
	public function __construct(Simples_Transport $transport = null, $index = null) {
		if (isset($index)) {
			$this->index($index) ;
		}
		parent::__construct($transport);
	}
	
}