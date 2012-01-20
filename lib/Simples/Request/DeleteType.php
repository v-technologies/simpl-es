<?php

/**
 * Delete a type.
 * 
 * @author Sébastien Charrier <scharrier@gmail.com>
 * @package	Simples
 * @subpackage Request
 */
class Simples_Request_DeleteType extends Simples_Request {
	
	/**
	 * Definition
	 * 
	 * @var array
	 */
	protected $_definition = array(
		'method' => self::DELETE,
		'required' => array(
			'options' => array('index')
		),
		'magic' => 'type'
	) ;
}