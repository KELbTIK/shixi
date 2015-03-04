<?php

class SJB_Admin_Builder_DisplayListingBuilder extends SJB_Function
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
		else {

			$listing = new SJB_Listing(array(), $listingTypeSID);
			$listing->addIDProperty();
			$listing->addActivationDateProperty();
			$listing->addUsernameProperty();
			$listing->addKeywordsProperty();
			$listing->addPicturesProperty();
			$listing->addListingTypeIDProperty();
			$listing->addPostedWithinProperty();

			$listingTypeID = SJB_ListingTypeManager::getListingTypeIDBySID($listing->listing_type_sid);

			$display_form = new SJB_Form($listing);
			$display_form->registerTags($tp);

			$form_fields = $display_form->getFormFieldsInfo();

			$listing_structure = SJB_ListingManager::createTemplateStructureForListing($listing);

			$metaDataProvider = SJB_ObjectMother::getMetaDataProvider();
			$tp->assign(
				'METADATA', array(
				'listing' => $metaDataProvider->getMetaData($listing_structure['METADATA']),
				'form_fields' => $metaDataProvider->getFormFieldsMetadata($form_fields)));
			$tp->assign('form_fields', $form_fields);
			$tp->filterThenAssign('listing', $listing_structure);

			$formBuilder = SJB_FormBuilderManager::getFormBuilder(SJB_FormBuilderManager::FORM_BUILDER_TYPE_DISPLAY, $listingTypeID);
			$formBuilder->setChargedTemplateProcessor($tp);
			$tp->assign('listingTypeInfo', SJB_ListingTypeManager::getListingTypeInfoBySID($listingTypeSID));
			$tp->assign('currentTheme', SJB_TemplateSupplier::getUserCurrentTheme());
		}

		$tp->assign('errors', $errors);
		$tp->display('bf_displaylisting.tpl');
	}
}
