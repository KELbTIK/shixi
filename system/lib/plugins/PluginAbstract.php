<?php

class SJB_PluginAbstract
{
	function pluginSettings()
	{
		return array();
	}
	
	public static function init()
	{
	}

	/**
	 * @param array  $criteria
	 * @param string $settingName
	 * @return string
	 */
	protected static function getLocation(array $criteria, $settingName = '')
	{
		$location = isset($criteria['Location']['location']['value']) ? $criteria['Location']['location']['value'] : '';
		$locationInfo['zipCode'] = isset($criteria['Location_ZipCode']['geo']['location']) ? $criteria['Location_ZipCode']['geo']['location'] : '';

		if (isset($criteria['Location_City']['like'])) {
			$locationInfo['city'] = $criteria['Location_City']['like'];
		}
		else if (isset($criteria['Location_City']['multi_like_and'][0])) {
			$locationInfo['city'] = $criteria['Location_City']['multi_like_and'][0];
		}

		if (isset($criteria['Location_State']['multi_like'])) {
			foreach ($criteria['Location_State']['multi_like'] as $stateSID) {
				if (!empty($stateSID)) {
					$stateInfo = SJB_StatesManager::getStateInfoBySID($stateSID);
					$locationInfo['state'] = !empty($stateInfo['state_code']) ? $stateInfo['state_code'] : '';
				}
			}
		}

		$result = '';
		if (!empty($location)) {
			$result = $location;
		}
		foreach ($locationInfo as $value) {
			if (!empty($value) && $value != $location) {
				if (!empty($result)) {
					$result .= ",{$value}";
				} else {
					$result = $value;
				}
			}
		}

		if (!empty($settingName) && empty($result)) {
			$result = SJB_Settings::getSettingByName($settingName);
		}

		if (!empty($result)) {
			$result = trim($result);
			$result = urlencode($result);
		}
		
		return empty($result) ? '' : $result;
	}
}