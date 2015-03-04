<?php

class SJB_GuestAlertDetails extends SJB_ObjectDetails
{
	public function getDetails()
	{
		return array (
			array (
				'id'		=> 'email',
				'caption'	=> 'Email',
				'type'		=> 'unique_email',
				'table_name' => 'guest_alerts',
				'length'	=> '200',
				'is_required'=> true,
				'is_system'=> true,
				'order'			=> 1,
			),
			array (
				'id'			=> 'email_frequency',
				'caption'		=> 'Email frequency',
				'type'			=> 'list',
				'list_values'	=> array(
					array(
						'id'		=> 'daily',
						'caption'	=> 'Daily',
					),
					array(
						'id'		=> 'weekly',
						'caption'	=> 'Weekly',
					),
					array(
						'id'		=> 'monthly',
						'caption'	=> 'Monthly',
					),
				),
				'is_required'	=> true,
				'is_system'		=> true,
				'order'			=> 2,
			)
		);
	}

	public function addDataProperty($data)
	{
		$this->addProperty(array(
			'id'			=> 'data',
			'caption'		=> 'Data',
			'type'			=> 'text',
			'is_required'	=> true,
			'is_system'		=> true,
			'order'			=> 3,
			'value'			=> $data,
		));
	}

	public function addConfirmationKeyProperty($value)
	{
		$this->addProperty(array(
			'id'			=> 'confirmation_key',
			'caption'		=> 'Confirmation Key',
			'type'			=> 'string',
			'is_required'	=> true,
			'is_system'		=> true,
			'order'			=> 4,
			'value'			=> $value,
		));
	}

	public function addSubscriptionDateProperty($value)
	{
		$this->addProperty(array(
			'id'			=> 'subscription_date',
			'caption'		=> 'Subscription date',
			'type'			=> 'date',
			'is_required'	=> false,
			'is_system'		=> true,
			'order'			=> 5,
			'value'			=> $value,
		));
	}

	public function addStatusProperty($value)
	{
		$this->addProperty(array(
			'id' => 'status',
			'caption' => 'Status',
			'type' => 'list',
			'list_values' => array(
				array(
					'id' => SJB_GuestAlert::STATUS_ACTIVE,
					'caption' => 'Active',
				),
				array(
					'id' => SJB_GuestAlert::STATUS_INACTIVE,
					'caption' => 'Inactive',
				),
				array(
					'id' => SJB_GuestAlert::STATUS_UNCONFIRMED,
					'caption' => 'Unconfirmed',
				),
				array(
					'id' => SJB_GuestAlert::STATUS_UNSUBSCRIBED,
					'caption' => 'Unsubscribed',
				),
			),
			'is_required' => false,
			'is_system' => true,
			'value' => $value,
		));
	}

	public function addListingTypeIDProperty($value)
	{
		$this->addProperty(array(
			'id' => 'listing_type_id',
			'caption' => 'Listing Type ID',
			'type' => 'string',
			'is_required' => false,
			'is_system' => true,
			'value' => $value,
		));
	}
}
