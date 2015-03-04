<?php

class SJB_Admin_Builder_Save extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('edit_form_builder');
		return parent::isAccessible();
	}

	public function execute()
	{
		$result = false;
		$error  = '';

		try {
			$builderData = new SJB_FormBuilderData($_REQUEST);
			$result = SJB_FormBuilderManager::save($builderData);
		} catch (Exception $e) {
			$error = SJB_I18N::getInstance()->gettext('Backend', $e->getMessage());
		}

		echo json_encode(array(
			'success' => $result,
			'message' => $error,
		));
		exit;
	}
}
