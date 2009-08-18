<?php
/**
 * Custom FrontController resource
 *
 * @deprecated 08/18/09
 * @author Akeem Philbert <akeemphilbert@gmail.com>
 * @copyright IFPHP (c) 2009
 */
 require_once 'Ifphp/Controller/Front.php';

class Ifphp_Application_Resource_Frontcontroller extends Zend_Application_Resource_Frontcontroller
{
    /**
     * Retrieve front controller instance
     *
     * @return Zend_Controller_Front
     */
    public function getFrontController()
    {
        if (null === $this->_front) {
            $this->_front = Ifphp_Controller_Front::getInstance();
        }
        return $this->_front;
    }
}