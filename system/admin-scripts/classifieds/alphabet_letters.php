<?php
class SJB_Admin_Classifieds_AlphabetLetters extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('configure_system_settings');
		return parent::isAccessible();
	}
	
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$action = SJB_Request::getVar('action', 'list');
		$template = 'alphabet_letters.tpl';

		switch ($action) {
			case 'new':
				$template = 'input_form_alphabet.tpl';
				$ABFfield = new SJB_AlphabetManager($_REQUEST);
				$add_ab_form = new SJB_Form($ABFfield);
				$add_ab_form->registerTags($tp);
				$tp->assign("form_fields", $add_ab_form->getFormFieldsInfo());
				break;
			case 'edit':
				$ABSid = SJB_Request::getVar('sid');
				$ABInfo = SJB_AlphabetManager::getADInfoBySID($ABSid);
				$ABFfield = new SJB_AlphabetManager($ABInfo);
				$edit_form = new SJB_Form($ABFfield);
				$edit_form->registerTags($tp);
				$ABFfield->setSID($ABSid);
				$tp->assign("form_fields", $edit_form->getFormFieldsInfo());
				$tp->assign("alphabet_sid", $ABSid);
				$template = 'input_form_alphabet.tpl';
				break;
			case 'save':
				$errors = null;
				$ABSid = SJB_Request::getVar('sid');
				$_REQUEST['value'] = strip_tags(SJB_Request::getVar('value'));
				$ABFfield = new SJB_AlphabetManager($_REQUEST);
				if($ABSid)
					$ABFfield->setSID($ABSid);
				$add_ab_form = new SJB_Form($ABFfield);
				if($add_ab_form->isDataValid($errors)) {
					SJB_AlphabetManager::saveAlphabet($ABFfield);
					$action = 'list';
				}
				else {
					$action = 'edit';
					$template = 'input_form_alphabet.tpl';
					$ABFfield->setSID($ABSid);
					$add_ab_form->registerTags($tp);
					$tp->assign("form_fields", $add_ab_form->getFormFieldsInfo());
					$tp->assign("alphabet_sid", $ABSid);
				}
				break;
			case 'move_up':
				SJB_AlphabetManager::moveUpABBySID(SJB_Request::getVar('sid'));
				$action = 'list';
				break;
			case 'move_down':
				SJB_AlphabetManager::moveDownABdBySID(SJB_Request::getVar('sid'));
				$action = 'list';
				break;
			case 'delete':
				SJB_AlphabetManager::deleteAlphabetBySID(SJB_Request::getVar('sid'));
				$action = 'list';
				break;

		}
		if($action == 'list') {
			$alphabetInfo = SJB_AlphabetManager::getAlphabetInfo();
			$tp->assign("alphabetInfo", $alphabetInfo);
		}
		$tp->assign("action", $action);
		$tp->display($template);
	}
}
