<?php

class SJB_Social_LinkedinPeopleSearchResults extends SJB_Function
{
	public function execute()
	{
		if (class_exists('SJB_SocialPlugin') && in_array('linkedin', SJB_SocialPlugin::getAvailablePlugins()) && SJB_Settings::getSettingByName('li_allowPeopleSearch')) {
			$liSearch = SJB_Request::getVar('li_search', false);
			$oLinkedin = SJB_SocialPlugin::getActiveSocialPlugin();

			if ('Resume' == $_REQUEST['listing_type']['equal'] && $liSearch && $oLinkedin instanceof LinkedinSocialPlugin) {
				SJB_Session::setValue('linkedinPeopleSearch', true);
				$request = &$_REQUEST;
				/*
				 * keywords=[space delimited keywords]
				 * sort=[connections|recommenders|distance|relevance]
				 * postal-code=[postal code]
				 * start=[number]& count=[1-25]&  facet=[facet code, values]& facets=[facet
				 *
				 * info:
				 * http://developer.linkedin.com/docs/DOC-1191
				 */

				$sKeywords = '';
				$sZip = !empty($request['ZipCode']['geo']['location']) ? $request['ZipCode']['geo']['location'] : '';
				$aIndustry = !empty($request['JobCategory']['multi_like']) ? $request['JobCategory']['multi_like'] : array();
				$sIndustry = '';
				$sCount = !empty($request['count']) ? (int)$request['count'] : 10;

				if (!empty($request['keywords']) && is_array($request['keywords'])) {
					foreach ($request['keywords'] as $keywords)
					{
						$sKeywords = $keywords;
					}
				}

				$aFields = array(
					'keywords' => $sKeywords,
					'postal-code' => $sZip,
					'count' => $sCount,
				);

				foreach ($aIndustry as $industryName)
				{
					if ($industryKey = SJB_LinkedinFields::getIndustryCodeByIndustryName($industryName)) {
						$sIndustry .= ',' . $industryKey;
					}
				}

				if (!empty($sIndustry)) {
					$aFields['facets'] = 'industry';
					$aFields['facet'] = 'industry' . $sIndustry;
				}


				$liResults = $oLinkedin->peopleSearch($aFields);

				if (isset($liResults->{'num-results'}) && (int)$liResults->{'num-results'} >= 0) {
					$tp = SJB_System::getTemplateProcessor();

					if (empty($sKeywords)) {
						$tp->assign('liKeywordEmpty', true);
					}
					$tp->assign('liResults', $oLinkedin->preparePeopleStructure($liResults));
					$tp->assign('liNumResults', (int)$liResults->{'num-results'});

					$tp->assign('linkedinSearchIsAllowed', true);
					$linkedinPeopleSearch = SJB_Session::getValue('linkedinPeopleSearch');
					$tp->assign('linkedinSearch', (!empty($linkedinPeopleSearch) && 'no' === $linkedinPeopleSearch && !empty($_GET['searchId'])) ? 'notChecked' : 'no');
					$tp->display('linkedin_people_search_results.tpl');
				}
			}
			else {
				SJB_Session::setValue('linkedinPeopleSearch', 'no');
			}
		}


	}
}
