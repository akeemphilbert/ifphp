<?php
require_once 'Ifphp/models/Supports.php';

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
//        //here I will be testing the models
//        $user = new Ifphp\models\Users();
//        $result = $user->getById(2);
//        echo sizeof($result);
//        echo $result->username;
        
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

