<?php

class SJB_Admin_Classifieds_ListingFeeds extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('set_xml_feeds');
		return parent::isAccessible();
	}

	public function execute()
	{
		// таблица с данными xml-feeds
		$feedsTable = 'listing_feeds';
		$action = SJB_Request::getVar('action', '');
		$feedId = SJB_Request::getVar('feedId', null);

		$feedsArray = SJB_DB::query("SELECT f.*, f.count as count_listings, l.sid as typeId, l.id as type FROM {$feedsTable} as f LEFT JOIN `listing_types` as l ON (l.sid=f.type) ORDER BY f.sid");
		$listingTypes = SJB_DB::query("SELECT * FROM `listing_types`");

		$siteURL = SJB_System::getSystemSettings('SITE_URL');
		$tmp = strrchr(SJB_System::getSystemSettings('SITE_URL'), "/");
		$siteURL = str_replace($tmp, "", $siteURL);

		$tp = SJB_System::getTemplateProcessor();
		$tp->assign('listingTypes', $listingTypes);

		// данные для создания файлов-шаблонов для feed
		$module = SJB_System::getModuleManager()->getCurrentModuleAndFunction();
		$templatePath = SJB_TemplatePathManager::getAbsoluteTemplatesPath();
		$filePath = $templatePath . "_system/" . $module[0] . "/";

		switch ($action) {
			case 'add':
				// если была отправка формы
				if (isset($_REQUEST['addFeed']) && $_REQUEST['addFeed'] != '') {
					$feed_name = $_REQUEST['feed_name'];
					$feed_template = $_REQUEST['feed_template'];
					$feed_desc = $_REQUEST['feed_desc'];
					$typeId = $_REQUEST['typeId'];
					$count_listings = $_REQUEST['count_listings'];
					$feed_mimetype = $_REQUEST['mime_type'];

					// создадим шаблон для нового feed
					$feed_template = preg_replace("/^feed_|\.tpl$/", "", $feed_template);
					$feed_template = "feed_" . $feed_template . ".tpl";
					$filePath = $filePath . $feed_template;

					$fileSystem = new SJB_Filesystem();
					if (!file_exists($filePath)) {
						$fileSystem->createFile($filePath);
					} else {
						$errors[] = array('code' => 'FILE_ALREADY_EXISTS', 'message' => "Template '$filePath' already exists!");
						$tp->assign('feed', array(
								'name' => $feed_name,
								'template' => $feed_template,
								'description' => $feed_desc,
								'type' => $typeId,
								'count_listings' => $count_listings,
								'mime_type' => $feed_mimetype,
							)
						);
						$tp->assign('errors', $errors);
						$tp->display("add_listing_feed.tpl");
						break;
					}

					SJB_DB::queryExec("INSERT INTO {$feedsTable} SET `name`=?s, `template`=?s, `description`=?s, `type`=?n, `count`=?n, `mime_type`=?s", $feed_name, $feed_template, $feed_desc, $typeId, $count_listings, $feed_mimetype);
					$site_url = SJB_System::getSystemSettings("SITE_URL");
					SJB_HelperFunctions::redirect($site_url . "/listing-feeds/");
				}

				$tp->display("add_listing_feed.tpl");
				break;

			case 'edit':
				// если была отправка формы редактирования элемента
				if ($form_submitted = SJB_Request::getVar('submit')) {
					$feed_name = $_REQUEST['feed_name'];
					$feed_template = $_REQUEST['feed_template'];
					$feed_desc = $_REQUEST['feed_desc'];
					$feedId = $_REQUEST['feedId'];
					$typeId = $_REQUEST['typeId'];
					$count_listings = $_REQUEST['count_listings'];
					$feed_mimetype = $_REQUEST['mime_type'];

					SJB_DB::queryExec("UPDATE {$feedsTable} SET `name`=?s, `template`=?s, `description`=?s, `type`=?n, `count`=?n, `mime_type`=?s WHERE `sid`=?n LIMIT 1", $feed_name, $feed_template, $feed_desc, $typeId, $count_listings, $feed_mimetype, $feedId);
					$site_url = SJB_System::getSystemSettings("SITE_URL");

					if ($form_submitted == 'save_info')
						SJB_HelperFunctions::redirect($site_url . "/listing-feeds/");
				}
				$feed = SJB_DB::query("SELECT f.*, f.count as count_listings  FROM {$feedsTable} as f WHERE `sid`=?n", $feedId);
				$feed = array_pop($feed);

				$tp->assign("feed", $feed);
				$tp->display("edit_listing_feed.tpl");
				break;

			case 'delete':
				$feed = SJB_DB::query("SELECT f.*, f.count as count_listings FROM {$feedsTable} as f WHERE `sid`=?n", $feedId);
				$feed = array_pop($feed);
				$filePath = $filePath . $feed['template'];

				$fileSystem = new SJB_Filesystem();
				if (file_exists($filePath)) {
					$fileSystem->deleteFile($filePath);
				} else {
					$errors[] = array('code' => 'FILE_NOT_EXISTS', 'message' => "Template for " . $feed['name'] . ": '$filePath' not exists!");
				}
				SJB_DB::queryExec("DELETE FROM {$feedsTable} WHERE `sid`=?n LIMIT 1", $feedId);

				$site_url = SJB_System::getSystemSettings("SITE_URL");
				SJB_HelperFunctions::redirect($site_url . "/listing-feeds/");
				break;
		}

		if ($action == '') {
			$tp->assign('siteURL', $siteURL);
			$tp->assign('feeds', $feedsArray);
			$tp->display('listing_feeds.tpl');
		}
	}
}
