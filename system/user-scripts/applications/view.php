<?php

class SJB_Applications_View extends SJB_Function
{
	private $pages;
	private $totalPages;
	private $currentPage;
	
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$appsPerPage = SJB_Request::getVar('appsPerPage', 10);
		$this->currentPage = SJB_Request::getVar('page', 1);
		$currentUser = SJB_UserManager::getCurrentUser();
		$appJobId = SJB_Request::getVar('appJobId', false, null, 'int');
		$score = SJB_Request::getVar('score', false);
		$orderBy = SJB_Request::getVar('orderBy', 'date');
		$order = SJB_Request::getVar('order', 'desc');
		$displayTemplate = "view.tpl";
		$errors = array();

		// не бум пускать незарегенных
		if (SJB_UserManager::isUserLoggedIn() === false) {
			$tp->assign("ERROR", "NOT_LOGIN");
			$tp->display("../miscellaneous/error.tpl");
			return;
		}

		$filename = SJB_Request::getVar('filename', false);

		if ($filename) {
			$appsID = SJB_Request::getVar('appsID', false);
			if ($appsID) {
				$file = SJB_UploadFileManager::openApplicationFile($filename, $appsID);
				if (!$file)
					$errors['NO_SUCH_FILE'] = true;
			}
			else
				$errors['NO_SUCH_APPS'] = true;
		}

		if (!is_numeric($this->currentPage) || $this->currentPage < 1) {
			$this->currentPage = 1;
		}

		if (!is_numeric($appsPerPage) || $appsPerPage < 1) {
			$appsPerPage = 10;
		}

		if ($order != 'asc' && $order != 'desc') {
			$order = 'desc';
		}

		if (!empty($score) && $score != 'passed' && $score != 'not_passed') {
			$score = false;
		}

		$tp->assign("orderBy", $orderBy);
		$tp->assign("order", $order);
		if (isset($orderBy) && isset($order) && $orderBy != "") {
			switch ($orderBy) {
				case "date":
					$orderInfo = array('sorting_field' => 'date', 'sorting_order' => $order);
					break;
				case "title":
					$orderInfo = array('sorting_field' => 'Title', 'sorting_order' => $order, 'inner_join' => array('table' => 'listings', 'field1' => 'sid', 'field2' => 'listing_id'));
					break;
				case "applicant":
					$orderInfo = false;
					$sortByUsername = true;
					break;
				case "status":
					$orderInfo = array('sorting_field' => 'status', 'sorting_order' => $order);
					break;
				case "score":
					$orderInfo = array('sorting_field' => 'score', 'sorting_order' => $order);
					break;
				case "company":
					$orderInfo = array('sorting_field' => 'CompanyName', 'sorting_order' => $order, 'inner_join' => array('table' => 'listings', 'field1' => 'sid', 'field2' => 'listing_id'), 'inner_join2' => array('table1' => 'users', 'table2' => 'listings', 'field1' => 'sid', 'field2' => 'user_sid'), );
					break;
				default:
					$orderInfo = array('sorting_field' => 'date', 'sorting_order' => $order);
			}
		}
		if ($currentUser->getUserGroupSID() == 41) { // Работадатель

			switch (SJB_Request::getVar('action', '')) {
				case "approve":
					$applications = SJB_Request::getVar('applications', '');
					if (!empty($applications)) {
						if (is_array($applications)) {
							foreach ($applications as $key => $value) {
								$this->approveApplication($key);
							}
						} else {
							$this->approveApplication($applications);
						}
					}
					break;

				case "reject":
					$applications = SJB_Request::getVar('applications', '');
					if (!empty($applications)) {
						if (is_array($applications)) {
							foreach ($applications as $key => $value) {
								$this->rejectApplication($key);
							}
						} else {
							$this->rejectApplication($applications);
						}
					}
					break;

				case "delete":
					if (isset($_POST["applications"]))
						foreach ($_POST["applications"] as $key => $value)
							SJB_Applications::hideEmp($key);
					break;
			}

			$whereSubuser = '';
			if (!empty($subuser)) {
				$whereSubuser = ' and `subuser_sid` = ' . SJB_DB::quote($subuser);
			}
			$jobs = SJB_DB::query('select `Title` as `title`, `sid` as `id` from `listings` where `user_sid` = ?n' . $whereSubuser, $currentUser->sid);

			$listingTitle = null;
			foreach ($jobs as $job) {
				if ($job['id'] == $appJobId)
					$listingTitle = $job['title'];
			}

			$apps = $this->executeApplicationsForEmployer($appsPerPage, $appJobId, $currentUser, $score, $orderInfo, $listingTitle);
			if (empty($apps) && $this->currentPage > 1) {
				$this->currentPage = 1;
				$apps = $this->executeApplicationsForEmployer($appsPerPage, $appJobId, $currentUser, $score, $orderInfo, $listingTitle);
			}

			foreach ($apps as $i => $app) {
				$apps[$i]["job"] = SJB_ListingManager::getListingInfoBySID($apps[$i]["listing_id"]);
				if (!empty($apps[$i]["job"]['screening_questionnaire'])) {
					$screening_questionnaire = SJB_ScreeningQuestionnaires::getInfoBySID($apps[$i]["job"]['screening_questionnaire']);
					$passing_score = 0;
					switch ($screening_questionnaire['passing_score']) {
						case 'acceptable':
							$passing_score = 1;
							break;
						case 'good':
							$passing_score = 2;
							break;
						case 'very_good':
							$passing_score = 3;
							break;
						case 'excellent':
							$passing_score = 4;
							break;
					}
					if ($apps[$i]['score'] >= $passing_score)
						$apps[$i]['passing_score'] = 'Passed';
					else
						$apps[$i]['passing_score'] = 'Not passed';
				}
				if (isset($apps[$i]["resume"]) && !empty($apps[$i]["resume"]))
					$apps[$i]["resumeInfo"] = SJB_ListingManager::getListingInfoBySID($apps[$i]["resume"]);
				// если это анонимный соискатель - то возьмем имя из пришедшего поля 'username'
				if ($apps[$i]['jobseeker_id'] == 0) {
					$apps[$i]["user"]["FirstName"] = $apps[$i]['username'];
				} else {
					$apps[$i]["user"] = SJB_UserManager::getUserInfoBySID($apps[$i]["jobseeker_id"]);
					$apps[$i]['user']['stateInfo'] = SJB_StatesManager::getStateInfoBySID($apps[$i]['user']['Location_State']);
					if (isset($apps[$i]['user']['stateInfo']['state_code'])) {
						$apps[$i]['user']['Location']['State_Code'] = $apps[$i]['user']['stateInfo']['state_code'];
					}
				}
			}

			$tp->assign("appsPerPage", $appsPerPage);
			$tp->assign("currentPage", $this->currentPage);
			$tp->assign("pages", $this->pages);
			$tp->assign("totalPages", $this->totalPages);
			$tp->assign("appJobs", $jobs);
			$tp->assign("score", $score);
			$tp->assign("current_filter", $appJobId);
			$tp->assign("listing_title", $listingTitle);
		}
		else { // Соискатель

			if (SJB_Request::getVar('action', '', 'POST') == "delete") {
				foreach (SJB_Request::getVar('applications', array(), 'POST') as $key => $value)
					SJB_Applications::hideJS($key);
			}

			$apps = SJB_Applications::getByJobseeker($currentUser->sid, $orderInfo);
			for ($i = 0; $i < count($apps); ++$i) {
				$apps[$i]["job"] = SJB_ListingManager::getListingInfoBySID($apps[$i]["listing_id"]);
				$apps[$i]["company"] = SJB_UserManager::getUserInfoBySID($apps[$i]["job"]["user_sid"]);
			}

			$displayTemplate = "view_seeker.tpl";
		}

		if (isset($sortByUsername)) {
			$sortKeys = array();
			$order = ($order == "desc") ? SORT_DESC : SORT_ASC;
			foreach ($apps as $key => $value) {
				if (!isset($apps[$key]["user"]["FirstName"])) $apps[$key]["user"]["FirstName"] = '';
				if (!isset($apps[$key]["user"]["LastName"])) $apps[$key]["user"]["LastName"] = '';
				$sortKeys[$key] = $apps[$key]["user"]["FirstName"] . " " . $apps[$key]["user"]["LastName"];
			}
			array_multisort($sortKeys, $order, SORT_REGULAR, $apps);
		}

		if (empty($apps) && (empty($errors['NOT_OWNER_OF_APPLICATIONS']))) {
			$errors['APPLICATIONS_NOT_FOUND'] = true;
		}

		$tp->assign("METADATA", SJB_Application::getApplicationMeta());
		$tp->assign("applications", $apps);
		$tp->assign("errors", $errors);
		$tp->display($displayTemplate);
	}

	/**
	 * @param $applicationID
	 */
	private function rejectApplication($applicationID)
	{
		$applicationInfo = SJB_Applications::getBySID($applicationID);
		$jobseekerSID = $applicationInfo['jobseeker_id'];
		SJB_Applications::reject($applicationID);
		if (SJB_UserNotificationsManager::isUserNotifiedOnApplicationsRejection($jobseekerSID)) {
			SJB_Notifications::sendUserApplicationApproveOrRejectLetter($applicationID, 'rejected');
		}
		$statisticSID = SJB_Statistics::getStatisticsByObjectSID($applicationID, 'apply');
		if ($statisticSID) {
			SJB_Statistics::updateStatistics($statisticSID, array('approve' => 0, 'reject' => 1));
		}
	}

	/**
	 * @param $applicationID
	 */
	private function approveApplication($applicationID)
	{
		$applicationInfo = SJB_Applications::getBySID($applicationID);
		$jobseekerSID = $applicationInfo['jobseeker_id'];
		SJB_Applications::accept($applicationID);
		if (SJB_UserNotificationsManager::isUserNotifiedOnApplicationsApproval($jobseekerSID)) {
			SJB_Notifications::sendUserApplicationApproveOrRejectLetter($applicationID, 'approved');
		}
		$statisticSID = SJB_Statistics::getStatisticsByObjectSID($applicationID, 'apply');
		if ($statisticSID) {
			SJB_Statistics::updateStatistics($statisticSID, array('approve' => 1, 'reject' => 0));
		}
	}

	private function executeApplicationsForEmployer($appsPerPage, $appJobId, SJB_User $currentUser, $score, $orderInfo, $listingTitle)
	{
		$limit['countRows'] = $appsPerPage;
		$limit['startRow'] = $this->currentPage * $appsPerPage - ($appsPerPage);
		$subuser = false;
		if ($appJobId) {
			$isUserOwnerApps = SJB_Applications::isUserOwnsAppsByAppJobId($currentUser->getID(), $appJobId);
			if (!$isUserOwnerApps) {
				SJB_FlashMessages::getInstance()->addWarning('NOT_OWNER_OF_APPLICATIONS', array('listingTitle' => $listingTitle));
			}
			$allAppsCountByJobID = SJB_Applications::getCountAppsByJob($appJobId, $score);
			$this->setPaginationInfo($appsPerPage, $allAppsCountByJobID);
			$apps = SJB_Applications::getByJob($appJobId, $orderInfo, $score, $limit);
		} else {
			if ($currentUser->isSubuser()) {
				$subuserInfo = $currentUser->getSubuserInfo();
				if (!SJB_Acl::getInstance()->isAllowed('subuser_manage_listings', $subuserInfo['sid'])) {
					$subuser = $subuserInfo['sid'];
				}
			}
			$allAppsCount = SJB_Applications::getCountApplicationsByEmployer($currentUser->getSID(), $score, $subuser);
			$this->setPaginationInfo($appsPerPage, $allAppsCount);
			$apps = SJB_Applications::getByEmployer($currentUser->getSID(), $orderInfo, $score, $subuser, $limit);
		}
		
		return $apps;
	}

	/**
	 * @param $appsPerPage
	 * @param $appsCount
	 */
	private function setPaginationInfo($appsPerPage, $appsCount)
	{
		$this->totalPages = ceil($appsCount / $appsPerPage);
		if (empty($this->totalPages)) {
			$this->totalPages = 1;
		}

		$this->pages = array();
		for ($i = $this->currentPage - 2; $i < $this->currentPage + 3; $i++) {
			if ($i == $this->totalPages) {
				break;
			} else {
				if ($i > 0) {
					$pages[] = $i;
				}
				if ($i * $appsPerPage > $appsCount) {
					break;
				}
			}
		}

		if (array_search(1, $this->pages) === false) {
			array_unshift($this->pages, 1);
		}
		if (array_search($this->totalPages, $this->pages) === false) {
			array_push($this->pages, $this->totalPages);
		}
	}

}
