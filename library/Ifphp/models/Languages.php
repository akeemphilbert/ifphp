<?php
/**
 * This file will contain all the functionality to deal 
 * with the Languages interaction with the Languages Table or anything
 * to do with the Languages
 * 
 * @version 0.1
 * @author 
 * @author Akeem Philbert <akeemphilbert@gmail.com>
 * @package Ifphp
 * @subpackage models
 * @copyright 2009 Ifphp
 */


/**
 * @see Ifphp/core/AbstractModel
 */
require_once 'Ifphp/core/AbstractModel.php';

/**
 * This class contains all the db interactions for the Languages
 */
class Languages extends AbstractModel{
	protected $_name = 'languages';
	
	/**
	 * Get all available languages
	 * 
	 * @return Zend_Db_Table_Rowset
	 */
	public function getAll()
	{
		$select = $this->select();
		
		return $this->fetchAll($select);
	}
	
}