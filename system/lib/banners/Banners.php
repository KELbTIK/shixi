<?php 

class SJB_Banners
{
	/**
	 * Database banners table
	 *
	 * @var string
	 */
	var $bannersTable = 'banners';

	/**
	 * Database banners table
	 *
	 * @var string
	 */
	var $bannerGroupsTable = 'banner_groups';

	/**
	 * Error text
	 * @var string
	 */
	var $bannersError;

	/**
	 * Create new banner object
	 *
	 * @return Banners
	 */
	public function __construct()
	{
		$uploadDir = SJB_System::getSystemSettings('FILES_DIR');
		$bannersPath = $uploadDir . 'banners/';
		if (!file_exists($bannersPath))
			mkdir($bannersPath, 0777);
	}

	/**
	 * Get banners DB-table name
	 *
	 * @return unknown
	 */
	function getBannersTable()
	{
		return $this->bannersTable;
	}

	/**
	 * Get site URL
	 *
	 * @return string
	 */
	public static function getSiteUrl()
	{
		$siteUrl = SJB_System::getSystemSettings('USER_SITE_URL');
		if (!empty($siteUrl))
			return $siteUrl;

		$siteUrl = SJB_System::getSystemsettings('SITE_URL');
		$siteUrl = str_replace('/admin/', '', $siteUrl);
		$siteUrl = str_replace('/admin', '', $siteUrl);
		return $siteUrl;
	}

	/**
	 * Get random banner by group ID
	 *
	 * @param string $groupID
	 * @return integer
	 */
	public static function getBannerIdByGroupID($groupID)
	{
		$currentId = SJB_DB::queryValue('SELECT b.id
										FROM banners as b
										INNER JOIN banner_groups as bg
										ON (bg.sid = b.groupSID)
										WHERE bg.id = ?s ORDER BY RAND() LIMIT 1',
			$groupID);
		return empty($currentId) ? false : $currentId;
	}
	
	function getBannerUserSID($sid)
	{
		$res = SJB_DB::queryValue("	SELECT b.user_sid
							FROM `{$this->bannersTable}` as b
							WHERE b.id = ?n LIMIT 1", $sid);
		return $res ? $res : false;
	}


	/**
	 * Get random ACTIVE banner by group ID
	 *
	 * @param string $groupID
	 * @return bool|array
	 */
	public static function getActiveBannerIdByGroupID($groupID)
	{
		$number_banners_display_at_once = self::getNumberBannersDisplayAtByGroupID($groupID);
		$result = SJB_DB::query('SELECT b.id
										FROM banners as b
										INNER JOIN banner_groups as bg
										ON (bg.sid = b.groupSID)
										WHERE b.active = 1 AND bg.id = ?s ORDER BY RAND() LIMIT ?n',
			$groupID, $number_banners_display_at_once);

		return empty($result) ? false : $result;
	}

	public static function getNumberBannersDisplayAtByGroupID($groupID)
	{
		$number_banners_display_at_once = SJB_DB::query('SELECT `number_banners_display_at_once` FROM `banner_groups` WHERE id = ?s', $groupID);
		if ($number_banners_display_at_once) {
			$number_banners_display_at_once = array_pop($number_banners_display_at_once);
			return array_pop($number_banners_display_at_once);
		}
		return 1;
	}

	/**
	 * Get banner properties by banner ID
	 *
	 * @param integer $id
	 * @return array
	 */
	function getBannerProperties($id)
	{
		$res = SJB_DB::query("SELECT b.*, bg.id as groupID  FROM `{$this->bannersTable}` as b INNER JOIN banner_groups as bg ON (bg.sid = b.groupSID) WHERE b.id = ?n LIMIT 1", $id);
		return array_pop($res);
	}

	/**
	 * Get path to banner image by banner ID
	 *
	 * @param integer $id
	 * @return string
	 */
	function getBannerImagePath($id)
	{
		$image = SJB_DB::query("SELECT `image_path` FROM `{$this->bannersTable}` WHERE `id` = ?n LIMIT 1", $id);
		return array_pop($image);
	}

	/**
	 * Get all banners from table
	 *
	 * @return array
	 */
	function getAllBanners()
	{
		$res = SJB_DB::query("SELECT * FROM `{$this->bannersTable}`");
		foreach ($res as $key => $val) {
			if ($val['show'] > 0)
				$res[$key]['ctr'] = $val['click'] / $val['show'];
		}
		return $res;
	}


	/**
	 * Get all banners in group by group SID
	 *
	 * @param integer $groupSID
	 * @return array
	 */
	function getBannersByGroupSID($groupSID)
	{
		$res = SJB_DB::query("SELECT b.*, bg.id as groupID FROM `{$this->bannersTable}` as b INNER JOIN banner_groups as bg ON (bg.sid = b.groupSID) WHERE b.groupSID = ?n ORDER BY `id` DESC", $groupSID);
		foreach ($res as $key => $val) {
			if ($val['show'] > 0)
				$res[$key]['ctr'] = ($val['click'] / $val['show']) * 100;
			if ($val['user_sid']) 
				$res[$key]['user'] = SJB_UserManager::getUserInfoBySID($val['user_sid']);
		}

		return $res;
	}


	/**
	 * get banner URL
	 *
	 * @return string
	 */
	public static function getBannersPath()
	{
		return self::getSiteUrl() . '/files/banners';
	}

	/**
	 * Add new banner to table
	 *
	 * @param string $title
	 * @param string $link target link of banner
	 * @param string $imagePath
	 * @param integer $sx
	 * @param integer $sy
	 * @param string $type
	 * @param integer $active as boolean - 1 or 0
	 * @param int|string $groupSID
	 * @param array $params
	 * @param null $userSID
	 * @param int $contractSID
	 * @return boolean
	 */
	function addBanner($title, $link, $imagePath, $sx, $sy, $type, $active, $groupSID = '', $params, $userSID = null, $contractSID = 0)
	{
		$status = "approved";
		$link = preg_replace("|\.\./|u", '', $link);
		$productInfo = SJB_ProductsManager::getProductInfoBySID($params['product_sid']);
		$productExtraInfo = unserialize($productInfo['serialized_extra_info']);
		if (!empty($productExtraInfo['approve_by_admin'])) {
			$status = "pending";
		}
		return SJB_DB::query("
			INSERT INTO `{$this->bannersTable}`
			SET  `title` = ?s, `link` = ?s, `image_path` = ?s, `type` = ?s, `width` = ?n, `height` = ?n, `active`=?n, `groupSID`=?n, openBannerIn =?s, bannerType = ?s, code = ?s, `user_sid` = ?n, `status` = ?s, `contract_sid` = ?n",
			$title, $link, $imagePath, $type, $sx, $sy, $active, $groupSID, $params['openBannerIn'], $params['bannerType'], $params['code'], $userSID, $status, $contractSID);
	}

	/**
	 * Update banner info
	 *
	 * @param integer $id banner ID(SID)
	 * @param string $title
	 * @param string $link
	 * @param string $imagePath
	 * @param integer $sx
	 * @param integer $sy
	 * @param string $type
	 * @param integer $active as boolean - 1 or 0
	 * @param integer $groupSID
	 * @return boolean
	 */
	function updateBanner($id, $title, $link, $imagePath, $sx, $sy, $type, $active, $groupSID = '', $params)
	{
		$link = preg_replace("|\.\./|u", '', $link);
		SJB_DB::query("UPDATE `{$this->bannersTable}`
								SET  `title` = ?s, `link` = ?s, `image_path` = ?s, `type` = ?s, `width` = ?n, `height` = ?n, `active` = ?n, `groupSID` = ?n, openBannerIn =?s, bannerType = ?s, code = ?s 
								WHERE `id` = ?n",
			$title, $link, $imagePath, $type, $sx, $sy, $active, $groupSID, $params['openBannerIn'], $params['bannerType'], $params['code'], $id);
		return true;
	}


	/**
	 * Increment number of banner views by ID
	 *
	 * @param integer $id banner ID(SID)
	 * @return boolean
	 */
	public static function incrementShowCounter($bannersIDs)
	{
		SJB_DB::query('UPDATE `banners` SET `show` = `show` + 1 WHERE `id` in (?l)', $bannersIDs);
		return true;
	}


	/**
	 * Increment number of banner clicks by ID
	 *
	 * @param integer $id banner ID(SID)
	 * @return boolean
	 */
	function incrementClickCounter($id)
	{
		SJB_DB::query("UPDATE `{$this->bannersTable}` SET `click` = `click`+1 WHERE `id` = ?n LIMIT 1", $id);
		return true;
	}


	/**
	 * Delete banner by ID
	 *
	 * @param integer $id banner ID(SID)
	 * @return boolean
	 */
	function deleteBanner($id)
	{
		// path to uploaded files dir
		$filesDir = SJB_System::getSystemSettings('FILES_DIR');
		$bannersDir = $filesDir . 'banners/';

		$bannerInfo = $this->getBannerImagePath($id);

		if (empty($bannerInfo['image_path'])) {
			SJB_DB::query("DELETE FROM `{$this->bannersTable}` WHERE `id` = ?n LIMIT 1", $id);
			return true;
		}

		$bannerImagePath = $bannersDir . basename($bannerInfo['image_path']);

		if (!file_exists($bannerImagePath)) {
			$this->bannersError = 'Banner image is not exist';
			SJB_DB::query("DELETE FROM `{$this->bannersTable}` WHERE `id` = ?n LIMIT 1", $id);
			return false;
		}

		$deleteFile = unlink($bannerImagePath);

		if ($deleteFile === false) {
			$this->bannersError = 'Can\'t delete banner image';
			SJB_DB::query("DELETE FROM `{$this->bannersTable}` WHERE `id` = ?n LIMIT 1", $id);
			return false;
		}

		$res = SJB_DB::query("DELETE FROM `{$this->bannersTable}` WHERE `id` = ?n LIMIT 1", $id);

		return $res;
	}


	/**
	 * Delete banner image file by ID
	 *
	 * @param integer $id
	 * @return boolean
	 */
	function deleteBannerImage($id)
	{
		// path to uploaded files dir
		$filesDir = SJB_System::getSystemSettings('FILES_DIR');
		$bannersDir = $filesDir . 'banners/';
		$bannerInfo = $this->getBannerImagePath($id);
		$bannerImagePath = $bannersDir . basename($bannerInfo['image_path']);

		if (!file_exists($bannerImagePath)) {
			$this->bannersError = 'Banner image is not exist';
			return false;
		}

		$deleteFile = unlink($bannerImagePath);

		if ($deleteFile === false) {
			$this->bannersError = 'Can\'t delete banner image';
			return false;
		}
		return SJB_DB::query("UPDATE `{$this->bannersTable}` SET `image_path` = '' WHERE `id` = ?n", $id);
	}


	/**
	 * Get banner META data
	 *
	 * @return array
	 */
	function getBannersMeta()
	{
		$meta = array
		(
			array
			(
				'id' => 'title',
				'caption' => 'Banner Title',
				'type' => 'text',
				'is_required' => false,
				'is_system' => true,
				'order' => 1,
			),
			array
			(
				'id' => 'link',
				'caption' => 'Banner Link',
				'type' => 'text',
				'is_required' => false,
				'is_system' => true,
				'order' => 2,
			),
			array
			(
				'id' => 'image',
				'caption' => 'Banner File',
				'comment' => 'Choose image file or flash banner file',
				'type' => 'file',
				'is_required' => false,
				'is_system' => true,
				'order' => 3,
			),
			array
			(
				'id' => 'code',
				'caption' => 'Banner Code',
				'type' => 'text',
				'is_required' => false,
				'is_system' => true,
				'order' => 3,
			),
			array
			(
				'id' => 'width',
				'caption' => 'Banner Width',
				'type' => 'text',
				'is_required' => false,
				'is_system' => true,
				'order' => 3,
			),
			array
			(
				'id' => 'height',
				'caption' => 'Banner Height',
				'type' => 'text',
				'is_required' => false,
				'is_system' => true,
				'order' => 3,
			),
			array
			(
				'id' => 'active',
				'caption' => 'Active',
				'type' => 'boolean',
				'is_required' => false,
				'is_system' => true,
				'order' => 4,
			),
			array
			(
				'id' => 'groupSID',
				'caption' => 'Banner Group',
				'type' => 'list',
				'values' => $this->getAllBannerGroups(),
				'is_required' => false,
				'is_system' => true,
				'order' => 6,
			),
			array
			(
				'id' => 'openBannerIn',
				'caption' => 'Open Banner In',
				'type' => 'list',
				'values' => array(array('id' => '_self', 'caption' => 'Current Window'), array('id' => '_blank', 'caption' => 'New Window')),
				'is_required' => false,
				'is_system' => true,
				'order' => 6,
			),
		);

		return $meta;
	}

	/**
	 * Get all banner groups
	 * @return array
	 */
	function getAllBannerGroups()
	{
		return SJB_DB::query("SELECT * FROM `{$this->bannerGroupsTable}`");
	}

	/**
	 * Get banner group by SID
	 * @param integer $sid
	 * @return array
	 */
	function getBannerGroupBySID($sid)
	{
		$res = SJB_DB::query("SELECT * FROM `{$this->bannerGroupsTable}` WHERE `sid` = ?n LIMIT 1", $sid);
		return array_pop($res);
	}

	/**
	 * Get banner group SID by banner ID(SID)
	 * @param integer $sid banner SID
	 * @return integer
	 */
	function getBannerGroupSIDByBannerSID($sid)
	{
		return SJB_DB::queryValue("	SELECT bg.sid
							FROM `{$this->bannerGroupsTable}` as bg
							LEFT JOIN `{$this->bannersTable}` as b
							ON (bg.sid = b.groupSID) 
							WHERE b.id = ?n LIMIT 1", $sid);
	}

	/**
	 * Delete banner group by SID
	 * @param integer $sid
	 * @return unknown
	 */
	function deleteBannerGroup($sid)
	{
		// for delete - delete all banners with group and delete group
		$banners = $this->getBannersByGroupSID($sid);
		foreach ($banners as $banner)
			$this->deleteBanner($banner['id']);

		$res = SJB_DB::query("DELETE FROM `{$this->bannerGroupsTable}` WHERE `sid` = ?n", $sid);
		return $res;
	}

	/**
	 * Add new banner group
	 * @param string $id name of banner group
	 * @return unknown
	 */
	function addBannerGroup($id)
	{
		return SJB_DB::query("INSERT INTO `{$this->bannerGroupsTable}` SET `id` = ?s", $id);
	}

	/**
	 * Update banner group info
	 * @param integer $sid
	 * @param string $id
	 * @return unknown
	 */
	function updateBannerGroup($sid, $id, $number_banners)
	{
		return SJB_DB::query("UPDATE `{$this->bannerGroupsTable}` SET `id` = ?s, `number_banners_display_at_once`=?n WHERE `sid`=?n", $id, $number_banners, $sid);
	}

	/**
	 * Update banner Active status
	 * @param integer $sid
	 * @param boolean $status
	 * @return unknown
	 */
	function updateActiveStatus($sid, $status)
	{
		return SJB_DB::query("UPDATE `{$this->bannersTable}` SET `active` = ?n WHERE `id`=?n", $status, $sid);
	}
	
	function updateStatus($sid, $status)
	{
		return SJB_DB::query("UPDATE `{$this->bannersTable}` SET `status` = ?s WHERE `id`=?n", $status, $sid);
	}
	
	function getBannerIDByContract($contractSID)
	{
		$res = SJB_DB::queryValue("SELECT `id` FROM `{$this->bannersTable}` WHERE `contract_sid` = ?n LIMIT 1", $contractSID);
		return $res ? $res : false;
	}
	
	public function updateBannerContract($contractSID, $bannerSID)
	{
		return SJB_DB::query("UPDATE `{$this->bannerGroupsTable}` SET `contract_sid` = ?n WHERE `id` = ?n", $contractSID, $bannerSID);
	}
}

