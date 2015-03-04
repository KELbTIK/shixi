<?php
class SJB_Statistics_MyReports extends SJB_Function
{
	public function execute()
	{
		$template = SJB_Request::getVar('display_template', 'my_reports.tpl');
		$action = SJB_Request::getVar('action', 'quickStat');
		$tp = SJB_System::getTemplateProcessor();
		$errors = array();
		$currentUser = SJB_UserManager::getCurrentUser();
		if (empty($currentUser)) {
			$tp->assign('ERROR', 'NOT_LOGIN');
			$tp->display('../miscellaneous/error.tpl');
			return;
		} else {
			if (SJB_UserGroupManager::getUserGroupIDBySID($currentUser->getUserGroupSID()) == 'Employer') {
				switch ($action) {
					case 'generalStat':
						$generalStat = SJB_Statistics::getEmployerGeneralStatistics($currentUser->getSID());
						$tp->assign('generalStat', $generalStat);
						break;
					case 'jobsStat':
						$active = SJB_Request::getVar('active', 1);
						$sortingField = SJB_Request::getVar('sortingField', 'postedDate');
						$sortingOrder = SJB_Request::getVar('sortingOrder', 'DESC');
						$jobsStat = SJB_Statistics::getEmployerJobsStatistics($currentUser->getSID(), $active, $sortingField, $sortingOrder);
						$tp->assign('jobsStat', $jobsStat);
						$tp->assign('active', $active);
						$tp->assign('sortingField', $sortingField);
						$tp->assign('sortingOrder', $sortingOrder);
						break;
					case 'quickStat':
						$quickStat = SJB_Statistics::getEmployerQuickStatistics($currentUser->getSID());
						$tp->assign('quickStat', $quickStat);
						break;
					default:
						break;
				}
			} else {
				$errors['NOT_EMPLOYER'] = true;
			}
		}
		$tp->assign('errors', $errors);
		$tp->display($template);
	}
}
