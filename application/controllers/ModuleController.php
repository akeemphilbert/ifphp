<?php

require_once 'Ifphp/models/Languages.php';
require_once 'Ifphp/models/Categories.php';
require_once 'Ifphp/models/Posts.php';
/**
 * @deprecated 08/15/09
 */
class ModuleController extends Zend_Controller_Action
{
	public function init()
	{
		
	}
	
	public function categoriesAction()
	{
		
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
		$languages = new Languages();
		$this->view->languages = $languages->getAll();
		
	}
	
}