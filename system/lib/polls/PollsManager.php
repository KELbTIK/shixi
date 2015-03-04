<?php

class SJB_PollsManager extends SJB_Object
{
	function SJB_PollsManager($poll_info)
	{
		$this->db_table_name = 'polls';
		$this->details = new SJB_PollsDetails($poll_info);
		$this->field_type = isset($poll_info['type']) ? $poll_info['type'] : null;
		$this->order = isset($poll_info['order']) ? $poll_info['order'] : null;
	}
	
	public static function getPollsInfo($sorting_field, $sorting_order, $limit, $num_rows, &$pollsCount)
	{
		$order = 'ORDER BY ';
		switch ($sorting_field) {
			case 'user_group':
				$order .= " user_group_sid {$sorting_order}";
				break;
			case 'status':
				$order .= " `active` {$sorting_order}";
				break;
			default:
				$order .= " {$sorting_field}  {$sorting_order}"; 
				break;
		}
		$pollsCount = count(SJB_DB::query('SELECT * FROM `polls`'));
		$result = SJB_DB::query("SELECT * FROM `polls` {$order} LIMIT {$limit}, {$num_rows}");
		$polls = array();
		foreach ($result as $key => $val) {
			$polls[$key] = $val;
			if (!empty($val['user_group_sid'])) {
				$polls[$key]['user_group'] = SJB_UserGroupManager::getUserGroupNameBySID($val['user_group_sid']);
			} else {
				$polls[$key]['user_group'] = 'All';
			}
		}
		return $polls;
	}
	
	public static function getPollInfoBySID($sid)
	{
		return SJB_ObjectDBManager::getObjectInfo('polls', $sid);
	}
	
	public static function savePoll($poll)
	{
		return SJB_ObjectDBManager::saveObject('polls', $poll);
	}
	
	public static function deletePollBySID($sid)
	{
		if (SJB_ObjectDBManager::deleteObjectInfoFromDB('polls', $sid)) {
			SJB_DB::query('DELETE FROM `polls_field_list` WHERE `field_sid` = ?n', $sid);
			SJB_DB::query('DELETE FROM `polls_results` WHERE `poll_sid` = ?n', $sid);
		}
	}
	
	public static function getPollsForDisplay()
	{
		return  SJB_DB::query('SELECT * FROM `poll` WHERE `active`=1 ORDER BY `order`');
	}
	
	public static function getFieldBySID($sid)
	{
		$poll_info = SJB_PollsManager::getPollInfoBySID($sid);
		if (empty($poll_info)) {
			return null;
		}
		$poll = new SJB_PollsManager($poll_info);
		$poll->setSID($sid);
		return $poll;
	}
	
	public static function getFieldInfoBySID($sid)
	{
		return SJB_ObjectDBManager::getObjectInfo('polls', $sid);
	}
	
	public static function activatePollBySID($sid)
	{
		return SJB_DB::query('UPDATE `polls` SET `active`=1 WHERE `sid`=?n', $sid);
	}
	
	public static function deactivatePollBySID($sid)
	{
		return SJB_DB::query('UPDATE `polls` SET `active`=0 WHERE `sid`=?n', $sid);
	}
	
	public static function isActive($sid, $userGroupSID, $langId)
	{
		$poll = SJB_DB::queryValue("SELECT `active` FROM `polls` WHERE `sid` = ?n AND `start_date` <= NOW() AND (`end_date` IS NULL OR `end_date` >= NOW()) AND (`user_group_sid` = '' OR `user_group_sid` = ?n) AND (`language` = '' OR `language` = ?s)", $sid, $userGroupSID, $langId);
		if ($poll)
			return $poll;
		return false;
	}
	
	public static function getPollForDisplay($userGroupSID, $langId)
	{
		$poll = SJB_DB::queryValue("SELECT `sid` FROM `polls` WHERE `active` = '1' AND `start_date` <= NOW() AND (`end_date` IS NULL OR `end_date` >= NOW()) AND (`user_group_sid` = '' OR `user_group_sid` = ?n) AND (`language` = '' OR `language` = ?s) ORDER BY RAND() LIMIT 1", $userGroupSID, $langId);
		if ($poll)
			return $poll;
		return false;
	}
	
	public static function isVoted($sid, $IP)
	{
		return SJB_DB::queryValue('SELECT count(*) FROM `polls_results` WHERE `poll_sid`=?n AND `IP`=?s',$sid, $IP);
	}
	
	public static function addPollResult($sid, $value, $IP)
	{
		return SJB_DB::query('INSERT INTO `polls_results` (`poll_sid`, `value`, `IP`) VALUES (?n, ?n, ?s)', $sid, $value, $IP);
	}
	
	public static function getCountVotesBySID($sid)
	{
		return SJB_DB::queryValue('SELECT count(*) FROM `polls_results` `pr`
			INNER JOIN `polls_field_list` `pfl` ON `pr`.`value` = `pfl`.`sid` AND `pfl`.`field_sid` = `pr`.`poll_sid`
			WHERE `pr`.`poll_sid` = ?n', $sid);
	}
	
	public static function getPollResultsBySID($sid)
	{
		return SJB_DB::query('SELECT count(pr.`poll_sid`) as `count`, pr.`value`, pfl.`value` as `question`
							 FROM `polls_field_list` `pfl`
							 LEFT JOIN `polls_results` `pr`  ON `pfl`.`sid` = `pr`.`value` AND `pfl`.`field_sid` = `pr`.`poll_sid`
							 WHERE `pfl`.`field_sid` = ?n
							 GROUP BY `pfl`.`sid` ORDER BY `order` ASC', $sid);
	}
}

class SJB_UserPollsManager extends SJB_Object
{
	function SJB_UserPollsManager($poll_info, $sid = 0)
	{
		$this->db_table_name = 'polls';
		$this->details = new SJB_UserPollsDetails($poll_info);
		$this->field_type = isset($poll_info['type']) ? $poll_info['type'] : null;
		$this->order = isset($poll_info['order']) ? $poll_info['order'] : null;
	}
	
	public static function getListValuesBySID($sid)
	{
		$values = SJB_DB::query('SELECT `sid`, `value` FROM `polls_field_list` WHERE `field_sid`=?n ORDER BY `order`', $sid);
		$field_values = array();
		
		foreach ($values as $value) {
			$field_values[] = array('id' => $value['sid'], 'caption' => $value['value']);
		}
		return $field_values;
	}
	
	public static function getValueBySID($sid)
	{
		return SJB_DB::queryValue('SELECT `question` FROM `polls` WHERE `sid`=?n', $sid);
	}
}

class SJB_UserPollsDetails extends SJB_ObjectDetails
{
	function SJB_UserPollsDetails($poll_info)
	{
		$details_info = SJB_UserPollsDetails::getDetails($poll_info);
		
		foreach ($details_info as $detail_info) {
			if (isset($poll_info[$detail_info['id']])) {
				$detail_info['value'] = $poll_info[$detail_info['id']];
			} else 
				$detail_info['value'] = isset($detail_info['value']) ? $detail_info['value'] : '';
			$this->properties[$detail_info['id']] = new SJB_ObjectProperty($detail_info);
		}
	}
	
	public static function getDetails($poll_info)
	{
		return array(
				array
				(
					'id'		=> 'poll',
					'caption'	=> SJB_UserPollsManager::getValueBySID($poll_info['sid']), 
					'value'     => '',
					'type'		=> 'list',
					'length'	=> '20',
					'list_values' => SJB_UserPollsManager::getListValuesBySID($poll_info['sid']),
					'is_system'	=> true,
				)
		 );		
	}
}

class SJB_PollsDetails extends SJB_ObjectDetails
{
	
	function SJB_PollsDetails($poll_info)
	{
		$details_info = SJB_PollsDetails::getDetails($poll_info);
		foreach ($details_info as $detail_info) {
			if (isset($poll_info[$detail_info['id']])) {
				$detail_info['value'] = $poll_info[$detail_info['id']];
			} else 
				$detail_info['value'] = isset($detail_info['value']) ? $detail_info['value'] : '';
			$this->properties[$detail_info['id']] = new SJB_ObjectProperty($detail_info);
		}
	}
	
	public static function getDetails()
	{
		$i18n = SJB_I18N::getInstance();
		return array(
				array
				(
					'id'		=> 'language',
					'caption'	=> 'Language',
					'type'		=> 'list',
					'length'	=> '20',
					'list_values' => $i18n->getActiveFrontendLanguagesData(),
					'is_required'=> false,
					'is_system'	=> true,
				),
				array
				(
					'id'		=> 'question',
					'caption'	=> 'Question', 
					'type'		=> 'text',
					'length'	=> '20',
					'is_required'=> true,
					'is_system'	=> true,
				),
				array
				(
					'id'		=> 'user_group_sid',
					'caption'	=> 'User Group', 
					'type'		=> 'list',
					'length'	=> '20',
					'list_values' => SJB_UserGroupManager::getAllUserGroupsIDsAndCaptions(),
					'is_required'=> false,
					'is_system'	=> true,
				),
				array
				(
					'id'		=> 'start_date',
					'caption'	=> 'Start Date', 
					'type'		=> 'date',
					'length'	=> '20',
					'is_required'=> true,
					'is_system'	=> true,
				),
				array
				(
					'id'		=> 'end_date',
					'caption'	=> 'End Date', 
					'type'		=> 'date',
					'length'	=> '20',
					'comment' 	=> 'Leave this field empty to set Never Expire',
					'is_required'=> false,
					'is_system'	=> true,
				),
				array
				(
					'id'		=> 'display_results',
					'caption'	=> 'Display Results for Users', 
					'type'		=> 'boolean',
					'length'	=> '20',
					'is_required'=> false,
					'is_system'	=> true,
				),
				array
				(
					'id'		=> 'show_total_votes',
					'caption'	=> 'Show Total Votes', 
					'type'		=> 'boolean',
					'length'	=> '20',
					'is_required'=> false,
					'is_system'	=> true,
				),
		 );		
	}
}

class SJB_PollsEditListController extends SJB_EditListController
{
	function SJB_PollsEditListController($input_data, $poll_info)
	{
		parent::SJB_EditListController($input_data, new SJB_PollsManager($poll_info), new SJB_PollsListItemManager);
	}
}

class SJB_PollsListItemManager extends SJB_ListItemManager
{
	function SJB_PollsListItemManager()
	{
		$this->table_prefix = 'polls';
	}
}

class SJB_PollsDisplayListController extends SJB_DisplayListController
{
	function SJB_PollsDisplayListController($input_data, $poll_info)
	{
		parent::SJB_DisplayListController($input_data, new SJB_PollsManager($poll_info), new SJB_PollsListItemManager);
		$this->field_info = $poll_info;
	}
}
