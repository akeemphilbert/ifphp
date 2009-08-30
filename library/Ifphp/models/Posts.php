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
require_once 'Ifphp/dtos/Status.php';

/**
 * This class contains all the db interactions for the Posts
 */
class Posts extends AbstractModel
{
    protected $_name = 'posts';
    protected $_rowClass = 'Post';
    protected $_referenceMap = array(
        'Feed'=>array(
            'columns'   => 'feedId',
            'refTableClass' =>  'Feeds',
            'refColumns'    =>  'id'
        )
    );

    /**
     * Get posts by feed id
     *
     * @param integer $feedId
     * @param integer $page
     * @param integer $limit
     * @return Zend_Db_Table_Rowset
     */
    public function getByFeedId($feedId,$page=1,$limit=0,$isCount=false)
    {
        $select = $this->select();
        $select->where('feedId = ?',$feedId);
        $columns = $isCount ? array('total'=>'COUNT(posts.id)') : '*';
        $feedColumns = $isCount ? array() : array('feedTitle'=>'title','siteUrl','feedSlug'=>'slug','feedDescription'=>'description');

        $select->from('posts',$columns);
        $select->setIntegrityCheck(false);
        $select->join('feeds','feeds.id = posts.feedId',$feedColumns);
        $select->where('feeds.statusId = ?',Status::ACTIVE);
        $select->order('posts.publishDate DESC');
        
        if ($limit)
        $select->limitPage($page, $limit);
        return $isCount ? $this->fetchRow($select,null,1,0) : $this->fetchAll($select);
    }

    /**
     * Get recent posts
     *
     * @param integer $page
     * @param integer $limit
     */
    public function getRecent($page=1,$limit=0,$isCount=false)
    {
        $select = $this->select();
        $columns = $isCount ? array('total'=>'COUNT(posts.id)') : '*';
        $feedColumns = $isCount ? array() : array('feedTitle'=>'title','siteUrl','feedSlug'=>'slug','feedDescription'=>'description');
        $select->from('posts',$columns);

        $select->setIntegrityCheck(false);
        $select->join('feeds','feeds.id = posts.feedId',$feedColumns);
        $select->where('feeds.statusId = ?',Status::ACTIVE);
        $select->order('posts.publishDate DESC');
        if ($limit)
        $select->limitPage($page, $limit);
        return $isCount ? $this->fetchRow($select,null,1,0) : $this->fetchAll($select);
    }

    /**
     * Get posts by category
     *
     * @param integer $category
     * @param integer $page
     * @param integer $limit
     * @return Zend_Db_Table_Rowset
     */
    public function getByCategory($category,$page=1,$limit=0,$isCount=false)
    {
        $select = $this->select()->setIntegrityCheck(false);
        $columns = $isCount ? array('total'=>'COUNT(posts.id)') : '*';
        $feedColumns = $isCount ? array() : array('feedTitle'=>'title','siteUrl','feedSlug'=>'slug','feedDescription'=>'description');
        $select->from('posts',$columns);
        $select->join('feeds','feeds.id = posts.feedId',$feedColumns);
        $select->where('feeds.statusId = ?',Status::ACTIVE);
        $select->join('categories','categories.id = feeds.categoryId',array());
        $select->where('categories.id = ?',$category);
        $select->order('posts.publishDate DESC');
        if ($limit)
        $select->limitPage($page, $limit);
        return $isCount ? $this->fetchRow($select,null,1,0) : $this->fetchAll($select);
    }

    /**
     * Get posts by language
     *
     * @param integer $language
     * @param integer $page
     * @param integer $limit
     * @return Zend_Db_Table_Rowset
     */
    public function getByLanguage($language,$page=1,$limit=0,$isCount=false)
    {
        $select = $this->select()->setIntegrityCheck(false);
        $columns = $isCount ? array('total'=>'COUNT(posts.id)') : '*';
        $feedColumns = $isCount ? array() : array('feedTitle'=>'title','siteUrl','feedSlug'=>'slug','feedDescription'=>'description');
        $select->from('posts',$columns);
        $select->join('feeds','feeds.id = posts.feedId',$feedColumns);
        $select->where('feeds.statusId = ?',Status::ACTIVE);
        $select->join('languages','languages.id = feeds.languageId',array());
        $select->where('languages.id = ?',$language);
        $select->order('posts.publishDate DESC');
        if ($limit)
        $select->limitPage($page, $limit);
        return $isCount ? $this->fetchRow($select,null,1,0) : $this->fetchAll($select);
    }

    /**
     * Get feed by link
     *
     * @param string $link
     * @return Post
     */
    public function getByLink($link)
    {
        $select = $this->select();
        $select->where('link = ?',$link);
        return $this->fetchRow($select);
    }
    
    /**
    * @todo need to figiure out a way to clear only Post cache by IDs
    *
    */    
        public function clearCache(){
            
        }
}