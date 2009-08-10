<?php

require_once 'Ifphp/models/Languages.php';
require_once 'Ifphp/models/Categories.php';
require_once 'Ifphp/models/Posts.php';

class ModuleController extends Zend_Controller_Action
{
	public function init()
	{
		
	}
	
	public function categoriesAction()
	{
		//setup the available categories
    	$categories = new Categories();
    	$this->view->categories = $categories->getAll();
	}
	
	public function smartCategoriesAction()
	{
		
	}
	
	public function recentBlogUpdateAction()
	{
		
	}
	
	public function popularBlogAction()
	{
		
	}
	
	public function languagesAction()
	{
		require_once 'Ifphp/models/Languages.php';
		$languages = new Langauges();
		//@todo finish this thought!
//		$this->view->languages = $languages->getAll();
		
	}
	
}