<?php

class SJB_Admin_Builder_SearchFormBuilder extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('edit_form_builder');
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$errors = array();
		$listingTypeSID = 0;
		$listingTypeID = 0;

		if (isset($_REQUEST['passed_parameters_via_uri'])) {
			$params = SJB_FixedUrlParamProvider::getParams($_REQUEST);
			if ($params) {
				$listingTypeID = array_pop($params);
				$listingTypeSID = SJB_ListingTypeManager::getListingTypeSIDByID($listingTypeID);
			}
		}

		if (!$listingTypeSID) {
			$errors['WRONG_LISTING_TYPE_ID_SPECIFIED'] = true;
		}

		$listing = new SJB_Listing(array(), $listingTypeSID);
		$listing->addIDProperty();
		$listing->addActivationDateProperty();
		$listing->addUsernameProperty();
		$listing->addKeywordsProperty();
		$listing->addPicturesProperty();
		$listing->addListingTypeIDProperty();
		$listing->addPostedWithinProperty();

		$search_form_builder = new SJB_SearchFormBuilder($listing);
		$search_form_builder->registerTags($tp);

		$form_fields = $search_form_builder->getFormFieldsInfo();

		$tp->assign('form_fields', $form_fields);

		$metaDataProvider = SJB_ObjectMother::getMetaDataProvider();
		$tp->assign(
			'METADATA',
			array(
				'form_fields' => $metaDataProvider->getFormFieldsMetadata($form_fields)
			)
		);

		$formBuilder = SJB_FormBuilderManager::getFormBuilder(SJB_FormBuilderManager::FORM_BUILDER_TYPE_SEARCH, $listingTypeID);
		$formBuilder->setChargedTemplateProcessor($tp);
		$tp->assign('listingTypeInfo', SJB_ListingTypeManager::getListingTypeInfoBySID($listingTypeSID));
		$tp->assign('builderMode', true);
		$tp->assign('currentTheme', SJB_TemplateSupplier::getUserCurrentTheme());

		SJB_System::getTemplateProcessor();

		$tp->display('bf_searchform.tpl');
	}
}
