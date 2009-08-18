<?php

class Status extends Zend_Db_Table_Rowset
{
	const ACTIVE = 1;
    const PENDING = 2;
    const INACTIVE = 3;
}