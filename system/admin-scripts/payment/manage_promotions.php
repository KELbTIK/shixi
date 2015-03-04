<?php

class SJB_Admin_Payment_ManagePromotions extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('manage_promotions');
		return parent::isAccessible();
	}
	
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$action = SJB_Request::getVar('action', false);
		$template = 'manage_promotions.tpl';
		$errors = array();
		
		switch ($action) {
			case 'add':
				$promotionCodeInfo = $_REQUEST;
				$formSubmitted = SJB_Request::getVar('event', false);
				$formSubmitted = $formSubmitted == 'save_code'?true:false;
				$template = 'add_promotion.tpl';
				if (!isset($promotionCodeInfo['active']))
					$promotionCodeInfo['active'] = 1;
				$promotionCode = new SJB_Promotions($promotionCodeInfo);
				$addCodeForm = new SJB_Form($promotionCode);
				$addCodeForm->registerTags($tp);
				if ($formSubmitted && $addCodeForm->isDataValid($errors)){
					SJB_PromotionsManager::savePromotionCode($promotionCode);
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/promotions/");
				}
				$formFields = $addCodeForm->getFormFieldsInfo();
				$tp->assign("form_fields", $formFields);
				
				$metaDataProvider = SJB_ObjectMother::getMetaDataProvider();
				$tp->assign(
					"METADATA",  
					array ( 
						"form_fields" => $metaDataProvider->getFormFieldsMetadata($formFields), 
					)
				);
				break;
			case 'edit':
				$template = 'edit_promotion.tpl';
				$promotionSID = SJB_Request::getVar('sid', false);
				$formSubmitted = SJB_Request::getVar('event', false);
				$formSubmitted = $formSubmitted == 'save_code'?true:false;
				$promotionCodeInfo = SJB_PromotionsManager::getCodeInfoBySID($promotionSID);
				if ($promotionCodeInfo) {
					$promotionCodeInfo = array_merge($promotionCodeInfo, $_REQUEST);
					$promotionCode = new SJB_Promotions($promotionCodeInfo);
					$addCodeForm = new SJB_Form($promotionCode);
					$addCodeForm->registerTags($tp);
					$promotionCode->setSID($promotionCodeInfo['sid']);
					$i18n = SJB_I18N::getInstance();
					$endDate = !empty($promotionCodeInfo['end_date'])?$i18n->getInput('date', $promotionCodeInfo['end_date']):'';
					if ($formSubmitted && $addCodeForm->isDataValid($errors) && (empty($endDate) || $endDate >= date('Y-m-d') || !$promotionCodeInfo['active'])){
						SJB_PromotionsManager::savePromotionCode($promotionCode);
						SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/promotions/");
					}
					elseif ($formSubmitted && $promotionCodeInfo['active'] == 1 && $endDate < date('Y-m-d')) {
						$errors["'Active'"] = 'Please change the expiration date first';
					}

					$formFields = $addCodeForm->getFormFieldsInfo();
					$tp->assign("form_fields", $formFields);
					$tp->assign("sid", $promotionSID);
					
					$metaDataProvider = SJB_ObjectMother::getMetaDataProvider();
					$tp->assign(
						"METADATA",  
						array ( 
							"form_fields" => $metaDataProvider->getFormFieldsMetadata($formFields), 
						)
					);
				}
				else 
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/promotions/");
				break;
			case 'delete':
				$promotionSID = SJB_Request::getVar('sid', 0);
				SJB_PromotionsManager::deleteCodeBySID($promotionSID);
				SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/promotions/");
				break;
			case 'deactivate':
				$promotionSID = SJB_Request::getVar('sid', false);
				SJB_PromotionsManager::deactivateCodeBySID($promotionSID);
				SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/promotions/");
				break;
			case 'setting':
				$enablePromotionCodes = SJB_Request::getVar('enable_promotion_codes', 0);
				SJB_Settings::updateSetting('enable_promotion_codes', $enablePromotionCodes);
				SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/promotions/");
				break;
			case 'activate':
				$promotionSID = SJB_Request::getVar('sid', false);
				$promotionCodeInfo = SJB_PromotionsManager::getCodeInfoBySID($promotionSID);

				if (empty($promotionCodeInfo['end_date']) || $promotionCodeInfo['end_date'] > date('Y-m-d')) {
					$currentUses = SJB_PromotionsManager::getUsesCodeBySID($promotionSID);
					$maxUses = $promotionCodeInfo['maximum_uses'];
					if (!$maxUses || $maxUses > $currentUses) {
						SJB_PromotionsManager::activateCodeBySID($promotionSID);
						SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/promotions/");
					} else {
						$errors['MAX_USES_ACHIEVED'] = true;
					}
				} else {
					$errors['DATE_IS_NOT_VALID'] = true;
					$tp->assign('errors', $errors);
				}
			default:
				$promotions = SJB_PromotionsManager::getAllPromotionsInfo();
				foreach ($promotions as $key=> $promotion) {
					$promotions[$key]['uses'] = SJB_PromotionsManager::getUsesCodeBySID($promotion['sid']);
					if ($promotions[$key]['uses'] >= $promotions[$key]['maximum_uses'] && $promotions[$key]['maximum_uses'] != 0) {
						$promotions[$key]['active'] = 'used';
					}
				}
				$currency = SJB_CurrencyManager::getDefaultCurrency();
				$tp->assign('currency', $currency);
				$tp->assign('promotions', $promotions);
				break;
		}
		$tp->assign('errors', $errors);
		$tp->display($template);
	}
}