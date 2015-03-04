<?php

class SJB_Admin_Breadcrumbs_ManageBreadcrumbs extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('configure_breadcrumbs');
		return parent::isAccessible();
	}

	public function execute()
	{
		$breadcrumbs = new SJB_Breadcrumbs();
		$errors = array();

		if (isset($_REQUEST['action'])) {
			// проверим, задали element_id или нет
			if (!isset($_REQUEST['element_id'])) {
				// задаем текст ошибки и сбрасываем 'action', чтобы ничего не выполнялось
				$errors[] = "NOT_ID";
				$_REQUEST['action'] = '';
			} else {
				$element_id = $_REQUEST['element_id'];
			}

			switch ($_REQUEST['action']) {
				case 'add':
					// если была отправка формы добавления элемента
					if (isset($_REQUEST['addElement']) && $_REQUEST['addElement'] != '') {
						$item_name = $_REQUEST['item_name'];
						$item_uri = $_REQUEST['item_uri'];

						$breadcrumbs->addElement($item_name, $item_uri, $element_id);

						$site_url = SJB_System::getSystemSettings("SITE_URL");
						SJB_HelperFunctions::redirect($site_url . "/manage-breadcrumbs/");
					}
					$parentElement = $breadcrumbs->getElement($element_id);

					$tp = SJB_System::getTemplateProcessor();
					$tp->assign("parentElement", $parentElement);
					$tp->display("add_item.tpl");
					break;

				case 'edit':
					// если была отправка формы редактирования элемента
					if (isset($_REQUEST['updateElement']) && $_REQUEST['updateElement'] != '') {
						$item_name = $_REQUEST['item_name'];
						$item_uri = $_REQUEST['item_uri'];

						$breadcrumbs->updateElement($item_name, $item_uri, $element_id);

						$site_url = SJB_System::getSystemSettings("SITE_URL");
						SJB_HelperFunctions::redirect($site_url . "/manage-breadcrumbs/");
					}
					$editElement = $breadcrumbs->getElement($element_id);

					$tp = SJB_System::getTemplateProcessor();
					$tp->assign("editElement", $editElement);
					$tp->display("edit_item.tpl");
					break;

				case 'delete':
					$breadcrumbs->deleteElement($element_id);
					$site_url = SJB_System::getSystemSettings("SITE_URL");
					SJB_HelperFunctions::redirect($site_url . "/manage-breadcrumbs/");
					break;

				default:
					break;
			}
		}

		$navStructure = $breadcrumbs->makeStructure();

		$tp = SJB_System::getTemplateProcessor();
		$tp->assign('ERRORS', $errors);
		$tp->assign("navStructure", $navStructure);
		$tp->display("manage_breadcrumbs.tpl");
	}
}
