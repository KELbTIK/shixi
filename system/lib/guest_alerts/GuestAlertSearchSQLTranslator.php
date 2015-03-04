<?php

class SJB_GuestAlertSearchSQLTranslator extends SJB_SearchSqlTranslator
{
	
	function _getSelectStatement()
	{
		return 'SELECT `' . $this->object_table_prefix . '`.`sid` as `object_sid` ';
	}

	function _getFromStatement($inner_join = '')
	{
		return 'FROM `' . $this->object_table_prefix . '`  ';
	}
	
	function _getGroupStatement()
	{
		return null;
	}
	
}
