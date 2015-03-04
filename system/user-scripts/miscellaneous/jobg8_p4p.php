<?php
class SJB_Miscellaneous_Jobg8P4p extends SJB_Function
{
	public function execute()
	{
		/***************************************************
		 * Integration of JobSource Jobg8 script
		 *
		 * This script integrate P4P of JobG8
		 ***************************************************/
		/*
		  For example in SJB there is a user "emp", с user_id = 8, emal = emp@emp.com, username = EMPjob
		  Are we correct to assume that the encryption parameters will be as follows:

		  ADHOC is ON:
		  ?cid=810388&a=ADHOC&email=emp@emp.com&adv=EMPjob

		  ADHOC is OFF:
		  ?cid=810388&a=8&email=emp@emp.com&adv=EMPjob
		  */

		$tp = SJB_System::getTemplateProcessor();


		if (SJB_UserManager::isUserLoggedIn()) {

			$currentUser = SJB_UserManager::getCurrentUserInfo();
			$currentUsername = $currentUser['username'];
			$userEmail = $currentUser['email'];
			$username = $currentUser['CompanyName'];

			if (empty($username)) {
				$username = $currentUser['username'];
			}

			// our jobg8 Job Board ID
			$jobboardID = SJB_Settings::getSettingByName('jobg8_jobboard_id_p4p');
			$jobg8_p4p_url = SJB_Settings::getSettingByName('jobg8_p4p_url');
			$cid = SJB_Settings::getSettingByName('jobg8_cid');


			$markup = '';
			$mode = '';

			// check current user for individual markup value
			$result = SJB_DB::query("SELECT * FROM `users_markup` WHERE `user_sid` = ?n", $currentUser['sid']);
			if (!empty($result)) {
				$markup = $result[0]['markup'];
			}

			// check individual adhoc mode
			if ($currentUser['jobg8_adhoc'] == 1) {
				$adhoc_mode = true;
			} else {
				$adhoc_mode = false;
			}

			// look jobg8 p4p-integration doc (parameter 'a')
			if ($adhoc_mode) {
				$mode = 'ADHOC';
			} else {
				$mode = $currentUser['sid'];
			}


			//////////////////////////////////
			// set region field for P4P
			// check tax countries and states list
			//////////////////////////////////
			$taxRegions = array(
				'Canada' => array(
					"Alberta" => "AB",
					"British Columbia" => "BC",
					"Manitoba" => "MB",
					"New Brunswick" => "NB",
					"Newfoundland and Labrador" => "NL",
					"Nova Scotia" => "NS",
					"Northwest Territories" => "NT",
					"Nunavut" => "NU",
					"Ontario" => "ON",
					"Prince Edward Island" => "PE",
					"Quebec" => "QC",
					"Saskatchewan" => "SK",
					"Yukon" => "YT",
				),
				'Germany' => 'DEU',
				'Spain' => 'ESP',
				'Ireland' => 'IRL',
			);


			// check country
			$taxRegionCode = '';
			$userCountry = $currentUser['Country'];
			$userState = $currentUser['State'];

			if (!empty($userCountry) && !empty($userState) && array_key_exists($userCountry, $taxRegions)) {
				if (isset($taxRegions[$userCountry]) && is_string($taxRegions[$userCountry])) {
					$taxRegionCode = $taxRegions[$userCountry];
				} elseif (isset($taxRegions[$userCountry]) &&
						is_array($taxRegions[$userCountry]) &&
						array_key_exists($userState, $taxRegions[$userCountry])
				) {
					// check region
					$taxRegionCode = $taxRegions[$userCountry][$userState];
				}
			}


			if ($markup == '' || !is_numeric($markup)) {
				if ($mode == 'ADHOC') {
					$message = "?cid={$cid}&a={$mode}&email={$userEmail}&adv={$username}&region={$taxRegionCode}";
				} else {
					$message = "?cid={$cid}&a={$mode}&region={$taxRegionCode}";
				}
			} else {
				if ($mode == 'ADHOC') {
					$message = "?cid={$cid}&a={$mode}&email={$userEmail}&adv={$username}&m={$markup}&region={$taxRegionCode}";
				} else {
					$message = "?cid={$cid}&a={$mode}&m={$markup}&region={$taxRegionCode}";
				}
			}


			// use RSA library for crypt
			$sshKey = JobG8IntegrationPlugin::getRsaKey();
			$keyArray = explode(' ', $sshKey, 3);

			$keyLength = $keyArray[0];
			$exponent = $keyArray[1];
			$modulus = $keyArray[2];
			// Encrypt the message
			$encryptedData = rsa_encrypt($message, $exponent, $modulus, $keyLength);
			// Base64 encode the encrypted data
			$output = urlencode(base64_encode($encryptedData));


			$tp->assign('jobg8_p4p_url', $jobg8_p4p_url);
			$tp->assign('jobboardID', $jobboardID);
			$tp->assign('encoded_data', $output);

			$tp->display('jobg8_p4p.tpl');
		}
		else {
			$tp->assign("return_url", base64_encode(SJB_Navigator::getURIThis()));
			//$tp->assign("ajaxRelocate", true);
			$tp->display("../users/login.tpl");
		}
	}
}