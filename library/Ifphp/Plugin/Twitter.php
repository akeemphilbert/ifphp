<?php
/**
 * Twitter Plugin
 *
 * @author Akeem Philbert <akeemphilbert@gmail.com>
 * @copyright IFPHP (c) 2009
 */
class Ifphp_Plugin_Twitter extends Ifphp_Controller_Plugin_Abstract
{
    /**
     * Zend service object
     *
     * @var Zend_Service_Twitter
     */
    protected $_twitter;
    
    protected function _init()
    {
        try
        {
            $config = new Zend_Config_ini(APPLICATION_PATH.'/configs/twitter.ini');
            $this->_twitter = new Zend_Service_Twitter($config->userName,$config->password);
            $response = $this->_twitter->account->verifyCredentials();
           

        }
        catch (Zend_Exception $error)
        {
            
        }
    }
    
    public function addPost(Post $post,Feed $feed)
    {

    }

    public function addFeed(Feed $feed)
    {
        
    }
}