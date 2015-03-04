<?php


class SJB_Payment_MyInvoices extends SJB_Function
{
	public function isAccessible()
	{
		if ($this->getAclRoleID()) {
			$this->setPermissionLabel('subuser_manage_subscription');
		}
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$userInfo = SJB_Authorization::getCurrentUserInfo();
		if (empty($userInfo)) {
			$tp->assign("ERROR", "NOT_LOGIN");
			$tp->display("../miscellaneous/error.tpl");
			return;
		}
		$template = SJB_Request::getVar('template', 'my_invoices.tpl');
		$searchTemplate = SJB_Request::getVar('search_template', 'invoice_search_form.tpl');

		/***************************************************************/
		$_REQUEST['action'] = 'search';
		$_REQUEST['user_sid']['equal'] = $userInfo['sid'];
		if (!isset($_REQUEST['date'])) {
			$i18n = SJB_ObjectMother::createI18N();
			$_REQUEST['date']['not_less'] = $i18n->getDate(date('Y-m-d', time() - 30 * 24 * 60 * 60));
			$_REQUEST['date']['not_more'] = $i18n->getDate(date('Y-m-d'));
		}
		$invoice = new SJB_Invoice(array());
		$invoice->addProperty(array(
				'id' => 'username',
				'type' => 'string',
				'value' => '',
				'is_system' => true,
			)
		);

		$aliases = new SJB_PropertyAliases();
		$aliases->addAlias(array(
				'id' => 'username',
				'real_id' => 'user_sid',
				'transform_function' => 'SJB_UserDBManager::getUserSIDsLikeSearchString',
			)
		);

		$searchFormBuilder = new SJB_SearchFormBuilder($invoice);
		$criteriaSaver = new SJB_InvoiceCriteriaSaver();
		if (isset($_REQUEST['restore'])) {
			$_REQUEST = array_merge($_REQUEST, $criteriaSaver->getCriteria());
		}
		$criteria = $searchFormBuilder->extractCriteriaFromRequestData($_REQUEST, $invoice);
		$searchFormBuilder->setCriteria($criteria);
		$searchFormBuilder->registerTags($tp);
		$tp->display($searchTemplate);

		/********************** S O R T I N G *********************/
		$criteria = $searchFormBuilder->extractCriteriaFromRequestData($_REQUEST, $invoice);
		$searcher = new SJB_InvoiceSearcher();

		$foundInvoices = array();
		$foundInvoicesInfo = array();
		if (SJB_Request::getVar('action', '') == 'search') {
			$foundInvoices = $searcher->getObjectsByCriteria($criteria, $aliases);
			$criteriaSaver->setSession($_REQUEST, $searcher->getFoundObjectSIDs());
		} elseif (isset($_REQUEST['restore'])) {
			$foundInvoices = $criteriaSaver->getObjectsFromSession();
		}
		foreach ($foundInvoices as $id => $invoice) {

			$invoice->addProperty(array(
				'id' => 'sid',
				'type' => 'string',
				'value' => $invoice->getSID(),
			));

			$subUserSid = $invoice->getPropertyValue('subuser_sid');
			if ($subUserSid) {
				$payer = SJB_UserManager::getUserNameByUserSID($subUserSid);
			}
			else {
				$userSid = $invoice->getPropertyValue('user_sid');
				$payer = SJB_UserManager::getUserNameByUserSID($userSid);
			}

			$invoice->addProperty(array(
				'id' => 'payer',
				'type' => 'string',
				'value' => $payer,
			));


			$foundInvoices[$id] = $invoice;
			$foundInvoicesInfo[$invoice->getSID()] = SJB_InvoiceManager::getInvoiceInfoBySID($invoice->getSID());
		}

		$sortingField = SJB_Request::getVar('sorting_field', 'sid');
		$sortingOrder = SJB_Request::getVar('sorting_order', 'DESC');

		if ($invoice->propertyIsSet($sortingField)) {
			$sortArray = array();
			$sortedFoundInvoicesInfo = array();

			foreach ($foundInvoices as $id => $invoice) {
				$sortArray[$id] = $invoice->getPropertyValue($sortingField);
			}

			if ($sortingOrder == 'ASC') {
				asort($sortArray);
			}
			elseif ($sortingOrder == 'DESC') {
				arsort($sortArray);
			}

			foreach ($sortArray as $id => $value) {
				$sortedFoundInvoicesInfo[$id] = $foundInvoicesInfo[$id];
			}
		}
		else {
			$sortedFoundInvoicesInfo = $foundInvoicesInfo;
		}

		$formCollection = new SJB_FormCollection($foundInvoices);
		$formCollection->registerTags($tp);

		$subUsers = SJB_UserManager::getSubUsers($userInfo['sid']);
		$isSubUserExists = (!empty($subUsers)) ? true : false;

		$tp->assign('isSubUserExists', $isSubUserExists);
		$tp->assign('sorting_field', $sortingField);
		$tp->assign('sorting_order', $sortingOrder);
		$tp->assign('found_invoices', $sortedFoundInvoicesInfo);
		$tp->display($template);

	}
}
