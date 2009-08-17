<?php
require_once 'Ifphp/models/Supports.php';
require_once 'Ifphp/models/Posts.php';
require_once 'Ifphp/core/SyndicateController.php';

class IndexController extends Ifphp_SyndicateController
{

    public function init()
    {
        parent::init();
        
    }

    /**
     * Site landing page
     */
    public function indexAction()
    {
        $posts = new Posts();
        $post = $this->view->posts = $posts->getRecent(1,10);      
        
        //setting the pagination              
        
        $this->_getParam('page')?$this->_getParam('page'):1;
                 
        $paginator = Zend_Paginator::factory($this->view->posts);        
		$paginator->setCurrentPageNumber($page);
		$paginator->setPageRange(5);		
		$paginator->setItemCountPerPage(10);
					
		Zend_Registry::get('logger')->info(count($paginator));	
		$this->view->paginator = $paginator;
		
    }
    
    /**
     * This renders the about page
     * @return void
     */
    public function aboutAction()
    {
    	
    }
    
    /**
     * This renders the support page
     * @return void
     */
    public function supportAction()
    {    	
        //here I will be testing the models
        $supports = new Supports();
        if($this->getRequest()->isGet() && 
        	$this->getRequest()->getParam('term') != null
        	
        ){
        	$term = $this->getRequest()->getParam('term');        		
        	$result = $supports->search($term);
        	$this->view->supports = $result;
        	$this->view->term = $term;
        }
        
    }
    
    public function clearAction()
    {
    	$supports = new Supports();
    	$supports->clear();
    }

}