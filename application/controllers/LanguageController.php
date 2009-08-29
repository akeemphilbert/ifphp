<?php
/**
 * Controller that manages all languages
 *
 * @author Akeem Philbert <akeemphilbert@gmail.com>
 * @copyright IFPHP (c) 2009
 */

 require_once 'Ifphp/models/Languages.php';
 require_once 'Ifphp/models/Posts.php';
 require_once 'Ifphp/core/SyndicateController.php';

class LanguageController extends Zend_Controller_Action
{
    public function viewAction()
    {
        $languages = new Languages();
        $this->view->language = $languages->getBySlug($this->getRequest()->getParam('id'));

        if (!$this->view->language)
        throw new Zend_Exception('Invalid language specified');

        $posts = new Posts();
        $limit = 5;
        $page = $this->getRequest()->getParam('page') ? $this->getRequest()->getParam('page') : 1;
        
        $this->view->posts = $posts->getByLanguage($this->view->language->id,$page,$limit);

        $total = $posts->getByLanguage($this->view->language->id,1,0,true)->total;
        $this->view->paginator = Zend_Paginator::factory($total);
        $this->view->paginator->setCurrentPageNumber($page);
        $this->view->paginator->setItemCountPerPage($limit);

        $this->view->keywords = implode('', array('ifphp','news aggragator','support,'.$this->view->language->title));


        //TODO add paging
    }

    public function listAction()
    {
        //setup the available languages
    	$languages = new Languages();
    	$this->view->languages = $languages->getAll();
    }
}