<?php

class SJB_Admin_Payment_TransactionHistory extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('transaction_history');
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$errors = array();

		/********** A C T I O N S   W I T H   T R A N S A C T I O N S **********/
		$action = SJB_Request::getVar('action_name', SJB_Request::getVar('action', false));
		$transactions_sids = SJB_Request::getVar('transactions', false);

		if ($action && $transactions_sids) {
			$_REQUEST['restore'] = 1;
			if ($action == 'delete') { // DELETE
				foreach ($transactions_sids as $transaction_sid => $value)
					SJB_TransactionManager::deleteTransactionBySID($transaction_sid);
			}
			else {
				unset($_REQUEST['restore']);
			}
		}

		/**********  D E F A U L T   V A L U E S   F O R   S E A R C H  **********/

		$_REQUEST['action'] = 'filter';

		$i18n = SJB_ObjectMother::createI18N();
		if (!isset($_REQUEST['date'])) {
			$_REQUEST['date']['not_less'] = $i18n->getDate(date('Y-m-d', time() - 30 * 24 * 60 * 60));
			$_REQUEST['date']['not_more'] = $i18n->getDate(date('Y-m-d', time() + 24 * 60 * 60));
		}
		else {
			if (!$i18n->isValidDate($_REQUEST['date']['not_less']) && !empty($_REQUEST['date']['not_less']))
				$errors[] = 'INVALID_PERIOD_FROM';
			if (!$i18n->isValidDate($_REQUEST['date']['not_more']) && !empty($_REQUEST['date']['not_more']))
				$errors[] = 'INVALID_PERIOD_TO';
		}

		/************************ S E A R C H   F O R M ***************************/
		$transaction = new SJB_Transaction();
		$transaction->addProperty(array(
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
				'transform_function' => 'SJB_UserManager::getUserSIDsLikeUsername',
			)
		);

		$search_form_builder = new SJB_SearchFormBuilder($transaction);
		$criteria_saver = new SJB_TransactionCriteriaSaver();
		if (isset($_REQUEST['restore']))
			$_REQUEST = array_merge($_REQUEST, $criteria_saver->getCriteria());
		$criteria = $search_form_builder->extractCriteriaFromRequestData($_REQUEST, $transaction);
		$search_form_builder->setCriteria($criteria);
		$search_form_builder->registerTags($tp);
		$tp->display('payment_form.tpl');

		/********************  S E A R C H  ************************/

		$paginator = new SJB_TransactionHistoryPagination();

		$searcher = new SJB_TransactionSearcher($paginator);
		if (SJB_Request::getVar('action', '') == 'filter') {
			$transactions = $searcher->getObjectsByCriteria($criteria, $aliases);
			if (empty($transactions) && $paginator->currentPage != 1) {
				SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/payments/?page=1');
			}
			$criteria_saver->setSession($_REQUEST, $searcher->getFoundObjectSIDs());
		} elseif (isset($_REQUEST['restore'])) {
			$transactions = $criteria_saver->getObjectsFromSession();
		}

		$paginator->setItemsCount($searcher->getAffectedRows());

		$found_trans = array(); $found_trans_sids = array();
		foreach ($transactions as $transaction){
				$user_sid = $transaction->getPropertyValue('user_sid');
				$username = SJB_UserManager::getUserNameByUserSID($user_sid);

				$transaction->addProperty(array(
					'id' => 'username',
					'type' => 'string',
					'value' => $username,
				));

				$found_trans[$transaction->getSID()] = $transaction;
				$found_trans_sids[$transaction->getSID()] = $transaction->getSID();

		}
		$sorted_found_trans_sids = $found_trans_sids;
		$form_collection = new SJB_FormCollection($found_trans);
		$form_collection->registerTags($tp);

		$tp->assign('paginationInfo', $paginator->getPaginationInfo());
		$tp->assign('errors', $errors);
		$tp->assign('found_transactions_sids', $sorted_found_trans_sids);
		$tp->display('payments.tpl');
	}
}
