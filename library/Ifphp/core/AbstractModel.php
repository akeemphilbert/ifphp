<?php
/**
 * The purpose was to have a a base where project models 
 * can inherit project dependencies
 * 
 * @author Albert Rosa <rosalbert@gmail.com>
 * @version 0.1
 * @package Ifphp_Model
 *
 */

/**
 * namespace for class
 */

/**
 * @see Zend_Db_Table_Abstract
 */
require_once 'Zend/Db/Table/Abstract.php';

/**
 * @see Zend_Cache
 */
require_once 'Zend/Cache.php';


/**
 * @see Zend_Registry
 */
require_once 'Zend/Registry.php';

/**
 * Class that defines the model dependencies
 * @author albert
 * @author Akeem Philbert <akeemphilbert@gmail.com>
 * @copyright IFPHP (c) 2009
 */
class AbstractModel extends Zend_Db_Table_Abstract{	

	/**
	 * This is overridden to handle cache
	 * 
	 * (non-PHPdoc)
	 * @see library/Zend/Db/Table/Zend_Db_Table_Abstract#fetchRow($where, $order)
	 */
//commented out because there is a problem with the save function on Zend_Db_Table_Row because after save it callse fetchRow without a $where statement (and that causes an error on md5())
//	public function fetchRow($where=null, $order=null)
//	{	
//		$name = $this->_name .'_'. md5($where);
//		
//		$data = $this->loadCache($name);
//		
//		if(!$data)
//		{
//			$data = parent::fetchRow($where, $order);					  
//			$this->saveCache($data, $name);
//		}
//		return $data; 
//	}
	
	/**
	 * This is overridden to handle cache
	 * 
	 * (non-PHPdoc)
	 * @see library_Zend/Db/Table/Zend_Db_Table_Abstract#fetchAll($where, $order, $count, $offset)
	 */
	public function fetchAll($where=null, $order=null, $count=null, $offset=null)
	{
		$name = $this->_name .'_'. md5($where);
		$data = $this->loadCache($name);
		
		if(!$data)
		{
			$data = parent::fetchAll($where, $order, $count, $offset);					  
			$this->saveCache($data, $name);
		}
		return $data; 

	}
	
	/**
	 * This will obtain the cache system
	 * 
	 * @return Zend_Cache
	 */
	private function cache(){
		$config  = new Zend_Config_Xml(APPLICATION_PATH.'/configs/cache.xml',APPLICATION_ENV );
    	
        $cache = Zend_Cache::factory(
        	$config->cache->frontend->name,        	
        	$config->cache->backend->name,
        	$config->cache->frontend->toArray(),        	
        	$config->cache->backend->toArray()
        );
        return $cache;
	}
	
	/**
	 * This will save the results to cache
	 *  
	 *  @use Zend_Config_Xml
	 *  
	 * @param Zend_Db_Table_Row | Zend_Db_Table_Rowset $result Results from db query
	 * @param string $cacheName the cache name 
	 * @return Zend_Db_Table_Row | Zend_Db_Table_RowSet
	 */
	private function saveCache($result, $cacheName)
	{
		$config = new Zend_Config_Ini(APPLICATION_PATH.'/configs/application.ini',APPLICATION_ENV );
		if($config->cache->enabled){		
			$cache = $this->cache();
			$cache->save($result, $cacheName);
		}
	}
	
	/**
	 * This will load the result from the cache if exist
	 * 
	 * @param string $cacheName
	 * @return Zend_Db_Table_Row | Zend_Db_Table_Rowset | False returns False if no data was saved to cache
	 */
	private function loadCache($cacheName)
	{
		$cache = $this->cache();
		$config = new Zend_Config_Ini(APPLICATION_PATH.'/configs/application.ini',APPLICATION_ENV );
				
		if(
			$config->cache->enabled &&
			$data = $cache->load($cacheName))
		{
			Zend_Registry::getInstance()->logger->info($cacheName . ': loaded from cache');
			return $data;
		}
		else
		{
			Zend_Registry::getInstance()->logger->info($cacheName . ': not loaded from cache');
			return false;
		}
	}
	
	
	/**
	 * Returns the table row based on the id passed
	 * 
	 * @param string|int $id the id to search for
	 * @return Zend_DbTable_Row
	 */
	public function getById($id)
	{
		$select = $this->select()->where('id = ?',$id);
		return AbstractModel::fetchRow($select);
	}
	
	/**
	 * This will clear the cache by the id 
	 * or completely if nothing is passed in
	 * 
	 * @param string $cacheId the cache id to clear out
	 * @return void
	 */
	public function clear($cacheId=null)
	{
		$cache = $this->cache();
		if($cacheId)
		{
			$cache->remove($cacheId);
		}
		else
		{
			$cache->clean(Zend_Cache::CLEANING_MODE_ALL);	
		}
	}
	
	/**
	 * 
	 * 
	 * (non-PHPdoc)
	 * @see Db/Table/Zend_Db_Table_Abstract#insert($data)
	 */
 	public function insert(array $data)
    {
        // add a timestamp
        if (empty($data['created'])) {
            $data['created'] = time();
        }
        return parent::insert($data);
    }
    
	/**
	 * (non-PHPdoc)
	 * @see Db/Table/Zend_Db_Table_Abstract#update($data, $where)
	 */
    public function update(array $data, $where)
    {
        // add a timestamp
        if (empty($data['modified'])) {
            $data['modified'] = time();
        }
        return parent::update($data, $where);
    }
    
}