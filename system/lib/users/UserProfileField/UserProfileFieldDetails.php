<?php

class SJB_UserProfileFieldDetails extends SJB_ObjectDetails
{
    function SJB_UserProfileFieldDetails( $user_profile_field_info )
    {
        $details_info = SJB_UserProfileFieldDetails::getDetails( $user_profile_field_info );
        foreach ( $details_info as $detail_info ) {
            if ( isset( $user_profile_field_info[$detail_info['id']] ) ) {
                $detail_info['value'] = $user_profile_field_info[$detail_info['id']];
            }
            else {
                $detail_info['value'] = isset( $detail_info['value'] ) ? $detail_info['value'] : '';
            }
            $this->properties[$detail_info['id']] = new SJB_ObjectProperty( $detail_info );
        }
    }

    public static function getDetails( $user_profile_field_info )
    {
        $common_details_info = array(
            array(
                'id' => 'id',
                'caption' => 'ID',
                'type' => 'unique_string',
            	'validators' => array(
					'SJB_UserFieldIdValidator',
            		'SJB_OneCharValidator',
            		'SJB_UniqueSystemUserProfileFieldsValidator'
				),
                'length' => '20',
                'table_name' => 'user_profile_fields',
                'is_required' => true,
                'is_system' => true,
            ),
            array(
                'id' => 'caption',
                'caption' => 'Caption',
                'type' => 'string',
                'length' => '20',
                'is_required' => true,
                'is_system' => true,
            ),
            array(
                'id' => 'type',
                'caption' => 'Type',
                'type' => 'list',
                'list_values' => array(
                    array(
                        'id' => 'string',
                        'caption' => 'String',
                    ),
                    array(
                        'id' => 'text',
                        'caption' => 'Text',
                    ),
                    array(
                        'id' => 'list',
                        'caption' => 'List',
                    ),
					array(
						'id' => 'multilist',
						'caption' => 'MultiList',
					),
                    array(
                        'id' => 'boolean',
                        'caption' => 'Boolean',
                    ),
                    array(
                        'id' => 'date',
                        'caption' => 'Date',
                    ),
                    array(
                        'id' => 'picture',
                        'caption' => 'Picture',
                    ),
                    array(
                        'id' => 'logo',
                        'caption' => 'Logo',
                    ),
                    array(
                        'id' => 'email',
                        'caption' => 'Email',
                    ),
                    array(
                        'id' => 'tree',
                        'caption' => 'Tree',
                    ),
                    array(
                        'id' => 'video',
                        'caption' => 'Video',
                    ),
					array(
						'id' => 'geo',
						'caption' => 'Geographical',
					),
					array(
                        'id' => 'location',
                        'caption' => 'Location',
                    ),
					array(
						'id' => 'youtube',
						'caption' => 'YouTube',
					),
				),
                'length' => '',
                'is_required' => true,
                'is_system' => true,
            ),
            array(
                'id' => 'is_required',
                'caption' => 'Required',
                'type' => 'boolean',
                'length' => '',
                'is_required' => false,
                'is_system' => true,
            ),
        );

        $field_type = isset( $user_profile_field_info['type'] ) ? $user_profile_field_info['type'] : null;
        $extra_details_info = SJB_UserProfileFieldDetails::getDetailsByFieldType( $field_type );
        return array_merge( $common_details_info, $extra_details_info );
    }

	public static function getDetailsByFieldType( $field_type )
    {
        return SJB_TypesManager::getExtraDetailsByFieldType( $field_type );
    }

	/**
	 * get Infill instructions field
	 * @return array
	 */
	public static function getInfillInstructions($value = '')
	{
		return array(
			'id' => 'instructions',
			'caption' => 'Infill Instructions',
			'type' => 'text',
			'length' => '',
			'table_name' => 'user_profile_fields',
			'is_required' => false,
			'is_system' => true,
			'order' => 100,
			'value' => $value,
		);
	}
	
	public static function getParentSID($value = '')
	{
		return array(
			'id' => 'parent_sid',
			'type' => 'id',
			'length' => '',
			'table_name' => 'user_profile_fields',
			'is_required' => false,
			'is_system' => true,
			'order' => 100,
			'value' => $value,
		);
	}

	public static function getDisplayAsProperty($value, $fieldType)
	{
		if ($fieldType == 'list') {
			$listValues = array(
				1 => array('id' => 'list',          'caption' => 'List'),
				2 => array('id' => 'radio_buttons', 'caption' => 'Radio Buttons'),
			);
		} else {
			$listValues = array(
				1 => array('id' => 'multilist',  'caption' => 'MultiList'),
				2 => array('id' => 'checkboxes', 'caption' => 'Checkboxes'),
			);
		}
		return array(
			'id' => 'display_as',
			'caption' => 'Display as',
			'value' => ($value),
			'type' => 'list',
			'length' => '',
			'list_values' => $listValues,
			'is_required' => false,
			'is_system' => true,
		);
	}
}

