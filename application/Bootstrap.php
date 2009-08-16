<?php
/**
 * 
 * @author Albert Rosa
 *
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	/**
	 * This will autoload based on the folder structure to files
	 * 
	 * @return Zend_Application_Module_Autoloader
	 */
	protected function _initAutoload(){
		$autoloader = new Zend_Application_Module_Autoloader(array(
            'namespace' => 'Default_',
            'basePath'  => dirname(__FILE__),
        ));

        $autoloader->addResourceTypes(
            array(
                'Ifphp' => array(
                    'namespace' => 'Ifphp',
                    'path' => APPLICATIONPATH.'/../library'
                )
            )
        );
        return $autoloader;
		
	}	
	
	/**
	 * Added page caching to the site
	 * (non-PHPdoc)
	 * @see library/Zend/Application/Bootstrap/Zend_Application_Bootstrap_Bootstrap#run()
	 */
	public function run(){
		$this->setupCache();
		
		parent::run();
			
	}
	
	/**
	 * Run custom setup functions. This was abstracted from run for Unit Testing purposes
	 * NOTE: order is important loggers must be before locale due to logging locale errors 
	 * @return void
	 */
	protected function _bootstrap($resource=null)
	{
		parent::_bootstrap($resource);

		$this->setupLoggers();
		$this->setUpLocale();	
		$this->setupRoutes();
		$this->setupIdentity();
		$this->setupViewHelpers();		
	}
	
	/**
	 * This is where we set up the caching for the applcation
	 * 
	 * @todo currently the memcachedoptions are not working 
	 * 
	 * @return void
	 */
	private function setupCache()
    {	
    	$config  = new Zend_Config_Xml(APPLICATION_PATH.'/configs/cache.xml',APPLICATION_ENV );
    	
        $cache = Zend_Cache::factory(
        	$config->cache->frontend->name,        	
        	$config->cache->backend->name,
        	$config->cache->frontend->toArray(),        	
        	$config->cache->backend->toArray()
        );
        $cache->start();
        
    }
    
    /**
     * This is where we set up the locale and set the translation adapters
     * 
     * @return void
     */
    private function setUpLocale()
    {
    	//this is the locale		
		try {
    		$locale = new Zend_Locale(Zend_Locale::BROWSER);
		} catch (Zend_Locale_Exception $e) {
    		$locale = new Zend_Locale('en_US');
    		
		}
		Zend_Locale::setDefault($locale);
    	
		// here we define the translator
		$translatePath = APPLICATION_PATH.'/languages/translate.xml';
    	    
    	$options = array('log'=>Zend_Registry::get('logger'),'disableNotices'=>false, 
    		'logMessage'=>"Untranslated message within '%locale%': %message% : %word%");
    	$translate = new Zend_Translate('tmx',
			$translatePath,$locale, $options
		);
		
		// hwere we add the translator to the registry
		Zend_Registry::set('translate', $translate);
		
		// here we set all forms attached to the translator
		// this will translate everything on the form
		Zend_Form::setDefaultTranslator($translate);
		
    }
    
    /**
     * This is where we set up the routes
     * 
     * @return void
     */
    private function setupRoutes(){
    	$config  = new Zend_Config_Ini(APPLICATION_PATH.'/configs/routes.ini',APPLICATION_ENV );
		$router = new Zend_Controller_Router_Rewrite();
		$router->addConfig($config, 'routes');
		
		$controller = Zend_Controller_Front::getInstance();
		$controller->setRouter($router);
    }
    
    
    /**
     * Here we set the loggers for logging errors
     * @todo add zend_log_writer_email 
     * @return void
     */
    private function setupLoggers(){
    	$config  = new Zend_Config_Ini(APPLICATION_PATH.'/configs/application.ini',APPLICATION_ENV );
    	$today = Zend_Date::now(); 
    	
    	$writer = new Zend_Log_Writer_Stream($config->log->location.$today->get('M_d_Y').'.txt');

    	$fbug = new Zend_Log_Writer_Firebug();
    	
    	$logger = new Zend_Log($writer);
//    	$logger = new Zend_Log($fbug);
    	$logger->addWriter($fbug);
    	
    	Zend_Registry::set('logger',$logger);
    	
    		
    }
    
    /**
     * Setup the user session
     * 
     * @return void
     */
    public function setupIdentity()
    {
    	Zend_Session::start();
    }
    
    /**
     * Sets up the view helpers
     * 
     * @return void
     */
    public function setupViewHelpers(){
    	$view = new Zend_View();
		$view->addHelperPath(APPLICATION_PATH . "/views/helpers/", "Ifphp_View_Helper");
		
		$viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
		$viewRenderer->setView($view);
		Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
    	
    }
    
}

