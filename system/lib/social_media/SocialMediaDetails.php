<?php

class SJB_SocialMediaDetails extends SJB_ObjectDetails
{
	var $properties;
	var $details;
	var $common_fields;

	public static function getCommonFields()
	{
		$listing_field_manager = new SJB_ListingFieldManager();
		$common_details = $listing_field_manager->getCommonListingFieldsInfo();
		foreach ($common_details as $key => $details) {
			if ($details['type'] == 'video' || $details['id'] == 'Title') {
				unset($common_details[$key]);
			}
			elseif ($details['type'] == 'location') {
				// Remove 'Search Within' field
				foreach ($details['fields'] as $fieldKey => $field) {
					if ($field['id'] == 'ZipCode') {
						unset($common_details[$key]['fields'][$fieldKey]);
					}
				}
			} else {
				$common_details[$key]['is_required'] = 0;
			}
			if (empty($details['order'])) {
				$maxOrder = 0;
				foreach ($common_details as $val) {
					if ($val['order'] > $maxOrder) {
						$maxOrder = $val['order'];
					}
				}
				$maxOrder += 0.5;
				$common_details[$key]['order'] = $maxOrder;
			}
		}
		return $common_details;
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
				'id'            => 'post_template',
				'caption'       => 'Post template',
				'type'          => 'string',
				'length'        => '20',
				'is_required'   => true,
				'is_system'     => true,
				'order'         => 10000,
			)
		);
	}
}
