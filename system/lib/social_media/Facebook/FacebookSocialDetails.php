<?php

class SJB_FacebookSocialDetails extends SJB_SocialMediaDetails
{
	public $properties;
	public $details;
	public $postingFields;
	public $commonFields;
	public $systemFields;

	public function SJB_FacebookSocialDetails($info)
	{
		$this->commonFields = parent::getCommonFields();
		$this->postingFields = self::getPostingFields();
		$this->systemFields = self::getSystemFields();

		$detailsInfo = self::getDetails($this->commonFields);
		$sortArray = array();
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
			$accountID  = SJB_Request::getVar('account_id', false);
			if (isset($info[$detailInfo['id']])) {
				$detailInfo['value'] = $info[$detailInfo['id']];
			} elseif ($detailInfo['id'] == 'hash_tags') {
				$detailInfo['value'] = '#Jobs';
			} elseif ($detailInfo['id'] == 'post_template') {
				$detailInfo['value'] = '{$user.CompanyName}: {$listing.Title} ({$listing.' . $locationPrefix . '.City}, {$listing.' . $locationPrefix . '.State})';
			} elseif (isset($accountID) && $detailInfo['id'] == 'account_id') {
				$detailInfo['value'] = $accountID;
			}
			$this->properties[$detailInfo['id']] = new SJB_ObjectProperty($detailInfo);
		}
	}

	public static function getDetails($commonDetails)
	{
		foreach ($commonDetails as $key => $val) {
			if ($val['id'] == 'Title') {
				unset($commonDetails[$key]);
			} else {
				$commonDetails[$key]['is_required'] = false;
			}
		}
		$systemFields = self::getsystemFields();
		$postingFields = self::getPostingFields();

		return array_merge($commonDetails, $systemFields, $postingFields);
	}

	public static function getSystemFields()
	{
		return array(
			array(
				'id'            => 'account_id',
				'caption'       => 'Facebook Account',
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
				'table_name'    => 'facebook_feeds',
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
	}

	public static function getPostingFields()
	{
		return array(
			array(
				'id'            => 'update_every',
				'caption'       => 'Update every',
				'type'          => 'integer',
				'length'        => '20',
				'is_required'   => true,
				'is_system'     => true,
				'order'         => 2,
			),
			array(
				'id'            => 'posting_limit',
				'caption'       => 'Posting Limit',
				'type'          => 'integer',
				'length'        => '20',
				'is_required'   => false,
				'is_system'     => true,
				'order'         => 3,
				'comment'       => 'Leave blank for unlimited posting.',
			),
			array(
				'id'            => 'hash_tags',
				'caption'       => 'Hash tags',
				'type'          => 'string',
				'length'        => '20',
				'is_required'   => false,
				'is_system'     => true,
				'order'         => 1000,
			),
			array(
				'id'            => 'post_template',
				'caption'       => 'Post template',
				'comment'       => 'Link to listing details page and hash tags will be added automatically to the end of each post.',
				'type'          => 'string',
				'length'        => '20',
				'is_required'   => true,
				'is_system'     => true,
				'order'         => 10000,
			)
		);
	}
}
