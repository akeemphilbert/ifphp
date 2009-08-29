<?php
/**
 * Mail Resource Plugin
 *
 * @author Akeem Philbert <akeemphilbert@gmail.com>
 */
class Ifphp_Application_Resource_Mail extends Zend_Application_Resource_ResourceAbstract
{
  public function init()
  {
      foreach ($this->getOptions() as $key => $value)
      {
         Zend_Registry::getInstance()->mailAccounts = $this->_options['accounts'];
      }
  }
}