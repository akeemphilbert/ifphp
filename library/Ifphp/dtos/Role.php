<?php
/**
 * Role data transfer class
 * 
 * @author Akeem Philbert <akeemphilbert@gmail.com>
 * @copyright IFPHP (c) 2009
 */
class Role extends Zend_Db_Table_Row
{
	const ADMIN = 1;
	const USER = 2;
	const SUBMITTER = 3;
}