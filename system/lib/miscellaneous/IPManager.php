<?php

class SJB_IPManager
{
	public static function getAllBannedIPs($limit = false, $sort = false) 
	{
		$add_limit = '';
		$add_sorting = '';
		if ($limit)
			$add_limit = " LIMIT {$limit['limit']}, {$limit['num_rows']}";
		if ($sort) 
			$add_sorting = " ORDER BY `{$sort['field']}` {$sort['order']}";
		return SJB_DB::query("SELECT * FROM `banned_ips` {$add_sorting} {$add_limit} ");
	}
	
	public static function makeIPBanned($ip)
	{
		return SJB_DB::query("INSERT INTO `banned_ips` SET `value`=?s", $ip);
	}
	
	public static function makeIPEnabled($id)
	{
		return SJB_DB::query("DELETE FROM `banned_ips` WHERE `id`=?s", $id);
	}
	
	public static function makeIPEnabledByValue($ip) {
		return SJB_DB::query("DELETE FROM `banned_ips` WHERE `value`=?s", $ip);
	}
	
	public static function getBannedIPByValue($ip)
	{
		$sql = SJB_DB::queryValue("SELECT `value` FROM `banned_ips` WHERE `value`=?s", $ip);
		return $sql ? $sql : false;
	}
	
	public static function getBannedIPByID($id)
	{
		$sql = SJB_DB::queryValue("SELECT `value` FROM `banned_ips` WHERE `id`=?s", $id);
		return $sql ? $sql : false;
	}
}