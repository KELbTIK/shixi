<?php

class SJB_NewsArticleDetails extends SJB_ObjectDetails
{
	
	public $properties;
	
	public function __construct($articleInfo)
	{
		$detailsInfo = self::getDetails();
		
		//SORT BY ORDER
		$sortArray = array();
		foreach ($detailsInfo as $index => $propertyInfo) {
			$sortArray[$index] = $propertyInfo['order'];
		}
		$sortArray = SJB_HelperFunctions::array_sort($sortArray);

		$sortedDetailsInfo = array();
		foreach ($sortArray as $index => $value) {
			$sortedDetailsInfo[$index] = $detailsInfo[$index];
		}
		
		foreach ($sortedDetailsInfo as $detailInfo) {
		    $detailInfo['value'] = '';
			if (isset($articleInfo[$detailInfo['id']])) {
				$detailInfo['value'] = $articleInfo[$detailInfo['id']];
			}
			
			$this->properties[$detailInfo['id']] = new SJB_ObjectProperty($detailInfo);
		}
	}
	
	
	public static function getDetails()
	{
		// GET LANGUAGES LIST
		$i18N = SJB_ObjectMother::createI18N();
		$frontEndLanguages = $i18N->getActiveFrontendLanguagesData();
		$languages   = array();
		foreach ($frontEndLanguages as $lang) {
			$languages[] = array(
				'id' => $lang['id'],
				'caption' => $lang['caption'],
			);
		}
			
		$details = array (
			array (
				'id'			=> 'title',
				'caption'		=> 'Title',
				'type'			=> 'string',
				'length'		=> '255',
				'is_required'	=> true,
				'is_system'		=> true,
				'order'			=> 0,
			),
			array (
				'id'			=> 'language',
				'caption'		=> 'Language',
				'type'			=> 'list',
				'length'		=> '30',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 1,
				'list_values'   => $languages,
			),
			array (
				'id'			=> 'brief',
				'caption'		=> 'Brief Text',
				'type'			=> 'text',
				'length'		=> '1000',
				'is_required'	=> true,
				'is_system'		=> true,
				'order'			=> 2,
			),
			array (
				'id'			=> 'text',
				'caption'		=> 'Full Text',
				'type'			=> 'text',
				'length'		=> '64535',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 3,
			),
			array (
				'id'			=> 'keywords',
				'caption'		=> 'Keywords',
				'type'			=> 'string',
				'length'		=> '255',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 4,
			),
			array (
				'id'			=> 'description',
				'caption'		=> 'Description',
				'type'			=> 'string',
				'length'		=> '255',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 5,
			),
			array (
				'id'			=> 'link',
				'caption'		=> 'Redirect To URL:',
				'type'			=> 'string',
				'length'		=> '255',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 6,
			),
			array (
				'id'			=> 'date',
				'caption'		=> 'Publication Date',
				'type'			=> 'date',
				'length'		=> '20',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 7,
			),
			array (
				'id'			=> 'expiration_date',
				'caption'		=> 'Expiration Date',
				'type'			=> 'date',
				'length'		=> '20',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 8,
			),
			array (
				'id'			=> 'image',
				'caption'		=> 'Image',
				'type'			=> 'picture',
				'length'		=> '255',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 9,
				'width'         => 100,
				'height'        => 100,
			),
			array (
				'id'			=> 'category_id',
				'caption'		=> 'Category',
				'type'			=> 'id',
				'length'		=> '20',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 10,
			),
			array (
				'id'			=> 'active',
				'caption'		=> 'Active',
				'type'			=> 'boolean',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 11,
			),

		);
		
		return $details;
	}
}