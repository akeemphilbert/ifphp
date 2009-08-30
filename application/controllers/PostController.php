<?php
require_once 'Ifphp/models/Posts.php';
require_once 'Ifphp/core/SyndicateController.php';

class PostController extends Zend_Controller_Action
{
	/**
	 * View a post. This updates the view stats and redirects to the external post
	 * 
	 * @return unknown_type
	 */
	public function viewAction()
	{
		$this->view->keywords = implode('', array('ifphp','news aggragator','support,'.$this->view->term));
	}

    public function recentAction()
    {
        $page = $this->getRequest()->getParam('page') ? $this->getRequest()->getParam('page') : 1;
        $limit = 5;
        $posts = new Posts();
        $this->view->posts = $posts->getRecent($page,$limit);
        $total = $posts->getRecent($page, 0,true)->total;
        $this->view->paginator = Zend_Paginator::factory($total);
        $this->view->paginator->setCurrentPageNumber($page);
        $this->view->paginator->setItemCountPerPage($limit);
    }
}