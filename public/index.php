<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
    
defined('LIBRARY_PATH')
    || define(':LIBRARY_PATH', realpath(dirname(__FILE__) . '/../library'));


// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)).PATH_SEPARATOR.APPLICATION_PATH);

/** Zend_Application */
require_once 'Zend/Application.php';
require_once 'Zend/Config/Ini.php';
require_once 'Zend/Registry.php';

// Create application, bootstrap, and run
$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini',APPLICATION_ENV);
Zend_Registry::getInstance()->config = $config;
$application = new Zend_Application(
    APPLICATION_ENV, 
    $config
);
$application->bootstrap()
            ->run();