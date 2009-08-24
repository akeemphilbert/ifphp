<?php

class SearchController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
       $this->_forward('result');
    }

    public function resultAction()
    {
        if ($this->getRequest()->isGet())
        {
            $page = $this->getRequest()->getParam('page') ? $this->getRequest()->getParam('page') : 1;
            $limit = 5;

            switch (strtolower($this->getRequest()->getParam('type')))
            {
                case 'blog':
                    $index = Zend_Search_Lucene::open(Zend_Registry::getInstance()->config->search->feed);
                break;
                default:
                case 'post':
                    $index = Zend_Search_Lucene::open(Zend_Registry::getInstance()->config->search->post);
                break;
             /*
                    require_once 'Zend/Search/Lucene/MultiSearcher.php';
                    $index = new Zend_Search_Lucene_Interface_MultiSearcher();
                    $index->addIndex(Zend_Search_Lucene::open(Zend_Registry::getInstance()->config->search->post));
                    $index->addIndex(Zend_Search_Lucene::open(Zend_Registry::getInstance()->config->search->feed));
                break;
              *
              */
            }
            
            $this->view->term =$this->getRequest()->getParam('term');//todo filter term
            $this->view->results = $index->find($this->view->term);

            $this->view->paginator = Zend_Paginator::factory($this->view->results);
            $this->view->paginator->setCurrentPageNumber($page);
            $this->view->paginator->setItemCountPerPage($limit);

        }
    }
    
    /**
     * Build search index
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

    /**
     * Build the blog search index
     *
     * @param boolean $isCount
     * @return boolean
     */
    public function buildBlogAction()
    {
        $isCount = $this->getRequest()->getParam('pretend');
        $index = Zend_Search_Lucene::create(Zend_Registry::getInstance()->config->search->feed);

        require_once 'Ifphp/models/Feeds.php';

        $feeds = new Feeds();
        $allFeeds = $feeds->getAll();

        if($isCount)
        {
            echo $allFeeds->count(). 'feeds would have been added to blog index';
            exit;
        }

        foreach($allFeeds as $feed)
        {
            $doc = new Zend_Search_Lucene_Document();
            $doc->addField(Zend_Search_Lucene_Field::Text('pid', $feed->id));
            $doc->addField(Zend_Search_Lucene_Field::Text('title', $feed->title));
            $doc->addField(Zend_Search_Lucene_Field::Text('siteUrl', $feed->siteUrl));
            $doc->addField(Zend_Search_Lucene_Field::Text('feedUrl', $feed->url));
            $doc->addField(Zend_Search_Lucene_Field::Text('created', $feed->created));
            $doc->addField(Zend_Search_Lucene_Field::Keyword('language', $feed->language));
            $doc->addField(Zend_Search_Lucene_Field::Keyword('category', $feed->category));
            $doc->addField(Zend_Search_Lucene_Field::Keyword('type','feed'));
            $doc->addField(Zend_Search_Lucene_Field::UnStored('description', $feed->description));
            $index->addDocument($doc);
        }

//        chown(Zend_Registry::getInstance()->search->feed,'www-data');
    }

    /**
     * Build the post search index
     *
     * @param boolean $isCount
     * @return boolean
     */
    public function buildPostAction()
    {
        $isCount = $this->getRequest()->getParam('pretend');
        $index = Zend_Search_Lucene::create(Zend_Registry::getInstance()->config->search->post);

        

        require_once 'Ifphp/models/Posts.php';
        require_once 'Ifphp/models/Feeds.php';
        require_once 'Ifphp/models/Categories.php';

        $posts = new Posts();
        $allPosts = $posts->getRecent(1,0);

        if ($isCount)
        {
            echo $allPosts->count().' posts would have been added to the post index';
            exit;
        }


        foreach($allPosts as $post)
        {
            $feed = $post->findParentFeeds();
            
            $doc = new Zend_Search_Lucene_Document();
            $doc->addField(Zend_Search_Lucene_Field::Text('pid', $post->id));
            $doc->addField(Zend_Search_Lucene_Field::Text('title', $post->title));
            $doc->addField(Zend_Search_Lucene_Field::Text('siteUrl', $post->siteUrl));
            $doc->addField(Zend_Search_Lucene_Field::Text('link', $post->link));
            $doc->addField(Zend_Search_Lucene_Field::Text('feedTitle', $feed->title));
            $doc->addField(Zend_Search_Lucene_Field::Text('feedSlug', $feed->slug));
            $doc->addField(Zend_Search_Lucene_Field::keyword('category', $feed->findParentCategories()->title));
            $doc->addField(Zend_Search_Lucene_Field::Text('description', $post->description));
            $doc->addField(Zend_Search_Lucene_Field::unIndexed('publishDate', $post->publishDate));
            $doc->addField(Zend_Search_Lucene_Field::Keyword('type','post'));
            $index->addDocument($doc);

        }
//        chown(Zend_Registry::getInstance()->search->post,'www-data');
    }


}

