<?php

require_once 'Zend/Feed.php';

require_once 'Ifphp/models/Categories.php';
require_once 'Ifphp/models/Languages.php';
require_once 'Ifphp/models/Feeds.php';
require_once 'Ifphp/models/Posts.php';
require_once 'Ifphp/models/Users.php';
require_once 'Ifphp/dtos/Status.php';
require_once 'Ifphp/dtos/Role.php';


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
    			$feedSource = Zend_Feed::import($form->url->getValue());
    			
    			if ($feedSource instanceof Zend_Feed_Rss)
    			$feed = $this->_processRSSFeed($feedSource,$user->id);
    			elseif ($feedSource instanceof Zend_Feed_Atom)
    			$feed = $this->_processAtomFeed($feedSource,$user->id);
    			
    			
    			$this->_flashMessenger->addMessage('Your feed has been added to the site');
    			$this->_redirect('/feed/view/id/'.$feed->id);
    			//TODO send out some kind of confirmation email as well with a bit of instructions
    			
    		}
    		catch (Zend_Feed_Exception $error)
    		{
    			$form->url->markAsError();
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
    	$this->view->feed = $feeds->getById($id);
    	//get posts
    	$posts = new Posts();
    	$this->view->posts = $posts->getByFeedId($id);
    	//TODO add pagination
    }
    
    public function pingAction()
    {
    	
    }
    
    public function listAction()
    {
    	
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
     * Process RSS Feed
     * 
     * @return Feed
     */
    protected function _processRSSFeed(Zend_Feed_Rss $feedSource,$userId)
    {
    	$form = $this->getSubmitForm();
    	
    	$feeds = new Feeds();
    	$feed = $feeds->createRow();
    	$feed->token = md5(uniqid($userId));
    	$feed->url = $form->url->getValue();
    	$feed->title = $feedSource->title();
    	$feed->description = $feedSource->description();
    	$feed->categoryId = $form->category->getValue();
    	$feed->languageId = $form->language->getValue();
    	$feed->siteUrl = $form->siteUrl->getValue();
    	$feed->statusId = Status::ACTIVE;
    	$feed->userId = $userId;
    	$feed->refreshRate = 120;//TODO this is sometimes stored in the feed
    	$feed->save();
    	
    	//parse feed
    	$posts = new Posts();
    	
    	foreach ($feedSource as $feedEntry)
    	{
    		$post = $posts->createRow();
    		$post->title = $feedEntry->title();
    		$post->description = $feedEntry->description();
    		$post->link = $feedEntry->link();
    		$post->feedId = $feed->id;
    		$date = new Zend_Date($feedEntry->pubDate());
    		$post->publishDate = $date->toValue();
    		$post->save();
    	}
    	
    	return $feed;
    }
    
	/**
     * Process Atom Feed
     * 
     * @return Feed
     */
    protected function _processAtomFeed(Zend_Feed_Atom $feedSource,$userId)
    {
    	$feeds = new Feeds();
    	$feed = $feeds->createRow();
    	$feed->token = md5(uniqid($userId));
    	$feed->url = $form->url->getValue();
    	$feed->title = $feedSource->title();
    	$feed->description = $feedSource->subTitle();
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
    		$post->title = $feedEntry->title();
    		$post->description = $feedEntry->summary();
    		$post->link = $feedEntry->link();
    		$post->feedId = $feed->id;
    		$date = new Zend_Date($feedEntry->published());
    		$post->publishDate = $date->toValue();
    		$post->save();
    	}
    	
    	return $feed;
    }
    
    


}