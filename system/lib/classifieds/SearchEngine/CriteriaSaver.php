<?php

class SJB_CriteriaSaver
{
	/**
	 * @var null|array
	 */
	var $object_sids	= null;
	var $criteria		= array();
	var $object_manager	= array();
	var $storage_id = null;
	
	function SJB_CriteriaSaver($storage_id, $object_manager)
	{
		$this->storage_id			= $storage_id;
		$this->object_manager       = $object_manager;
		$this->object_sids			= self::getObjectSIDs();
		$this->criteria 			= &$_SESSION[$storage_id]['criteria_values'];
		$this->order_info 			= &$_SESSION[$storage_id]['order_info'];
		$this->current_page			= &$_SESSION[$storage_id]['page'];
		$this->listings_per_page	= &$_SESSION[$storage_id]['listings_per_page'];
		$this->show_brief_or_detailed = &$_SESSION[$storage_id]['show_brief_or_detailed'];
		if (is_null($this->criteria))
			$this->criteria = array();
	}

	function setSession($request_data, $object_sids)
	{
		$this->setSessionForCriteria($request_data);
		$this->setSessionForObjectSIDs($object_sids);
	}

	function setSessionForCriteria($request_data)
	{
		$criteria_values = array();

		foreach ($request_data as $data_name => $data)
			if (is_array($data))
				$criteria_values[$data_name] = $data;

		$this->criteria = $criteria_values;
	}

	function setSessionForOrderInfo($request_data)
	{
		$sorting_field = isset($request_data['sorting_field']) ? $request_data['sorting_field'] : null;
		$sorting_order = isset($request_data['sorting_order']) ? $request_data['sorting_order'] : null;

		if (!empty($sorting_field) && !empty($sorting_order))
			$this->order_info = array(	'sorting_field' => $sorting_field,
										'sorting_order' => $sorting_order);
	}

	function setSessionForCurrentPage($current_page)
	{
		$this->current_page = $current_page;
	}

	function setSessionForListingsPerPage($listings_per_page)
	{
		$this->listings_per_page = $listings_per_page;
	}

	function setSessionForObjectSIDs($object_sids)
	{
		$_SESSION[$this->storage_id]['found_sids'] = implode(',', $object_sids);
		$this->object_sids = $object_sids;
	}
	
	/**
	 * Setting session for Brief or Detailed Search.
	 * @param array $show_brief_or_detailed
	 */
	function setSessionForBriefOrDetailedSearch($show_brief_or_detailed)
	{
		$this->show_brief_or_detailed = $show_brief_or_detailed;
	}

	function getObjectSIDs()
	{
		// По идее лучше использовать значения если они есть вместо того, чтобы
		// каждый раз брать и эксплодить и декомпресить значения
		if (!empty($this->object_sids)) {
			return $this->object_sids;
		}
		if (isset($_SESSION[$this->storage_id]['found_sids'])) {
			if (empty($_SESSION[$this->storage_id]['found_sids'])) {
				$this->object_sids = array();
			} else {
				$this->object_sids = explode(',', $_SESSION[$this->storage_id]['found_sids']);
			}
		}
		return $this->object_sids;
	}

	function getCriteria()
	{
		return $this->criteria;
	}

	function getOrderInfo()
	{
		return $this->order_info;
	}
	
	/**
	 * Getting "Brief Or Detailed" value from session
	 *
	 * @return string
	 */
	function getBriefOrDetailedSearch()
	{
		if (isset($_SESSION[$this->storage_id]['show_brief_or_detailed'])){
			return $this->show_brief_or_detailed;
		}
	}

	function getCurrentPage()
	{
		return $this->current_page;
	}

	function getListingsPerPage()
	{
		return $this->listings_per_page;
	}

	function getPreviousAndNextObjectID($object_sid)
	{
		if (empty($this->object_sids))
			return array('prev' => null, 'next' => null);

		$key = array_search($object_sid, $this->object_sids);

		if ($key !== false) {
			$previous_object_id = ($key > 0) ? $this->object_sids[$key-1] : null;
			$next_object_id     = ($key < count($this->object_sids)-1) ? $this->object_sids[$key+1] : null;

			return array('prev' => $previous_object_id, 'next' => $next_object_id);
		}
		return array('prev' => null, 'next' => null);
	}

	function getObjectsFromSession()
	{
		if (empty($this->object_sids))
			return array();

		$object_collection = array();

		foreach ($this->object_sids as $object_sid) {
			$object = $this->object_manager->getObjectBySID($object_sid);

			if (is_null($object))
				continue;

			$object_collection[$object_sid] = $object;
		}

		return $object_collection;
	}

    function createTemplateStructureForCriteria()
    {
		$structure = array();

		if (empty($this->criteria))
			return null;

		foreach ($this->criteria as $property_name => $criterion_value) {
			if (count($criterion_value) == 1)
				$criterion_value = array_pop($criterion_value);

			$structure[$property_name]['value'] = $criterion_value;
		}

		return $structure;
	}

	function createTemplateStructureForSearch()
	{
		$listings_number 	= count($this->object_sids);
		
		$pages_number = null;
		if ($this->listings_per_page > 0)
			$pages_number = ceil($listings_number / $this->listings_per_page);
		$structure = array
		(
			'listings_number' 	=> $listings_number,
			'pages_number' 		=> $pages_number,
			'listings_per_page' => $this->listings_per_page,
			'current_page' 		=> $this->current_page,
			'sorting_field' 	=> $this->order_info['sorting_field'],
			'sorting_order' 	=> $this->order_info['sorting_order'],
		);

		return $structure;
	}

	function resetSearchResultsDisplay()
	{
		$this->order_info			= null;
        $this->current_page			= null;
		$this->listings_per_page	= null;
	}

	public function setSessionForRefine($fieldID, $data)
	{
		$_SESSION['refine'][$fieldID] = $data;
		$this->refine[$fieldID] = $data;
	}

	public function getSessionForRefine($fieldID)
	{
		if (!empty($_SESSION['refine'][$fieldID])) {
			$data = $_SESSION['refine'][$fieldID];
			$this->refine[$fieldID] = $data;
		}
		return isset($this->refine[$fieldID]) ? $this->refine[$fieldID] : false;
	}
}
