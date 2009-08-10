<?php

require_once 'Zend/Feed.php';

require_once 'Ifphp/models/Categories.php';
require_once 'Ifphp/models/Languages.php';
require_once 'Ifphp/models/Feeds.php';
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
	
    public function init()
    {
    	$this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
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
    	//get flash messages
    	$this->view->messages = $this->_flashMessenger->getMessages();
    	
    	$config = new Zend_Config_Ini( APPLICATION_PATH . '/configs/forms.ini');
    	$form = new Zend_Form($config->feed->submit);
    	
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
    			
    			$feeds = new Feeds();
    			$feed = $feeds->createRow();
    			$feed->url = $form->url->getValue();
    			$feed->title = $feedSource->title();
    			$feed->description = $feedSource->description();
    			$feed->categoryId = $form->category->getValue();
    			$feed->languageId = $form->language->getValue();
    			$feed->siteUrl = $form->siteUrl->getValue();
    			$feed->statusId = Status::ACTIVE;
    			$feed->userId = $user->id;
    			$feed->refreshRate = 120;//TODO this is sometimes stored in the feed
    			$feed->save();
    			
    			$this->_flashMessenger->addMessage('Your feed has been added to the site');
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
    
    public function pingAction()
    {
    	
    }
    
    public function listAction()
    {
    	
    }
    
    
    
    
    


}