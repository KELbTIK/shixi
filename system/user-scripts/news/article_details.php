<?php

class SJB_News_ArticleDetails extends SJB_Function
{
	public function execute()
	{
		$errors = array();
		$tp = SJB_System::getTemplateProcessor();

		$i18n = SJB_I18N::getInstance();
		$lang = $i18n->getLanguageData($i18n->getCurrentLanguage());
		$langId = $lang['id'];

		// Category SID incoming as part of URL.
		$categoryId = SJB_Request::getVar("category_sid");

		if (isset($_REQUEST['passed_parameters_via_uri'])) {
			$passed_parameters_via_uri = SJB_UrlParamProvider::getParams();
			$categoryId = isset($passed_parameters_via_uri[0]) ? $passed_parameters_via_uri[0] : null;
		}

		if ($categoryId && $categoryId != 'category') {
			$article = false;

			if (is_null($categoryId)) {
				$errors['ITEM_SID_IS_EMPTY'] = 1;
			} else {
				$article = SJB_NewsManager::getActiveItemBySID($categoryId);
			}

			if (!$article) {
				$errors['ARTICLE_NOT_EXISTS'] = 1;
				echo SJB_System::executeFunction('static_content', 'show_static_content', array('pageid' => '404'));
				return;
			}

			$tp->assign('article', $article);

			$template = 'article_details.tpl';
		} else {
			$categoryId = isset($passed_parameters_via_uri[1]) ? $passed_parameters_via_uri[1] : null;

			// other params in query string
			$searchText = SJB_Request::getVar('search_text', false);
			$current_page = SJB_Request::getVar('page', 1);
			$itemsPerPage = 10;

			$action = SJB_Request::getVar('action');
			if ($action == 'search') {
				// COUNT FOR SEARCH ACTION
				$totalNews = SJB_NewsManager::getAllNewsCountBySearchText($searchText, $langId, true);
			} else {
				$totalNews = SJB_NewsManager::getAllNewsCount($categoryId, $langId, true);
			}

			$pages = ceil($totalNews / $itemsPerPage);
			if ($pages == 0) {
				$pages = 1;
			}
			if ($current_page > $pages) {
				$current_page = $pages;
			}

			if ($action == 'search') {
				// GET ARTICLES FOR SEARCH ACTION
				if ($totalNews == 0) {
					$articles = array();
				} else {
					$articles = SJB_NewsManager::searchArticles($searchText, $langId, true);
				}
			} else {
				$articles = SJB_NewsManager::getNewsByPage($current_page, $itemsPerPage, $categoryId, $langId, true);
			}

			$tp->assign('searchText', $searchText);
			$tp->assign('current_page', $current_page);
			$tp->assign('pages', $pages);
			$tp->assign('articles', $articles);

			$categories = SJB_NewsManager::getCategories($langId);

			$countOfNotEmptyCategories = 0;
			foreach ($categories as $category) {
				if ($category['count'] > 0) {
					$countOfNotEmptyCategories++;
				}
			}
			$showCategoriesBlock = false;
			if ($countOfNotEmptyCategories > 1) {
				$showCategoriesBlock = true;
			}

			$tp->assign('show_categories_block', $showCategoriesBlock);
			$tp->assign('categories', $categories);
			$tp->assign('current_category_sid', $categoryId);

			$template = 'articles_list.tpl';
		}
		$tp->display($template);
	}
}