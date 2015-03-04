<?php

class SJB_Admin_ListingImport_AddImport extends SJB_Function
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
			$user_type = SJB_Request::getVar('user_type');
			$user_name = SJB_Request::getVar('parser_user');
			$products = SJB_XmlImport::getProducts($user_type, $user_name, $errors);
			$response = array(
				'products' => empty($products) ? '' : SJB_XmlImport::translateProductsName($products),
				'error' => empty($errors) ? '' : array_pop($errors)
			);
			die(json_encode($response));
		}

		$tp = SJB_System::getTemplateProcessor();
		$add_level = SJB_Request::getVar('add_level', 1);

		// check for errors
		if ($add_level == '3') {
			$selectUserType = SJB_Request::getVar('selectUserType');
			$addNewUser = 0;
			if ($selectUserType == 'username') {
				$usr_name = (isset($_REQUEST['parser_user']) ? SJB_DB::quote($_REQUEST['parser_user']) : '');
				$usr_id = SJB_UserManager::getUserSIDbyUsername($usr_name);
				if (empty($usr_name)) {
					$errors[] = 'Please enter user name of existing user to the "User Name" field';
					$usr_name = '';
				} else {
					$user_sid_exists = SJB_UserManager::getUserSIDbyUsername($usr_name);
					if (empty($user_sid_exists)) {
						$errors[] = 'User "' . $usr_name . '" not exists. Please enter user name of existing user to the "User Name" field';
						$usr_name = '';
					}
				}
			}
			elseif ($selectUserType == 'group') {
				$userGroupSid = (isset($_REQUEST['parser_user']) ? $_REQUEST['parser_user'] : 0);
				$usr_id       = $userGroupSid;
				$usr_name     = SJB_UserGroupManager::getUserGroupIDBySID($usr_id);
				$addNewUser   = 1;
			}

			if ($errors) {
				$add_level = 2;
			}
		}


		$listings_type = SJB_ListingTypeManager::getAllListingTypesInfo();
		$types = array();
		foreach ($listings_type as $one) {
			$types[$one['sid']] = $one['id'];
		}
		$tp->assign('types', $types);
		$selected_logo_options = null;

		switch ($add_level) {

			case '1':
				$template = 'add_step_one.tpl';
				/*
				  $types = array();
				  foreach ( $listings_type as $one ) {
					  $types[$one['sid']] = $one['id'];
				  }
				  $tp->assign('types', $types);
				  */
				$tp->display('add_step_one.tpl');
				break;


			case '2':
				$template = 'add_step_two.tpl';

				$original_xml = SJB_Request::getVar('xml');
				$xml = $original_xml;

				$tree = '';
				$listing_fields = array();
				$logo_options_array = array('not_logo' => 'Do Not Import Logo',
					'import_logo' => 'Import Logo with Listings',
					'upload_logo' => 'Upload Logo for Imported Listings');

				$parsing_name       = SJB_Request::getVar('parser_name');
				$usr_name           = SJB_Request::getVar('parser_user');
				$pars_url           = SJB_Request::getVar('parser_url');
				$form_description   = SJB_Request::getVar('form_description', '', 'POST');
				$type_id            = SJB_Request::getVar('type_id', '', 'POST');
				$selectedLogoOption = SJB_Request::getVar('logo_options');
				$selectedLogoField  = SJB_Request::getVar('import_logo_field');
				$selectedProduct    = SJB_Request::getVar('postUnderProduct');
				$id                 = SJB_Request::getVar('id', 0, 'GET');
				$selected           = array();
				$a_selected         = array();

				if (!empty($_REQUEST['xml']) || $id > 0) {
					// step 2 OR edit exist

					if ($id > 0) { // load exist parser

						$parser_from_id = SJB_XmlImport::getSystemParsers($id);

						if (isset($parser_from_id[0]['name'])) {
							$parser_from_id = $parser_from_id[0];
						}

						$parsing_name = $parser_from_id['name'];
						$usr_id = $parser_from_id['usr_id'];
						$usr_name = $parser_from_id['usr_name'];
						$form_description = $parser_from_id['description'];
						$pars_url = $parser_from_id['url'];
						$type_id = $parser_from_id['type_id'];
						$selected_logo_options = unserialize($parser_from_id['logo_options']);
						$selectedLogoOption = $selected_logo_options['option'];
						$selectedLogoField= $selected_logo_options['field'];
						$selectedProduct = $parser_from_id['product_sid'];
						$xml = $parser_from_id['xml'];
						$xml = SJB_XmlImport::cleanXmlFromImport($xml);

						$map = unserialize($parser_from_id['maper']);
						$selected = array_values($map);
						$a_selected = array_keys($map);

					} else {
						$xml = SJB_XmlImport::cleanXmlFromImport($_REQUEST['xml']);
					}

					$sxml = new simplexml();
					$tree = $sxml->xml_load_file($xml, 'array');
					if (isset($tree['@content'])) {
						$tree = $tree[0];
					}
					
					if (is_array($tree)) {

						$tree = SJB_XmlImport::convertArray($tree);
						foreach ($tree as $key => $val) {
							unset($tree[$key]);
							// replace '@' and ':'
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
								} else {
									$listing_field = new SJB_ListingField($listing_field_info);
									$listing_field->setSID($listing_field_info['sid']);
									$listing_fields[$i]['id'] = $listing_field->details->properties['id']->value;
									$listing_fields[$i]['caption'] = $listing_field->details->properties ['id']->value;
									$i++;
								}
							}
						}
						$listing_fields[$i]['id'] = $listing_fields[$i]['caption'] = "date";
						$i++;
						$listing_fields[$i]['id'] = $listing_fields[$i]['caption'] = "url";
						$i++;
						$listing_fields[$i]['id'] = $listing_fields[$i]['caption'] = "external_id";
					} else {
						$errors[] = 'XML syntaxis error.';
						$template = 'add_step_one.tpl';
					}

				} else {
					$errors[] = 'Please input correct xml';
					$template = 'add_step_one.tpl';
				}

				$tp->assign('id', $id);
				$tp->assign('selected', $selected);
				$tp->assign('a_selected', $a_selected);
				$tp->assign('xml', htmlspecialchars($xml));
				$tp->assign('xmlToUser', $xml);
				$tp->assign('user_groups', SJB_UserGroupManager::getAllUserGroupsInfo());
				$tp->assign('form_name', $parsing_name);
				$tp->assign('form_user', $usr_name);
				$tp->assign('form_url', $pars_url);
				$tp->assign('form_description', $form_description);
				$type_name = SJB_ListingTypeManager::getListingTypeIDBySID($type_id);
				$tp->assign('type_id', $type_id);
				$tp->assign('type_name', $type_name);
				$tp->assign('errors', $errors);
				$tp->assign('tree', $tree);
				$tp->assign("fields", $listing_fields);
				$tp->assign('logo_options', $logo_options_array);
				$tp->assign('selectedLogoOption', $selectedLogoOption);
				$tp->assign('selectedLogoField', $selectedLogoField);
				$tp->assign('selectedProduct', $selectedProduct);
				$tp->assign("uploadMaxFilesize", SJB_UploadFileManager::getIniUploadMaxFilesize());
				$tp->display($template);
				break;

			case '3':
				$parsing_name       = (isset($_REQUEST['parser_name']) ? SJB_DB::quote($_REQUEST['parser_name']) : '');
				$pars_url           = (isset($_POST['parser_url']) ? SJB_DB::quote($_POST['parser_url']) : '');
				$selectedLogoOption = (isset($_POST['logo_options'])) ? $_POST['logo_options'] : '';
				$selectedLogoField  = (isset($_POST['import_logo_field'])) ? $_POST['import_logo_field'] : '';
				$form_description   = (isset($_REQUEST['form_description']) ? SJB_DB::quote($_REQUEST['form_description']) : "");
				$type_id            = (isset($_POST['type_id']) ? intval($_POST['type_id']) : "");
				$script             = (isset($_POST['custom_script']) && !empty($_POST['custom_script'])) ? SJB_DB::quote($_POST['custom_script']) : "";
				$script_users       = SJB_DB::quote(SJB_Request::getVar('custom_script_users', '', SJB_Request::METHOD_POST));
				$defaultValue       = SJB_Request::getVar('default_value', false);
				$defaultValueUser   = SJB_Request::getVar('user_default_value', false);
				$selectedProduct    = SJB_Request::getVar('postUnderProduct');
				$importType         = SJB_Request::getVar('import_type', 'increment');

				if ($defaultValue) {
					foreach ($defaultValue as $key => $val)
						$defaultValue[$key] = htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
				}
				if ($defaultValueUser) {
					foreach ($defaultValueUser as $key => $val)
						$defaultValueUser[$key] = htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
				}

				$original_xml = (!empty($_POST['xml']) ? SJB_DB::quote($_POST['xml']) : '');
				$id           = (isset($_GET['id']) ? intval($_GET['id']) : 0);
				$addQuery     = '';
				$username     = SJB_XmlImport::decodeSpecialEntities(SJB_Request::getVar('username', ''));
				$external_id  = str_replace('_dog_', '@', SJB_Request::getVar('external_id', ''));
				$site_url = SJB_System::getSystemSettings("SITE_URL");
				if ($addNewUser == 1 && empty($_REQUEST['mapped_user'])) {
					$error = 'Required user profile fields are not mapped';
					SJB_HelperFunctions::redirect($site_url . '/edit-import/?id=' . $id. '&save_error='.base64_encode($error));
				}
				if (!empty($_REQUEST['mapped']) && is_array($_REQUEST['mapped']) && !empty($original_xml) && empty($errors)) {
					// make map
					$map1 = array();
					$map2 = array();
					$serUserMap = '';

					foreach ($_REQUEST['mapped'] as $one) {
						$tmp = explode(':', $one);
						$map1[] = $tmp[0];
						$map2[] = $tmp[1];
					}
					if ($addNewUser == 1 && !empty($_REQUEST['mapped_user']) && is_array($_REQUEST['mapped_user'])) {
						// make map
						$mapUser1 = array();
						$mapUser2 = array();
						foreach ($_REQUEST['mapped_user'] as $one) {
							$tmp = explode(':', $one);
							$mapUser1[] = str_replace('user_', '', $tmp[0]);
							$mapUser2[] = $tmp[1];
						}
						foreach ($mapUser1 as $key => $val) {
							$val = SJB_XmlImport::decodeSpecialEntities($val);
							$mapUser[$val] = $mapUser2[$key];
						}
						$serUserMap = serialize($mapUser);
					}
					//$map = array_combine($map1, $map2); // PHP5
					foreach ($map1 as $key => $val) {
						$val = SJB_XmlImport::decodeSpecialEntities($val);
						$map[$val] = $map2[$key];
					}

					if ($selectedLogoOption && $selectedLogoOption != 'not_logo'){
						//get real data without any cache
						if (!SJB_ListingFieldDBManager::getListingFieldInfoByID('ListingLogo')) {
							$listing_field_info = array(
								'id'          => 'ListingLogo',
								'type'        => 'logo',
								'is_system'   => false,
								'is_required' => false,
								'caption'     => 'Listing Logo',
							);
							$listing_field = new SJB_ListingField($listing_field_info, $type_id);
							$pages         = SJB_PostingPagesManager::getFirstPageEachListingType();
							SJB_ListingFieldManager::saveListingField($listing_field, $pages);
						}
						if ($key = array_search('ListingLogo', $map) !== false) {
							unset($map[$key]);
						}
					}

					if ($defaultValue) {
						foreach ($defaultValue as $key => $val) {
							if ($val == '') {
								unset($defaultValue[$key]);
							}
						}
						$defaultValue = SJB_db::quote(serialize($defaultValue));
						$addQuery .= ", default_value = '" . $defaultValue . "'";
					}
					if ($defaultValueUser) {
						foreach ($defaultValueUser as $keyuser => $valuser) {
							if ($valuser == '') {
								unset($defaultValueUser[$keyuser]);
							}
						}
						$defaultValueUser = SJB_db::quote(serialize($defaultValueUser));
						$addQuery .= ", default_value_user = '" . $defaultValueUser . "'";
					}

					$queryParsUrl = SJB_DB::quote($pars_url);
					$queryImportType = SJB_DB::quote($importType);
					$queryId = intval($id);
					$query = "SET
							`custom_script_users` = ?s,
							`custom_script` = ?s,
							`type_id` = ?n,
							`name` = ?s,
							`description` = ?s,
							`url` = ?s,
							`usr_id` = ?n,
							`usr_name` = ?s,
							`maper_user` = ?s,
							`xml` = ?s,
							`add_new_user` = ?n,
							`username` = ?s,
							`external_id` = ?s,
							`product_sid` = ?n,
							`import_type` = ?s
							{$addQuery}";
					if ($id > 0) {
						SJB_DB::query("UPDATE `parsers` {$query} WHERE id = ?n", $script_users, $script, $type_id, $parsing_name, $form_description, $queryParsUrl, $usr_id, $usr_name, $serUserMap, $original_xml, $addNewUser, $username, $external_id, $selectedProduct, $queryImportType, $queryId);
					} else {
						$id = SJB_DB::query("INSERT INTO `parsers` {$query}", $script_users, $script, $type_id, $parsing_name, $form_description, $queryParsUrl, $usr_id, $usr_name, $serUserMap, $original_xml, $addNewUser, $username, $external_id, $selectedProduct, $queryImportType);
					}

					$errorFile = '';
					$xml_logo = null;
					switch ($selectedLogoOption) {
						case 'import_logo' :
							$map[$selectedLogoField] = 'ListingLogo';
							break;
						case 'upload_logo' :
							if ( ! empty($_FILES['upload_logo_file'])) {
								if ($_FILES['upload_logo_file']['error']) {
									$errorFile = SJB_UploadFileManager::getErrorId($_FILES['upload_logo_file']['error']);
								} else {
									$width = SJB_Settings::getSettingByName('listing_picture_width');
									$height = SJB_Settings::getSettingByName('listing_picture_height');
									$property_info['second_width']  = SJB_Settings::getSettingByName('listing_thumbnail_width');
									$property_info['second_height'] = SJB_Settings::getSettingByName('listing_thumbnail_height');
									$picture = new SJB_UploadPictureManager();
									$picture->setWidth($width);
									$picture->setHeight($height);
									if ($picture->isValidUploadedPictureFile('upload_logo_file')) {
										$xml_logo = "XMLImportLogo_{$id}";
										$picture->setUploadedFileID($xml_logo);
										$picture->uploadPicture('upload_logo_file', $property_info);
									}
								}
							}
							break;
					}
					$logo_options = serialize(array(
						'option' => $selectedLogoOption,
						'field' => $selectedLogoField
					));
					$serMap = serialize($map);
					if ($xml_logo) {
						SJB_DB::query("UPDATE `parsers` SET maper = ?s, `xml_logo` = ?s, logo_options = ?s  WHERE id = ?n", $serMap, $xml_logo, $logo_options, $id);
					} else {
						SJB_DB::query("UPDATE `parsers` SET maper = ?s, logo_options = ?s  WHERE id = ?n", $serMap, $logo_options, $id);
					}

					$form_submitted = SJB_Request::getVar('form_action');

					if ($form_submitted == 'save_info') {
						SJB_HelperFunctions::redirect($site_url . '/show-import/');
					} elseif ($form_submitted == 'apply_info') {
						$getterParameters = '?id=' . $id;
						if ($errorFile) {
							$getterParameters .= '&error=' . $errorFile;
						}
						SJB_HelperFunctions::redirect($site_url . '/edit-import/' . $getterParameters );
					}
				} else {
					if (empty($errors))
						$errors[] = 'No data to save';
					$tp->assign('errors', $errors);
					$tp->assign('xml', htmlspecialchars($original_xml));
					$tp->assign('xmlToUser', $original_xml);
					$tp->assign('form_name', $parsing_name);
					$tp->assign('form_user', $usr_name);
					$tp->assign('form_url', $pars_url);
					$tp->assign('form_description', $form_description);
					$tp->display('add_step_three.tpl');
				}
				break;
		}
	}
}
