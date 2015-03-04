<?php

class SJB_Applications
{
    const APPLICATION_SETTINGS_TYPE_EMAIL = 1;
    const APPLICATION_SETTINGS_TYPE_URL   = 2;

	public static function getById($id)
    {
        $res = SJB_DB::query("select * from applications where id = ?s", $id);
        if (count($res) > 0)
            return array_shift($res);
        return false;
    }

	public static function getByJob($listingID, $orderInfo = false, $score = false, $limit = false)
	{
		$order = SJB_Applications::generateOrderAndJoin($orderInfo);

		$limitFilter = '';
		if (!empty($limit)) {
			$limitFilter = "LIMIT {$limit['startRow']}, {$limit['countRows']}";
		}

		$scoreFilter['case'] = '';
		$scoreFilter['inner_join'] = '';
		$scoreFilter['score'] = '';
		if (!empty($score)) {
			$scoreFilter = self::getScoredApplications($score);
		}

		$apps = SJB_DB::query("
		SELECT `a`.*
			{$scoreFilter['case']}
		FROM `applications` `a`
			INNER JOIN `listings` l ON
				`l`.`sid` = `a`.`listing_id`
				{$scoreFilter['inner_join']}
				{$order['join']}
				WHERE `a`.`listing_id` = ?s AND`a`.`show_emp` = 1 {$scoreFilter['score']} {$order['order']} {$limitFilter}", $listingID);
		return $apps;
	}

	public static function getCountAppsByJob($listingID, $score = false)
	{
		$scoreFilter['case'] = '';
		$scoreFilter['inner_join'] = '';
		$scoreFilter['score'] = '';
		if (!empty($score)) {
			$scoreFilter = self::getScoredApplications($score);
		}

		$appsCount = SJB_DB::queryValue("
		SELECT
			COUNT(`a`.`listing_id`)
			{$scoreFilter['case']}
		FROM
			`applications` `a`
		INNER JOIN `listings` l ON
			`l`.`sid` = `a`.`listing_id`
			{$scoreFilter['inner_join']}
		WHERE `a`.`listing_id` = ?s AND `a`.`show_emp` = 1 {$scoreFilter['score']}", $listingID);

		return $appsCount;
	}

    public static function getByJobseeker($id, $orderInfo = false)
    {
        $order = SJB_Applications::generateOrderAndJoin($orderInfo);
        return SJB_DB::query("select a.* from `applications` a  {$order['join']} where a.`jobseeker_id` = ?s and `show_js` = 1 {$order['order']}", $id);
    }

    public static function generateOrderAndJoin($orderInfo = false)
    {
        $result['order'] = '';
        $result['join'] = '';
        if (isset($orderInfo['inner_join'])) {
            $result['join'] = " LEFT JOIN {$orderInfo['inner_join']['table']} ON  `{$orderInfo['inner_join']['table']}`.`{$orderInfo['inner_join']['field1']}`=a.`{$orderInfo['inner_join']['field2']}`";
            if (isset($orderInfo['sorting_field']))
                $result['order'] = " ORDER BY `{$orderInfo['inner_join']['table']}`.`{$orderInfo['sorting_field']}` {$orderInfo['sorting_order']}";
            if (isset($orderInfo['inner_join2'])) {
                $result['join'] .= " LEFT JOIN {$orderInfo['inner_join2']['table1']} ON  `{$orderInfo['inner_join2']['table1']}`.`{$orderInfo['inner_join2']['field1']}`=`{$orderInfo['inner_join2']['table2']}`.`{$orderInfo['inner_join2']['field2']}`";
                if (isset($orderInfo['sorting_field']))
                    $result['order'] = " ORDER BY `{$orderInfo['inner_join2']['table1']}`.`{$orderInfo['sorting_field']}` {$orderInfo['sorting_order']}";
            }
        }
        else {
            if (isset($orderInfo['sorting_field']))
                $result['order'] = 'ORDER BY a.`'.$orderInfo['sorting_field'].'` '.$orderInfo['sorting_order'];
            elseif (isset($orderInfo['sorting_fields']))
                $result['order'] = " ORDER BY a.`{$orderInfo['sorting_fields']['field1']}` a.`{$orderInfo['sorting_fields']['field2']}` {$orderInfo['sorting_order']}";
        }

        return $result;
    }

	public static function getByEmployer($userSID, $orderInfo, $score = false, $subuser = false, $limit = false)
	{
		$order = SJB_Applications::generateOrderAndJoin($orderInfo);
		$subuserFilter = '';
		if ($subuser !== false) {
			$subuserFilter = ' and `l`.`subuser_sid` = ' . SJB_DB::quote($subuser);
		}

		$limitFilter = '';
		if (!empty($limit)) {
			$limitFilter = "LIMIT {$limit['startRow']}, {$limit['countRows']}";
		}

		$scoreFilter['case'] = '';
		$scoreFilter['inner_join'] = '';
		$scoreFilter['score'] = '';
		if (!empty($score)) {
			$scoreFilter = self::getScoredApplications($score);
		}

		$apps = SJB_DB::query("
			SELECT `a`.*
				{$scoreFilter['case']}
			FROM
				`applications` `a`
			INNER JOIN `listings` l ON
				`l`.`sid` = `a`.`listing_id`
				{$scoreFilter['inner_join']}
				{$order['join']}
			WHERE `l`.`user_sid` = ?s AND `a`.`show_emp` = 1 {$scoreFilter['score']} {$subuserFilter} {$order['order']} {$limitFilter}", $userSID);
		return $apps;
	}

	public static function getCountApplicationsByEmployer($userSID, $score = false, $subuser = false)
	{
		$subuserFilter = '';
		if ($subuser !== false) {
			$subuserFilter = ' and `l`.`subuser_sid` = ' . SJB_DB::quote($subuser);
		}

		$scoreFilter['case'] = '';
		$scoreFilter['inner_join'] = '';
		$scoreFilter['score'] = '';
		if (!empty($score)) {
			$scoreFilter = self::getScoredApplications($score);
		}

		$appsCount = SJB_DB::queryValue("
			SELECT COUNT(`a`.`listing_id`)
				{$scoreFilter['case']}
			FROM
				`applications` `a`
			INNER JOIN `listings` l ON
				`l`.`sid` = `a`.`listing_id`
				{$scoreFilter['inner_join']}
			WHERE `l`.`user_sid` = ?s AND `a`.`show_emp` = 1 {$scoreFilter['score']}{$subuserFilter} ", $userSID);
		return $appsCount;
	}

	/**
	 * @param $score
	 */
	public static function getScoredApplications($score)
	{
		$scoreFilter['case'] = "
			,CASE `s`.`passing_score`
				WHEN 'acceptable' THEN 1
				WHEN 'good' THEN 2
				WHEN 'very_good' THEN 3
				WHEN 'excellent' THEN 4
				END
				";

		$scoreFilter['inner_join'] = "
		inner join `screening_questionnaires` `s` on
			`l`.`screening_questionnaire` = `s`.`sid`
		";

		if ($score == 'passed') {
			$scoreFilter['score'] = "
				and `s`.`passing_score` <= `a`.`score`
				";
		} elseif ($score == 'not_passed') {
			$scoreFilter['score'] = "
				and `s`.`passing_score` > `a`.`score`
				";
		}
		return $scoreFilter;
	}

	public static function getBySID($sid)
	{
		$apps = SJB_DB::query("
			SELECT
				`a`.*
			FROM
				`applications` a
			INNER JOIN `listings` l ON
					`l`.`sid` = `a`.`listing_id`
			WHERE a.`id` = ?n AND a.`show_emp` = 1 ", $sid);
		$apps = $apps?array_pop($apps):array();
		return $apps;
	}

    public static function getAppGroupsByEmployer($companyId)
    {
        return SJB_DB::query("
            select a.listing_id, a.id, count(*) as count from `applications` a
            inner join `listings` l on
                 `l`.`sid` = `a`.`listing_id`
            where `user_sid` = ?s and `show_emp` = 1 GROUP BY `a`.`listing_id`", $companyId);
    }

    /**
     * Is user applied to job posting
     *
     * @param int $listing_id
     * @param int $jobseeker_id
     * @return bool
     */
    public static function isApplied($listing_id, $jobseeker_id)
    {
        // if anonymous user - return false (it not applied)
        if (!$jobseeker_id)
            return false;

        return count(SJB_DB::query("select * from applications where listing_id = ?s and jobseeker_id = ?s", $listing_id, $jobseeker_id)) > 0;
    }

	public static function isListingAppliedForCompany($listing_id, $company_id)
    {
        return count(SJB_DB::query("
            SELECT a. * , l.user_sid FROM `applications` a
            INNER JOIN `listings` l ON l.sid = a.`listing_id`
            WHERE user_sid = ?s AND resume_id = ?s", $company_id, $listing_id)) > 0;
    }

	public static function isUserOwnerApps($user_sid, $apps_sid)
    {
        return count (SJB_DB::query("
            SELECT a. * , l.user_sid FROM `applications` a
            INNER JOIN `listings` l ON l.sid = a.`listing_id`
            WHERE l.user_sid = ?n AND id = ?n", $user_sid, $apps_sid)) > 0;
    }
    
    /**
     * Check if user owns applications By AppJobId 
     *
     * @param int $user_sid
     * @param int $apps_sid
     * @return unknown
     */
	public static function isUserOwnsAppsByAppJobId($user_sid, $app_job_id)
    {
        return count(SJB_DB::query("
            SELECT a. * , l.user_sid FROM `applications` a
            INNER JOIN `listings` l ON l.sid = a.`listing_id`
            WHERE l.user_sid = ?n AND a.listing_id = ?n", $user_sid, $app_job_id)) > 0;
    }

    /**
     * Creates new application
     *
     * @param int $listing_id
     * @param int $jobseeker_id
     * @param int|string $resume
     * @param string $type
     * @return Application|bool
     */
	public static function create($listing_id, $jobseeker_id, $resume, $comments, $file, $mimeType, $file_sid, $anonymous, $notRegisteredUserData = false, $questionnaire = '', $score = 0)
    {
        if (SJB_Applications::isApplied($listing_id, $jobseeker_id) && !is_null($jobseeker_id))
            return false;

        $file_id = '';
        if ($file_sid != '')
            $file_id = SJB_DB::queryValue("SELECT `id` FROM `uploaded_files` WHERE `sid` = ?s", $file_sid);

        // если апликейшн от незарегенного пользователя, то в поле show_js сразу пропишем 0
        if (empty($jobseeker_id)) {
            $jobSeekerName  = $notRegisteredUserData['name'];
            $jobSeekerEmail = $notRegisteredUserData['email'];
            $res = SJB_DB::query("
                insert into applications(`listing_id`, `jobseeker_id`, `comments`, `date`, `resume`, `file`, `mime_type`, `anonymous`, `show_js`, `username`, `email`, `file_id`, `questionnaire`, `score`)
                values(?s, ?s, ?s, NOW(), ?s, ?s, ?s, ?s, ?n, ?s, ?s, ?s, ?s, ?s)", $listing_id, 0, $comments, $resume, $file, $mimeType, $anonymous, 0, $jobSeekerName, $jobSeekerEmail, $file_id, $questionnaire, $score);

            return $res;
        }

        $res = SJB_DB::query("
            insert into applications(`listing_id`, `jobseeker_id`, `comments`, `date`, `resume`, `file`, `mime_type`, `anonymous`, `file_id`, `questionnaire`, `score`)
            values(?s, ?s, ?s, NOW(), ?s, ?s, ?s, ?s, ?s, ?s, ?s)", $listing_id, $jobseeker_id, $comments, $resume, $file, $mimeType, $anonymous, $file_id, $questionnaire, $score);

        return $res;
    }

	public static function remove($id)
    {
        SJB_DB::query("delete from applications where id = ?s", $id);
    }

	public static function hideJS($applicationId)
    {
        SJB_DB::query("update applications set `show_js` = 0 where id = ?s", $applicationId);
        SJB_Applications::deleteEmptyApplication($applicationId);
    }

	public static function hideEmp($applicationId)
    {
        SJB_DB::query("update applications set `show_emp` = 0 where id = ?s", $applicationId);
        SJB_Applications::deleteEmptyApplication($applicationId);
    }

	public static function deleteEmptyApplication ($applicationId)
	{
		$fileID = SJB_DB::queryValue("SELECT `file_id` FROM `applications` WHERE `id` = ?s", $applicationId);
		$res = SJB_DB::query("DELETE FROM `applications` WHERE `show_js` = 0 AND `show_emp` = 0 AND id = ?s", $applicationId);
		if ($res === true && !empty($fileID) && isset($fileID)) {
			SJB_UploadFileManager::deleteUploadedFileByID($fileID);
		}
	}

	public static function accept($applicationId)
    {
        SJB_DB::query("update applications set `status` = 'Approved' where id = ?s", $applicationId);
    }

	public static function reject($applicationId)
    {
        SJB_DB::query("update applications set `status` = 'Rejected' where id = ?s", $applicationId);
    }

	public static function saveNoteOnDB ($note, $applicationId)
    {
        return SJB_DB::query("update applications set `note` = ?s where id = ?s", $note, $applicationId);
    }
    
    /**
     * Gets an Application Email from Application Settings
     *
     * @param int $listing_id
     * @return string
     */
	public static function getApplicationEmailbyListingId($listing_id)
    {
    	$application_email = SJB_DB::queryValue("SELECT `value` FROM `listings_properties` WHERE `object_sid` = ?n AND `id` = ?s AND `add_parameter` = ?n AND `value` <> ''", $listing_id, 'ApplicationSettings', 1);
		if ($application_email)
			return $application_email;
		return '';
    }

}
	
class SJB_Application
{

    var $id = 0;

    function SJB_Application($id)
    {
        $this->id = $id;
    }

	public function accept()
    {
        return SJB_DB::query("update applications set status = 'Approved' where id = ?s", $this->id);
    }

	public function reject()
    {
        return SJB_DB::query("update applications set status = 'Rejected' where id = ?s", $this->id);
    }

	public function get()
    {
        $res = SJB_DB::query("select * from applications where id = ?s", $this->id);
        if (count($res) > 0)
            return $res[0];
        return false;
    }

    public static function getApplicationMeta()
    {
        $meta = array(
            "application" => array (
                "date" => array (
                    "type" => "date"
                    )
                )
            );
        return $meta;
    }

}
