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
        $this->view->category = $categories->getBySlug($this->getRequest()->getParam('id'));

        $posts = new Posts();
        $this->view->posts = $posts->getByCategory($this->view->category->id);

        //TODO add paging
    }

    public function listAction()
    {
        //setup the available categories
    	$categories = new Categories();
    	$this->view->categories = $categories->getAll();
    }
}