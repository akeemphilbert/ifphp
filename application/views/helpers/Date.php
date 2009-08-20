<?php
/**
 * View Helper to display date
 *
 * @author Albert Rosa <rosalbert@gmail.com>
 * @copyright IFPHP (c) 2009
 */

class Ifphp_View_Helper_Date extends Zend_View_Helper_Abstract{
	
	public function Date($date, $format = 'MM.dd.yyyy'){
		$date = new Zend_Date($date);
		return $date->toString($format);
	}
}