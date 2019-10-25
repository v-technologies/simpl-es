<?php

// Root directory
if (!defined('SIMPLES_ROOT')) {
	define('SIMPLES_ROOT', dirname(__FILE__) . DIRECTORY_SEPARATOR) ;
}

// Static class


/**
 * Simples autoload method.
 * 
 * @param string	$class		Class name to load
 */
function autoload_simples ($class) {
    $path = str_replace('_', DIRECTORY_SEPARATOR , $class);

    if (file_exists(SIMPLES_ROOT . $path . '.php')) {
        require_once(SIMPLES_ROOT . $path . '.php');
    }
}

// Register our custom autoload method
spl_autoload_register('autoload_simples');