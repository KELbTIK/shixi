<?php

namespace SJB\Smarty\Modifier;

class PaymentTranslator
{
	public static function translate($string, $domain = null, $mode = null)
	{
		if (empty($string)) {
			return $string;
		}

		$i18n = \SJB_I18N::getInstance();
		$matches = array();
		$result = array();
		if (strpos($string, ',') !== false) {
			$stringStack = explode(', ', $string);
		} else {
			$stringStack = array($string);
		}
		foreach ($stringStack as $string) {
			$isFeaturedPayment = preg_match("#Upgrade of (\".+\") Job to featured( \(.+\))?#", $string, $matchesFeatured);
			$isPriorityPayment = preg_match("#Upgrade of (\".+\") Job to priority( \(.+\))?#", $string, $matchesPriority);
			if ($isFeaturedPayment) {
				$matches = $matchesFeatured;
				$prefix = 'Upgrade of $listingTitle Job to featured';
			}
			else if ($isPriorityPayment) {
				$matches = $matchesPriority;
				$prefix = 'Upgrade of $listingTitle Job to priority';
			}

			if ($matches) {
				$listingTitle = $matches[1];
				$prefix = $i18n->gettext($domain, trim($prefix), $mode);
				$result[] = str_replace('{$listingTitle}', $listingTitle, $prefix) . $matches[2];
			}
		}
		return $result ? join(', ', $result) : $string;
	}
}