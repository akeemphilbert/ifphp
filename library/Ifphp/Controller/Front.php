<?php
/**
 * Custom Front Controller
 *
 * @author Akeem Philbert <akeemphilbert@gmail.com>
 * @copyright IFPHP (c) 2009
 */

 require_once 'Ifphp/Controller/Plugin/Broker.php';

class Ifphp_Controller_Front extends Zend_Controller_Front
{
   public function __construct()
   {
        parent::__construct();
        $this->_plugins = new Ifphp_Controller_Plugin_Broker();
   }

   /**
    * 
    * @return Ifphp_Controller_Plugin_Broker
    */
   public function getPluginBroker()
   {
       return $this->_plugins;
   }

   /**
    *
    * @return Ifphp_Controller_Front
    */
    public static function getInstance()
    {
        if (null == self::$_instance)
        {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}