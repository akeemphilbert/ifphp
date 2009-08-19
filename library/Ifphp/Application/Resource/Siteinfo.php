<?php
/**
 * Site info loader resource
 *
 * @author Akeem Philbert <akeemphilbert@gmail.com>
 * @copyright IFPHP (c) 2009
 */
class Ifphp_Application_Resource_Siteinfo extends Zend_Application_Resource_ResourceAbstract
{
    public function init()
    {
        foreach ($this->getOptions() as $index=>$value)
        {
            Zend_Registry::getInstance()->siteinfo->$index = $value;
        }
    }

    public function getSiteinfo()
    {
        
    }
}