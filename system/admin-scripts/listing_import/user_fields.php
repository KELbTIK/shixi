<?php
class SJB_Admin_ListingImport_UserFields extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('set_xml_import');
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();

		$errors = array();
		$selected = array();
		$a_selected = array();
		$defaultValue = array();

		$user_group_sid = SJB_Request::getVar('user_group_sid');
		$id = SJB_Request::getVar('id', 0);
		$tree = '';
		$username = '';

		if ($id > 0) { // load exist parser
			$parser_from_id = SJB_XmlImport::getSystemParsers($id);
			if (isset($parser_from_id [0] ['name'])) {
				$parser_from_id = $parser_from_id[0];
			}
			$xml = $parser_from_id ['xml'];
			$xml = SJB_XmlImport::cleanXmlFromImport($xml);
			$username = SJB_XmlImport::encodeSpecialEntities($parser_from_id['username']);
			$defaultValue = ($parser_from_id ['default_value_user'] != '') ? unserialize($parser_from_id ['default_value_user']) : array();
			if ($parser_from_id ['maper_user'] != '') {
				$map = unserialize($parser_from_id ['maper_user']);
				foreach ($map as $key => $val) {
					unset($map[$key]);
					$key = SJB_XmlImport::encodeSpecialEntities($key);
					$map[$key] = $val;
				}
				$selected = array_values($map);
				$a_selected = array_keys($map);
			}
		} else {
			$xml = SJB_XmlImport::cleanXmlFromImport(base64_decode($_REQUEST ['xml']));
		}

		$sxml = new simplexml ();
		$xml = stripslashes($xml);
		$tree = $sxml->xml_load_file($xml, 'array');
		if (isset($tree['@content'])) {
			$tree = $tree[0];
		}

		if (is_array($tree)) {
			$tree = SJB_XmlImport::convertArray($tree);
			foreach ($tree as $key => $val) {
				unset($tree[$key]);
				$key = SJB_XmlImport::encodeSpecialEntities($key);
				$tree[$key]['val'] = $val;
				$tree[$key]['key'] = $key;
			}
			$user_profile_fields = SJB_UserDetails::getDetails($user_group_sid);
			$i = count($user_profile_fields);
			foreach ($user_profile_fields as $key => $val) {
				if ($val['type'] == 'location') {
					foreach ($val['fields'] as $fieldInfo) {
						$userField = new SJB_UserProfileField ($fieldInfo);
						$userField->setSID($fieldInfo['sid']);
						$user_profile_fields[$i]['id'] = $val['id'].'_'.$userField->details->properties ['id']->value;
						$user_profile_fields[$i]['caption'] = $userField->details->properties ['id']->value;
						$i++;
					}
					unset($user_profile_fields[$key]);
				}
				if ($val['id'] == 'username') {
					unset($user_profile_fields[$key]);
				}
			}
		}
		else {
			$errors [] = 'XML syntaxis error.';
		}

		$tp->assign('username', $username);
		$tp->assign('id', $id);
		$tp->assign('selecteduser', $selected);
		$tp->assign('a_selecteduser', $a_selected);
		$tp->assign('xml', htmlspecialchars($xml));
		$tp->assign('errors', $errors);
		$tp->assign('tree', $tree);
		$tp->assign("fields", $user_profile_fields);
		$tp->assign('user_default_value', $defaultValue);

		$tp->display('user_fields.tpl');

	}
}