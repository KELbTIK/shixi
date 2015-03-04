<?php

class SJB_LinkedInSocialDetails extends SJB_SocialMediaDetails
{
	public $properties;
	public $postingFields;
	public $commonFields;
	public $systemFields;

	/**
	 * @param array $info
	 * @param bool $isGroupsExist
	 * @param bool $isAuthorized
	 */
	function __construct($info = array(), $isGroupsExist = false, $isAuthorized = false)
	{
		$this->commonFields = parent::getCommonFields();
		$this->postingFields = self::getPostingFields();
		$this->systemFields = self::getSystemFields($isGroupsExist, $isAuthorized);

		$detailsInfo = self::getDetails($this->commonFields, $isGroupsExist, $isAuthorized);

		$locationPrefix = '';
		foreach ($detailsInfo as $index => $propertyInfo) {
			$sortArray[$index] = $propertyInfo['order'];
			if ($propertyInfo['type'] == 'location') {
				$locationPrefix = $propertyInfo['id'];
			}
		}

		$sortArray = SJB_HelperFunctions::array_sort($sortArray);

		foreach ($sortArray as $index => $value) {
			$sortedDetailsInfo[$index] = $detailsInfo[$index];
		}

		foreach ($sortedDetailsInfo as $detailInfo) {
			$detailInfo['value'] = '';
			if (isset($info[$detailInfo['id']])) {
				$detailInfo['value'] = $info[$detailInfo['id']];
			}
			elseif ($detailInfo['id'] == 'post_template') {
				$detailInfo['value'] = '{$user.CompanyName}: {$listing.Title} ({$listing.'.$locationPrefix.'.City}, {$listing.'.$locationPrefix.'.State})';
			}
			$this->properties[$detailInfo['id']] = new SJB_ObjectProperty($detailInfo);
		}
	}


	/**
	 * @param $commonDetails
	 * @param bool $isGroupsExist
	 * @param bool $isAuthorized
	 * @return array
	 */
	public static function getDetails($commonDetails, $isGroupsExist = false, $isAuthorized = false)
	{
		foreach ($commonDetails as $key => $val) {
			if ($val['id'] == 'Title') {
				unset($commonDetails[$key]);
			} else {
				$commonDetails[$key]['is_required'] = false;
			}
		}

		$systemFields = self::getSystemFields($isGroupsExist, $isAuthorized);
		$postingFields = self::getPostingFields();

		return array_merge($commonDetails, $systemFields, $postingFields);
	}

	/**
	 * @param bool $isAuthorized
	 * @param bool $isGroupsExist
	 * @return array
	 */
	public static function getSystemFields($isGroupsExist = false, $isAuthorized = false)
	{
		$postToNetworkFields = array();
		$systemFields =  array(
			array(
				'id'            => 'account_id',
				'caption'       => 'LinkedIn Account',
				'type'          => 'string',
				'length'        => '255',
				'is_required'   => true,
				'is_system'     => true,
				'order'         => -2,
			),
			array(
				'id'            => 'feed_name',
				'caption'       => 'Feed Name',
				'type'          => 'unique_string',
				'table_name'    => 'linkedin_feeds',
				'validators'    => array(
					'SJB_IdWithSpaceValidator',
					'SJB_UniqueSystemValidator'
				),
				'length'        => '20',
				'is_required'   => true,
				'is_system'     => true,
				'order'         => -1,
			),
		);
		if (!empty($isAuthorized)) {
			$postToNetworkFields = array(
					array(
						'id'            => 'post_to_updates',
						'caption'       => 'Post to updates',
						'type'          => 'boolean',
						'is_required'   => false,
						'is_system'     => true,
						'order'         => 0,
					)
			);
			if ($isGroupsExist) {
				$postToNetworkFields = array_merge($postToNetworkFields,
					array(
						array(
							'id'            => 'post_to_groups',
							'caption'       => 'Post to group(s)',
							'type'          => 'boolean',
							'is_required'   => false,
							'is_system'     => true,
							'order'         => 0.5,
						),
						array(
							'id'            => 'groups',
							'caption'       => 'Group(s)',
							'type'          => 'multilist',
							'list_values'   => array(),
							'is_required'   => false,
							'is_system'     => true,
							'order'         => 1,
						)
					)
				);
			}

		}

		return array_merge($systemFields, $postToNetworkFields);
	}

	/**
	 * @return array
	 */
	public static function getPostingFields()
	{
		return array(
			array(
				'id'			=> 'update_every',
				'caption'		=> 'Update every',
				'type'			=> 'integer',
				'length'		=> '20',
				'is_required'	=> true,
				'is_system'		=> true,
				'order'			=> 100,
			),
			array(
				'id'			=> 'posting_limit',
				'caption'		=> 'Posting Limit',
				'type'			=> 'integer',
				'length'		=> '20',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 101,
				'comment'		=> 'Leave blank for unlimited posting.',
			),
			array(
				'id'			=> 'post_template',
				'caption'		=> 'Post template',
				'comment'		=> 'Link to listing details page will be added automatically to the end of each post.',
				'type'			=> 'string',
				'length'		=> '20',
				'is_required'	=> true,
				'is_system'		=> true,
				'order'			=> 1000,
			)
		);
	}
}
