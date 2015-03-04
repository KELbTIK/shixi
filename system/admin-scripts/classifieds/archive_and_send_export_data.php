<?php


class SJB_Admin_Classifieds_ArchiveAndSendExportData extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('export_listings');
		return parent::isAccessible();
	}

	public function execute()
	{
		SJB_ExportController::archiveAndSendExportFile();
	}
}
