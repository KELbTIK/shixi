<?php

class SJB_News_ShowNews extends SJB_Function
{
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();

		$i18n = SJB_I18N::getInstance();
		$lang = $i18n->getLanguageData($i18n->getCurrentLanguage());
		$langId = $lang['id'];

		// params
		$count = SJB_Settings::getSettingByName('number_news_on_main_page');
		$result = SJB_NewsManager::getLatestNews($count, $langId, SJB_Settings::getSettingByName('main_page_news_display_mode'));

		$articles = array();
		foreach ($result as $article) {
			$articles[] = SJB_NewsManager::createTemplateStructureForNewsArticle($article);
		}
		// clear unnecessary data
		unset($result);

		$tp->assign('count', $count);
		$tp->assign('articles_count', count($articles));
		$tp->assign('articles', $articles);

		$tp->display('news.tpl');
	}
}