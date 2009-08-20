<?php

class SearchController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        if($term = $this->getRequest()->getParam('term')){
        	$this->view->term =$term;
        	$config  = new Zend_Config_Ini(APPLICATION_PATH.'/configs/application.ini',APPLICATION_ENV );
			$index = Zend_Search_Lucene::open($config->search->feeds);
        	

			$this->view->results = $index->find($this->view->term);
        }
        $this->view->keywords = implode('', array('ifphp','news aggragator','search,'.$this->view->term));
    }
    
    /**
     * Buld search index
     * @todo this should be a command line thing
     * @deprecated 08/19/09  use the command zf build search instead
     */
    public function buildAction(){
    	// Create index
    	$config  = new Zend_Config_Ini(APPLICATION_PATH.'/configs/application.ini',APPLICATION_ENV );
		$index = Zend_Search_Lucene::create($config->search->feeds);
		require_once 'Ifphp/models/Feeds.php';
		
		$feeds = new Feeds();
		$AllFeeds = $feeds->getAll();
		
		
		foreach($AllFeeds as $feed){
			
		$doc = new Zend_Search_Lucene_Document();
			$doc->addField(Zend_Search_Lucene_Field::Text('title', $feed->title));
			$doc->addField(Zend_Search_Lucene_Field::Text('siteUrl', $feed->siteUrl));
			$doc->addField(Zend_Search_Lucene_Field::Text('feedUrl', $feed->url));
			$doc->addField(Zend_Search_Lucene_Field::Text('language', $feed->language));
			$doc->addField(Zend_Search_Lucene_Field::Text('category', $feed->category));
			$doc->addField(Zend_Search_Lucene_Field::Text('title', $feed->title));
			$doc->addField(Zend_Search_Lucene_Field::UnStored('description', $feed->description));
			$index->addDocument($doc);
		
		}
		echo sizeof($AllFeeds). ' added';die();
    	
    }


}

