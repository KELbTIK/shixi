<?php

class SJB_Admin_Users_EditListItem extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('edit_user_groups_profile_fields');
		return parent::isAccessible();
	}

	public function execute()
	{
		$template_processor = SJB_System::getTemplateProcessor();
		$errors = array();
		$UserProfileFieldListItemManager = new SJB_UserProfileFieldListItemManager;
		if (!isset($_REQUEST['field_sid'], $_REQUEST['item_sid'])) {
			echo 'The system cannot proceed as some key paramaters are missed';
		} else {
			if (is_null($list_item = $UserProfileFieldListItemManager->getListItemBySID($_REQUEST['item_sid']))) {
				echo 'Wrong parameters are specified';
			} else {
				$list_item_info['value'] = $list_item->getValue();
				$template_processor->assign("list_item_info", $list_item_info);
				if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'save') {
					$list_item->setValue($_REQUEST['list_item_value']);
					if (empty($_REQUEST['list_item_value'])) {
						$errors = array('Value' => 'EMPTY_VALUE');
					} else {
						$UserProfileFieldListItemManager->saveListItem($list_item);
						SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/edit-user-profile-field/edit-list/?field_sid=" . $_REQUEST['field_sid']);
					}
				}

				$user_profile_field = SJB_UserProfileFieldManager::getFieldBySID($_REQUEST['field_sid']);
				$user_profile_field_info = SJB_UserProfileFieldManager::getFieldInfoBySID($_REQUEST['field_sid']);
				$template_processor->assign("user_profile_field_info", $user_profile_field_info);
				$template_processor->assign("user_group_sid", $user_profile_field->getUserGroupSID());
				$template_processor->assign("user_profile_field_sid", $_REQUEST['field_sid']);
				$template_processor->assign("item_sid", $_REQUEST['item_sid']);
				$template_processor->assign("list_item_value", htmlspecialchars($list_item->getValue()));
				$template_processor->assign("errors", $errors);
				$template_processor->assign("user_group_info", SJB_UserGroupManager::getUserGroupInfoBySID($user_profile_field->getUserGroupSID()));
				$template_processor->display("user_profile_list_item_editing.tpl");
			}
		}
	}
}
