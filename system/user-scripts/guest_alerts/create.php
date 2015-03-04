<?php

class SJB_GuestAlerts_Create extends SJB_Function
{

	/**
	 * @var SJB_ListingCriteriaSaver
	 */
	protected $criteriaSaver;

	/**
	 * @var string
	 */
	protected $listingTypeID;

	/**
	 * @var array
	 */
	protected $criteriaData;

	/**
	 * @var string
	 */
	protected $searchID;

	protected $template;

	public function isAccessible()
	{
		$this->searchID = SJB_Request::getVar('searchId', '');
		$this->criteriaSaver = new SJB_ListingCriteriaSaver($this->searchID);
		$this->criteriaData = $this->criteriaSaver->getCriteria();
		$this->listingTypeID = SJB_GuestAlertManager::getListingTypeIDFromCriteria($this->criteriaData);
		$this->setPermissionLabel('use_' . $this->listingTypeID . '_alerts');
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$isFormSubmitted = SJB_Request::getVar('action');
		$guestAlert = new SJB_GuestAlert($_REQUEST);
		$form = new SJB_Form($guestAlert);
		$form->registerTags($tp);
		$errors = array();

		if ($isFormSubmitted && $form->isDataValid($errors)) {
			$this->saveNewGuestAlert($guestAlert, $tp);
		}
		else {
			$form_fields = $form->getFormFieldsInfo();
			$tp->assign('form_fields', $form_fields);
			$tp->assign('searchId', $this->searchID);

			if ($this->isDuplicateEmailError($errors)) {
				$email = $guestAlert->getAlertEmail();
				$unSubscribedGuestAlertSID = SJB_GuestAlertManager::isGuestAlertUnSubscribedByEmail($email);
				if ($unSubscribedGuestAlertSID > 0) {
					SJB_GuestAlertManager::deleteGuestAlertBySID($unSubscribedGuestAlertSID);
					$this->saveNewGuestAlert($guestAlert, $tp);
				}
				else {
					$this->template = 'replace.tpl';
				}
			}
			else {
				$tp->assign('errors', $errors);
				$this->template = 'create.tpl';
			}
		}

		$tp->display($this->template);
	}

	/**
	 * @param SJB_GuestAlert $guestAlert
	 * @param SJB_TemplateProcessor $tp
	 */
	public function saveNewGuestAlert(SJB_GuestAlert $guestAlert, SJB_TemplateProcessor $tp)
	{
		$guestAlert->addDataProperty(serialize($this->criteriaData));
		$guestAlert->addListingTypeIDProperty($this->listingTypeID);
		$guestAlert->save();
		$listingTypeSID = SJB_ListingTypeManager::getListingTypeSIDByID($this->listingTypeID);
		SJB_GuestAlertStatistics::saveEventSubscribed($listingTypeSID, $guestAlert->getSID());
		SJB_Notifications::sendConfirmationEmailForGuest($guestAlert);
		$tp->assign('email', $guestAlert->getAlertEmail());
		$this->template = 'alert_created.tpl';
	}

	private function isDuplicateEmailError($errors)
	{
		$emailError = SJB_Array::get($errors, 'Email');
		return 'NOT_UNIQUE_VALUE' === $emailError;
	}
}
