<?php
/**
 * Url
 *
 * @author Akeem Philbert <akeemphilbert@gmail.com>
 * @copyright IFPHP (c) 2009
 */
class Ifphp_View_Helper_AbsoluteUrl extends Zend_View_Helper_Abstract
{
    public function AbsoluteUrl()
    {
        return Zend_Registry::getInstance()->siteinfo->url;
    }
}