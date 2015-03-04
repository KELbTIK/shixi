<?php

class SJB_StaticContent_ShowStaticContent extends SJB_Function
{
	public function execute()
	{
		$page_id = SJB_Request::getVar('pageid', null);

		if ($page_id) {
			$tp = SJB_System::getTemplateProcessor();
			$i18n = SJB_I18N::getInstance();
			$lang = SJB_Request::getVar('lang', $i18n->getCurrentLanguage());
			$staticContent = SJB_StaticContent::getStaticContentByIDAndLang($page_id, $lang);

			if (empty($staticContent)) {
				$def_lang = SJB_System::getSettingByName('i18n_default_language');
				$staticContent = SJB_StaticContent::getStaticContentByIDAndLang($page_id, $def_lang);
			}

			if (!empty($staticContent)) {
				if ($page_id == '404') {
					$isLoggedIn = SJB_Authorization::isUserLoggedIn();
					if ($isLoggedIn) {
						$listingTypesInfo	= SJB_ListingTypeManager::getAllListingTypesInfo();
						$currentUserInfo	= SJB_Authorization::getCurrentUserInfo();
						$userGroupinfo		= SJB_UserGroupManager::getUserGroupInfoBySID($currentUserInfo['user_group_sid']);
						$acl				= SJB_Acl::getInstance();

						$tp->assign('acl', $acl);
						$tp->assign('listingTypesInfo', $listingTypesInfo);
						$tp->assign('userGroupInfo', $userGroupinfo);
					}

					$staticContent['content'] = htmlspecialchars_decode($staticContent['content']);
					$tp->assign('isLoggedIn', $isLoggedIn);
				}
				if (empty($staticContent['content'])) // Null создаёт ошибку в smarty
					$staticContent['content'] = '';
				$tp->assign('staticContent', $staticContent['content']);
				$tp->display('static_content.tpl');
			}
		}
	}
}



