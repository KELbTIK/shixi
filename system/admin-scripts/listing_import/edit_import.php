<?php
class SJB_Admin_ListingImport_EditImport extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('set_xml_import');
		return parent::isAccessible();
	}

	public function execute()
	{
		$errors = array();
		if (SJB_Request::isAjax()) {
			$response = null;
			if ($logoId = SJB_Request::getVar('logo_id')) {
				SJB_UploadFileManager::deleteUploadedFileByID($logoId, 'logo');
				$parsersId = SJB_Request::getVar('id');
				SJB_DB::query("UPDATE `parsers` SET `xml_logo` = NULL WHERE id = ?n", $parsersId);
			} elseif ($userName = SJB_Request::getVar('parser_user')) {
				$userType = SJB_Request::getVar('user_type');
				$products = SJB_XmlImport::getProducts($userType, $userName, $errors);
				$response = array(
					'products' => empty($products) ? '' : SJB_XmlImport::translateProductsName($products),
					'error' => empty($errors) ? '' : array_pop($errors)
				);
				$response = json_encode($response);
			}
			die($response);
		}

		$tp = SJB_System::getTemplateProcessor();
		$original_xml = (!empty ($_REQUEST ['xml']) ? $_REQUEST ['xml'] : '');
		$xml = $original_xml;

		$tree = '';
		$listing_fields = array();

		$parsing_name        = (isset ($_REQUEST ['parser_name']) ? $_REQUEST ['parser_name'] : '');
		$usr_name            = (isset ($_REQUEST ['parser_user']) ? $_REQUEST ['parser_user'] : '');
		$pars_url            = (isset ($_REQUEST ['parser_url']) ? $_REQUEST ['parser_url'] : '');
		$form_description    = (isset($_POST['form_description']) ? $_POST['form_description'] : "");
		$type_id             = (isset($_POST['type_id']) ? intval($_POST['type_id']) : "");
		$custom_script       = SJB_Request::getVar('custom_script', '');
		$custom_script_users = SJB_Request::getVar('custom_script_users', '');
		$add_new_user        = (isset($_POST['add_new_user']) ? intval($_POST['add_new_user']) : 0);
		$username            = SJB_Request::getVar('username', '');
		$external_id         = SJB_Request::getVar('external_id', '');
		$defaultValue        = array();
		$logo_options_array  = array('not_logo' => 'Do Not Import Logo',
			'import_logo' => 'Import Logo with Listings',
			'upload_logo' => 'Upload Logo for Imported Listings');

		$id                 = (isset ($_GET ['id']) ? intval($_GET ['id']) : 0);
		$selected           = array();
		$a_selected         = array();
		$selectedLogoOption = null;
		$selectedLogoField  = null;
		$xml_logo           = null;
		$selectedProduct    = SJB_Request::getVar('postUnderProduct');
		$save_error			= SJB_Request::getVar('save_error');
		
		if ($save_error) {
			$errors[] = base64_decode($save_error);
		}

		if (!empty ($_REQUEST ['xml']) || $id > 0) {
			// step 2 OR edit exist


			if ($id > 0) { // load exist parser

				$parser_from_id = SJB_XmlImport::getSystemParsers($id);
				if (isset($parser_from_id [0] ['name'])) {
					$parser_from_id = $parser_from_id[0];
				}
				$parsing_name        = $parser_from_id ['name'];
				$usr_id              = $parser_from_id ['usr_id'];
				$usr_name            = $parser_from_id ['usr_name'];
				$form_description    = $parser_from_id ['description'];
				$pars_url            = $parser_from_id ['url'];
				$type_id             = $parser_from_id ['type_id'];
				$custom_script       = $parser_from_id ['custom_script'];
				$custom_script_users = $parser_from_id ['custom_script_users'];
				$add_new_user        = $parser_from_id ['add_new_user'];
				$importType          = $parser_from_id ['import_type'];
				$xml                 = $parser_from_id ['xml'];
				$xml_logo            = $parser_from_id ['xml_logo'];
				$xml                 = SJB_XmlImport::cleanXmlFromImport($xml);
				$defaultValue        = ($parser_from_id ['default_value'] != '') ? unserialize($parser_from_id ['default_value']) : array();
				$username            = $parser_from_id ['username'];
				$map                 = unserialize($parser_from_id ['maper']);
				$external_id         = str_replace('@', '_dog_', $parser_from_id['external_id']);
				$selected_logo_options = unserialize($parser_from_id['logo_options']);
				$selectedLogoOption  = $selected_logo_options['option'];
				$selectedLogoField   = $selected_logo_options['field'];
				
				if ($selected_logo_options['option'] == 'upload_logo') {
					$upload_manager = new SJB_UploadPictureManager();
					$upload_manager->getUploadedPictureInfo($xml_logo.'_thumb');
					$logo_link = $upload_manager->getUploadedFileLink($xml_logo.'_thumb');
					$tp->assign('logo_link', $logo_link);
				}
				foreach ($map as $key => $val) {
					unset($map[$key]);
					$key = SJB_XmlImport::encodeSpecialEntities($key);
					$map[$key] = $val;
				}
				$selected   = array_values($map);
				$a_selected = array_keys($map);
				$selectedProduct = $parser_from_id['product_sid'];
			} else {

				$xml = SJB_XmlImport::cleanXmlFromImport($_REQUEST ['xml']);
			}

			$sxml = new simplexml ();
			$xml  = stripslashes($xml);
			$tree = $sxml->xml_load_file($xml, 'array');
			if (isset($tree['@content']))
				$tree = $tree[0];

			if (is_array($tree)) {

				$tree = SJB_XmlImport::convertArray($tree);
				foreach ($tree as $key => $val) {
					unset($tree[$key]);
					$key = SJB_XmlImport::encodeSpecialEntities($key);
					$tree[$key]['val'] = $val;
					$tree[$key]['key'] = $key;
				}
				$field_types = array(0, $type_id);
				$listing_fields = array();
				$i = 0;
				foreach ($field_types as $type) {
					$listing_fields_info = SJB_ListingFieldManager::getListingFieldsInfoByListingType($type);
					foreach ($listing_fields_info as $listing_field_info) {
						if ($listing_field_info['type'] == 'location') {
							foreach ($listing_field_info['fields'] as $fieldInfo) {
								$listing_field = new SJB_ListingField ($fieldInfo);
								$listing_field->setSID($fieldInfo['sid']);
								$listing_fields[$i]['id'] = $listing_field_info['id'].'_'.$listing_field->details->properties ['id']->value;
								$listing_fields[$i]['caption'] = $listing_field->details->properties ['id']->value;
								$i++;
							}
						}
						else {
							$listing_field = new SJB_ListingField ($listing_field_info);
							$listing_field->setSID($listing_field_info ['sid']);
							$listing_fields[$i]['id'] = $listing_field->details->properties ['id']->value;
							$listing_fields[$i]['caption'] = $listing_field->details->properties ['id']->value;
							$i++;
						}
					}

				}
				$listing_fields[$i]['id'] = $listing_fields[$i]['caption']= "date";
				$i++;
				$listing_fields[$i]['id'] = $listing_fields[$i]['caption'] = "url";
				$i++;
				$listing_fields[$i]['id'] = $listing_fields[$i]['caption'] = "external_id";
			} else {
				$errors [] = 'XML syntaxis error.';
			}

		} else {
			$errors [] = 'Please input correct xml';
		}
		if(empty($selectedProduct)) {
			$errors[] = 'Please select a product';
		}

		if (!filter_var($pars_url, FILTER_VALIDATE_URL)) {
			$errors[] = 'Please input correct URL';
		}

		$error = SJB_Request::getVar('error', false, 'GET');
		if ($error) {
			$errors[$error] = true;
		}
		
		$userType = empty($add_new_user) ? 'username' : 'group';
		if ($userType == 'group') {
			$userName = SJB_UserGroupManager::getUserGroupSIDByID($usr_name);
		} else {
			$userName = $usr_name;
		}
		$products = SJB_XmlImport::getProducts($userType, $userName, $errors);
		
		$tp->assign('id', $id);
		$tp->assign('selected', $selected);
		$tp->assign('a_selected', $a_selected);
		$tp->assign('xml', htmlspecialchars($xml));
		$tp->assign('xml_logo', $xml_logo);
		$tp->assign('xmlToUser', $xml);
		$tp->assign('default_value', $defaultValue);

		$tp->assign('form_name', $parsing_name);
		$tp->assign('form_user', $usr_name);
		$tp->assign('form_user_sid', $usr_id);
		$tp->assign('form_url', $pars_url);
		$tp->assign('form_description', $form_description);
		$tp->assign('custom_script', $custom_script);
		$tp->assign('custom_script_users', $custom_script_users);
		$tp->assign('username', $username);
		$tp->assign('external_id', $external_id);
		$tp->assign('import_type', $importType);

		$tp->assign('user_groups', SJB_UserGroupManager::getAllUserGroupsInfo());
		$type_name = SJB_ListingTypeManager::getListingTypeIDBySID($type_id);

		$tp->assign('add_new_user', $add_new_user);
		$tp->assign('type_id', $type_id);
		$tp->assign('type_name', $type_name);
		$tp->assign('errors', $errors);
		$tp->assign('tree', $tree);
		$tp->assign("fields", $listing_fields);
		$tp->assign('logo_options', $logo_options_array);
		$tp->assign('selectedLogoOption', $selectedLogoOption);
		$tp->assign('selectedLogoField', $selectedLogoField);
		$tp->assign('selectedProduct', $selectedProduct);
		$tp->assign('products', $products);
		$tp->assign("uploadMaxFilesize", SJB_UploadFileManager::getIniUploadMaxFilesize());

		$tp->display('add_step_two.tpl');

	}
}
