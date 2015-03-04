<?php

class SJB_EmailLog extends SJB_Object
{
	function SJB_EmailLog($email_info)
	{
		$this->db_table_name = 'email_log';
		$this->details = new SJB_EmailLogDetails($email_info);
	}
	
	public static function writeToLog($email, $result = false, $error_msg = false)
	{
		$username = '';
		$admin = '';
		if (SJB_Settings::getSettingByName('notification_email') != $email->recipient_email) {
			$username = SJB_UserManager::getUserSIDbyEmail($email->recipient_email);
		}
		if (!$username) {
			$admin = SJB_SubAdminManager::getUserSIDbyEmail($email->recipient_email);
			$admin = $admin?$admin:'admin';
		}
		$status = 'Delivered';
		if (!$result) {
			$status = 'Undelivered';
		}
		elseif ('Not Sent' === $result) {
			$status = $result;
		}
		SJB_DB::query("INSERT INTO `email_log` (`date`, `subject`, `email`, `message`, `username`, `admin`, `status`, `error_msg`) VALUES (NOW(), ?s, ?s, ?s, ?s, ?s, ?s, ?s)", $email->subject, $email->recipient_email, $email->text, $username, $admin, $status, $error_msg);
	}
}


class SJB_EmailLogDetails extends SJB_ObjectDetails 
{
	var $properties;
	var $details;
	
	function SJB_EmailLogDetails($email_info)
	{
		$details_info = self::getDetails();
		foreach ($details_info as $detail_info) {
		    $detail_info['value'] = '';
			if (isset($email_info[$detail_info['id']]))
				$detail_info['value'] = $email_info[$detail_info['id']];
				
			$this->properties[$detail_info['id']] = new SJB_ObjectProperty($detail_info);
		}
	}
	
	public static function getDetails()
	{
		$details =  array (
		    array (
				'id'			=> 'date',
				'caption'		=> 'Date',
				'type'			=> 'date',
				'length'		=> '20',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> null,
			),
			array (
				'id'			=> 'subject',
				'caption'		=> 'Subject',
				'type'			=> 'string',
				'length'		=> '20',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> null,
			),
			array (
				'id'			=> 'email',
				'caption'		=> 'Email',
				'type'			=> 'email',
				'length'		=> '20',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> null,
			),
			array (
				'id'			=> 'message',
				'caption'		=> 'Message',
				'type'			=> 'text',
				'length'		=> '20',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> null,
			),
			array (
				'id'			=> 'username',
				'caption'		=> 'Username',
				'type'			=> 'string',
				'length'		=> '20',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> null,
			),
			array (
				'id'			=> 'admin',
				'caption'		=> 'Admin',
				'type'			=> 'string',
				'length'		=> '20',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> null,
			),
			array (
				'id'			=> 'status',
				'caption'		=> 'Status',
				'type'			=> 'list',
				'list_values'		=> array(
					array('id' => 'Delivered', 
						  'caption' => 'Delivered'),
					array('id' => 'Undelivered', 
						  'caption' => 'Undelivered'),
				),
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> null,
			),
		);
		
		return $details;
	}
}

class SJB_EmailLogManager extends SJB_ObjectManager
{
	public static function getObjectBySID($email_sid)
	{
		$email_info = self::getEmailInfoBySID($email_sid);
		if (!is_null($email_info)) {
			$email = new SJB_EmailLog($email_info);
			$email->setSID($email_info['sid']);
			return $email;
		}
		return null;
	}
	
	public static function getEmailInfoBySID($email_sid)
	{
		return SJB_ObjectDBManager::getObjectInfo("email_log", $email_sid);
	}
}

class SJB_EmailLogCriteriaSaver extends SJB_CriteriaSaver
{
	function SJB_EmailLogCriteriaSaver($searchId = 'EmailSearcher')
	{
		$searchId = 'EmailSearcher_'.$searchId;
		parent::SJB_CriteriaSaver($searchId, new SJB_EmailLogManager);
	}
}

class SJB_EmailLogSearcher extends SJB_Searcher
{
	/**
	 * @var null|\SJB_EmailLogInfoSearcher
	 */
	var $infoSearcher = null;
	
	function SJB_EmailLogSearcher($limit = false, $sorting_field = false, $sorting_order = false)
	{
		$this->infoSearcher = new SJB_EmailLogInfoSearcher($limit, $sorting_field, $sorting_order);
		parent::__construct($this->infoSearcher, new SJB_EmailLogManager);
	}
	
	function getAffectedRows()
	{
		return $this->infoSearcher->affectedRows;
	}
}

class SJB_EmailLogInfoSearcher extends SJB_ObjectInfoSearcher
{
	public $limit = false;
	public $sorting_field = false;
	public $sorting_order = false;
	public $affectedRows = 0;
	
	function SJB_EmailLogInfoSearcher($limit = false, $sorting_field = false, $sorting_order = false)
	{
		parent::__construct('email_log');
		$this->limit = $limit;
		$this->sorting_field = $sorting_field;
		$this->sorting_order = $sorting_order;
	}

	function getObjectInfo($sorting_fields, $inner_join = false, $relevance = false)
	{
		$SearchSqlTranslator = new SJB_EmailLogSearchSQLTranslator($this->table_prefix);
        $sql_string = $SearchSqlTranslator->buildSqlQuery( $this->criteria, $this->valid_criterion_number, array($this->sorting_field => $this->sorting_order));
        SJB_DB::queryExec($sql_string);
		$this->affectedRows = SJB_DB::getAffectedRows();
		if ($this->limit !== false)
			if (isset($this->limit['limit']))
				$sql_string .= " LIMIT " . $this->limit['limit'] . ", ".$this->limit['num_rows'];
			else
				$sql_string .= " LIMIT " . $this->limit . ", 100";
		return SJB_DB::query($sql_string);
	}
}

class SJB_EmailLogSearchSQLTranslator extends SJB_SearchSqlTranslator 
{
	function _getSelectStatement()
	{
		return "SELECT `".$this->object_table_prefix."`.`sid` as `object_sid`, if (`users`.`username` != '', `users`.`username`, if(`subadmins`.`username` != '', `subadmins`.`username`, `email_log`.`admin`)) sorting_username ";
	}
	
	function _getFromStatement($inner_join = false)
	{
		$from_block =  "FROM `{$this->object_table_prefix}` 		
		LEFT JOIN `users` ON `users`.`sid` = `".$this->object_table_prefix."`.`username`
		LEFT JOIN `subadmins` ON `subadmins`.`sid` = `".$this->object_table_prefix."`.`admin` ";
		return $from_block;
	}
	
	function _getSortingStatement($sorting_fields)
	{
		$sorting_block = null;

		if (!empty($sorting_fields)) {
			$sorting_block = " ORDER BY ";
			$delimiter = null;

			foreach($sorting_fields as $sorting_field_name => $sorting_order) {
				if ($sorting_field_name == 'username') {
					$sorting_field_name = 'sorting_username';
					$sorting_block .= $delimiter . $sorting_field_name . " " . $sorting_order;
				}
				else
					$sorting_block .= $delimiter . " `$this->object_table_prefix`.`$sorting_field_name` " . $sorting_order;
				$delimiter = ", ";
			}
		}

		return $sorting_block;
	}
	
	function _getWhereSystemStatement($criteria)
	{
		$where_system_block	= 'WHERE 1 ';
		foreach ($criteria as $property_criteria) {
			$where_AND_block = '';
			foreach ($property_criteria as $criterion) {
				if ($criterion->isValid()) {
					if ($criterion instanceof SJB_NullCriterion ||
							$criterion instanceof SJB_MultiLikeCriterion ||
							$criterion instanceof SJB_SimpleEqual ||
							$criterion instanceof SJB_RelevanceCriterion ||
							$criterion instanceof SJB_TreeCriterion ||
							$criterion instanceof SJB_InSetCriterion ||
							$criterion instanceof SJB_LikeCriterion ||
							$criterion instanceof SJB_MultiLikeANDCriterion) {
						if ($criterion->property_name == 'keywords') {
							$criterion->property_name = 'subject';  
							$where_AND_block .= 'AND (' . $criterion->getSystemSQL() . ' ';
							$criterion->property_name = 'message'; 
							$where_AND_block .= 'OR ' . $criterion->getSystemSQL() . ') ';
						}
						elseif ($criterion->property_name == 'username') {
							$where_AND_block .= "AND (`{$this->object_table_prefix}`.".$criterion->getSystemSQL().' ';
							$where_AND_block .= "OR `users`.".$criterion->getSystemSQL().' ';
							$where_AND_block .= "OR `subadmins`.".$criterion->getSystemSQL().' ';
							$criterion->property_name = 'admin';  
							$where_AND_block .= "OR `{$this->object_table_prefix}`.".$criterion->getSystemSQL().') ';
						}
						else 
							$where_AND_block .= "AND `{$this->object_table_prefix}`.".$criterion->getSystemSQL() . ' ';
					}
					else {
						if ($criterion instanceof SJB_BooleanCriterion) {
							$sql = $criterion->getSystemSQL();
							if ($sql !== null)
								$where_AND_block .= 'AND ' . str_replace('____', "`{$this->object_table_prefix}`.", $criterion->getSystemSQL())  . ' ';
						}
						elseif ($criterion instanceof SJB_CompanyLikeCriterion) {
							$where_AND_block .= 'AND '.$criterion->getSystemSQL().' ';
						}
						else if ($criterion instanceof SJB_AccessibleCriterion || $criterion instanceof SJB_AnyWordsCriterion) {
							$where_AND_block .= 'AND '.$criterion->getSystemSQL($this->object_table_prefix).' ';
						}
						else {
							$where_AND_block .= "AND `{$this->object_table_prefix}`.".$criterion->getSystemSQL().' ';
						}
					}
				}
			}

			if (!empty($where_AND_block))
				$where_system_block .= $where_AND_block;
		}

		return $where_system_block;
	}
}