<?php
/**
 * Feed Command Line Functionality is encapsulated here
 *
 * @author Akeem Philbert <akeemphilbert@gmail.com>
 * @author IFPHP (c) 2009
 */

require_once 'Zend/Tool/Framework/Provider/Abstract.php';
require_once 'Zend/Tool/Framework/Provider/Pretendable.php';

class Ifphp_Tool_Framework_Provider_Feed extends Zend_Tool_Framework_Provider_Abstract implements Zend_Tool_Framework_Provider_Pretendable
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

    /**
     * Update feed
     * 
     * @param string $feed feed url to update
     */
    public function update($feed=null)
    {
        $this->_init();
        require_once 'Ifphp/models/Feeds.php';
        $feeds = new Feeds();
        
        if ($feed)
        {
            $feed = $feeds->getBySiteUrl($feed);
            if (!$feed)
            return;
            else
            {
                $this->updateFeed($feed);
            }
        }
        else
        {
            $tfeeds = $feeds->fetchAll($feeds->select());

            if ($this->_registry->getRequest()->isPretend())
            {
                echo 'This would update '.$tfeeds->count().' feeds';
                exit;
            }

            foreach ($tfeeds as $feed)
            {
                $this->updateFeed($feed);
            }

        }
    }

    /**
     * Add feed to system
     * 
     * @param Feed $feed
     */
    private function updateFeed(Feed $feed)
    {
        require_once 'Ifphp/models/Posts.php';
        $feedSource = Zend_Feed_Reader::import($feed->url);

        $posts = new Posts();

        $tdate = $feedSource->current()->getDateModified();
        $tdate = new Zend_Date($tdate);

        while ($feedSource->valid() && $tdate->toValue() > $feed->lastPing && !$posts->getByLink($feedSource->current()->getPermaLink()))
        {
            $tdate = $feedSource->current()->getDateModified();
            $tdate = new Zend_Date($tdate);

            $defaultFilterChain = new Zend_Filter();
            $defaultFilterChain->addFilter(new Ifphp_Filter_XSSClean());
            $defaultFilterChain->addFilter(new Zend_Filter_StringTrim());
            $defaultFilterChain->addFilter(new Zend_Filter_StripTags());

            $post = $posts->createRow();
            $post->title = $defaultFilterChain->filter($feedSource->current()->getTitle());
            $post->description = $defaultFilterChain->filter($feedSource->current()->getDescription());
            $post->feedId = $defaultFilterChain->filter($feed->id);
            $post->link = $defaultFilterChain->filter($feedSource->current()->getPermaLink());
            $post->publishDate = $tdate->toValue();
            $post->save();
            Ifphp_Controller_Front::getInstance()->getPluginBroker()->addPost($post, $feed);
            $feedSource->next();
        }

         $feed->lastPing = time();
         $feed->save();
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
        $application->bootstrap()->bootstrap(array('db'));
    }
}
