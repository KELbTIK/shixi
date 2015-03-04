<?php

class SJB_Admin_News_NewsCategories extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('manage_news');
		return parent::isAccessible();
	}

	public function execute()
	{
		$errors = array();
		$messages = array();
        $action = SJB_Request::getVar('action', false);

        $tp = SJB_System::getTemplateProcessor();

        $archiveCategory = SJB_NewsManager::getCategoryByName('Archive');
        $tp->assign('archive_category', $archiveCategory);

        /****************** ACTIONS ***************************/

        switch ($action) {

            case 'save_display_setting':
                // save setting 'show_news_on_main_page'
                $settings = SJB_Request::getVar('settings');
                SJB_Settings::updateSettings($settings);
				$messages[] = 'NEWS_SETTINGS_SUCCESSFULLY_SAVED';
                break;

            case 'add':
                $categoryName = SJB_Request::getVar('category_name');
                if (empty($categoryName)) {
                    $errors['Category Name'] = 'EMPTY_VALUE';
                    break;
                }
                $isExists = SJB_NewsManager::checkExistsCategoryName($categoryName);
                if ($isExists) {
                    $errors['Category Name'] = 'NOT_UNIQUE_VALUE';
                    break;
                }
				if (SJB_NewsManager::addCategory($categoryName)) {
					$messages[] = 'NEWS_CATEGORY_SUCCESSFULLY_ADDED';
				} else {
					$errors[] = 'NEWS_CATEGORY_NOT_SAVED';
				}
                break;

            case 'edit':
                $categoryId = SJB_Request::getVar('category_sid');

                $formSubmitted = SJB_Request::getVar('submit');
                if ($formSubmitted) {
                    $newCategoryName = SJB_Request::getVar('category_name');
                    if (!empty($newCategoryName)) {
                        $isExists = SJB_NewsManager::checkExistsCategoryName($newCategoryName);
                        if (!$isExists) {
							if (SJB_NewsManager::updateCategory($categoryId, $newCategoryName)) {
								$messages[] = 'NEWS_CATEGORY_SUCCESSFULLY_SAVED';
							} else {
								$errors[] = 'NEWS_CATEGORY_NOT_SAVED';
							}
                        } else {
                            $errors['Category Name'] = 'NOT_UNIQUE_VALUE';
                        }
                    } else {
                        $errors['Category Name'] = 'EMPTY_VALUE';
                    }
                    if (($formSubmitted == 'save_category') && empty($errors)) {
                        SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/news-categories/');
                    }
                }
				if (SJB_Request::getVar('newsAdded', false)) {
					$messages[] = 'NEWS_SUCCESSFULLY_ADDED';
				}
				if (SJB_Request::getVar('newsDeleted', false)) {
					$messages[] = 'NEWS_SUCCESSFULLY_DELETED';
				}
				if (SJB_Request::getVar('newsSaved', false)) {
					$messages[] = 'NEWS_SUCCESSFULLY_SAVED';
				}

				$category = SJB_NewsManager::getCategoryBySid($categoryId);
				$paginator = new SJB_NewsPagination($categoryId, $category['name']);
				$paginator->setItemsCount(SJB_NewsManager::getAllNewsCount($categoryId));

				$articles  = SJB_NewsManager::getArticlesByCategorySid($categoryId, $paginator->sortingField, $paginator->sortingOrder, $paginator->currentPage, $paginator->itemsPerPage);
				$i18N = SJB_ObjectMother::createI18N();

				$tp->assign('paginationInfo', $paginator->getPaginationInfo());
				$tp->assign('frontendLanguages', $i18N->getActiveFrontendLanguagesData());
				$tp->assign('messages', $messages);
                $tp->assign('errors', $errors);
                $tp->assign('category', $category);
                $tp->assign('articles', $articles);
                $tp->display('edit_category.tpl');
                break;

            case 'delete':
                $categorySID = SJB_Request::getVar('category_sid');
                if (!empty($categorySID)) {
					if (SJB_NewsManager::deleteCategoryBySid($categorySID)) {
						$messages[] = 'NEWS_CATEGORY_SUCCESSFULLY_DELETED';
					} else {
						$errors[] = 'NEWS_CATEGORY_NOT_DELETED';
					}
                }
                break;

            case 'move_up':
                $categoryId = SJB_Request::getVar('category_sid');
                SJB_NewsManager::moveUpCategoryBySID($categoryId);
                break;

            case 'move_down':
                $categoryId = SJB_Request::getVar('category_sid');
                SJB_NewsManager::moveDownCategoryBySID($categoryId);
                break;
        }

		if ($action != 'edit') {
			$categories         = SJB_NewsManager::getCategories();
			$showNewsOnMainPage = SJB_Settings::getSettingByName('show_news_on_main_page');

			// get number of news for categories
			foreach ($categories as $key=>$category) {
				// remove archive from categories list
				if ($category['name'] == 'Archive') {
					unset($categories[$key]);
					continue;
				}
				$counter = SJB_NewsManager::getAllNewsCount($category['sid'], null);
				$categories[$key]['count'] = $counter;
			}
			$tp->assign('categories', $categories);
			$tp->assign('messages', $messages);
			$tp->assign('show_news_on_main_page', $showNewsOnMainPage);
			$tp->assign('number_news_on_main_page', SJB_Settings::getSettingByName('number_news_on_main_page'));
			$tp->assign('main_page_news_display_mode', SJB_Settings::getSettingByName('main_page_news_display_mode'));
			$tp->assign('errors', $errors);
			$tp->display('categories_list.tpl');
		}
	}
}
