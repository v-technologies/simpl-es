<?php

use PHPUnit\Framework\TestCase;

class Simples_HttpTestCase extends TestCase {
	
	/**
	 * @var Simples_Transport_Http|null
	 */
	protected $client;

	/**
	 * @var array
	 */
	protected $defaultHttpConfig = [
		'host' => '127.0.0.1',
		'port' => 9200,
		'protocol' => 'http',
		'timeout' => 1000,
		'check' => true,
		'index' => null,
		'type' => null
	]; 
	
	/**
	 * Redefine http config from env var
	 * given by phpunit.xml
	 */
	protected function setUp() : void {
		foreach ($this->defaultHttpConfig as $key => $value) {
			if (getenv($key)) {
				$this->defaultHttpConfig[$key] = getenv($key);
			}
		}
		parent::setUp();
		$this->client = new Simples_Transport_Http($this->getTransportHttpConfig());
	}

	/**
	 * @return []
	 */
	protected function getTransportHttpConfig()
	{
		return $this->defaultHttpConfig;
	}
}