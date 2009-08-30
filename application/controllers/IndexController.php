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
        $limit = 5;

        $page = $this->getRequest()->getParam('page') ? $this->getRequest()->getParam('page') : 1;
    	$posts = new Posts();
    	$this->view->posts = $posts->getRecent($page,$limit);
        $total = $posts->getRecent($page, 0,true)->total;
        $this->view->paginator = Zend_Paginator::factory($total);
        $this->view->paginator->setCurrentPageNumber($page);
        $this->view->paginator->setItemCountPerPage($limit);
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
        $this->view->keywords = implode('', array('ifphp','news aggragator','support,'.$this->view->term));
    }
    
    public function clearAction()
    {
    	$supports = new Supports();
    	$supports->clear();
    }

}