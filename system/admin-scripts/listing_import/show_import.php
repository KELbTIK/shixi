<?php

class SJB_Admin_ListingImport_ShowImport extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('set_xml_import');
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();

		$action = (isset($_GET['action']) ? $_GET['action'] : '');
		$id = (isset($_GET['id']) ? intval($_GET['id']) : 0);

		if ($id > 0) {
			switch ($action) {
				case 'activate':
					SJB_XmlImport::activate($id);
					break;

				case 'deactivate':
					SJB_XmlImport::deactivate($id);
					break;

				default:
					break;
			}
		}

		$systemParsers = SJB_XmlImport::getSystemParsers('', true);
		$tp->assign('systemParsers', $systemParsers);
		$tp->display('show_import_list.tpl');
	}
}
