<?php
/**
 * Website bootstrap
 *
 * @author Albert Rosa <rosalbert@gmail.com>
 * @author Akeem Philbert <akeemphilbert@gmail.com>
 *
 * @copyright IFPHP (c) 2009
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    public function _bootstrap($resource=null)
    {
        Ifphp_Controller_Front::getInstance();
        parent::_bootstrap($resource);
    }


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
                    'path' => APPLICATION_PATH.'/../library'
                )
            )
        );
        return $autoloader;
		
	}
    
	/**
	 * This is where we set up the caching for the applcation
	 * 
	 * @todo currently the memcachedoptions are not working. thsi could be reconfigured as a bootstrap resource
	 * 
	 * @return void
	 */
	protected function _initCache()
        {
//            Zend_Registry::set('cacheConfig',new Zend_Config_Xml(APPLICATION_PATH.'/configs/cache.xml',APPLICATION_ENV,array('allowModifications'=>true)));
            
            //TODO this should be in a seperate file. the output isn't compatible with xml and i suspect ini as well
//            Zend_Registry::getInstance()->cacheConfig->pageCache->frontend->options->regexps = array(
//               '^/$' => array('cache' => true),
//               '^/index/' => array('cache' => true),
//               '^/weblogs/$' => array('cache' => true),
//               '^/feed/' => array('cache' => true),
//               '^/about/' => array('cache' => true)
//            );

//           Zend_Debug::dump(Zend_Registry::getInstance()->cacheConfig->pageCache->frontend->options->toArray());
//           die();

//            $cache = Zend_Cache::factory(
//                Zend_Registry::getInstance()->cacheConfig->pageCache->frontend->name,
//                Zend_Registry::getInstance()->cacheConfig->pageCache->backend->name,
//                Zend_Registry::getInstance()->cacheConfig->pageCache->frontend->options->toArray(),
//                Zend_Registry::getInstance()->cacheConfig->pageCache->backend->options->toArray()
//             );
//
//             $cache->start();

//            $writer = new Zend_Config_Writer_Xml(array('config'   => Zend_Registry::getInstance()->cacheConfig,
//                                           'filename' => APPLICATION_PATH.'/configs/test-config.xml'));
//            $writer->write();
        }

    /**
     * Here we set the loggers for logging errors
     * @todo add zend_log_writer_email
     * @return void
     */
    protected function _initLog(){
    	$config  = new Zend_Config_Ini(APPLICATION_PATH.'/configs/application.ini',APPLICATION_ENV );
    	$today = Zend_Date::now();

    	$writer = new Zend_Log_Writer_Stream($config->log->location.$today->get('M_d_Y').'.txt');

    	$fbug = new Zend_Log_Writer_Firebug();

    	$logger = new Zend_Log($writer);
    	$logger->addWriter($fbug);

    	Zend_Registry::set('logger',$logger);
    }
    
    /**
     * This is where we set up the locale and set the translation adapters
     * @deprecate this could be set in the site.ini using resoruces
     * @return void
     */
    protected function _initLocale()
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
     * @deprecated this could be set in the site.ini
     * @return void
     */
    protected function _initRoutes(){
    	$config  = new Zend_Config_Ini(APPLICATION_PATH.'/configs/routes.ini',APPLICATION_ENV );
		$router = new Zend_Controller_Router_Rewrite();
		$router->addConfig($config, 'routes');
		
		$controller = Zend_Controller_Front::getInstance();
		$controller->setRouter($router);
    }
    
    /**
     * Setup the user session
     * 
     * @return void
     */
    protected function _initIdentity()
    {
    	Zend_Session::start();
    }
    
    /**
     * Sets up the view helpers
     * 
     * @return void
     */
    protected function _initViewHelpers(){
    	$view = new Zend_View(array('basePath'=>APPLICATION_PATH . "/views/"));
        $view->addHelperPath(APPLICATION_PATH . "/views/helpers/", "Ifphp_View_Helper");
//        $view->addFilterPath(APPLICATION_PATH . "/views/filters/", "Ifphp_Filter");
        $view->addHelperPath("ZendX/JQuery/View/Helper", "ZendX_JQuery_View_Helper");


        $doctypeHelper = new Zend_View_Helper_Doctype();
        $doctypeHelper->doctype('XHTML1_TRANSITIONAL');

        $viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
        $viewRenderer->setView($view);
        Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
    }

    /**
     * Setup default pagination options
     */
    protected function _initPagination()
    {
        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('partials/pagination/default.phtml');
    }
    
}