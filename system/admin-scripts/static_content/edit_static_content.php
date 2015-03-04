<?PHP

class SJB_Admin_StaticContent_EditStaticContent extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('manage_static_content');
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$errors = '';

		if (!isset($_REQUEST['name']))
			$_REQUEST['name'] = '';
		if (!isset($_REQUEST['lang']))
			$_REQUEST['lang'] = '';

		$action = SJB_Request::getVar('action', '');
		$form_submitted = SJB_Request::getVar('formSubmitted');

		if ($action == 'add') {
			if (($error = SJB_StaticContentAuxil::isValidNameID($_REQUEST['name'], $_REQUEST['page_id'])) == '') {
				if (!SJB_StaticContent::getStaticContentByIdAndLang($_REQUEST['page_id'], $_REQUEST['lang'])) {
					$contentInfo = array(
						'id' => $_REQUEST['page_id'],
						'name' => $_REQUEST['name'],
						'lang' => $_REQUEST['lang'],
					);
					if (SJB_StaticContent::addStaticContent($contentInfo))
						SJB_HelperFunctions::redirect(SJB_System::getSystemSettings("SITE_URL") . '/stat-pages/');
					else
						$errors = SJB_StaticContentAuxil::warning('Error', 'Cannot add new static page');
				} else
					$errors = SJB_StaticContentAuxil::warning('Error', 'Dublicate pare ID and Language. Please specify another ID or/and Language');
			} else
				$errors = SJB_StaticContentAuxil::warning('Error', $error);
		}

		if ($action == 'change') {
			$staticContent = SJB_StaticContent::getStaticContentByIDAndLang($_REQUEST['page_id'], $_REQUEST['lang']);

			if (!$staticContent || $staticContent['sid'] == $_REQUEST['page_sid']) {
				$content = SJB_Request::getVar('content');
				if ((SJB_System::getSystemSettings('isDemo') || SJB_System::getIfTrialModeIsOn()) && SJB_HelperFunctions::findSmartyRestrictedTagsInContent($tp, $content)) {
					$errors = SJB_StaticContentAuxil::warning('Error', 'Php tags are not allowed');
				}
				else {
					$contentInfo = array(
						'id' => $_REQUEST['page_id'],
						'name' => $_REQUEST['name'],
						'content' => $content,
						'lang' => $_REQUEST['lang'],
					);

					if (SJB_StaticContent::changeStaticContent($contentInfo, $_REQUEST['page_sid'])) {
						if ($form_submitted == 'save_content')
							SJB_HelperFunctions::redirect(SJB_System::getSystemSettings("SITE_URL") . '/stat-pages/');
					} else {
						$errors = SJB_StaticContentAuxil::warning('Error', 'Cannot update page');
					}
				}
			} else {
				$errors = SJB_StaticContentAuxil::warning('Error', 'Dublicate pare ID and Language. Please specify another ID or/and Language');
			}

			$action = 'edit';
		}


		if ($action == 'delete') {
			if (SJB_StaticContent::deleteStaticContent($_REQUEST['page_sid'])) {
				SJB_HelperFunctions::redirect(SJB_System::getSystemSettings("SITE_URL") . '/stat-pages/');
			}
			$errors = SJB_StaticContentAuxil::warning('Error', 'Cannot delete static page');
		}

		$tp->assign('languages', SJB_I18N::getInstance()->getActiveFrontendLanguagesData());
		if ($action == 'edit') {
			$page = SJB_StaticContent::getStaticContent($_REQUEST['page_sid']);
			$tp->assign('page', array_map('htmlspecialchars', $page));

			$pageInfo = array(
				'module' => 'static_content',
				'function' => 'show_static_content',
				'parameters' => array('pageid' => SJB_Request::getVar('pageid', '')),
			);
			$tp->assign('pageInfo', $pageInfo);
			$tp->assign('page_content', $page['content']);
			$tp->assign('page_sid', $_REQUEST['page_sid']);
			$tp->assign('page', $page);
			$tp->assign('error', $errors);
			$tp->display('static_content_change.tpl');
			return;
		}

		$tp->assign('pages', SJB_StaticContent::getStaticContents());
		$tp->assign('error', $errors);
		$tp->display('static_content.tpl');
	}
}
