<?php

class SJB_Admin_Users_Users extends SJB_Function
{
	public function isAccessible()
	{
		$passedParametersViaUri = SJB_UrlParamProvider::getParams();
		$userGroupID = $passedParametersViaUri ? array_shift($passedParametersViaUri) : false;
		$this->setPermissionLabel('manage_' . mb_strtolower($userGroupID, 'UTF-8'));
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$template = SJB_Request::getVar('template', 'users.tpl');
		$searchTemplate = SJB_Request::getVar('search_template', 'user_search_form.tpl');
		$passedParametersViaUri = SJB_UrlParamProvider::getParams();
		$userGroupID = $passedParametersViaUri ? array_shift($passedParametersViaUri) : false;
		$userGroupSID = $userGroupID ? SJB_UserGroupManager::getUserGroupSIDByID($userGroupID) : null;
		$errors = array();
		/********** A C T I O N S   W I T H   U S E R S **********/
		$action = SJB_Request::getVar('action_name');

		if (!empty($action)) {
			$users_sids = SJB_Request::getVar('users', array());
			$_REQUEST['restore'] = 1;

			switch ($action) {

				case  'approve':
					foreach ($users_sids as $user_sid => $value) {
						$username = SJB_UserManager::getUserNameByUserSID($user_sid);
						SJB_UserManager::setApprovalStatusByUserName($username, 'Approved');
						SJB_UserManager::activateUserByUserName($username);
						SJB_UserDBManager::deleteActivationKeyByUsername($username);

						if (!SJB_SocialPlugin::getProfileSocialID($user_sid)) {
							SJB_Notifications::sendUserWelcomeLetter($user_sid);
						} else {
							SJB_Notifications::sendUserApprovedLetter($user_sid);
						}
					}
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . SJB_Navigator::getURI());
					break;

				case  'reject':
					$rejection_reason = SJB_Request::getVar('rejection_reason', '');
					foreach ($users_sids as $user_sid => $value) {
						$username = SJB_UserManager::getUserNameByUserSID($user_sid);
						SJB_UserManager::setApprovalStatusByUserName($username, 'Rejected', $rejection_reason);
						SJB_UserManager::deactivateUserByUserName($username);
						SJB_Notifications::sendUserRejectedLetter($user_sid);
					}
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . SJB_Navigator::getURI());
					break;

				case  'activate':
					foreach ($users_sids as $user_sid => $value) {
						$username = SJB_UserManager::getUserNameByUserSID($user_sid);
						$userinfo = SJB_UserManager::getUserInfoByUserName($username);
						SJB_UserManager::activateUserByUserName($username);
						if ($userinfo['approval'] == 'Approved') {
							SJB_UserDBManager::deleteActivationKeyByUsername($username);
							SJB_Notifications::sendUserApprovedLetter($user_sid);
						}
					}
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . SJB_Navigator::getURI());
					break;

				case 'deactivate':
					foreach ($users_sids as $user_sid => $value) {
						$username = SJB_UserManager::getUserNameByUserSID($user_sid);
						SJB_UserManager::deactivateUserByUserName($username);
					}
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . SJB_Navigator::getURI());
					break;

				case 'delete':
						foreach (array_keys($users_sids) as $user_sid) {
							try {
								SJB_UserManager::deleteUserById($user_sid);
							} catch (Exception $e) {
								$errors[] = $e->getMessage();
							}
						}
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . SJB_Navigator::getURI());
					break;

				case 'send_activation_letter':
					foreach ($users_sids as $user_sid => $value)
						SJB_Notifications::sendUserActivationLetter($user_sid);
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . SJB_Navigator::getURI());
					break;

				case 'change_product':
					$productToChange = SJB_Request::getVar('product_to_change');
					
					if ( empty($productToChange) )
						$productToChange = 0;
					foreach ( $users_sids as $user_sid => $value ) {
						$user = SJB_UserManager::getObjectBySID($user_sid);
						// UNSUBSCRIBE selected
						if ($productToChange == 0) {
							SJB_ContractManager::deleteAllContractsByUserSID($user_sid);
						} else {
							$productInfo = SJB_ProductsManager::getProductInfoBySID($productToChange);
							$listingNumber = SJB_Request::getVar('number_of_listings', null);
							if (is_null($listingNumber) && !empty($productInfo['number_of_listings']))
								$listingNumber = $productInfo['number_of_listings'];

							$contract = new SJB_Contract(array('product_sid' => $productToChange, 'numberOfListings' => $listingNumber, 'is_recurring' => 0));
							$contract->setUserSID($user_sid);
							$contract->saveInDB();
							if ($contract->isFeaturedProfile()) {
								SJB_UserManager::makeFeaturedBySID($user_sid);
							}
						}
					}
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . SJB_Navigator::getURI());
					break;
					
				case 'ban_ip':
					$cantBanUsers = array();
					foreach ($users_sids as $user_sid => $value) {
						$user = SJB_UserManager::getUserInfoBySID($user_sid);
						if ($user['ip'] && !SJB_IPManager::getBannedIPByValue($user['ip'])) {
							SJB_IPManager::makeIPBanned($user['ip']);
						} else {
							$cantBanUsers[] = $user['username'];
						}
					}
					if ($cantBanUsers) {
						$tp->assign('cantBanUsers', $cantBanUsers);
					} else {
						SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . SJB_Navigator::getURI());
					}
					break;

				case 'unban_ip':
					$cantUnbanIPs = array();
					foreach ($users_sids as $user_sid => $value) {
						$user = SJB_UserManager::getUserInfoBySID($user_sid);
						if ($user['ip'] !== '') {
							if (SJB_IPManager::getBannedIPByValue($user['ip'])) {
								SJB_IPManager::makeIPEnabledByValue($user['ip']);
							}
							elseif (SJB_UserManager::checkBan($errors, $user['ip'])) {
								$cantUnbanIPs[] = $user['ip'];
							}
						}
					}
					if ($cantUnbanIPs) {
						$tp->assign('rangeIPs', $cantUnbanIPs);
					} else {
						SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . SJB_Navigator::getURI());
					}
					break;

				default:
					unset($_REQUEST['restore']);
					break;
			}
			if (empty($errors)) {
				SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . SJB_Navigator::getURI());
			}
		}

		/***************************************************************/

		$_REQUEST['action'] = 'search';

		$user = new SJB_User(array(), $userGroupSID);

		$user->addProperty(array
			(
				'id' => 'user_group',
				'type' => 'list',
				'value' => '',
				'is_system' => true,
				'list_values' => SJB_UserGroupManager::getAllUserGroupsIDsAndCaptions()
			)
		);
		
		$user->addProperty(array
			(
				'id' => 'registration_date',
				'type' => 'date',
				'value' => '',
				'is_system' => true
			)
		);
		$user->addProperty(array(
				'id' => 'approval',
				'caption' => 'Approval',
				'type' => 'list',
				'list_values' => array(
					array(
						'id' => 'Pending',
						'caption' => 'Pending',
					),
					array(
						'id' => 'Approved',
						'caption' => 'Approved',
					),
					array(
						'id' => 'Rejected',
						'caption' => 'Rejected',
					)
				),
				'length' => '10',
				'is_required' => false,
				'is_system' => true,
			)
		);

		// get array of accessible products
		$productsSIDs = SJB_ProductsManager::getProductsIDsByUserGroupSID($userGroupSID);
		$products = array();
		foreach ($productsSIDs as $key => $productSID) {
			$product = SJB_ProductsManager::getProductInfoBySID($productSID);
			$products[$key] = $product;
			if (!empty($product['pricing_type']) && $product['pricing_type'] == 'volume_based' && !empty($product['volume_based_pricing'])) {
				$volumeBasedPricing = $product['volume_based_pricing'];
				$minListings = min($volumeBasedPricing['listings_range_from']);
				$maxListings = max($volumeBasedPricing['listings_range_to']);
				$countListings = array();
				for ($i = $minListings; $i <= $maxListings; $i++) {
					$countListings[] = $i;
				}
				$products[$key]['count_listings'] = $countListings;
			}
		}

		$user->addProperty(array
			(
				'id' => 'product',
				'type' => 'list',
				'value' => '',
				'list_values' => $products,
				'is_system' => true
			)
		);

		$aliases = new SJB_PropertyAliases();

		$aliases->addAlias(array(
				'id' => 'user_group',
				'real_id' => 'user_group_sid',
				'transform_function' => 'SJB_UserGroupManager::getUserGroupSIDByID'
			)
		);

		$aliases->addAlias(array(
				'id' => 'product',
				'real_id' => 'product_sid',
			)
		);
		
		$_REQUEST['user_group']['equal'] = $userGroupSID;

		$search_form_builder = new SJB_SearchFormBuilder($user);
		$criteria_saver = new SJB_UserCriteriaSaver();

		if (isset($_REQUEST['restore'])) {
			$_REQUEST = array_merge($_REQUEST, $criteria_saver->getCriteria());
		}

		$criteria = $search_form_builder->extractCriteriaFromRequestData($_REQUEST, $user);
		$search_form_builder->setCriteria($criteria);
		$search_form_builder->registerTags($tp);
		
		$userGroupInfo = SJB_UserGroupManager::getUserGroupInfoBySID($userGroupSID);
		if (SJB_Request::getVar('online', '') == '1') {
			$tp->assign("online", true);
		}
		$tp->assign('userGroupInfo', $userGroupInfo);
		$tp->assign('products', $products);
		$tp->assign('selectedProduct', isset($_REQUEST['product']['simple_equal']) ? $_REQUEST['product']['simple_equal'] : '');
		$tp->display($searchTemplate);

		/********************** S O R T I N G *********************/
		$paginator = new SJB_UsersPagination($userGroupInfo, SJB_Request::getVar('online', ''), $template);

		$firstLastName = '';
		if (!empty($_REQUEST['FirstName']['equal'])) {
			$name['FirstName']['any_words'] = $name['LastName']['any_words']  = $_REQUEST['FirstName']['equal'];
			$firstLastName = $_REQUEST['FirstName'];
			unset($_REQUEST['FirstName']);
			$_REQUEST['FirstName']['fields_or'] = $name;
		}
		$criteria = $search_form_builder->extractCriteriaFromRequestData($_REQUEST, $user);
		$inner_join = false;

		// if search by product field
		if (isset($_REQUEST['product']['simple_equal']) && $_REQUEST['product']['simple_equal'] != '') {
			$inner_join = array('contracts' => array('join_field' => 'user_sid', 'join_field2' => 'sid', 'join' => 'INNER JOIN'));
		}

		if (SJB_Request::getVar('online', '') == '1') {
			$maxLifeTime = ini_get("session.gc_maxlifetime");
			$currentTime = time();
			$innerJoinOnline = array(
				'user_session_data_storage' => array(
					'join_field' => 'user_sid',
					'join_field2' => 'sid',
					'select_field' => 'session_id',
					'join' => 'INNER JOIN',
					'where' => "AND unix_timestamp(`user_session_data_storage`.`last_activity`) + {$maxLifeTime} > {$currentTime}"
				)
			);
			if ($inner_join) {
				$inner_join = array_merge($inner_join, $innerJoinOnline);
			} else {
				$inner_join = $innerJoinOnline;
			}
		}

		$searcher = new SJB_UserSearcher(array('limit' => ($paginator->currentPage - 1) * $paginator->itemsPerPage, 'num_rows' => $paginator->itemsPerPage), $paginator->sortingField, $paginator->sortingOrder, $inner_join);

		$found_users = array();
		$found_users_sids = array();

		if (SJB_Request::getVar('action', '') == 'search') {
			$found_users = $searcher->getObjectsSIDsByCriteria($criteria, $aliases);
			$criteria_saver->setSession($_REQUEST, $searcher->getFoundObjectSIDs());
		}
		elseif (isset($_REQUEST['restore'])) {
			$found_users = $criteria_saver->getObjectsFromSession();
		}
		foreach ($found_users as $id => $userID) {
			$user_info = SJB_UserManager::getUserInfoBySID($userID);
			$contractInfo = SJB_ContractManager::getAllContractsInfoByUserSID($user_info['sid']);
			$user_info['products'] = count($contractInfo);
			$found_users[$id] = $user_info;
		}

		$paginator->setItemsCount($searcher->getAffectedRows());
		$sorted_found_users_sids = $found_users_sids;

		/****************************************************************/
		$tp->assign("userGroupInfo", $userGroupInfo);
		$tp->assign("found_users", $found_users);
		$searchFields = '';
		foreach ($_REQUEST as $key => $val) {
			if (is_array($val)) {
				foreach ($val as $fieldName => $fieldValue) {
					if (is_array($fieldValue)) {
						foreach ($fieldValue as $fieldSubName => $fieldSubValue) {
							$searchFields .= "&{$key}[{$fieldSubName}]=" . array_pop($fieldSubValue);
						}
					} else {
						$searchFields .= "&{$key}[{$fieldName}]={$fieldValue}";
					}
				}
			}
		}
		$tp->assign('paginationInfo', $paginator->getPaginationInfo());
		$tp->assign("searchFields", $searchFields);
		$tp->assign("found_users_sids", $sorted_found_users_sids);
		$tp->assign('errors', $errors);
		$tp->display($template);
	}
}
