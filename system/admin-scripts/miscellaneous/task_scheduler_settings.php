<?php

class SJB_Admin_Miscellaneous_TaskSchedulerSettings extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('set_task_scheduler');
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$action = SJB_Request::getVar('action');
		$template = 'task_scheduler_settings.tpl';

		$isPseudoCronEnabled = intval(SJB_Settings::getSettingByName('isPseudoCronEnabled')) == 1;

		if (SJB_Request::getVar('command', null, 'post') == 'manage-pseudo-cron') {
			SJB_Settings::updateSetting('isPseudoCronEnabled', SJB_Request::getVar('isEnabled', 'off', 'post') == 'on' ? '1' : '0');
			SJB_Settings::updateSetting('numberOfPageViewsToExecCronIfExceeded', SJB_Request::getVar('numberOfPageViewsToExecCronIfExceeded', null, 'post'));
			SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/task-scheduler-settings');
		}

		$tp->assign('isPseudoCronEnabled', $isPseudoCronEnabled);

		$numberOfPageViewsToExecCronIfExceeded = intval(SJB_Settings::getSettingByName('numberOfPageViewsToExecCronIfExceeded'));
		$tp->assign('cronPath', SJB_BASE_DIR . 'cron/index.php');
		$tp->assign('numberOfPageViewsToExecCronIfExceeded', $numberOfPageViewsToExecCronIfExceeded);

		if ($action != 'log_view') {

			$last_executed_date = SJB_System::getSettingByName('task_scheduler_last_executed_date');

			$expired_listings_id = SJB_ListingManager::getExpiredListingsSID();
			$count_expired_listings = count($expired_listings_id);

			$expired_contracts_id = SJB_ContractManager::getExpiredContractsID();
			$count_expired_contracts = count($expired_contracts_id);

			$res = SJB_DB::query("SELECT * FROM `task_scheduler_log` ORDER BY `sid` DESC LIMIT 1");

			$tp->assign('last_executed_date', $last_executed_date);
			$tp->assign('task_scheduler_log', array_pop($res));
		}
		else {
			$log_file = array();
			$res = SJB_DB::query("SELECT `log_text` FROM `task_scheduler_log` ORDER BY `sid` DESC LIMIT 30");
			foreach ($res as $record) {
				$text = $record['log_text'];
				if ($text)
					$log_file[] = $text;
			}

			$tp->assign('log_content', $log_file);
			$template = 'task_scheduler_log_view.tpl';
		}

		$tp->display($template);
	}
}