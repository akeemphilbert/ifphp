<?php
/**
 * This file will contain all the functionality to deal 
 * with the Category interaction with the Category Table or anything
 * to do with the category
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
 * This class contains all the db interactions for the category
 */
class Categories extends AbstractModel{
	protected $_name = 'categories';
	
	public function getAll()
	{
		$select = $this->select();
		return $this->fetchAll($select);
	}
	
}