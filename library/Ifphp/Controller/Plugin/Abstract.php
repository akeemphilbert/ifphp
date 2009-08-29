<?php

/**
 * Description of Abstract
 *
 * @author Akeem Philbert <akeemphilbert@gmail.com>
 * @copyright IFPHP (c) 2009.
 */
class Ifphp_Controller_Plugin_Abstract extends Zend_Controller_Plugin_Abstract
{
    public function addPost(Post $post, Feed $feed){}
    public function addFeed(Feed $feed){}
}