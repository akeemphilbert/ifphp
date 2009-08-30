<?php
/**
 * PHP Template.
 
 $request = Zend_Controller_Front::getInstance()->getRequest();
$this->headTitle($request->getActionName())
     ->headTitle($request->getControllerName());
*/
class Ifphp_View_Helper_GetAction extends Zend_View_Helper_Abstract{
	
	public function GetAction(){
             return Zend_Controller_Front::getInstance()->getRequest()->getActionName();
	}
}