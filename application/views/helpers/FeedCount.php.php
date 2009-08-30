<?php
require_once '/Ifphp/models/Feeds.php';

class FeedCount_View_Helper_Date extends Zend_View_Helper_Abstract{
	
	public function FeedCount(){
             $feeds = new Feeds();
             return sizeof($feeds->getAll());
	}
}