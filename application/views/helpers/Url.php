<?php
/**
 * Url
 *
 * @author Akeem Philbert <akeemphilbert@gmail.com>
 * @copyright IFPHP (c) 2009
 */
class Ifphp_View_Helper_Url extends Zend_View_Helper_Abstract
{
    public function Url()
    {
        return Zend_Registry::getInstance()->siteinfo->url;
    }
}