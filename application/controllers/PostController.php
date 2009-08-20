<?php 
class PostController extends Zend_Controller_Action
{
	/**
	 * View a post. This updates the view stats and redirects to the external post
	 * 
	 * @return unknown_type
	 */
	public function viewAction()
	{
		$this->view->keywords = implode('', array('ifphp','news aggragator','support,'.$this->view->term));
	}
}