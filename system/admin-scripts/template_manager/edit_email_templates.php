<?php

class SJB_Admin_TemplateManager_EditEmailTemplates extends SJB_Function
{
	/**
	 * @var SJB_TemplateProcessor
	 */
	public $tp;

	/**
	 * @var string
	 */
	protected $successMessage;

	/**
	 * @var string
	 */
	protected $error;

	/**
	 * @var string
	 */
	protected $errors = array();
	/**
	 * @var string
	 */
	protected $template;

	/**
	 * @var array
	 */
	protected $etGroups;

	public function __construct($acl, $params, $aclRoleID)
	{
        parent::__construct($acl, $params, $aclRoleID);
		$this->tp = SJB_System::getTemplateProcessor();
		$this->successMessage = '';
		$this->error = '';
		$this->template = 'manage_email_templates.tpl';
	}

	public function isAccessible()
	{
		$this->setPermissionLabel('edit_templates_and_themes');
		return parent::isAccessible();
	}

	public function execute()
	{
		$errors = array();
		
		$this->setEtGroups(SJB_EmailTemplateEditor::getEmailTemplateGroups());
		if (isset($_REQUEST['passed_parameters_via_uri'])) {
			$passed_parameters_via_uri = SJB_UrlParamProvider::getParams();
			$etGroup 	= SJB_Array::get($passed_parameters_via_uri, 0);
			$etSID 		= SJB_Array::get($passed_parameters_via_uri, 1);
			$action 	= SJB_Array::get($passed_parameters_via_uri, 2);

			if ($etGroup && SJB_Array::get($this->etGroups, $etGroup)) {
				$this->tp->assign('group', $etGroup);
				if ($etSID) {
					switch ($action) {
						case 'delete':
							$this->deleteEmailTemplate($etGroup, $etSID);
							break;

						case 'getvars':
							$this->prepareTemplateVarsInfo($etGroup);
							exit();
							break;

						default:
							if (isset($_FILES['file']) && $_FILES['file']['name'] && $_FILES['file']['error']) {
								$errors['Attachment'] = 'UPLOAD_ERR_INI_SIZE';
							} else {
								$filename = SJB_Request::getVar('filename', false);
								if ($filename) {
									SJB_UploadFileManager::openEmailTemplateFile($filename, $etSID);
									$errors['NO_SUCH_FILE'] = true;
								}
							}
							$this->editEmailTemplate($etSID, $errors);
							break;
					}
				}
				else {
					$this->addNewTemplateForm($etGroup);
					$this->getEmailTemplatesByGroup($etGroup);
				}
			}
		}
		else {
		$this->addNewTemplateForm();
		}
		if ($errors || $this->errors) {
			$errors = array_merge($errors, $this->errors);
		}

		$this->tp->assign('message', $this->successMessage);
		$this->tp->assign('error', $this->error);
		$this->tp->assign('errors', $errors);
		$this->tp->assign('etGroups', $this->etGroups);
		$this->tp->assign('uploadMaxFilesize', SJB_UploadFileManager::getIniUploadMaxFilesize());
		$this->tp->display($this->template);
	}

	protected function editEmailTemplate($sid, &$errors = array())
	{
		$tplInfo = SJB_EmailTemplateEditor::getEmailTemplateInfoBySID($sid);

		if ($tplInfo) {
			$tplInfo = array_merge($tplInfo, $_REQUEST);
			$emailTemplate = new SJB_EmailTemplate($tplInfo);

			$emailTemplate->setSID($sid);

			$emailTemplate_edit_form = new SJB_Form($emailTemplate);
			$form_is_submitted = SJB_Request::getVar('action');
			
			// php tags are not allowed in trial mode
			if (SJB_System::getIfTrialModeIsOn() || SJB_System::getSystemSettings('isDemo')) {
				if (SJB_HelperFunctions::findSmartyRestrictedTagsInContent($this->tp, $emailTemplate->getPropertyValue('text')))
					$errors['Text'] = 'Php tags are not allowed';
			}
			if ($form_is_submitted && $emailTemplate_edit_form->isDataValid($errors)) {
				SJB_EmailTemplateEditor::saveEmailTemplate($emailTemplate);
				if ($form_is_submitted == 'save_info') {
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/edit-email-templates/' . $emailTemplate->getPropertyValue('group'));
				}
				$this->successMessage = 'You have successfully saved your changes';
			}

			$emailTemplate_edit_form->registerTags($this->tp);

			// prepare email templates variables info
			$this->prepareTemplateVarsInfo(SJB_Array::get($tplInfo, 'group'), SJB_Array::get($tplInfo, 'name'));

			$this->tp->assign('form_fields', $emailTemplate_edit_form->getFormFieldsInfo());;
			$this->tp->assign('tplInfo', $tplInfo);

			$this->template = 'edit_email_template.tpl';

		}
		else {
			$this->error = 'INVALID_EMAIL_TEMPLATE_SID_WAS_SPECIFIED';
		}

	}

	/**
	 * @param string $etGroup
	 */
	protected function getEmailTemplatesByGroup($etGroup)
	{
		$templates = SJB_EmailTemplateEditor::getEmailTemplatesByGroup($etGroup);
		$this->tp->assign('templates', $templates);
	}

	/**
	 * add new template process
	 * @return bool
	 */
	protected function addNewTemplateForm($etGroup = '')
	{
		$errors 					= null;
		$form_is_submitted 			= SJB_Request::getVar('et_submit', false);
		$tplInfo 					= array_merge(array('group' => $etGroup), $_REQUEST);
		$emailTemplate 				= new SJB_EmailTemplate($tplInfo);
		$emailTemplate_edit_form 	= new SJB_Form($emailTemplate);

		$emailTemplate_edit_form->registerTags($this->tp);

		if ($form_is_submitted) {
			if ( $emailTemplate_edit_form->isDataValid($errors)) {

				$etGroupExists = SJB_Array::get($this->etGroups, $emailTemplate->getPropertyValue('group'));
				if ($etGroupExists) {
					$emailTemplate->setPropertyValue('user_defined', true);
					SJB_EmailTemplateEditor::saveEmailTemplate($emailTemplate);

					$this->successMessage 		= 'Email template is successfully added';
					$emailTemplate 				= new SJB_EmailTemplate(array('group' => $etGroup));
					$emailTemplate_edit_form 	= new SJB_Form($emailTemplate);

					$emailTemplate_edit_form->registerTags($this->tp);
				}
				else {
					$this->error = 'WRONG_GROUP';
				}
			}
		}

		$this->errors = $errors;
		$this->tp->assign('form_fields', $emailTemplate_edit_form->getFormFieldsInfo());;
	}

	public function setEtGroups($etGroups)
	{
		$this->etGroups = $etGroups;
	}

	public function getEtGroups()
	{
		return $this->etGroups;
	}

	private function deleteEmailTemplate($group, $templateSID)
	{
		SJB_EmailTemplateEditor::deleteEmailTemplateBySID($templateSID);
		SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/edit-email-templates/' . $group);
	}

	private function prepareTemplateVarsInfo($group, $tplName = '')
	{
		switch ($group) {
			case 'user':
				$this->prepareTplVarsForUser();
				break;

			case 'listing':
				$this->prepareTplVarsForUser();
				$this->prepareTplVarsForListing();
				break;

			case 'product':
				$this->prepareTplVarsForUser();
				$this->prepareTplVarsForProduct();
				break;

			case 'alerts':
				if ( in_array($tplName, array('Job Email Alert', 'Listing Email Alert', 'Resume Email Alert'))) {
					$this->prepareTplVarsForUser();
					$this->prepareTplVarsForListing();
					$this->prepareTplVarsForAlert();
				}
				break;

			case 'other':
				switch ($tplName) {
					case 'Application Approval Email':
					case 'Application Rejection Email':
						$this->prepareTplVarsForUser();
						$this->prepareTplVarsForListing();
						break;

					case 'Tell a Friend Email':
						$this->prepareTplVarsForListing();
						break;

					case 'New Private Message Notification':
						$this->prepareTplVarsForUser();
						break;

					case 'Screening Questionnaire Auto Reply':
						$this->prepareTplVarsForListing();
						break;

					case 'Password Change Email':
						$this->prepareTplVarsForUser();
						break;

					case 'Sub-admin Registration Email':
						$subAdmin = SJB_ObjectMother::createSubAdmin($_REQUEST);
						$fields = $subAdmin->getPropertyList();
						$fields = $this->echoVars($fields);
						$this->tp->assign('subadmin', $fields);
						break;
					case 'Apply Now Email':
						$this->prepareTplVarsForUser('Employer');
						break;
				}
				break;
		}
	}

	private function prepareTplVarsForAlert()
	{
		$savedSearches = SJB_SavedSearches::getAutoNotifySavedSearchesForET();
		if (!empty($savedSearches))
			$this->tp->assign('searchTplVars',  $this->echoVars($savedSearches));
	}

	/**
	 * prepare template variables for Product emails
	 */
	private function prepareTplVarsForProduct()
	{
		$productTypes = array('post_listings', 'access_listings', 'mixed_product', 'featured_user', 'banners', 'custom_product');
		foreach ($productTypes as $key => &$productType) {
			$products = SJB_ProductsManager::getProductsByProductType($productType);
			if (!empty($products)) {
				$productInfo = array_pop($products);
				$productExtraInfo = SJB_ProductsManager::getProductExtraInfoBySID($productInfo['sid']);
				$productInfo = array_merge($productInfo, $productExtraInfo);
				$productType = array('id' => $productType, 'caption' => SJB_ProductsManager::getProductTypeByID($productType));
				$fields = SJB_ProductsManager::createTemplateStructureForProductForEmailTpl($productInfo);
				$fields = array_merge($fields, $productExtraInfo);
				unset($fields['METADATA']);
				$productType['fields'] = $this->echoVars($fields);
			} else {
				unset($productTypes[$key]);
			}
		}
		$this->tp->assign('productTypes', $productTypes);
	}

	private function prepareTplVarsForListing()
	{
		$listingTypes = SJB_ListingTypeManager::getAllListingTypesInfo();
		foreach ($listingTypes as &$listingType) {
			$listing = SJB_ObjectMother::createListing(array(), $listingType['sid']);
			$fields = SJB_ListingManager::createTemplateStructureForListing($listing);
			unset($fields['METADATA']);
			unset($fields['user']);
			$listingType['fields'] = $this->echoVars($fields);
		}
		$this->tp->assign('listingTypes', $listingTypes);
	}

	private function prepareTplVarsForUser($onlyUserGroupID = false)
	{
		$userGroups = SJB_UserGroupManager::getAllUserGroupsIDsAndCaptions();
		foreach ($userGroups as $key => &$userGroupInfo) {
			if ($onlyUserGroupID && $userGroupInfo['key'] != $onlyUserGroupID) {
				unset($userGroups[$key]);
				continue;
			}

			$user = SJB_ObjectMother::createUser(array(), $userGroupInfo['id']);
			$fields = SJB_UserManager::createTemplateStructureForUser($user);
			unset($fields['METADATA']);
			unset($fields['password']);
			$userGroupInfo['fields'] = $this->echoVars($fields);
		}
		$this->tp->assign('userGroups', $userGroups);
	}

	private function echoVars($userGroupInfo, &$fields = array(), &$fieldTree = array())
	{
		foreach ($userGroupInfo as $key => $val) {
			if (!is_array($val)) {
				array_push($fieldTree, $key);
				array_push($fields, implode('.', $fieldTree));
				array_pop($fieldTree);
			}
			else {
				array_push($fieldTree, $key);
				$this->echoVars($val, $fields, $fieldTree);
			}
		}
		array_pop($fieldTree);
		return $fields;
	}
}

