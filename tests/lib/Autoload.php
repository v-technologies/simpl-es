<?php

if (!defined('SIMPLES_TESTS_ROOT')) {
	define('SIMPLES_TESTS_ROOT', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR);
}

/**
 * Simples autoload method for tests.
 *
 * @param string	$class		Class name to load
 */
function autoload_simples_tests ($class) {
	$path = str_replace('_', DIRECTORY_SEPARATOR , $class);
	if (file_exists(SIMPLES_TESTS_ROOT . $path . '.php')) {
		require_once(SIMPLES_TESTS_ROOT . $path . '.php');
	}
}

// Register our custom autoload method
spl_autoload_register('autoload_simples_tests');
