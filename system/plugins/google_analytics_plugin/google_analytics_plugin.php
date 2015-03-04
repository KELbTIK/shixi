<?php

/**
 * Integration SJB to Google Analytics plugin class
 * Google Analytics Official Website:
 * http://www.google.com/analytics/
 * Helps analyze traffic statistics and visitor preferences
 */
class GoogleAnalyticsPlugin extends SJB_PluginAbstract
{
	public static function init()
	{
		if (SJB_System::getSettingByName('google_TrackingID') != '') {
			$page_head = '<script type="text/javascript">'
					   . 'var _gaq = _gaq || [];'
					   . '_gaq.push(["_setAccount", "' . SJB_System::getSettingByName('google_TrackingID') . '"]);'
					   . '_gaq.push(["_addOrganic", "images.yandex.ru", "text"]);'
					   . '_gaq.push(["_addOrganic", "images.google.ru", "q"]);'
					   . '_gaq.push(["_addOrganic", "go.mail.ru", "q"]);'
					   . '_gaq.push(["_addOrganic", "gogo.ru", "q"]);'
					   . '_gaq.push(["_addOrganic", "nova.rambler.ru", "query"]);'
					   . '_gaq.push(["_addOrganic", "rambler.ru", "words"]);'
					   . '_gaq.push(["_addOrganic", "google.com.ua", "q"]);'
					   . '_gaq.push(["_addOrganic", "search.ua", "q"]);'
					   . '_gaq.push(["_setDomainName", "none"]);'
					   . '_gaq.push(["_setAllowLinker", true]);'
					   . '_gaq.push(["_trackPageview"]);'
					   . '(function() {'
					   . 'var ga = document.createElement("script"); ga.type = "text/javascript"; ga.async = true;'
					   . 'ga.src = ("https:" == document.location.protocol ? "https://ssl" : "http://www") + ".google-analytics.com/ga.js";'
					   . 'var s  = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ga, s);'
					   . '})();'
					   . '</script>';

			$head = SJB_System::getPageHead();
			SJB_System::setPageHead($head . ' ' .  $page_head);
		}
	}

	public function pluginSettings ()
	{
		return array(
			array(
				'id'			=> 'google_TrackingID',
				'caption'		=> 'Tracking ID:',
				'type'			=> 'string',
				'length'		=> '15',
				'validators'    => array(
					'SJB_GoogleAnalyticsIdValidator'
				),
				'is_required'	=> true,
				'order'			=> null,
				'comment'		=> 'This is tracking ID for Google Analytics.<br/>Once you enter it and save changes your Google Analytics tracking code would be automatically placed in index.php file<br/>and you will be able to track Google Analytics statistics in your GA account.<br/>You can find your Tracking ID in your Google Analytics Account > Admin > Tracking Code.'
			),
		);
	}
}
