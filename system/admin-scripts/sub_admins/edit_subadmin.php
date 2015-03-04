<?php

class SJB_Admin_SubAdmins_EditSubadmin extends SJB_Function
{
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();

		$subAdminSID = SJB_Request::getVar('subadmin', 0);

		if (!empty($subAdminSID) && $adminInfo = SJB_SubAdminManager::getSubAdminInfoBySID($subAdminSID)) {
			$editedSubAdminInfo = $_REQUEST;
			$subAdminInfo = array_merge($adminInfo, $editedSubAdminInfo);

			// create subAdmin object
			$oSubAdmin = SJB_ObjectMother::createSubAdmin($subAdminInfo);
			$oSubAdmin->setSID($adminInfo['sid']);
			$oSubAdmin->makePropertyNotRequired("password");


			// permissions
			$acl = SJB_SubAdminAcl::getInstance();

			$type = 'subadmin';
			$resources = $acl->getResources();
			$perms = SJB_SubAdminAcl::getAllPermissions($type, $oSubAdmin->getSID());
			// /permissions

			SJB_SubAdminAcl::mergePermissionsWithResources($resources, $perms);

			$registration_form = SJB_ObjectMother::createForm($oSubAdmin);
			$action = SJB_Request::getVar('action', '');

			$registration_form->registerTags($tp);
			$errors = array();

			if ('save' == $action || $action == 'apply') {

				if ($adminInfo['username'] == $subAdminInfo['username']) {
					$oSubAdmin->deleteProperty('username');
				}

				if ($adminInfo['email'] == $subAdminInfo['email']) {
					$oSubAdmin->deleteProperty('email');
				}

				if ($registration_form->isDataValid($errors)) {
					$password_value = $oSubAdmin->getPropertyValue('password');
					if (empty($password_value['original'])) {
						$oSubAdmin->deleteProperty('password');
					}

					// save subAdmin
					SJB_SubAdminManager::saveSubAdmin($oSubAdmin);

					$role = $oSubAdmin->getSID();
					SJB_Acl::clearPermissions($type, $role);

					foreach ($resources as $name => $resource) {
						SJB_SubAdminAcl::allow($name, $type, $role, SJB_SubAdminAcl::definePermission($name), SJB_Array::get($resource, 'params', ''));
					}
					
					SJB_FlashMessages::getInstance()->addMessage('CHANGES_SAVED');
					
					if ($action == 'save') {
						SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/manage-subadmins/");
					}
				}
				SJB_SubAdminAcl::mergePermissionsWithRequest($resources);
			}

			SJB_SubAdminAcl::prepareSubPermissions($resources);

			$tp->assign("errors", $errors);
			$tp->assign("form_fields", $registration_form->getFormFieldsInfo());
			$tp->assign('groups', SJB_SubAdminAcl::getPermissionGroups());
			$tp->assign('resources', $resources);
			$tp->assign('type', $type);
			$tp->assign('sid', $subAdminInfo['sid']);

			$tp->display('add_subadmin.tpl');
		}
	}
}
