<?php
require_once 'Zend/Test/PHPUnit/ControllerTestCase.php';

class ControllerTestCase extends Zend_Test_PHPUnit_ControllerTestCase
{
	/**
	 * @var Zend_Application
	 */
	protected $application;
	/**
	 * (non-PHPdoc)
	 * @see Test/PHPUnit/Zend_Test_PHPUnit_ControllerTestCase#setUp()
	 */
	public function setUp()
	{
		$this->bootstrap = array($this,'appBootstrap');
		parent::setUp();
	}
	/**
	 * Bootstrap application for unit testing 
	 * 
	 * @return void
	 */
	public function appBootstrap()
	{
		$this->application = new Zend_Application(APPLICATION_ENV,APPLICATION_PATH.'/configs/application.ini');
		$this->application->bootstrap();
	}
}