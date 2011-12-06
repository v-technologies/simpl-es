<?php

/**
 * Static calls. For simple usages of Simples. 
 * 
 * This utility class can be used if you don't want to maintain your client
 * persistent. It gives you the ability to connect/disconnect/request your 
 * ES server without any configuration / development.
 * 
 * Example of usage :
 * Simples::connect(array('host' => 'es.vtech.fr')) ;
 * echo Simples::current()->search('charly')->limit(5)->total ;  // Where's charly ?
 * Simples::disconnect() ;
 * 
 * @author Sébastien Charrier <scharrier@gmail.com>
 * @package	Simples
 */
class Simples {
	
	/**
	 * Factory.
	 * 
	 * @var \Simples_Factory
	 */
	static protected $_factory ;
	
	/**
	 * Transport client.
	 * 
	 * @var \Simples_Transport
	 */
	static protected $_client ;
	
	/**
	 * Connect your Simples to your ES instance.
	 * 
	 * @param array		$config		[optionnal] Configuration
	 * @return \Simple_Transport	Connection client instance 
	 */
	public function connect(array $config = array()) {
		if (!self::connected()) {
			self::_client($config)->connect() ;
		}
		
		return self::_client() ;
	}
	
	/**
	 * Check if you're connected, now.
	 * 
	 * @return bool
	 */
	public function connected() {
		return isset(self::$_client) && self::$_client->connected() ;
	}
	
	/**
	 * Disconnect from the ES instance. 
	 */
	public function disconnect() {
		if (self::connected()) {
			self::_client()->disconnect() ;
		}
	}
	
	/**
	 * Returns the current client.
	 * 
	 * @return \Simple_Transport
	 */
	public function current() {
		return self::_client() ;
	}
	
	/**
	 * Returns the current client. If it doesn't exists, generates it.
	 * 
	 * @param array		$config		[optionnal] Client configuration
	 * @return \Simples_Client 
	 */
	protected function _client(array $config = array()) {
		if (!isset(self::$_client)) {
			$driver = 'http' ;
			if (isset($config['driver'])) {
				$driver = $config['driver'] ;
			}
			self::$_client = self::_factory()->transport($driver, $config) ;
		}
		
		return self::$_client ; 
	}
	
	/**
	 * Gets the current factory (and generates it if necessary).
	 * 
	 * @return \Simple_Factory
	 */
	protected function _factory() {
		if (!isset(self::$_factory)) {
			self::$_factory = new Simples_Factory() ;
		}
		return self::$_factory ;
	}
}