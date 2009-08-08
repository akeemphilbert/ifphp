<?php

require_once 'Ifphp/models/Categories.php';

/**
 * Manage Site feeds that are submited by users
 * 
 * @author Akeem Philbert <akeemphilbert@gmail.com>
 * 
 */
class FeedController extends Zend_Controller_Action
{

	
    public function init()
    {
        /* Initialize action controller here */
    }
    
    public function indexAction()
    {
    	
    }
	
    /**
     * Submit Feed to website
     * 
     * @return 
     */
    public function submitAction()
    {
    	$categories = new Categories();
    	$this->view->categories = $categories->getAll();
    	
    	$config = new Zend_Config_Ini( APPLICATION_PATH . '/configs/forms.ini');
    	$form = new Zend_Form($config->feed->submit);
        
    	if ($this->getRequest()->isPost() && $form->isValid($_POST))
    	{
    		//TODO save data in db
    	}
    	
    	$this->view->form = $form;
    	
    }
    
    public function pingAction()
    {
    	
    }
    
    public function listAction()
    {
    	
    }
    
    
    
    
    


}