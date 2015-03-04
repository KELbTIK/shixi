<?php

class SJB_Admin_Miscellaneous_UpdateToNewVersion extends SJB_Function
{
	public function isAccessible()
	{
		return true;
	}

	public function execute()
	{
		$action = SJB_Request::getVar('action', null);
		switch ($action) {
			case 'updatingToNewVersion':
				$this->updatingProcess();
				break;
			default :
				$this->updateToNewVersion();
		}
		
		if ($action) {
			exit();
		}
	}

	private function updateToNewVersion()
	{
		ini_set('max_execution_time', 0);
		$tp     = SJB_System::getTemplateProcessor();
		$errors = array();

		$isZipExtLoaded = extension_loaded('zip');
		$tp->assign('zip_extension_loaded', $isZipExtLoaded);

		$updatesDir  = SJB_System::getSystemSettings('SJB_UPDATES_DIR');
		$updateToVer = SJB_Request::getVar('to_version');
		if (!empty($updateToVer)) {
			// remove all path elements
			$updateToVer = basename($updateToVer);
			$startPath = $updatesDir . $updateToVer . DIRECTORY_SEPARATOR . "updater.php";

			if (file_exists($startPath)) {
				include_once $startPath;
			} else {
				echo '<p class="error">' . SJB_I18N::getInstance()->gettext(null, 'Update package files are missing') . '</p>';
			}
			$tp->display('updater_starts.tpl');
		} else {
			$formSubmitted = SJB_Request::getVar('update_to_version');
			$wayToUpdate = SJB_Request::getVar('way_to_update');

			if (!$isZipExtLoaded) {
				$errors[] = "The update process cannot be continued. There is no Zip-extension for PHP installed on your server.\nPlease install it and try again.";
			}

			if (!empty($formSubmitted) && $isZipExtLoaded) {
				// OK. we need to create Zend_Rest_Client, check user and get available updates if allowed
				$client = new Zend_Rest_Client(SJB_System::getSystemSettings('SJB_UPDATE_SERVER_URL'));

				$result = $client->getUpdateLink(
					SJB_Request::getVar('auth_username'),
					SJB_Request::getVar('auth_password'),
					SJB_System::getSystemSettings('version'))->get();

				if ($result->isSuccess()) {
					if (isset($result->error)) {
						$errors[] = (string) $result->error;
						$tp->assign("wayToUpdate", $wayToUpdate);
					}
					if (isset($result->update)) {
						$updateLink = (string) $result->update;
						$downloadedFileName = basename($updateLink);
						$unzipDirname       = basename($updateLink, '.zip');
						$downloadedFilePath = $updatesDir . $downloadedFileName;
						// download update file to cache/updates folder

						$result = copy($updateLink, $downloadedFilePath);
						if ($result) {
							if ($isZipExtLoaded) {
								$zip = new ZipArchive;
								$res = $zip->open($downloadedFilePath);
								if ($res === true) {
									$zip->extractTo($updatesDir . DIRECTORY_SEPARATOR . $unzipDirname . DIRECTORY_SEPARATOR);
									$zip->close();
								} else {
									$errors[] = 'Failed to extract upgrade package files';
								}
							} else {
								$errors[] = "The update process cannot be continued. There is no Zip-extension for PHP installed on your server.\nPlease install it and try again.";
							}
						} else {
							$errors[] = 'Failed to download upgrade package';
						}

						if ($wayToUpdate == 'autoUpdate') {
							SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('ADMIN_SITE_URL') . '/update-to-new-version/?to_version=' . urlencode($unzipDirname));
						} elseif ($wayToUpdate == 'makeArchive') {
							$ignorePaths = array(
								'system/plugins/facebook_social_plugin',
								'system/plugins/linkedin_social_plugin',
								'system/plugins/broadbean_integration_plugin',
								'system/plugins/jobg8_integration_plugin',
								'system/plugins/mobile',
								'system/plugins/api',
								'system/plugins/facebook_app',
								'templates/Facebook',
								'templates/BusinessView',
								'templates/ClearView',
								'templates/EnhancedView',
								'templates/ElegantView',
								'templates/mobile'
							);
							$serializedFilesInfo = file_get_contents($updatesDir . $unzipDirname . DIRECTORY_SEPARATOR . 'files_info_serialized.info');
							$filesInfo           = unserialize($serializedFilesInfo);
							$filesList = array_keys($filesInfo);
							$updateManager = new SJB_UpdateManager($unzipDirname);
							foreach ($filesInfo as $key => $fileInfo) {
								$filepath  = $fileInfo['filepath'];
								foreach ($ignorePaths as $ignorePath) {
									if (strpos($filepath, $ignorePath) !== false) {
										$ignorePath = SJB_BASE_DIR . $ignorePath;
										if (!file_exists($ignorePath)) {
											unset($filesInfo[$key]);
											continue 2;
										}
									}
								}
							}
							$errors = $updateManager->createZipArchiveWithFiles($filesList, $filesInfo);
							if (empty($errors)) {
								$updateManager->sendArchiveToUser();
								exit;
							}
						}
					}
				}
			}
			$tp->assign('errors', $errors);
			$tp->display('update_to_new_version.tpl');
		}
	}

	private function updatingProcess()
	{
		$response = array();
		
		$status = SJB_Request::getVar('status', null);
		if (!$status) {
			$response['success'] = false;
			$response['message'] = 'Status not set.';
		} else {
			$response = $this->updatingProcessController($status);
			if (!isset($response['success'])) {
				$response['success'] = true;
			}
		}
		
		echo json_encode($response);
	}

	private function updatingProcessController($status)
	{
		$response = array();
		
		switch ($status) {
			case 'prepareForUpdate':
				$response['status'] = 'prepareForUpdateCompleted';
				break;
			case 'updateFiles':
				$this->lockPatchDatabaseOnlyUsingSJB();
				$response['status'] = 'updateFilesShowPreloader';
				break;
			case 'updateFilesStart':
				$_REQUEST['updateFilesStart'] = true;
				$this->updateToNewVersion();
				$response['status'] = 'updateFilesCompleted';
				break;
			case 'updateDatabase':
				$response['percentagePerPatch'] = SJB_UpdateManager::updateDatabasePercentagePerPatch();
				$response['status']             = 'updateDatabaseShowPreloader';
				break;
			case 'updateDatabaseStart':
				$_REQUEST['updateDatabaseStart'] = true;
				$this->updateToNewVersion();
				if ($GLOBALS['updateDatabasePatched'] === true) {
					$response['status'] = 'updateDatabasePatchSet';
				}
				else if ($GLOBALS['updateDatabasePatched'] === false) {
					$response['status'] = 'updateDatabaseCompleted';
					SJB_UpdateManager::removeDatabasePatchFile();
					$this->unlockPatchDatabaseOnlyUsingSJB();
				} else {
					$response['message'] = $GLOBALS['updateDatabasePatched'];
					$response['success'] = false;
				}
				break;
			case 'updateRestPart':
				$response['status'] = 'updateRestPartShowPreloader';
				break;
			case 'updateRestPartStart':
				$_REQUEST['updateRestPart'] = true;
				$this->updateToNewVersion();
				$response['status'] = 'updateRestPartCompleted';
				break;
		}
		
		return $response;
	}

	private function lockPatchDatabaseOnlyUsingSJB()
	{
		$patchDatabaseOnlyUsingSJB = SJB_Settings::getSettingByName('patchDatabaseOnlyUsingSJB');
		if ($patchDatabaseOnlyUsingSJB === false) {
			SJB_Settings::addSetting('patchDatabaseOnlyUsingSJB', '1');
		}
		else if (!$patchDatabaseOnlyUsingSJB) {
			SJB_Settings::updateSetting('patchDatabaseOnlyUsingSJB', '1');
		}
	}

	private function unlockPatchDatabaseOnlyUsingSJB()
	{
		SJB_Settings::updateSetting('patchDatabaseOnlyUsingSJB', '0');
	}
}
