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
	 * Definition
	 * 
	 * @var array
	 */
	protected $_definition = array(
		'method' => self::GET,
		'path' => '_status'
	) ;
}