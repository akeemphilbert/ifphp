<?php
/**
 * Controller that manages all categories
 *
 * @author Akeem Philbert <akeemphilbert@gmail.com>
 * @copyright IFPHP (c) 2009
 */

 require_once 'Ifphp/models/Categories.php';
 require_once 'Ifphp/models/Posts.php';
 require_once 'Ifphp/core/SyndicateController.php';

class CategoryController extends Zend_Controller_Action
{
    public function viewAction()
    {
        $categories = new Categories();
        $feeds = new Feeds();
        $this->view->category = $categories->getBySlug($this->getRequest()->getParam('id'));
        $this->view->category->feeds = $feeds->getByCategory($this->view->category->id);

        $posts = new Posts();

        $limit = 5;
        $page = $this->getRequest()->getParam('page') ? $this->getRequest()->getParam('page') : 1;
        $this->view->posts = $posts->getByCategory($this->view->category->id,$page,$limit);

        $total = $posts->getByCategory($this->view->category->id,1,0,true)->total;
        $this->view->paginator = Zend_Paginator::factory($total);
        $this->view->paginator->setCurrentPageNumber($page);
        $this->view->paginator->setItemCountPerPage($limit);
        
        $this->view->keywords = implode('', array('ifphp','news aggragator','support,'.$this->view->categpry->title));
        

        //TODO add paging
    }

    public function listAction()
    {
        //setup the available categories
    	$categories = new Categories();
    	$this->view->categories = $categories->getAll();
    }
    
    /**
     * This is where the smart categores get displayed
     * @todo define how smart categores and ordered and searched
     * @return unknown_type
     */
    public function smartAction()
    {    	
        //setup the available categories
    	$categories = new Categories();
    	$this->view->categories = $categories->getAll();
    }
}