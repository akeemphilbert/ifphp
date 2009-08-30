<?php
/**
* @see Posts
*/
require_once 'Ifphp/models/Posts.php';

/**
*@see Feeds
**/
require_once 'Ifphp/models/Feeds.php';

/**
 * Cache Plugin responsible for updating the cache
 *
 * @author Albert Rosa <rosalbert@gmail.com>
 * @copyright IFPHP (c) 2009
 */
class Ifphp_Plugin_Cache extends Ifphp_Controller_Plugin_Abstract
{
    /**
    * This is to clear the cache once a post has been added and a feed added
    */
    public function addPost($post,$feed)
    {
            $posts = new Posts();
            $posts->clear(); // this clears the all the cache
    }
}