<?php
class Ifphp_View_Helper_Translate extends Zend_View_Helper_Abstract{
	
	
	
	public function Translate($text){
		$translated = $text;
		
		try{
    		$locale = new Zend_Locale(Zend_Locale::BROWSER);                                
		    $translate = Zend_Registry::get('translate');
    		
		if (!$translate->isAvailable($locale)) {
    		$translate->setLocale(Zend_Locale::getDefault());
		} 
		                                   
		      $translated = $translate->_($text);
		} catch (Exception $e) {
		    Zend_Registry::get('logger')->err($e->getMessage());
		}
		
		return $translated;
	}
	
	
}