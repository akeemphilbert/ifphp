<?php
/**
 * This file will contain all the functionality to deal 
 * with the Supports interaction with the Supports Table or anything
 * to do with the Supports
 * 
 * @version 0.1
 * @author Albert Rosa <rosalbert@gmail.com>
 * @package Ifphp
 * @subpackage models
 * @copyright 2009 Ifphp
 */


/**
 * @see Ifphp/core/AbstractModel
 */
require_once 'Ifphp/core/AbstractModel.php';

/**
 * This class contains all the db interactions for the Supports
 */
class Supports extends AbstractModel{
	protected $_name = 'supports';
	
	/**
	 * This is a rudementary search by title only
	 * 
	 * @param string $term the term to search for
	 * @return Zend_Db_Table_Rowset
	 */
	public function search($term)
	{
		$select = $this->select()->where('title LIKE ?', '%'.$term.'%');
		
		Zend_Registry::getInstance()->logger->info($select->__toString());
		
		return $this->fetchAll($select);
	}		
	
}