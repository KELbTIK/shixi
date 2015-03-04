<?php

class SJB_Admin_News_ManageNews extends SJB_Function
{
	public function isAccessible()
	{
		if ($this->getAclRoleID()) {
			$this->setPermissionLabel('manage_news');
		}
		return parent::isAccessible();
	}

	public function execute()
	{
		$errors = array();
		$action = $this->getNewsAction();

		$tp = SJB_System::getTemplateProcessor();

		$categoryId = SJB_Request::getVar('category_sid');
		if (empty($categoryId)) {
			$categoryId = SJB_Request::getVar('category_id');
		}
		$category = SJB_NewsManager::getCategoryBySid($categoryId);

		$tp->assign('category_id', $categoryId);
		$tp->assign('category', $category);

		$allCategories = SJB_NewsManager::getCategories();
		$tp->assign('all_categories', $allCategories);

		/****************** ACTIONS ***************************/
		switch ($action) {

			case 'add':
				$article = new SJB_NewsArticle($_REQUEST);
				$articleAddForm = new SJB_Form($article);
				$articleAddForm->registerTags($tp);

				$formSubmitted = SJB_Request::getVar('form_submit', false);
				if ($formSubmitted && $articleAddForm->isDataValid($errors)) {
					SJB_NewsDBManager::saveNewsArticle($article);

					$articleSID = $article->getSID();
					if ($articleSID) {
						SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/news-categories/?action=edit&category_sid={$categoryId}&newsAdded=1");
						exit;
					} else {
						$errors[] = 'UNABLE_TO_ADD_ARTICLE';
					}
				} else {
					$article = new SJB_NewsArticle($_REQUEST);
					$article->deleteProperty('category_id'); // cause it set in form by get param

					$articleAddForm = new SJB_Form($article);
					$articleAddForm->registerTags($tp);
					$formFields = $articleAddForm->getFormFieldsInfo();

					$tp->assign('form_fields', $formFields);

					$metaDataProvider = SJB_ObjectMother::getMetaDataProvider();
					$tp->assign(
						"METADATA",
						array(
							"form_fields" => $metaDataProvider->getFormFieldsMetadata($formFields),
						)
					);
				}
				$tp->assign('uploadMaxFilesize', SJB_UploadFileManager::getIniUploadMaxFilesize());
				$tp->assign('errors', $errors);
				$tp->display('add_article.tpl');
				break;

			case 'edit':
				$itemSID = SJB_Request::getVar('article_sid', false);

				if (!$itemSID) {
					$errors[] = 'NO_ITEM_SID_PRESENT';
				} else {
					$articleInfo = SJB_NewsManager::getNewsArticleInfoBySid($itemSID);
					$articleInfo = array_merge($articleInfo, $_REQUEST);

					$article = new SJB_NewsArticle($articleInfo);
					$articleEditForm = new SJB_Form($article);
					$articleEditForm->registerTags($tp);

					$formSubmitted = SJB_Request::getVar('form_submit', false);
					if ($formSubmitted && $articleEditForm->isDataValid($errors)) {
						$article->setSID($itemSID);

						// if need to change article category
						$moveToCategory = SJB_Request::getVar('article_category');
						if (!empty($moveToCategory) && is_numeric($moveToCategory)) {
							$article->setPropertyValue('category_id', $moveToCategory);
						}

						SJB_NewsDBManager::saveNewsArticle($article);
						SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/news-categories/?action=edit&category_sid={$categoryId}&newsSaved=1");
					} else {
						$formFields = $articleEditForm->getFormFieldsInfo();

						$tp->assign('form_fields', $formFields);
						$tp->assign('article_sid', $itemSID);

						$metaDataProvider = SJB_ObjectMother::getMetaDataProvider();
						$tp->assign(
							"METADATA",
							array(
								"form_fields" => $metaDataProvider->getFormFieldsMetadata($formFields),
							)
						);
					}
				}

				$tp->assign('errors', $errors);
				$tp->assign('category', $category);
				$tp->assign("uploadMaxFilesize", SJB_UploadFileManager::getIniUploadMaxFilesize());
				$tp->display('edit_article.tpl');
				break;

			case 'delete':
				$itemSIDs = SJB_Request::getVar('news');
				foreach ($itemSIDs as $sid => $item)
					SJB_NewsManager::deleteArticleBySID($sid);
				SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/news-categories/?action=edit&category_sid={$categoryId}&newsDeleted=1");
				break;

			case 'delete_image':
				$articleSid = SJB_Request::getVar('article_sid');

				SJB_NewsManager::deleteArticleImageByArticleSid($articleSid);

				// get category
				$article = SJB_NewsManager::getNewsArticleBySid($articleSid);
				$categoryId = $article->getCategoryId();

				SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/manage-news/?action=edit&article_sid={$articleSid}&category_sid={$categoryId}");
				break;

			case 'activate':
				$itemSIDs = SJB_Request::getVar('news');
				foreach ($itemSIDs as $sid => $item)
					SJB_NewsManager::activateItemBySID($sid);
				SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/news-categories/?action=edit&category_sid={$categoryId}");
				break;

			case 'deactivate':
				$itemSIDs = SJB_Request::getVar('news');
				foreach ($itemSIDs as $sid => $item)
					SJB_NewsManager::deactivateItemBySID($sid);
				SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/news-categories/?action=edit&category_sid={$categoryId}");
				break;

			case 'archive':
				$itemSIDs = SJB_Request::getVar('news');
				foreach ($itemSIDs as $sid => $item)
					SJB_NewsManager::moveArticleToArchiveBySid($sid);
				SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/news-categories/?action=edit&category_sid={$categoryId}");
				break;

			default:
				$page = SJB_Request::getVar('page', 1);
				$itemsPerPage = SJB_Request::getVar('items_per_page', 10);
				$totalNewsCount = SJB_NewsManager::getAllNewsCount($categoryId);

				$pages = ceil($totalNewsCount / $itemsPerPage);

				// get news for current page
				$news = SJB_NewsManager::getNewsByPage($page, $itemsPerPage);

				$tp->assign('news', $news);
				$tp->assign('pages', $pages);
				$tp->assign('items_per_page', $itemsPerPage);
				$tp->assign('current_page', $page);

				$tp->display('manage_news.tpl');
				break;
		}

	}

	public function getNewsAction()
	{
		$action = SJB_Request::getVar('action_name', false);
		if (!$action)
			$action = SJB_Request::getVar('action');
		return $action;
	}
}
