<?php
class Ifphp_View_Helper_Date extends Zend_View_Helper_Abstract{
	
	public function Date($date, $format = 'MM.dd.yyyy'){
		$date = new Zend_Date($date);
		return $date->toString($format);
	}
}