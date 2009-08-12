<?php
/**
 * This file will contain all the functionality to deal 
 * with the Posts interaction with the Posts Table or anything
 * to do with the Posts
 * 
 * @version 0.1
 * @author Akeem Philbert <akeemphilbert@gmail.com>
 * @author Albert Rosa <rosalbert@gmail.com>
 * @package Ifphp
 * @subpackage models
 * @copyright 2009 Ifphp
 */


/**
 * @see Ifphp/core/AbstractModel
 */
require_once 'Ifphp/core/AbstractModel.php';
require_once 'Ifphp/dtos/Post.php';

/**
 * This class contains all the db interactions for the Posts
 */
class Posts extends AbstractModel
{
	protected $_name = 'posts';
	protected $_rowClass = 'Post';
	
	/**
	 * Get posts by feed id
	 * 
	 * @param integer $feedId
	 * @param integer $page
	 * @param integer $limit
	 * @return Zend_Db_Table_Rowset
	 */
	public function getByFeedId($feedId,$page=1,$limit=0)
	{
		$select = $this->select();
		$select->where('feedId = ?',$feedId);
		return $this->fetchAll($select,$page,$limit);
	}

    /**
     * Get recent posts
     *
     * @param integer $page
     * @param integer $limit
     */
    public function getRecent($page=1,$limit=0)
    {
        $select = $this->select();
        $select->order('publishDate DESC');
        return $this->fetchAll($select,$page,$limit);
    }
}