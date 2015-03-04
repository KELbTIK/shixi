<?php

class SJB_Admin_Users_BannedIps extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('manage_banned_ips');
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$action = SJB_Request::getVar('action');
		$ip = SJB_Request::getVar('banned_ip', false);
		$id = SJB_Request::getVar('id', false);
		$sort = SJB_Request::getVar('sort', false);
		$page = SJB_Request::getVar('page', 1);
		$items_per_page = SJB_Request::getVar('ip_per_page', 50);
		$errors = array();

		switch ($action) {
			case 'ban':
				if (!$ip || !preg_match('#^([0-9]{1,3})\.([0-9\*]{1,3})\.([0-9\*]{1,3})\.([0-9\*]{1,3})$#', $ip, $out))
					$errors['WRONG_FORMAT'] = 1;
				if (!$errors) {
					if (SJB_IPManager::getBannedIPByValue($ip))
						$errors['IP_ALREADY_EXIST'] = 1;
					else {
						if (!SJB_IPManager::makeIPBanned($ip))
							$errors['IP_NOT_BANNED'] = 1;
					}
				}
				break;
			case 'unban':
				if (!$id)
					$errors['ID_NOT_FOUND'] = 1;
				if (!$errors) {
					if (SJB_IPManager::getBannedIPByID($id)) {
						if (!SJB_IPManager::makeIPEnabled($id))
							$errors['IP_NOT_ENABLED'] = 1;
					}
					else
						$errors['IP_WAS_NOT_BANNED'] = 1;
				}
				break;
			default:
				break;
		}

		$countAllBannedIPs = SJB_DB::queryValue('SELECT count(*) FROM `banned_ips`');
		$pages = array();
		for ($i = $page - 3; $i < $page + 3; $i++) {
			if ($i > 0)
				$pages[] = $i;
			if ($i * $items_per_page > $countAllBannedIPs)
				break;
		}

		$totalPages = ceil($countAllBannedIPs / $items_per_page);
		if (empty($totalPages))
			$totalPages = 1;
		if (array_search(1, $pages) === false)
			array_unshift($pages, 1);
		if (array_search($totalPages, $pages) === false)
			array_push($pages, $totalPages);

		$bannedIPs = SJB_IPManager::getAllBannedIPs(array('limit' => ($page - 1) * $items_per_page, 'num_rows' => $items_per_page), $sort);

		$tp->assign('pages', $pages);
		$tp->assign('currentPage', $page);
		$tp->assign('totalPages', $totalPages);
		$tp->assign('ip_per_page', $items_per_page);
		$tp->assign('sort', $sort);
		$tp->assign('action', $action);
		$tp->assign('errors', $errors);
		$tp->assign('bannedIPs', $bannedIPs);
		$tp->display('banned_ips.tpl');
	}
}