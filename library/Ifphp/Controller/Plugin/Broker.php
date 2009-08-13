<?php
/**
 * Description of Broker
 *
 * @author Akeem Philbert <akeemphilbert@gmail.com>
 */
class Ifphp_Controller_Plugin_Broker extends Zend_Controller_Plugin_Broker
{
    /**
     * Called when a post is added to the system
     *
     * @param Post $post
     * @param Feed $feed
     */
    public function addPost(Post $post, Feed $feed)
    {
        foreach ($this->_plugins as $plugin) {
            if ($plugin instanceof Ifphp_Controller_Plugin_Abstract)
            {
                try {
                    $plugin->addPost($post,$feed);
                } catch (Exception $error) {
                    if (Zend_Controller_Front::getInstance()->throwExceptions()) {
                        throw $error;
                    } else {
                        $this->getResponse()->setException($error);
                    }
                }
            }
       }
    }
}