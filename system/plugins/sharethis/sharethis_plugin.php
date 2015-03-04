<?php

class ShareThisPlugin extends SJB_PluginAbstract
{

	function pluginSettings()
	{
		$listingTypes = SJB_ListingTypeManager::getAllListingTypesInfo();
		$listingPages = array();
		foreach ($listingTypes as $listingType) {
			$listingPages[] = array (
				'id'			=> 'display_on_'.strtolower($listingType['id']).'_page',
				'caption'		=> 'Display for '.$listingType['name'].' details',
				'type'			=> 'boolean',
				'length'		=> '50',
				'order'			=> null,
			);
		}
		$currentFirlds = array( 
			array (
				'id'			=> 'header_code',
				'caption'		=> 'ShareThis Button Header Code',
				'type'			=> 'text',
				'length'		=> '50',
				'order'			=> null,
			),
			array (
				'id'			=> 'code',
				'caption'		=> 'ShareThis Button Code',
				'type'			=> 'text',
				'comment'			=> 'To get a code for a button with different type and style, <a href="http://sharethis.com/publishers/get-sharing-button" target="_blank">click here</a><br/>The received code you will need to paste to the text areas above. ',
				'length'		=> '50',
				'order'			=> null,
			),
			array (
				'id'			=> 'display_for_all_pages',
				'caption'		=> 'Display for All Pages',
				'type'			=> 'boolean',
				'length'		=> '50',
				'order'			=> null,
			),
			array (
				'id'			=> 'display_on_news_page',
				'caption'		=> 'Display for News',
				'type'			=> 'boolean',
				'length'		=> '50',
				'order'			=> null,
			),
		);
		return array_merge($currentFirlds, $listingPages);
	}
}
