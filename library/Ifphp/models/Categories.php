<?php
/**
 * This file will contain all the functionality to deal 
 * with the Category interaction with the Category Table or anything
 * to do with the category
 * 
 * @version 0.1
 * @author 
 * @author Akeem Philbert <akeemphilbert@gmail.com>
 * @package Ifphp
 * @subpackage models
 * @copyright (c) 2009 Ifphp
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
        protected $_dependentTables = array('Feeds');
	
	/**
	 * Get all available categories
	 * 
	 * @return Zend_Db_Table_Rowset
	 */
	public function getAll()
	{
		$select = $this->select();
		return $this->fetchAll($select);
	}

        /**
         * Get category by slug
         * @param string $slug
         * @return Category
         */
        public function getBySlug($slug)
        {
            $select = $this->select();
            $select->where('slug = ?',$slug);
            return $this->fetchRow($select);
        }
	
}