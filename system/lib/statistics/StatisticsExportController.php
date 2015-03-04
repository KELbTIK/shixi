<?php

class SJB_StatisticsExportController
{
	public static function createExportDirectory()
	{
		$export_files_dir = SJB_System::getSystemSettings("EXPORT_FILES_DIRECTORY");
		if (!is_dir($export_files_dir)) 			mkdir($export_files_dir, 0777);
        return true;
	}
	
	public static function getGeneralExportData($statistics, $userGroups, $listingTypes, $filter = array())
	{
		$export_data = array();
		foreach ($filter as $nameBlock => $val) {
			switch ($nameBlock) {
				case 'popularity':
					$export_data['viewTitle']['Title'] = 'Popularity';
					$export_data['siteView'] = array();
					foreach ($listingTypes as $listingType) 
						$export_data['viewListing'.$listingType['id']] = array();
				break;
				case 'users':
					$export_data['userTitle']['Title'] = 'Users';
					foreach ($userGroups as $userGroup) 
						$export_data['addUser'.$userGroup['id']] = array();
					$export_data['addSubAccount'] = array();
					
					$export_data['deleteUser'] = array();
				break;
				case 'listings':
					$export_data['listingsTitle']['Title'] = 'Listings';
					foreach ($listingTypes as $listingType) {
						$export_data['addListing'.$listingType['id']] = array();
						if ($listingType['key'] == 'Job') {
							$export_data['addListingFeatured'.$listingType['id']] = array();
						}
						$export_data['addListingPriority'.$listingType['id']] = array();
					}
					foreach ($listingTypes as $listingType) 
						$export_data['deleteListing'.$listingType['id']] = array();
				break;	
				case 'applications':
					$export_data['applyTitle']['Title'] = 'Applications';
					$export_data['apply'] = array();
					$export_data['applyApproved'] = array();
					$export_data['applyRejected'] = array();
				break;
				case 'alerts':
					$export_data['alertTitle']['Title'] = 'Alerts';
					foreach ($listingTypes as $listingType) 
						$export_data['addAlert'.$listingType['id']] = array();
					foreach ($listingTypes as $listingType) 
						$export_data['sentAlert'.$listingType['id']] = array();
					foreach ($listingTypes as $listingType)
						$export_data[SJB_GuestAlertStatistics::EVENT_SENT.$listingType['id']] = array();
					foreach ($listingTypes as $listingType)
						$export_data[SJB_GuestAlertStatistics::EVENT_SUBSCRIBED.$listingType['id']] = array();
				break;
				case 'sales':
					$export_data['salesTitle']['Title'] = 'Sales';
					$export_data['totalAmount'] = array();
					foreach ($userGroups as $userGroup) 
						$export_data['amount_'.$userGroup['id']] = array();
				break;	
				case 'plugins':
					$export_data['pluginsTitle']['Title'] = 'Plugins';
					$export_data['viewMobileVersion'] = array();
					$export_data['addUserlinkedin'] = array();
					$export_data['addUserfacebook'] = array();
					$export_data['addUsergoogle'] = array();
					$export_data['partneringSites'] = array();
				break;
			}
		}
		foreach ($statistics as $date => $statistic) {
			$date = $date=='total'?'Total':$date;
			foreach ($statistic as $key => $value) {
				if (isset($export_data[$key])) {
					$export_data[$key]['Title'] = $value['title'];
					$export_data[$key][$date] = $value['statistic'];
				}
			}
			if (isset($export_data['viewTitle']))
				$export_data['viewTitle'][$date] = '';
			if (isset($export_data['userTitle']))
				$export_data['userTitle'][$date] = '';
			if (isset($export_data['listingsTitle']))
				$export_data['listingsTitle'][$date] = '';
			if (isset($export_data['applyTitle']))
				$export_data['applyTitle'][$date] = '';
			if (isset($export_data['alertTitle']))
				$export_data['alertTitle'][$date] = '';
			if (isset($export_data['salesTitle']))
				$export_data['salesTitle'][$date] = '';
			if (isset($export_data['pluginsTitle']))
				$export_data['pluginsTitle'][$date] = '';
		}
		return $export_data;
	}
	
	public static function getListingExportData($statistics, $listingTypeID)
	{
		$export_data = array();
		
		foreach ($statistics as $key => $statistic) {
			$export_data[$key]['title'] = $statistic['generalColumn'];
			$export_data[$key]['regular'] = $statistic['regular'];
			if ($listingTypeID == 'Job')
				$export_data[$key]['featured'] = $statistic['FeaturedListings'];
				
			$export_data[$key]['priority'] = $statistic['PriorityListings'];
			$export_data[$key]['total'] = $statistic['total'];
			$export_data[$key]['percent'] = $statistic['percent']."%";
		}
		return $export_data;
	}
	
	public static function getAppAndViesExportData($statistics, $exportProperties)
	{
		$export_data = array();
		foreach ($statistics as $key => $statistic) {
			foreach ($exportProperties as $fieldName => $property) {
				if ($fieldName == 'companyName')
					$export_data[$key][$fieldName] = !empty($statistic['CompanyName'])?$statistic['CompanyName']:$statistic['username'];
				elseif (in_array($fieldName, array('totalView', 'totalApply')))
					$export_data[$key][$fieldName] = !empty($statistic[$fieldName])?$statistic[$fieldName]:0;
				else
					$export_data[$key][$fieldName] = $statistic[$fieldName];
			}
		}
		return $export_data;
	}
	
	public static function getSalesExportData($statistics, $exportProperties)
	{
		$export_data = array();
		$currency = SJB_CurrencyManager::getDefaultCurrency();
		foreach ($statistics as $key => $statistic) {
			foreach ($exportProperties as $fieldName => $property) {
				if ($fieldName == 'percent')
					$export_data[$key][$fieldName] = $statistic[$fieldName].'%';
				elseif ($fieldName == 'total')
					$export_data[$key][$fieldName] = $currency['currency_sign'].$statistic[$fieldName];
				else
					$export_data[$key][$fieldName] = $statistic[$fieldName];
			}
		}
		return $export_data;
	}

	/**
	 * @static
	 * @param $exportProperties
	 * @param $exportData
	 * @param $exportFileName
	 * @param $title
	 */
	public static function makeXLSExportFile($exportData, $exportFileName, $title)
	{
		SJB_HelperFunctions::makeXLSExportFile($exportData, $exportFileName, $title);
	}
	
	public static function makeCSVExportFile($exportData, $exportFileName, $title)
	{
		SJB_HelperFunctions::makeCSVExportFile($exportData, $exportFileName, $title);
	}
	
	public static function archiveAndSendExportFile($fileName, $ext)
	{
		$exportFilesDir = SJB_System::getSystemSettings("EXPORT_FILES_DIRECTORY");

		if (empty($exportFilesDir))
			return;

		$archiveFilePath = SJB_Path::combine($exportFilesDir, "{$fileName}.tar.gz");

		$oldPath = getcwd();						
		chdir($exportFilesDir);
		
		$tar = new Archive_Tar("{$fileName}.tar.gz", 'gz');
		$tar->create("{$fileName}.{$ext}");
		
		chdir($oldPath);
		for ($i = 0; $i < ob_get_level(); $i++) {
			ob_end_clean();
		}
		header("Content-type: application/octet-stream");
		header("Content-disposition: attachment; filename={$fileName}.tar.gz");  
		header("Content-Length: " . filesize($archiveFilePath));
		readfile($archiveFilePath);
//		SJB_Filesystem::delete($exportFilesDir);
		exit();
	}

	public static function getGuestAlertsExportData($statistics)
	{
		$export_data = array();

		foreach ($statistics as $key => $statistic) {
			$export_data[$key]['guest email'] = $statistic['generalColumn'];
			$export_data[$key]['total'] = $statistic['total'];
			$export_data[$key]['percent'] = $statistic['percent'].'%';
		}
		return $export_data;
	}
}
