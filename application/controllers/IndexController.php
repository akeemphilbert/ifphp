<?php
require_once 'Ifphp/models/Supports.php';
require_once 'Ifphp/models/Posts.php';

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    /**
     * Site landing page
     */
    public function indexAction()
    {
        $posts = new Posts();
        $this->view->posts = $posts->getRecent(1,10);
//        Zend_Debug::dump($this->view->posts);
//        die();
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