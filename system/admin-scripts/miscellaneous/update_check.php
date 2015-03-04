<?php

class SJB_Admin_Miscellaneous_UpdateCheck extends SJB_Function
{
	// marker for session storage of update info
	const SESSION_UPDATE_TAG = 'update_check_state';

	public function isAccessible()
	{
		return true;
	}

	public function execute()
	{
		$action = SJB_Request::getVar('action');

		$sessionUpdateData = SJB_Session::getValue(self::SESSION_UPDATE_TAG);

		if ($action == 'mark_as_closed') {
			if (is_array($sessionUpdateData)) {
				$sessionUpdateData['closed_by_user'] = true;
				SJB_Session::setValue(self::SESSION_UPDATE_TAG, $sessionUpdateData);
			}
			exit;
		}

		// check updates
		$serverUrl = SJB_System::getSystemSettings('SJB_UPDATE_SERVER_URL');
		$version   = SJB_System::getSystemSettings('version');

		// CHECK FOR UPDATES
		$updateInfo    = SJB_Session::getValue(self::SESSION_UPDATE_TAG);
		if (empty($updateInfo)) {
			// check URL for accessibility
			$ch = curl_init($serverUrl);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_exec($ch);

			$urlInfo = curl_getinfo($ch);

			$availableVersion   = array();
			$updateStatus = '';
			if ($urlInfo['http_code'] > 0) {
				// OK. Url is accessible - lets get update info
				try {
					$client = new Zend_Rest_Client($serverUrl);
					$result  = $client->isUpdateAvailable($version['major'], $version['minor'], $version['build'], SJB_System::getSystemSettings('USER_SITE_URL'))->get();
					if ($result->isSuccess()) {
						$updateStatus = (string)$result->updateStatus;
						switch ($updateStatus) {
							case 'available':
								$availableVersion = array(
									'major' => (string) $result->version->major,
									'minor' => (string) $result->version->minor,
									'build' => (string) $result->version->build,
								);
								break;
						}
					}
				} catch (Exception $e) {
					SJB_Error::writeToLog('Update Check: ' . $e->getMessage());
				}
			}

			$updateInfo = array(
				'availableVersion'  => $availableVersion,
				'updateStatus' => $updateStatus,
			);

			SJB_Session::setValue(self::SESSION_UPDATE_TAG, $updateInfo);
		} else {
			if (isset($updateInfo['availableVersion']) && !empty($updateInfo['availableVersion'])) {
				if ($updateInfo['availableVersion']['build'] <= $version['build']) {
					$updateInfo = array(
						'availableVersion'  => $updateInfo['availableVersion'],
						'updateStatus' => 'none',
					);
				}
			}
		}

		echo json_encode($updateInfo);
		exit;
	}

}