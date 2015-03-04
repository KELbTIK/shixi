<?php

class SJB_Builder_GetFields extends SJB_Function
{
	public function execute()
	{
		$fieldsHolderID = SJB_Request::getVar('fieldsHolderID', null);

		if (!$fieldsHolderID) {
			throw new Exception('FieldsHolderID is not specified');
		}

		$formBuilder = SJB_FormBuilderManager::getFormBuilder();
		$formBuilder->prepareFieldsHolder($fieldsHolderID);

		$tp = $formBuilder->getChargedTemplateProcessor();
		$activeFields = $formBuilder->getActiveFields();
		$locationKeys = array();
		$sortKeys = array();
		foreach ($activeFields as $key => $activeField) {
			if (!empty($activeField['parent_sid'])) {
				$parentInfo = SJB_ListingFieldManager::getFieldInfoBySID($activeField['parent_sid']);
				if ($formBuilder->getBuilderType() == 'search') {
					$activeFields[$key] = $parentInfo;
					$activeFields[$key]['fields'] = array();
					$activeFields[$key]['fields'][$activeField['sid']] = $activeField;
					$activeFields[$key]['fields'][$activeField['sid']]['parentID'] = $parentInfo['id'];
					$activeFields[$key]['b_order'] = $activeField['b_order'];
				}
			}
			elseif ($activeField['type'] == 'location')  {
				$activeFields[$key]['fields'] = array();
				if ($formBuilder->getBuilderType() == 'search') {
					$activeFields[$key]['fields'][$activeField['sid']] = $activeField;
				} 
			}
		}
		
		foreach ($activeFields as $key => $activeField) {
			$sortKeys[$key] = $activeField['b_order'];
		}

		array_multisort($sortKeys, SORT_ASC, SORT_REGULAR, $activeFields);
		
		$tp->assign('fields_active', $activeFields);
		$tp->assign('fieldsHolderID', $fieldsHolderID);
		$tp->assign('holderType', SJB_Request::getVar('holderType', 'wide'));
		$tp->assign('holderTitle', SJB_Request::getVar('holderTitle', ''));
		$tp->assign('listingTypeID', $formBuilder->getListingTypeID());

		$template = $formBuilder->getFormFieldSetTemplate();
		$tp->display('../builder/' . $template);
	}
}
