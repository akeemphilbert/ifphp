<?php
/**
 * PHP Template.
 
 $request = Zend_Controller_Front::getInstance()->getRequest();
$this->headTitle($request->getActionName())
     ->headTitle($request->getControllerName());
*/
class Ifphp_View_Helper_GetController extends Zend_View_Helper_Abstract{
	
	public function GetController(){
             return Zend_Controller_Front::getInstance()->getRequest()->getControllerName(); 
	}
}