<?php

class SJB_Admin_ListingImport_RunImport extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('set_xml_import');
		return parent::isAccessible();
	}

	public function execute()
	{
		$errors = array();
		$tp = SJB_System::getTemplateProcessor();
		$id = (isset($_GET ['id']) ? intval($_GET['id']) : 0);

		if ($id > 0) {
			$parserFromID = SJB_XmlImport::getSystemParsers($id);
			$parserFromID = $parserFromID ? array_pop($parserFromID) : array();
			if (!empty($parserFromID['product_sid'])) {
				$result = SJB_XmlImport::runImport($id);
				$tp->assign('id', $id);
				$tp->assign('result', $result);
			} else {
				$errors[] = 'Please select a product';
			}
		}
		else {
			$errors[] = 'Undefined ID parser';
		}

		$tp->assign('errors', $errors);
		$tp->display('run_import.tpl');
	}
}