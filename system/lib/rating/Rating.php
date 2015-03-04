<?php
class SJB_Rating extends SJB_Object
{
	private static $num = 0;
	private static $rating = 0;
	var $width = 0;
	private static $title = 1;//'Please, Vote!';
	
	public static function getRatingNumToListing($listing_sid)
	{
		$result = SJB_DB::query("SELECT COUNT(*) AS `num` , ROUND((SUM(vote) / COUNT(*)), 1) AS rating FROM `rating` WHERE `listing_id` = ?n ", $listing_sid);
		$row = array_shift($result);
		self::$rating = (is_numeric($row['rating'])?$row['rating']:0);
		self::$num = (is_numeric($row['num']) ? $row['num'] : 0);			
		return self::$num;
	}
	
	public static function getRatingToListing()
	{
		return self::$rating;
	}
	
	public static function canRate($listing_sid, $title = false)
	{
		if (SJB_UserManager::isUserLoggedIn()) {
			$user_info = SJB_UserManager::getCurrentUserInfo();
			$user_id = $user_info['sid'];
		}
		else  {
			if ($title)
			    self::$title = 3;//'Please sign in to vote ';
			return false;
		}
		$result = SJB_DB::query("SELECT vote FROM `rating` WHERE `user_id` = {$user_id} AND listing_id = ?n ", $listing_sid);
		if (count($result) == 0){
			if ($title)
			    self::$title = 1;//'Please, Vote!';
			return true;
		}
		
		if ($title)
		    self::$title = 2;//"You've already voted";
		return false;
	}
	
	public static function getRatingTplToListing($listing_sid)
	{
		SJB_Rating::canRate($listing_sid , true);
		return array(
			'rating' => self::$rating,
			'title' => self::$title
		);
	}

	public static function setRaiting($rate, $listing_sid, $user_id)
	{
		if ($rate < 1 || $rate > 5 || !SJB_Rating::canRate($listing_sid))
		    return false;
		SJB_DB::query("INSERT INTO rating VALUES (NULL, '{$user_id}', '$listing_sid', '$rate', NOW())");
		$result = SJB_DB::query("SELECT COUNT(*) AS `num` , ROUND((SUM(vote) / COUNT(*)), 1) AS rating FROM `rating` WHERE `listing_id` = ?n ", $listing_sid);
		$row = array_shift($result);
		return array('rating' => $row['rating'], 'total'=>$row['num']);
	}

	public static function getRatingListing($listing_sid)
	{
		return SJB_DB::query("SELECT id, ctime, r.vote AS vote, u.username , u.sid as user_sid, u.email , u.user_group_sid FROM `rating` as r , users as u WHERE u.sid=user_id AND  r.listing_id = ?n ORDER BY r.ctime DESC", $listing_sid);
	}

	public static function deleteRating($rating_id)
	{
		SJB_DB::query("DELETE FROM `rating`  WHERE id = ?n ", $rating_id);
	}

	public static function deleteRatingByUserSID($user_id)
	{
		return SJB_DB::query("DELETE FROM `rating`  WHERE user_id = ?n ", $user_id);
	}

	public static function getListingSIDByRatingSID($rating_id)
	{
		return SJB_DB::query("SELECT  listing_id FROM `rating`  WHERE id = ?n ", $rating_id);
	}
}