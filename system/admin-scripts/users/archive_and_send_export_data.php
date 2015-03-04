<?php


class SJB_Admin_Users_ArchiveAndSendExportData extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('export_users');
		return parent::isAccessible();
	}

	public function execute()
	{
		SJB_UsersExportController::archiveAndSendExportFile();
	}
}
