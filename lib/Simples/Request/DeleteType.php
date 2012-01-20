<?php

/**
 * Delete an index.
 * 
 * @author Sébastien Charrier <scharrier@gmail.com>
 * @package	Simples
 * @subpackage Request
 */
class Simples_Request_DeleteIndex extends Simples_Request_CreateIndex {
	
	/**
	 * Definition
	 * 
	 * @var array
	 */
	protected $_definition = array(
		'method' => self::DELETE,
		'magic' => 'index'
	) ;
}