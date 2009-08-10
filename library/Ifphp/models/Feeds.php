<?php
/**
 * This file will contain all the functionality to deal 
 * with the Feeds interaction with the Feeds Table or anything
 * to do with the Feed
 * 
 * @version 0.1
 * @author 
 * @package Ifphp
 * @subpackage models
 * @copyright 2009 Ifphp
 */

/**
 * @see Ifphp/core/AbstractModel
 */
require_once 'Ifphp/core/AbstractModel.php';

/**
 * This class contains all the db interactions for the Feed
 */
class Feeds extends AbstractModel{
	protected $_name = 'feeds';
	
	
	/**
	 * Returns the complete Select statement for a basic feed
	 * 
	 * @return Zend_Db_Table_Select
	 */
	public function getSelect(){
		$select = $this->select()->setIntegrityCheck(false)->from(array('feeds'=>'feeds'));		
		$select->join(array('categories'=>'categories'), 'feeds.categoryId = categories.id',
			array('category'=>'title'));
		$select->join(array('languages'=>'languages'),'feeds.languageId = languages.id', 
			array('language'=>'languages.title'));
			
		return $select;
		
	}
	
	/**
	 * Returns the feeds that relate to a specific category
	 * 
	 * @param int $categoryId
	 * @return Zend_Db_Table_RowSet
	 */
	public function getByCategory($categoryId){
		
		$select = $this->getSelect();
		$select->where('categories.id = ?', $categoryId);

		return $this->fetchAll($select);
		
	}
	
	/**
	 * Returns all the Feeds if a where is defined it will filter for the
	 * Feeds that match that criteria
	 * 
	 * @param Zend_Db_Table_Select $where
	 * @return Zend_Db_Table_RowSet
	 */
	public function getAll($where = null){
		$select = $this->getSelect();
		
		if($where){
			$select->where($where);
		}
		
		return $this->fetchAll($select);	
	}
	
}