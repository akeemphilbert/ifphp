<?php
/**
 * Search Command Line Funcitonality is encapsulated here
 *
 * @author Akeem Philbert <akeemphilbert@gmail.com>
 * @author IFPHP (c) 2009
 */

require_once 'Zend/Tool/Framework/Provider/Abstract.php';
require_once 'Zend/Tool/Framework/Provider/Pretendable.php';

class Ifphp_Tool_Framework_Provider_Search extends Zend_Tool_Framework_Provider_Abstract implements Zend_Tool_Framework_Provider_Pretendable
{
   public function say($name='Akeem')
    {
        if ($this->_registry->getRequest()->isPretend())
        {
            echo 'I would say hello to '.$name;
        }
        else
        {
            echo 'Hello '.$name.', from my provider';
        }
    }

    public function build($type=null)
    {
        $this->_init();

        $isCount = $this->_registry->getRequest()->isPretend();
        
        switch ($type)
        {
            case 'blogs':
            case 'feeds':
                $this->buildBlogSearch($isCount);
            break;
            case 'posts':
                $this->buildPostSearch($isCount);
            break;
            default:
                $this->buildBlogSearch($isCount);
                $this->buildPostSearch($isCount);
            break;
        }
        
    }

    /**
     * Build the blog search index
     *
     * @param boolean $isCount
     * @return boolean
     */
    protected function buildBlogSearch($isCount=false)
    {
        $index = Zend_Search_Lucene::create(Zend_Registry::getInstance()->config->search->feed);

        require_once 'Ifphp/models/Feeds.php';

        $feeds = new Feeds();
        $allFeeds = $feeds->getAll();
        
        if($isCount)
        {
            echo $allFeeds->count(). 'feeds would have been added to blog index';
            return true;
        }

        foreach($allFeeds as $feed)
        {
            $doc = new Zend_Search_Lucene_Document();
            $doc->addField(Zend_Search_Lucene_Field::Text('pid', $feed->id));
            $doc->addField(Zend_Search_Lucene_Field::Text('title', $feed->title));
            $doc->addField(Zend_Search_Lucene_Field::Text('siteUrl', $feed->siteUrl));
            $doc->addField(Zend_Search_Lucene_Field::Text('feedUrl', $feed->url));
            $doc->addField(Zend_Search_Lucene_Field::Text('language', $feed->language));
            $doc->addField(Zend_Search_Lucene_Field::Text('category', $feed->category));
            $doc->addField(Zend_Search_Lucene_Field::UnStored('description', $feed->description));
            $index->addDocument($doc);
        }

        chown(Zend_Registry::getInstance()->config->search->feed,'www-data');
        return true;
    }

    /**
     * Build the post search index
     * 
     * @param boolean $isCount
     * @return boolean
     */
    protected function buildPostSearch($isCount=false)
    {
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
            $doc->addField(Zend_Search_Lucene_Field::Text('feedDescription', $feed->description));
            $doc->addField(Zend_Search_Lucene_Field::keyword('category', $feed->findParentCategories()->title));
            $doc->addField(Zend_Search_Lucene_Field::Text('description', $post->description));
            $doc->addField(Zend_Search_Lucene_Field::unIndexed('publishDate', $post->publishDate));
            $doc->addField(Zend_Search_Lucene_Field::Keyword('type','post'));
            $index->addDocument($doc);

        }
        chown(Zend_Registry::getInstance()->config->search->post,'www-data');
        return true;
    }

    private function _init()
    {
        // Define path to application directory
        defined('APPLICATION_PATH')
            || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../../../../application'));

        // Define application environment
        defined('APPLICATION_ENV')
            || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

        // Ensure library/ is on include_path
        set_include_path(implode(PATH_SEPARATOR, array(
            realpath(APPLICATION_PATH . '/../library'),
            get_include_path(),
        )).PATH_SEPARATOR.APPLICATION_PATH);

        /** Zend_Application */
        require_once 'Zend/Application.php';
        require_once 'Zend/Config/Ini.php';
        require_once 'Zend/Registry.php';
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini',APPLICATION_ENV);
        Zend_Registry::getInstance()->config = $config;
        $application = new Zend_Application(APPLICATION_ENV,$config);
        $application->bootstrap()->bootstrap(array('db','search'));
    }
}
