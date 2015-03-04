<?php

class SJB_Admin_Polls_ManagePolls extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('manage_polls');
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$action = SJB_Request::getVar('action_name', false);
		$action = $action?$action:'list';
		$template = 'manage_polls.tpl';
		$errors = array();

		switch ($action) {
			case 'new':
				$template = 'input_form_poll.tpl';
				$_REQUEST['start_date'] = date('Y-m-d');
				$field = new SJB_PollsManager($_REQUEST);
				$form = new SJB_Form($field);
				$form->registerTags($tp);
				$tp->assign('form_fields', $form->getFormFieldsInfo());
				break;
			case 'save':
				$sid = SJB_Request::getVar('sid');
				$_REQUEST['value'] = strip_tags(SJB_Request::getVar('value'));
				$_REQUEST['user_group_sid'] = SJB_Request::getInt('user_group_sid', 0);
				$field = new SJB_PollsManager($_REQUEST);
				if ($sid)
					$field->setSID($sid);
				$form = new SJB_Form($field);
				if ($form->isDataValid($errors)) {
					SJB_PollsManager::savePoll($field);
					$_REQUEST['sid'] = $field->sid;
					$save = SJB_Request::getVar('save', false);
					$action = 'edit';
					if ($save) {
						SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/manage-polls/');
						break;
					}
				}
				else {
					$action = 'new';
					if ($sid)
						$action = 'edit';
					$template = 'input_form_poll.tpl';
					$field->setSID($sid);
					$form->registerTags($tp);
					$tp->assign('form_fields', $form->getFormFieldsInfo());
					$tp->assign('errors', $errors);
					$tp->assign('sid', $sid);
					break;
				}
			case 'edit':
				$sid = SJB_Request::getVar('sid');
				$info = SJB_PollsManager::getPollInfoBySID($sid);
				$field = new SJB_PollsManager($info);
				$edit_form = new SJB_Form($field);
				$edit_form->registerTags($tp);
				$field->setSID($sid);
				$tp->assign('form_fields', $edit_form->getFormFieldsInfo());
				$tp->assign('sid', $sid);
				$tp->assign('action', 'edit');
				$template = 'input_form_poll.tpl';
				break;
			case 'edit_answer':
				$sid = SJB_Request::getVar('sid');
				$_REQUEST['field_sid'] = $sid;
				$event = SJB_Request::getVar('event');
				$pollInfo = SJB_PollsManager::getPollInfoBySID($sid);
				$edit_list_controller = new SJB_PollsEditListController($_REQUEST, $pollInfo);

				switch ($event) {
					case 'add':
						$item_sid = SJB_Request::getVar('item_sid');
						if ($edit_list_controller->isValidValueSubmitted()) {
							if ($item_sid) {
								$pollItemManager = new SJB_PollsListItemManager();
								$list_item = $pollItemManager->getListItemBySID($item_sid);
								$list_item->setValue(trim($_REQUEST['list_item_value']));
								$pollItemManager->saveListItem($list_item);
							}
							else
								if (!$edit_list_controller->saveItem())
									$tp->assign('error', 'LIST_VALUE_ALREADY_EXISTS');
						} else
							$tp->assign('error', 'LIST_VALUE_IS_EMPTY');
						break;
					case 'edit':
						$item_sid = SJB_Request::getVar('item_sid');
						$pollItemManager = new SJB_PollsListItemManager();
						$list_item = $pollItemManager->getListItemBySID($item_sid);
						$tp->assign('item_sid', $list_item->sid);
						$tp->assign('item_value', $list_item->value);
						$tp->assign('item_action', 'edit');
						break;
					case 'add_multiple':
						if ($edit_list_controller->isValidMultiValueSubmitted()) {
							if (!$edit_list_controller->saveItem(true))
								$tp->assign('error', 'LIST_VALUE_ALREADY_EXISTS');

						} else
							$tp->assign('error', 'LIST_VALUE_IS_EMPTY');
						break;
					case 'delete':
						$item_sid = SJB_Request::getVar('item_sid');
						if (is_array($item_sid)) {
							foreach ($item_sid as $sid => $val)
								$edit_list_controller->deleteItem($sid);
						} else
							$edit_list_controller->deleteItem();
						break;

					case 'move_up':
						$edit_list_controller->moveUpItem();
						break;

					case 'move_down':
						$edit_list_controller->moveDownItem();
						break;

					case 'save_order':
						if ($item_order = SJB_Request::getVar('item_order', false)) {
							$edit_list_controller->saveNewItemsOrder($item_order);
						}
						break;

					case 'sort':
						$edit_list_controller->sortItems(SJB_Request::getVar('field_sid'), SJB_Request::getVar('sorting_order'));
						$tp->assign('sorting_order', SJB_Request::getVar('sorting_order'));
						break;
				}
				$display_list_controller = new SJB_PollsDisplayListController($_REQUEST, $pollInfo);
				$display_list_controller->setTemplateProcessor($tp);
				$display_list_controller->display('edit_answer.tpl');
				break;
			case 'delete':
				$sid = SJB_Request::getVar('sid', false);
				$sids = SJB_Request::getVar('polls', false);
				if ($sid) {
					SJB_PollsManager::deletePollBySID($sid);
				} else if ($sids) {
					foreach ($sids as $sid => $val)
						SJB_PollsManager::deletePollBySID($sid);
				} else {
					$errors[] = 'NO_POLLS_SELECTED';
				}
				$action = 'list';
				break;
			case 'activate':
				$sids = SJB_Request::getVar('polls');
				if (is_array($sids)) {
					foreach ($sids as $sid => $val) {
						SJB_PollsManager::activatePollBySID($sid);
					}
				} else {
					$errors[] = 'NO_POLLS_SELECTED';
				}
				$action = 'list';
				break;
			case 'deactivate':
				$sids = SJB_Request::getVar('polls');
				if (is_array($sids)) {
					foreach ($sids as $sid => $val) {
						SJB_PollsManager::deactivatePollBySID($sid);
					}
				} else {
					$errors[] = 'NO_POLLS_SELECTED';
				}
				$action = 'list';
				break;
			case 'save_display_setting':
				$settings = SJB_Request::getVar('settings');
				SJB_Settings::updateSettings($settings);
				$action = 'list';
				break;
			case 'view_results':
				$sid = SJB_Request::getVar('sid', 0);
				$countVotes = SJB_PollsManager::getCountVotesBySID($sid);
				$pollResults = SJB_PollsManager::getPollResultsBySID($sid);
				$result = array();
				$i = 0;
				$colors = array('613978', 'aad434', 'f55c00', 'f9c635', 'f97c9e', '870000', '0ec300', '6f6f6f', '0400a5', '6eeffb', '000000', 'ff00ff');
				foreach ($pollResults as $poll) {
					$result[$i]['vote'] = $countVotes > 0 ? round((100 / $countVotes) * $poll['count'], 2) : 0;
					$result[$i]['width'] = $countVotes > 0 ? round((300 / $countVotes) * $poll['count'], 2) : 0;
					$result[$i]['value'] = $poll['question'];
					$result[$i]['color'] = $colors[$i];
					$i++;
				}
				$pollInfo = SJB_PollsManager::getPollInfoBySID($sid);
				$tp->assign('pollInfo', $pollInfo);
				$tp->assign('result', $result);
				$tp->assign('width', (count($pollResults) * 40) + (count($pollResults) - 1) * 3);
				$tp->assign('count_vote', $countVotes);
				$template = 'view_result.tpl';
				break;
		}
		if ($action == 'list') {
			$paginator = new SJB_PollsManagePagination();
			$pollsCount = 0;
			$pollsInfo = SJB_PollsManager::getPollsInfo($paginator->sortingField, $paginator->sortingOrder, ($paginator->currentPage - 1) * $paginator->itemsPerPage, $paginator->itemsPerPage, $pollsCount);
			$paginator->setItemsCount($pollsCount);
			$showPollsOnMainPage = SJB_Settings::getSettingByName('show_polls_on_main_page');
			$tp->assign('errors', $errors);
			$tp->assign('frontendLanguages', SJB_ObjectMother::createI18N()->getActiveFrontendLanguagesData());
			$tp->assign('pollsInfo', $pollsInfo);
			$tp->assign('show_polls_on_main_page', $showPollsOnMainPage);
			$tp->assign('paginationInfo', $paginator->getPaginationInfo());
		}
		if ($action !== 'edit_answer') {
			$tp->assign('action', $action);
			$tp->assign('polls', 1);
			$tp->display($template);
		}
	}
}
