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
	
	/**
	 * Overrides constructor : gives the ability to pass the index name in the first param.
	 * 
	 * @param array		$body				Request body.
	 * @param array		$options			Request options
	 * @param Simples_Transport $transport	Transport client
	 */
	public function __construct($body = null, $options = null, Simples_Transport $transport = null) {
		if (isset($body['type'])) {
			$options['type'] = $body['type'] ;
			unset($body['type']) ;
		}
		parent::__construct($body, $options, $transport);
	}
	
	/**
	 * Check now if we have an index.
	 * 
	 * @param mixed	 $body		Setter mode.
	 * @return array			Getter mode : the body.
	 * @throws Simples_Request_Exception 
	 */
	public function body($body = null) {
		if (!isset($body) && !isset($this->_options['type'])) {
			throw new Simples_Request_Exception('Empty key "type" : you should specify the type name !') ;
		}
		return parent::body($body) ;
	}
}