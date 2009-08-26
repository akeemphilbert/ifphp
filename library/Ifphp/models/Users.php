<?php
/**
 * This file will contain all the functionality to deal 
 * with the Users interaction with the Users Table or anything
 * to do with the user
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

/**
 * This class contains all the db interactions for the user
 */
class Users extends AbstractModel
{
	protected $_name = 'users';

    /**
     * Get user by email
     *
     * @param string $email
     * @return User
     */
    public function getByEmail($email)
    {
        $select = $this->select();
        $select->where('email = ?', $email);
        return $this->fetchRow($select);
    }
}