<?php
/**
 * View Helper to display feed count
 *
 * @author Albert Rosa <rosalbert@gmail.com>
 * @copyright IFPHP (c) 2009
 */


/**
* @see Ifphp_Models_Feeds
**/
require_once  'Ifphp/models/Feeds.php';

class Ifphp_View_Helper_FeedCount extends Zend_View_Helper_Abstract{
	
	public function FeedCount(){
             $feeds = new Feeds();
             return count($feeds->getAll());
	}
}