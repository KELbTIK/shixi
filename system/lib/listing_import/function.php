<?php

class SJB_XmlImport
{
	/**
	 * Storage for posted listings ids for each parser to realize 'snapshot' mode
	 * @var array
	 */
	static $postedByParser = array();
	
	static $addListingErrors = array();
	

	public static function cleanXmlFromImport($xml)
	{
		$xml = str_replace("\r", '', $xml ); // cut new line
		$xml = str_replace("\n", '', $xml ); // cut new line
		$xml = preg_replace('/&(?!amp;)/u', '&amp;', $xml ); // CUT comment
		$xml = preg_replace('#<([-a-z]*)\/>#siu', '<$1>.</$1>', $xml ); // make empty readible
		$xml = preg_replace('#(\<\!\-\-.*?\>)#siu', '', $xml ); // CUT comment
		return $xml;
	}

	public static function activate($id)
	{
		SJB_DB::query("UPDATE parsers SET active='1' WHERE id='{$id}'");
	}

	public static function deactivate($id)
	{
		SJB_DB::query("UPDATE parsers SET active='0' WHERE id='{$id}'");
	}

	public static function addListings($data, $usr_id, $parser_id, $script)
	{
		self::$addListingErrors = array();
		$parser = SJB_XmlImport::getSystemParsers($parser_id);
		if (!isset($parser[0])) {
			return;
		}

		$parser      = $parser[0];
		$currentUser = SJB_UserManager::getObjectBySID($usr_id );
		$listingFields = SJB_ListingFieldManager::getListingFieldsInfoByListingType(0);
		$listingFields = array_merge($listingFields, SJB_ListingFieldManager::getListingFieldsInfoByListingType($parser['type_id']));
		foreach ($data as $listing) {
			if (!$listing) {
				continue;
			}

			if (isset($listing['userSID'])) {
				$user = SJB_UserManager::getObjectBySID($listing['userSID']);
			} else {
				$user = $currentUser;
			}
			
			$listing['access_type'] = 'everyone';
			$listing['active']      = 1;

			if (empty($user)) {
				$listing['user_sid'] = '';
			} else {
				$listing['user_sid'] = $user->getSID();
			}
			
			if (!isset($listing['url'])) {
				$listing['url'] = '';
			}

			$external_id = isset ($listing['external_id']) ? $listing['external_id'] : '';
			$skip        = false;
			if (!empty($script)) {
				eval(stripslashes($script));
			}
			if ($skip) {
				continue;
			}

			// fix for new format of ApplicationSettings
			if (!empty($listing['ApplicationSettings'])) {
				if (preg_match("^[a-z0-9\._-]+@[a-z0-9\._-]+\.[a-z]{2,}\$^iu", $listing['ApplicationSettings'])) {
					$listing['ApplicationSettings'] = array( 'value' => $listing['ApplicationSettings'], 'add_parameter' => 1);
				} elseif(preg_match("^(https?:\/\/)^iu", $listing['ApplicationSettings'])) {
					$listing['ApplicationSettings'] = array( 'value' => $listing['ApplicationSettings'], 'add_parameter' => 2);
				} else {
					//put empty if not valid email or url
					$listing['ApplicationSettings'] = array( 'value' => '', 'add_parameter' => '');
				}
			}
			$logo_options = unserialize($parser['logo_options']);
			if ($logo_options['option'] == 'upload_logo') {
				$uploadManager = new SJB_UploadPictureManager();
				$uploadManager->getUploadedPictureInfo($parser['xml_logo']);
				$link = $uploadManager->getUploadedFileLink($parser['xml_logo']);
				$listing['ListingLogo'] = $link;
			}
			else if ($logo_options['option'] == 'import_logo' && filter_var($listing['ListingLogo'], FILTER_VALIDATE_URL)) {
				$tempImageName = 'ListingLogo_' . substr(md5((string) microtime(true)), 0, 10);
				$tempImagePath = SJB_System::getSystemSettings('UPLOAD_FILES_DIRECTORY') . '/' . $tempImageName;
				file_put_contents($tempImagePath, SJB_HelperFunctions::getUrlContentByCurl($listing['ListingLogo']));

				if (file_exists($tempImagePath)) {
					$_FILES['ListingLogo'] = array (
						'tmp_name' => $tempImagePath,
						'size'     => filesize($tempImagePath),
						'name'     => "{$tempImageName}.png",
						'type'     => '',
					);

					$propertyInfo['second_width']  = SJB_Settings::getSettingByName('listing_thumbnail_width');
					$propertyInfo['second_height'] = SJB_Settings::getSettingByName('listing_thumbnail_height');
					$uploadManager = new SJB_UploadPictureManager();
					$uploadManager->setUploadedFileID($tempImageName);
					$uploadManager->setHeight(SJB_Settings::getSettingByName('listing_picture_height'));
					$uploadManager->setWidth(SJB_Settings::getSettingByName('listing_picture_width'));
					$uploadManager->uploadPicture('ListingLogo', $propertyInfo);
					$listing['ListingLogo'] = $uploadManager->getUploadedFileLink($tempImageName);
					unlink($tempImagePath);
				}
			}

			foreach ($listingFields as $listingField) {
				if ($listingField['type'] == 'location') {
					foreach ($listingField['fields'] as $fields) {
						if (isset($listing[$listingField['id'].'_'.$fields['id']])) {
							if ($fields['id'] == 'Country') {
								$country = $listing[$listingField['id'].'_'.$fields['id']];
								$countrySID = SJB_CountriesManager::getCountrySIDByCountryName($country);
								if (!$countrySID) {
									$countrySID = SJB_CountriesManager::getCountrySIDByCountryCode($country);
								}
								$listing[$listingField['id']][$fields['id']] = $countrySID;
							}
							elseif ($fields['id'] == 'State') {
								$state = $listing[$listingField['id'].'_'.$fields['id']];
								$stateSID = SJB_StatesManager::getStateSIDByStateName($state);
								if (!$stateSID) {
									$stateSID = SJB_StatesManager::getStateSIDByStateCode($state);
								}
								$listing[$listingField['id']][$fields['id']] = $stateSID;
							}
							else {
								$listing[$listingField['id']][$fields['id']] = $listing[$listingField['id'].'_'.$fields['id']];
							}
						}
					}
				}
			}

			$listingObj = new SJB_Listing($listing, $parser['type_id']);
			$listingObj->deleteProperty('featured');
			$listingObj->deleteProperty('status');
			$listingObj->deleteProperty('reject_reason');
			$listingObj->addDataSourceProperty($parser_id);
			if ($logo_options['option'] != 'not_logo') {
				$field_info = SJB_ListingFieldDBManager::getListingFieldInfoByID('ListingLogo');
				SJB_UploadFileManager::fileImport($listing, $field_info);
			}
			if ($parser['product_sid'] && $user) {
				$contractsInfo = SJB_ContractManager::getAllContractsInfoByUserSID($user->getSID());
				$extraInfo     = array();
				$contractId    = null;
				foreach ($contractsInfo as $contractInfo) {
					if ($contractInfo['product_sid'] == $parser['product_sid']) {
						$extraInfo  = unserialize($contractInfo['serialized_extra_info']);
						$contractId = $contractInfo['id'];
						break;
					}
				}
				if (!$extraInfo && $parser['product_sid']) {
					$extraInfo = SJB_ProductsManager::getProductExtraInfoBySID($parser['product_sid']);
				}

				$listingObj->setProductInfo($extraInfo);
				$listingObj->addProperty(array(
					'id'        => 'contract_id',
					'type'      => 'id',
					'value'     => $contractId,
					'is_system' => true
				));
			} elseif (!$parser['product_sid']) {
				self::$addListingErrors[] = 'Listings cannot be posted without a product. Please select an appropriate product in the settings of XML Import.';
				continue;
			}else {
				self::$addListingErrors[] = 'Required user profile fields are not mapped';
				continue;
			}
			
			$listingSid = null;
			if (!empty($external_id)) {
				$listingObj->addExternalIdproperty($listing['external_id']);
				$existingSid = SJB_ListingManager::getListingSidByExternalId($external_id);
				if (is_numeric($existingSid)) {
					$listingSid = $existingSid;
				}
			}
			$properties = $listingObj->getProperties();
			foreach ($properties as $property) {
				$propertyType = $property->type->property_info['type'];
				if ($propertyType === 'tree') {
					if (strpos($property->value, ',') !== false) {
						$propertyValues = explode(',', $property->value);
						$treeValues = array();
						$valuesCollection = array();
						foreach ($propertyValues as $propertyValue) {
							$treeValues[] = "'$propertyValue'";
						}
						$treeValues = is_array($treeValues) ? implode(',', $treeValues) : '';
						$matchedValues = SJB_DB::query("SELECT `sid` FROM `listing_field_tree` WHERE `caption` IN (?w)", $treeValues);
						if (is_array($matchedValues)) {
							foreach($matchedValues as $matchedValue) {
								$valuesCollection[] = $matchedValue['sid'];
							}
							
							$parents = SJB_DB::query("SELECT DISTINCT `parent_sid` FROM `listing_field_tree` WHERE `parent_sid` IN (?l)", $valuesCollection);
							foreach($parents as $parent) {
								if (($key = array_search($parent['parent_sid'], $valuesCollection)) !== false) {
									unset($valuesCollection[$key]);
								}
							}
							
							$value = implode(',', $valuesCollection);
						}
					} else {
						$value = SJB_DB::queryValue("SELECT `sid` FROM  `listing_field_tree` WHERE `caption`='$property->value'");
					}
					if (empty($value)) {
						$value = '';
					}
					$listingObj->setPropertyValue($property->id, $value);
					$listingObj->getProperty($property->id)->type->property_info['value'] = $value;
				}
				if ($propertyType === 'complex') {
					$complexProperties = $property->type->complex->getProperties();
					if (is_array($complexProperties)) {
						foreach ($complexProperties as $complexProperty) {
							if (!$complexProperty->value)
							$listingObj->details->properties[$property->id]->type->complex->setPropertyValue($complexProperty->id, array(1=>''));
						}
					}
				}
				if ($propertyType === 'list' || $propertyType === 'multilist') {
					$ignoreProps = array('data_source', 'access_type');
					if (!in_array($property->id, $ignoreProps)) {
						if ($propertyType === 'multilist' && strpos($property->value, ',') !== false) {
							$propertyValues = explode(',', $property->value);
							$listValues = array();
							$valuesCollection = array();
							foreach ($propertyValues as $propertyValue) {
								$listValues[] = "'$propertyValue'";
							}
							$listValues = is_array($listValues) ? implode(',', $listValues) : '';
							$matchedValues = SJB_DB::query("SELECT `sid` FROM `listing_field_list` WHERE `value` IN (?w) AND `field_sid` = ?n", $listValues, $property->type->property_info['sid']);
							if (is_array($matchedValues)) {
								foreach($matchedValues as $matchedValue) {
									$valuesCollection[] = $matchedValue['sid'];
								}
								$value = implode(',', $valuesCollection);
							}
						} else {
							$value = SJB_DB::queryValue("SELECT `sid` FROM `listing_field_list` WHERE `value`= ?s AND `field_sid` = ?n", $property->value, $property->type->property_info['sid']);
						}
						if (empty($value)) {
							$value = '';
						}
						$listingObj->setPropertyValue($property->id, $value);
						$listingObj->getProperty($property->id)->type->property_info['value'] = $value;
					}
				}
			}
			
			// set listing sid if listing already exists
			$updatedListing = false;
			if (is_numeric($listingSid)) {
				$listingObj->setSID($listingSid);
				$updatedListing = true;
			}

			SJB_ListingManager::saveListing($listingObj);
			SJB_ProductsManager::incrementPostingsNumber($parser['product_sid']);
			$listingSid = $listingObj->getSID();
			SJB_Statistics::addStatistics('addListing', $listingObj->getListingTypeSID(), $listingSid, false, $extraInfo['featured'], $extraInfo['priority']);
			
			SJB_ListingManager::activateListingBySID($listingSid, false);
			if ($extraInfo['featured']) {
				SJB_ListingManager::makeFeaturedBySID($listingSid);
			}
			if ($extraInfo['priority']) {
				SJB_ListingManager::makePriorityBySID($listingSid);
			}
			
			// and save listing sid to self::$postedByParser storage
			self::$postedByParser[] = $listingSid;
			
			// set expiration date by Product for updated listing, like new listing publication
			if ($updatedListing) {
				SJB_ListingManager::setListingExpirationDateBySid($listingSid);
			}
		}
		
		SJB_BrowseDBManager::addListings(self::$postedByParser);
	}

	public static function getRootNode($xml)
	{
		preg_match('/<(.*?)>/i', $xml, $mathc );
		if (isset($mathc[1]) && strlen($mathc[1]) > 0)
			return $mathc[1];
		return false;
	}

	public static function megaReader($root, $array)
	{
		$tmp_arr = array();
		foreach ($array as $key => $val) {
			if ($key == $root) {
				$tmp_arr = array_merge($tmp_arr, $val);
			} elseif (is_array($val)) {
				$tmp_arr = array_merge($tmp_arr, self::megaReader($root, $val));
			}
		}
		return $tmp_arr;
	}

	public static function getListingArray($root, $tree)
	{
		return SJB_XmlImport::megaReader($root, $tree);
	}

	public static function parseData($found, $map, $defaultValues = array())
	{
        $data = array();
        $external_id = '';
        foreach ($found as $one) {
            $tmp = array();
            foreach ($map as $remote => $local) {
                if(strpos($remote, 'external_id') !== false) {
                    $external_id = str_replace("_external_id", "", $remote);
                    $remote = str_replace("_external_id", "", $remote);
                    $external_id = $one[$external_id];
                }
                if (isset($one[$remote])) {
                    // fix convert of &nbsp; to non-ASCII character
                    $one[$remote] = str_replace("&nbsp;", " ", $one[$remote]);
                    if (is_array($local)) {
                        foreach ($local as $arr) {
                            $tmp[$arr] = stripslashes(html_entity_decode($one[$remote], ENT_COMPAT,'UTF-8'));
                        }
                    }
                    else {
                        $tmp[$local] = stripslashes(html_entity_decode($one[$remote], ENT_COMPAT,'UTF-8'));
                        $tmp['external_id'] = $external_id;
                    }
                }
            }
            $data[] = array_merge($tmp, $defaultValues);
        }
        return $data;
	}

	public static function convertArray($array, $parent = '')
	{
		$tmp = array();
		foreach ($array as $key => $val) {
			if (is_array($val))
				$tmp = array_merge($tmp, self::convertArray($val, (!is_numeric($key) ? $key : '')));
			else
				$tmp[(! empty($parent) ? $parent . '_' : '') . $key] = $val;
		}

		return $tmp;
	}


	public static function is_multy($array)
	{
		foreach ($array as $ar) {
			if (!is_array($ar)) {
				return false;
			}
		}
		return true;
	}

	public static function runImport($id_pars = '')
	{
		$work_id = SJB_XmlImport::getSystemParsers($id_pars);
		$result = array('total' => 0, 'errors');

		foreach ($work_id as $pars) {
			$result['total']++;
			$map               = unserialize($pars['maper']);
			$defaultValues     = ($pars['default_value'] != '')?unserialize($pars['default_value']):array();
			$defaultValuesUser = ($pars['default_value_user'] != '')?unserialize($pars['default_value_user']):array();
			// MAP (REMOTE >> LOCAL)
			$usr_id            = $pars['usr_id'];

			if ($root = SJB_XmlImport::getRootNode($pars['xml'])) {
				$sxml      = new simplexml();
	            $xmlString = SJB_HelperFunctions::getUrlContentByCurl($pars['url']);
	            if ($xmlString === false) {
					$result['errors'][] = 'Failed to open data URL, data source - '.$pars['name'];
					continue;
	            }

				@$tree = $sxml->xml_load_file($xmlString, 'array');
				if (!$tree || ! is_array($tree)){
					$result['errors'][] = 'Failed to open data URL, data source - '.$pars['name'];
					continue;
				}

				if (isset($tree['@content']))
					$tree = $tree[0];

				$found = SJB_XmlImport::getListingArray($root, $tree);
				if (!SJB_XmlImport::is_multy($found)) {
					$tmp     = $found;
					$found   = array();
					$found[] = $tmp;
				}

				foreach ($found as $key => $val) {
					$found[$key] = SJB_XmlImport::convertArray($val);
				}

				// field in username to mapping it, and default mapping(incomingFieldName -> username)
				$parsUsername = $pars['username'];
				$mapUser[$parsUsername] = 'username';

				// check for non default mapping
				if ($pars['add_new_user'] == 1 && !empty($pars['maper_user'])) {
					$mapUser = unserialize($pars['maper_user']);
					if (array_key_exists($parsUsername, $mapUser)) {
						$mapUser[$parsUsername] = array($mapUser[$parsUsername], 'username');
					} else {
						$mapUser[$parsUsername] = 'username';
					}
				}

				$data = SJB_XmlImport::parseData($found, $map, $defaultValues);
				if ($pars['add_new_user'] == 1) {
					$dataUser = SJB_XmlImport::parseData($found, $mapUser, $defaultValuesUser);
					$user_group_sid = $pars['usr_id'];
					$userProfileFields = SJB_UserProfileFieldManager::getFieldsInfoByUserGroupSID($user_group_sid);
					foreach ($dataUser as $key => $user){
						if (isset($user['username']) && $user['username'] != '') {
							$username = preg_replace('/[\\/\\\:*?\"<>|%#$\s\'-]/u', '_',html_entity_decode($user['username']));
							$username = str_replace('&', 'And', $username);
							// If user_email_as_username set to TRUE

							$skip = false;
							$user['username'] = $username;
							$user['password']['confirmed'] = $user['password']['original'] = $username;
							if (SJB_UserGroupManager::isUserEmailAsUsernameInUserGroup($user_group_sid)) {
								$user['email'] = $username;
							}
							if (!empty($pars['custom_script_users'])) {
								eval(stripslashes($pars['custom_script_users']));
							}
							if ($skip) {
								continue;
							}

							if (!is_null($user_group_sid)) {
								$user_group_info = SJB_UserGroupManager::getUserGroupInfoBySID($user_group_sid);
								if ( isset($user_group_info['user_email_as_username']) && (($user_group_info['user_email_as_username']) == true) ) {
									$userSID = SJB_UserManager::getUserSIDbyEmail($user['email']);
								} else {
									$userSID = SJB_UserManager::getUserSIDbyUsername($user['username']);
								}
							}
							else {
								$userSID = SJB_UserManager::getUserSIDbyUsername($user['username']);
							}
							if (empty($userSID)) {
								foreach ($userProfileFields as $userProfileField) {
									if ($userProfileField['type'] == 'location') {
										foreach ($userProfileField['fields'] as $fields) {
											if (isset($user[$userProfileField['id'].'_'.$fields['id']])) {
												if ($fields['id'] == 'Country') {
													$country = $user[$userProfileField['id'].'_'.$fields['id']];
													$countrySID = SJB_CountriesManager::getCountrySIDByCountryName($country);
													if (!$countrySID) {
														$countrySID = SJB_CountriesManager::getCountrySIDByCountryCode($country);
													}
													$user[$userProfileField['id']][$fields['id']] = $countrySID;
												}
												elseif ($fields['id'] == 'State') {
													$state = $user[$userProfileField['id'].'_'.$fields['id']];
													$stateSID = SJB_StatesManager::getStateSIDByStateName($state);
													if (!$stateSID) {
														$stateSID = SJB_StatesManager::getStateSIDByStateCode($state);
													}
													$user[$userProfileField['id']][$fields['id']] = $stateSID;
												}
												else {
													$user[$userProfileField['id']][$fields['id']] = $user[$userProfileField['id'].'_'.$fields['id']];
												}
											}
										}
									}
								}
								$userObj = SJB_ObjectMother::createUser($user, $user_group_sid);
								$userObj->deleteProperty('active');
								$userObj->deleteProperty('featured');
								$userObj->deleteProperty('captcha');
								SJB_UserManager::saveUser($userObj);
								SJB_UserManager::activateUserByUserName($userObj->getUserName());
								$contract = new SJB_Contract(array('product_sid' => $pars['product_sid']));
								$contract->setUserSID($userObj->getSID());
								$contract->saveInDB();
								$data[$key]['userSID'] = $userObj->getSID();
							}
							else {
								$data[$key]['userSID'] = $userSID;
							}
						}
					}
				}
				
				// set start value for current parser
				self::$postedByParser = array();
				
				if (count($data) > 0) {
					SJB_XmlImport::addListings($data, $usr_id, $pars['id'], $pars['custom_script']);
				}
				
				// clear listings, not saved or updated by current snapshot import
				if ($pars['import_type'] == 'snapshot') {
					if (sizeof(self::$postedByParser)) {
						SJB_DB::queryExec("DELETE FROM `listings` WHERE `data_source` = ?n AND `sid` NOT IN (?l)", $pars['id'], self::$postedByParser);
					} else {
						SJB_DB::queryExec("DELETE FROM `listings` WHERE `data_source` = ?n", $pars['id']);
					}
				}
				
			} else {
				$result['errors'][] = 'Not correct XML in parser - '.$pars['name'];
				continue;
			}

		}
		if (!empty(self::$addListingErrors)) {
			if (!isset($result['errors'])) {
				$result['errors'] = self::$addListingErrors;
			} else {
				$result['errors'] = array_merge($result['errors'], self::$addListingErrors);
			}
		}
		return $result;
	}

	public static function getSystemParsers($id = '', $all = false)
	{
		return SJB_DB::query("SELECT * FROM parsers WHERE " . (!empty($id)?"id='{$id}'":(!$all?"active='1'":"active='0' OR active='1'")));
	}

	public static function getProducts($userType, $userName, &$errors)
	{
		$products = array();
		if ($userType == 'group') {
			$products = self::getProductsByUserGroup($userName);
		} else {
			try {
				$products = self::getProductsByUserName($userName);
			} catch (Exception $e) {
				$errors[] = SJB_I18N::getInstance()->gettext('Backend', $e->getMessage());
			}
			
		}

		return $products;
	}
	
	public static function decodeSpecialEntities($val)
	{
		$val = str_replace('_dog_', '@', $val);
		$val = str_replace('_col_', ':', $val);
		return $val;
	}
	
	public static function encodeSpecialEntities($val)
	{
		$val = str_replace('@', '_dog_', $val);
		$val = str_replace(':', '_col_', $val);
		return $val;
	}
	
	public static function translateProductsName($products)
	{
		foreach ($products as &$product) {
			$product['name'] = SJB_I18N::getInstance()->gettext('Backend', $product['name']);
		}
		
		return $products;
	}	
	
	private static function getProductsByUserGroup($userGroupSID)
	{
		$products = array();
		$productTypes = array('post_listings', 'mixed_product');
		foreach ($productTypes as $productType) {
			$productsByType = SJB_ProductsManager::getProductsByProductType($productType);
			foreach ($productsByType as $item => $product) {
				if ($product['user_group_sid'] != $userGroupSID) {
					unset($productsByType[$item]);
				}
			}
			$products = array_merge($products, $productsByType);
		}
		
		return $products;
	}


	/**
	 * @param $userName
	 * @return array
	 * @throws Exception
	 */
	private static function getProductsByUserName($userName)
	{
		$products = array();
		$userSid = SJB_UserManager::getUserSIDbyUsername($userName);
		if (empty($userSid)) {
			throw new Exception("User not exists. Please enter user name of existing user to the 'User Name' field.");
		}
		$contractsInfo = SJB_ContractManager::getAllContractsInfoByUserSID($userSid);
		if (empty($contractsInfo)) {
			throw new Exception("User doesn't have any product. Please select another user or add at least one posting product to the current user.");
		}
		foreach ($contractsInfo as $contractInfo) {
			$products[] = SJB_ProductsManager::getProductInfoBySID($contractInfo['product_sid']);
		}
		
		return $products;
	}
	
} // END of SJB_XmlImport


