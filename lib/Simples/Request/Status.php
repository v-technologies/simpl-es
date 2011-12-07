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
	 * Default param.
	 * 
	 * @var string
	 */
	protected $_default = 'index' ;
}