<?php

class SJB_ListingDetails extends SJB_ObjectDetails
{
	var $properties;
	var $details;

	function SJB_ListingDetails($listing_info, $listing_type_sid, $pageID = 0)
	{
		$details_info = SJB_ListingDetails::getDetails($listing_type_sid, $pageID);

		foreach ($details_info as $index => $property_info) {
			$sort_array[$index] = $property_info['order'];
		}
		$sort_array = SJB_HelperFunctions::array_sort($sort_array);

		foreach ($sort_array as $index => $value) {
			$sorted_details_info[$index] = $details_info[$index];
		}

		foreach ($sorted_details_info as $detail_info) {
			$detail_info['value'] = '';
			if (isset($listing_info[$detail_info['id']]))
				$detail_info['value'] = $listing_info[$detail_info['id']];
			$this->properties[$detail_info['id']] = new SJB_ObjectProperty($detail_info);
		}
	}
	
	public static function getDetails($listing_type_sid, $pageID = 0)
	{
		$system_details = array(
					array(
						'id'			=> 'featured',
						'caption'		=> 'Featured',
						'type'			=> 'boolean',
						'length'		=> '20',
						'is_required'	=> false,
						'is_system'		=> true,
						'order'			=> null,
					),	
					array(
						'id'			=> 'priority',
						'caption'		=> 'Priority Listing', 
						'type'			=> 'boolean',
						'length'		=> '20',
						'is_required'	=> false,
						'is_system'		=> true,
						'order'			=> null,
					));

		$listing_field_manager = new SJB_ListingFieldManager();
		$common_details = $listing_field_manager->getCommonListingFieldsInfo($pageID);
		$extra_details  = $listing_field_manager->getListingFieldsInfoByListingType($listing_type_sid, $pageID);

		$details = array_merge($common_details, $extra_details);
		foreach ($details as $key => $detail) {
			$details[$key]['is_system'] = true;
			if ($detail['type'] == 'complex' || $detail['id'] == 'ApplicationSettings' )
				$details[$key]['is_system'] = false;
			elseif ($detail['id'] == 'access_type') {
				$details[$key]['is_system'] = true;
				$details[$key]['list_values'] = array(
								array(
									'id'		=> 'everyone',
									'caption'	=> 'Everyone',
								),
								array(
									'id'		=> 'no_one',
									'caption'	=> 'No one',
								),
								array(
									'id'		=> 'only',
									'caption'	=> 'Only selected employers',
								),
								array(
									'id'		=> 'except',
									'caption'	=> 'Everyone except selected employers',
								),
							);
			}
		}

		$details = array_merge($system_details, $details);
		$isPageLast = SJB_PostingPagesManager::isLastPageByID($pageID, $listing_type_sid);
		if ($isPageLast) {
			
			$status_details = array(
					array (
						'id'			=> 'status',
						'caption'		=> 'Approve Status',
						'type'			=> 'list',
						'list_values'	=> array(
												array(
													'id'		=> 'pending',
													'caption'	=> 'Pending',
												),
												array(
													'id'		=> 'approved',
													'caption'	=> 'Approved',
												),
												array(
													'id'		=> 'rejected',
													'caption'	=> 'Rejected',
												),
											),
						'is_required'	=> false,
						'is_system'		=> true,
						'order'			=> 1000000,
					),
				);
			$details = array_merge($details, $status_details);
	
			
			$rejectReasonDetails = array(
					array (
						'id'			=> 'reject_reason',
						'caption'		=> 'Reject Reason',
						'type'			=> 'text',
						'is_required'	=> false,
						'is_system'		=> true,
						'order'			=> 1000000,
					)
				);
			$details = array_merge($details, $rejectReasonDetails);

			if (SJB_PluginManager::isPluginActive('CaptchaPlugin')
					&& SJB_System::getSettingByName('postJobCaptcha') && strpos(SJB_System::getURI(), '/add-listing/') === 0) {
				$details_captcha =  array
				   (
						array
						(
							'id'		=> 'captcha',
							'caption'	=> 'Enter code from image', 
							'type'		=> 'captcha',
							'length'	=> '20',
							'is_required'=> true,
							'is_system'=> true,
							'order'			=> 10000000,
						),
					);
				$details = array_merge($details, $details_captcha);
			}
		}
		return $details;
	}

	public static function order_minus(&$extra_details, $diff)
	{
		foreach($extra_details as $key => $value) {
			$extra_details[$key]['order'] += $diff;
		}
		return $extra_details;
	}

	function addUsernameProperty($username = null)
	{
		$this->addProperty(
			array(
				'id'		=> 'username',
				'type'		=> 'string',
				'value'		=> $username,
				'is_system' => true,
			)
		);

		return array(
			'id' 				 => 'username',
			'real_id' 			 => 'user_sid',
			'transform_function' => 'SJB_UserManager::getUserSIDByUsername',
		);
	}

	function addCompanyNameProperty($CompanyName = null)
	{
		$this->addProperty(
			array(
				'id'		=> 'CompanyName',
				'type'		=> 'string',
				'value'		=> $CompanyName,
				'is_system' => true,
				'caption'	=> 'CompanyName',
			)
		);
	}

	public function addPriorityProperty()
	{
		$this->addProperty(
			array(
				'id'			=> 'priority',
				'caption'		=> 'Priority Listing',
				'type'			=> 'boolean',
				'length'		=> '20',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> null,
			)
		);
	}
	
	function addEmailFrequencyProperty()
	{
	 $this->addProperty (
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
					'is_required'	=> false,
					'is_system'		=> true,
					'order'			=> 1000000,
				)
			);
	}
	
	function addPostedWithinProperty()
	{
	 $this->addProperty (
				array (
					'id'			=> 'PostedWithin',
					'caption'		=> 'Posted Within',
					'type'			=> 'list',
					'list_values'	=> array(
											array(
												'id'		=> '30',
												'caption'	=> 'Last 30 days',
											),
											array(
												'id'		=> '7',
												'caption'	=> 'Last 7 days',
											),
											array(
												'id'		=> '3',
												'caption'	=> 'Last 3 days',
											),
											array(
												'id'		=> '1',
												'caption'	=> 'Since Yesterday',
											),
										),
					'is_required'	=> false,
					'is_system'		=> true,
					'order'			=> 1000000,
				)
			);
	}

	function addListingTypeIDProperty($type_id)
	{
		if (SJB_MemoryCache::has('listingTypesInfo')) {
			$listing_types_info = SJB_MemoryCache::get('listingTypesInfo');
		}
		else {
			$listing_types_info = SJB_ListingTypeManager::getAllListingTypesInfo();
			SJB_MemoryCache::set('listingTypesInfo', $listing_types_info);
		}
		$list_values = array();
		foreach ($listing_types_info as $type_info) {
			$list_values[] = array('id' => $type_info['id'], 'caption' => $type_info['name']);
		}

		$this->addProperty(
			array(
				'id'			=> 'listing_type',
				'type'			=> 'list',
				'value'			=> $type_id,
				'is_system' 	=> true,
				'list_values' 	=> $list_values,
			)
		);

		return array(
			'id' 				 => 'listing_type',
			'real_id' 			 => 'listing_type_sid',
			'transform_function' => 'SJB_ListingTypeManager::getListingTypeSIDByID',
		);
	}

	/**
	 * Adding DataSource Property
	 *
	 * @param int $listing_feed_sid
	 */
	public function addDataSourceProperty($listing_feed_sid = 0)
	{
		$feeds_info	= SJB_DB::query('SELECT * FROM `parsers`');
		$list_values = array();
		foreach ($feeds_info as $feed_info) {
			$list_values[] = array('id' => $feed_info['id'], 'caption' => $feed_info['name']);
		}
		
		$this->addProperty(
			array(
				'id'		=> 'data_source',
				'type'		=> 'list',
				'value'		=> $listing_feed_sid,
				'is_system' => true,
				'caption'	=> 'Data Source',
				'list_values' 	=> $list_values,
			)
		);		
	}

	function addActivationDateProperty($activation_date = null)
	{
		$this->addProperty(
			array(
				'id'		=> 'activation_date',
				'type'		=> 'date',
				'value'		=> $activation_date,
				'is_system' => true,
			)
		);
	}

	function addFeaturedProperty($featured = false)
	{
		$this->addProperty(
			array(
				'id'			=> 'featured',
				'caption'		=> 'Featured',
				'type'			=> 'boolean',
				'length'		=> '20',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> null,
				'value'			=> $featured
			)
		);
	}

	function addFeaturedLastShowedProperty($lastShowed = null)
	{
		$this->addProperty(
			array(
				'id'			=> 'featured_last_showed',
				'caption'		=> 'Featured Last Showed',
				'type'			=> 'date',
				'length'		=> '20',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> null,
				'value'			=> $lastShowed
			)
		);
	}

	function addExpirationDateProperty($expiration_date = null)
	{
		$this->addProperty(
			array(
				'id'		=> 'expiration_date',
				'type'		=> 'date',
				'value'		=> $expiration_date,
				'is_system' => true,
			)
		);
	}

	function addActiveProperty($is_active)
	{
		$this->addProperty(
			array(
				'caption'		=> 'Status',
				'id'			=> 'active',
				'type'			=> 'list',
				'value'			=> $is_active,
				'is_system' 	=> true,
				'list_values'	=> array(
											array(
												'id'		=> '1',
												'caption'	=> 'Active',
											),
											array(
												'id'		=> '0',
												'caption'	=> 'Not active',
											),
										),				
			)
		);
	}

	function addKeywordsProperty($keywords)
	{
		$this->addProperty(
			array(
				'id'		=> 'keywords',
				'type'		=> 'text',
				'value'		=> $keywords,
				'is_system' => true,
			)
		);
	}

	function addPicturesProperty()
	{
		$this->addProperty(
			array(
				'id'		=> 'pictures',
				'type'		=> 'pictures',
				'is_system' => true,
				'caption'	=> 'Pictures',
				'value'		=> '',
			)
		);
	}
	
	function addCompleteProperty()
	{
		$this->addProperty(
			array(
				'id'		=> 'complete',
				'type'		=> 'integer',
				'is_system' => true,
				'caption'	=> 'Complete',
				'value'		=> '',
			)
		);
	}

	function addIDProperty($id)
	{
		$this->addProperty(
			array(
				'id'		=> 'id',
				'type'		=> 'string',
				'is_system' => true,
				'caption'	=> 'ID',
				'value'		=> $id,
			)
		);
		
		return array(		
			'id' 				 => 'id',
			'real_id' 			 => 'sid',
			'transform_function' => 'SJB_ListingManager::getListingSIDByID',
		);
	}
	
	function addNumberOfViewsProperty($number_of_views)
	{
		
		$this->addProperty(
			array(
				'id'		=> 'views',
				'type'		=> 'string',
				'is_system' => true,
				'caption'	=> 'Views',
				'value'		=> $number_of_views,
			)
		);
		
	}
	
		
	function addRejectReasonProperty()
	{
		$this->addProperty(
			array(
				'id'			=> 'reject_reason',
				'caption'		=> 'Reject Reason',
				'type'			=> 'text',
				'is_required'	=> false,
				'is_system'		=> true,
			)
		);
	}
	
	
	function addApplicationsProperty($apps)
	{
		$this->addProperty(
			array(
				'id'		=> 'applications',
				'type'		=> 'integer',
				'is_system' => false,
				'caption'	=> 'Applications',
				'value'		=> $apps,
			)
		);
		
		return array(		
			'id' 				 => 'applications',
			'real_id' 			 => 'id',
			'transform_function' => 'SJB_Applications::getByJob',
		);
	}
	
	public function addSubuserProperty($sid = 0)
	{
		$this->addProperty(
			array(
				'id'		=> 'subuser_sid',
				'type'		=> 'id',
				'is_system' => true,
				'caption'	=> 'Subuser sid',
				'value'		=> $sid,
			)
		);
	}
	
	public function addExternalIdproperty($ext_id = 0)
	{
		$this->addProperty(
			array(
				'id'		=> 'external_id',
				'type'		=> 'string',
				'is_system' => true,
				'caption'	=> 'ExternalId',
				'value'		=> $ext_id,
			)
		);
	}
	
	public function addProductProperty($listingTypeSid)
	{
		$products = SJB_ProductsManager::getAllProductsInfo();
		foreach ($products as $key => $product) {
			if (!isset($product['listing_type_sid']) || $product['listing_type_sid'] != $listingTypeSid) {
				unset($products[$key]);
			} else {
				$products[$key]['id'] = $product['sid'];
				$products[$key]['caption'] = $product['name'];
			}
		}
		$this->addProperty(array
			(
				'id' => 'product_info_sid',
				'type' => 'list',
				'value' => '',
				'list_values' => $products,
				'is_system' => true
			)
		);
		
		return array(
			'id' 				 => 'product_info_sid',
			'real_id' 			 => 'product_info',
			'transform_function' => 'SJB_ProductsManager::generateQueryBySID',
		);
	}
	
}