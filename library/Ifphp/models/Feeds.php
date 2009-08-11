<?php
/**
 * This file will contain all the functionality to deal 
 * with the Feeds interaction with the Feeds Table or anything
 * to do with the Feed
 * 
 * @version 0.1
 * @author Akeem Philbert <akeemphilbert@gmail.com>
 * @package Ifphp
 * @subpackage models
 * @copyright 2009 Ifphp
 */

/**
 * @see Ifphp/core/AbstractModel
 */
require_once 'Ifphp/core/AbstractModel.php';
require_once 'Ifphp/dtos/Feed.php';

/**
 * This class contains all the db interactions for the Feed
 */
class Feeds extends AbstractModel
{
	protected $_name = 'feeds';
	protected $_rowClass = 'Feed';
	
	/**
	 * Get all available categories
	 * 
	 * @return Zend_Db_Table_Rowset
	 */
	public function getAll($page=1,$limit=0)
	{
		$select = $this->select();
		return $this->fetchAll($select,null,$page,$limit);
	}
	
	/**
	 * Get Feed by Id
	 * 
	 * @param $id
	 * @return Feed
	 */
	public function getById($id)
	{
		$select = $this->select();
		$select->where('id = ?',$id);
		return $this->fetchRow($select);
	}

    /**
     * Get feed by site url
     *
     * @param string $url
     * @return Feed
     */
    public function getBySiteUrl($url)
    {
        $select = $this->select();
        $select->where('siteUrl = ?',$url);
        return $this->fetchRow($select);
    }
	
}