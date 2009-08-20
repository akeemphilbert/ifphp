<?php

require_once  'Ifphp/models/Feeds.php';

class Ifphp_View_Helper_FeedCount extends Zend_View_Helper_Abstract{
	
	public function FeedCount(){
             $feeds = new Feeds();
             return count($feeds->getAll());
	}
}