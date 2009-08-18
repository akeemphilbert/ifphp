<?php

require_once 'Zend/Feed.php';

require_once 'Ifphp/models/Categories.php';
require_once 'Ifphp/models/Languages.php';
require_once 'Ifphp/models/Feeds.php';
require_once 'Ifphp/models/Posts.php';
require_once 'Ifphp/models/Users.php';
require_once 'Ifphp/dtos/Status.php';
require_once 'Ifphp/dtos/Role.php';
require_once APPLICATION_PATH.'/views/filters/XSSClean.php';


/**
 * Manage Site feeds that are submited by users
 * 
 * @author Akeem Philbert <akeemphilbert@gmail.com>
 * @copyright IFHP (c) 2009.
 */
class FeedController extends Zend_Controller_Action
{
	/**
	 * Flash Messenger
	 * 
	 * @var Zend_Controller_Action_Helper_FlashMessenger
	 */
	protected $_flashMessenger;
	/**
	 * Redirector
	 * 
	 * @var Zend_Controller_Action_Helper_Redirector
	 */
	protected $_redirector;
	/**
	 * Submit Form
	 * 
	 * @var Zend_Form
	 */
	protected $_submitForm;
	
    public function init()
    {
    	$this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
    	$this->_redirector = $this->_helper->getHelper('Redirector');
    	$this->initView();
    }
    
    public function indexAction()
    {
    	$this->_forward('list');
    }
	
    /**
     * Submit Feed to website
     * 
     * @return 
     */
    public function submitAction()
    {
    	$form = $this->getSubmitForm();
    	
    	//setup the available categories
    	$categories = new Categories();
    	$this->view->categories = $categories->getAll();
    	foreach ($this->view->categories as $category)
    	{
    		$form->category->addMultiOptions(array($category->id=>$category->title));
    	}
    	
    	//setup the available languages
    	$languages = new Languages();
    	$this->view->languages = $languages->getAll();
    	foreach ($this->view->languages as $language)
    	{
    		$form->language->addMultiOptions(array($language->id=>$language->title));
    	}
        
    	//if it's a post submit let's save the information
    	if ($this->getRequest()->isPost() && $form->isValid($_POST))
    	{
    		//TODO this shoudl be wrapped in a transaction
    		//create user 
    		$users = new Users();
    		$user = $users->createRow();
    		$user->email = $form->email->getValue();
    		$user->username = 'temporaryusername'; //TODO put real username here eventually
    		$user->password = '';
    		$user->roleId = Role::SUBMITTER;
    		$user->statusId = Status::ACTIVE;
    		$user->save();
    		
    		try
    		{
    			$feedSource = Zend_Feed_Reader::import($form->url->getValue());

                //convert/parse feed to strongly typed objects
                $defaultFilterChain = new Zend_Filter();
                $defaultFilterChain->addFilter(new Ifphp_Filter_XSSClean());
                $defaultFilterChain->addFilter(new Zend_Filter_StringTrim());
                $defaultFilterChain->addFilter(new Zend_Filter_StripTags());
                
                $feeds = new Feeds();
                $feed = $feeds->createRow();
                $feed->token = md5(uniqid($user->id));
                $feed->url = $defaultFilterChain->filter($form->url->getValue());
                $feed->title = $defaultFilterChain->filter($feedSource->getTitle());
                $inflector = new Zend_Filter_Inflector(':title');
                $inflector->setRules(array(
                    ':title' => array('Word_SeparatorToDash','StringToLower','HtmlEntities')
                ));
                $feed->slug = $inflector->filter(array('title'=>$feed->title));

                $feed->description = $defaultFilterChain->filter($feedSource->getDescription());
                $feed->categoryId = $form->category->getValue();
                $feed->languageId = $form->language->getValue();
                $feed->siteUrl = $form->siteUrl->getValue();
                $feed->statusId = Status::ACTIVE;
                $feed->userId = $user->id;
                $feed->refreshRate = 120;//TODO this is sometimes stored in the feed
                $feed->save();

                //parse feed
                $posts = new Posts();

                foreach ($feedSource as $feedEntry)
                {
                    $post = $posts->createRow();
                    $post->title = $defaultFilterChain->filter($feedEntry->getTitle());
                    $post->description = $defaultFilterChain->filter($feedEntry->getDescription());
                    $post->link = $defaultFilterChain->filter($feedEntry->getPermaLink());
                    $post->feedId = $feed->id;
                    $date = new Zend_Date($feedEntry->getDateModified());
                    $post->publishDate = $date->toValue();
                    $post->save();
                }
    			
    			
    			$this->_flashMessenger->addMessage('Your feed has been added to the site. Your ping back url is http://ifphp.com/feed/ping-back/'.$feed->token);
    			$this->_redirect('/feed/view/'.$feed->slug);
    			//TODO send out some kind of confirmation email as well with a bit of instructions
    			
    		}
    		catch (Zend_Feed_Exception $error)
    		{
    			$form->url->markAsError();
                Zend_Registry::getInstance()->logger->err($error);
    			return;
    		}
    	}
    	
    	$this->view->form = $form;
    	
    }
    
    /**
     * View feed information
     * 
     * @return void
     */
    public function viewAction()
    {
    	//get flash messages
    	$this->view->messages = $this->_flashMessenger->getMessages();
    	
    	$id = $this->getRequest()->getParam('id');
    	//get feedinfo
    	$feeds = new Feeds();
    	$this->view->feed = $feeds->getBySlug($id);
        $this->view->feed->views++;
        $this->view->feed->save();
    	//get posts
    	$posts = new Posts();
    	$this->view->posts = $posts->getByFeedId($this->view->feed->id);
    	//TODO add pagination
    }

    /**
     * Ping site to check for updated posts
     */
    public function pingAction()
    {
        $form = $this->getPingForm();

        if ($this->getRequest()->isPost() && $form->isValid($_POST))
        {
            $feeds = new Feeds();
            $feed = $feeds->getBySiteUrl($form->url->getValue());
            $this->updateFeedPosts($feed);
        }

        $this->view->form = $form;
    }

    /**
     * Ping back for the site
     */
    public function pingBackAction()
    {
        $feeds = new Feeds();
        $feed = $feeds->getByToken($this->getRequest()->getParam('token'));
        $this->updateFeedPosts($feed);
        $this->getHelper('viewRenderer')->setNoRender();
        $this->_helper->layout->disableLayout();

        try {

                //set up a new factory Zend xmlrpc server and add classes
                $server = new Zend_XmlRpc_Server();
                $server->setClass('Ifphp_Ping_XmlRpc','pingback');

                //success
                echo $server->handle();

        } catch(Exception $e) {
                throw $e;
        }
    }
    
    public function listAction()
    {
    	$feeds = new Feeds();
    	$categories = new Categories();
    	$cats = $categories->getAll();

    	$this->view->feeds = $feeds->getAll();

    	$FeedsByCategory = array();

    	foreach($cats as $cat )
    		$FeedsByCategory[$cat->title] = $feeds->getByCategory($cat->id);

    	$this->view->feedsByCategory = $FeedsByCategory;

    	$this->view->categories = $cats;
    }

    /**
     * Recentl updated feed module
     */
    public function recentlyUpdatedAction()
    {
        $feeds = new Feeds();
        $this->view->feeds = $feeds->getRecentlyUpdated();
    }

    public function popularAction()
    {
        $feeds = new Feeds();
        $this->view->feeds = $feeds->getPopular();
    }
    
    /**
     * Get submit form
     * 
     * @return Zend_Form
     */
    protected function getSubmitForm()
    {
    	if (!$this->_submitForm)
    	{
    		$config = new Zend_Config_Ini( APPLICATION_PATH . '/configs/forms.ini');
    		$this->_submitForm = new Zend_Form($config->feed->submit);
    	}
    	
    	return $this->_submitForm;
    }

    /**
     * Get blog ping form
     *
     * @return Zend_Form
     */
    protected function getPingForm()
    {
       $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/forms.ini');
       $form = new Zend_Form($config->feed->ping);

       return $form;
    }

    /**
     * Get the latest posts from a feed
     *
     * @param Feed $feed
     */
    protected function updateFeedPosts(Feed $feed)
    {
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
    
    


}