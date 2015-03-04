<?php

class SJB_Admin_SubAdmins_AddSubadmin extends SJB_Function
{
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();

		$oSubAdmin = SJB_ObjectMother::createSubAdmin($_REQUEST);
		$registration_form = SJB_ObjectMother::createForm($oSubAdmin);

		$registration_form->registerTags($tp);
		$form_submitted = SJB_Request::getVar('action', '') == 'add';
		$errors = array();

		$acl = SJB_SubAdminAcl::getInstance();

		$type = 'subadmin';
		$resources = $acl->getResources();

		SJB_SubAdminAcl::mergePermissionsWithResources($resources);

		switch (SJB_Request::getVar('action'))
		{
			case 'save':
				if ($registration_form->isDataValid($errors)) {
					SJB_SubAdminManager::saveSubAdmin($oSubAdmin);
					$role = $oSubAdmin->getSID();
					SJB_Acl::clearPermissions($type, $role);
					foreach ($resources as $name => $resource) {
						SJB_SubAdminAcl::allow($name, $type, $role, SJB_SubAdminAcl::definePermission($name), SJB_Request::getVar($name . '_params'));
					}

					// get new defined permissions for notification letter
					$permissions = SJB_SubAdminAcl::getAllPermissions($type, $role);
					$resources = $acl->getResources();
					SJB_SubAdminAcl::mergePermissionsWithResources($resources, $permissions);
					SJB_Notifications::sendSubAdminRegistrationLetter($oSubAdmin, SJB_Request::get(), $resources);
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/manage-subadmins/');
				}
				break;

			case 'delete':
				$subadmins = SJB_Request::getVar('subadmin', array());

				foreach ($subadmins as $subadmin_sid) {
					$username = SJB_SubAdminManager::getUserNameBySubAdminSID($subadmin_sid);
					SJB_SubAdminManager::deleteSubAdminByUserName($username);
				}

				SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/manage-subadmins/');
				break;

			default:
				break;
		}

		$tp->assign('errors', $errors);
		$tp->assign('form_fields', $registration_form->getFormFieldsInfo());

		$aPermissionGroups = SJB_SubAdminAcl::getPermissionGroups();
		if ('save' == SJB_Request::getVar('action', '')) {
			SJB_SubAdminAcl::mergePermissionsWithRequest($resources);
		}
		SJB_SubAdminAcl::prepareSubPermissions($resources);

		$tp->assign('groups', $aPermissionGroups);
		$tp->assign('resources', $resources);
		$tp->assign('type', $type);
		$tp->assign('role', 0);

		$tp->display('add_subadmin.tpl');
	}
}
