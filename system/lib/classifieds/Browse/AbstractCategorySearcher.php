<?php

class SJB_AbstractCategorySearcher
{
	protected $field;

	public function SJB_AbstractCategorySearcher($field)
	{
		$this->field = $field;
	}

	public function getFieldSID($field)
	{
		$property = SJB_ListingManager::getPropertyByPropertyName($field);
		return $property->getSID();
	}

	public function decorateItems($request_data, $items)
	{
		return $this->_decorateItems($items, $request_data);
	}
	
	public function getItems($request_data, array $listingSids = array())
	{
		return $this->_get_Captions_with_Counts_Grouped_by_Captions($request_data, $listingSids);
	}
	
	protected function _get_Captions_with_Counts_Grouped_by_Captions($request_data, array $listingSids = array())
	{
		$columns =  '?w as caption';
		if (SJB_Settings::getValue('enableBrowseByCounter')) {
			$columns .=  ', count(*) as count';
		}
		$criteria = SJB_SearchFormBuilder::extractCriteriaFromRequestData($request_data);
		$sqlTranslator = new SJB_SearchSqlTranslator('listings');
		$whereStatement = $sqlTranslator->_getWhereStatement($criteria);
		if (!empty($listingSids)) {
			$whereStatement .= 'AND `listings`.`sid` IN (' . implode(',', $listingSids) . ')';
		}
		
		$res = SJB_DB::query("select {$columns} from listings {$whereStatement} and ?w != '' group by ?w", $this->field['field'], $this->field['field'], $this->field['field']);
		
		$result = array();
		foreach ($res as $value) {
			if (SJB_Settings::getValue('enableBrowseByCounter')) {
				$result[$value['caption']] = $value['count'];
			} else {
				$result[$value['caption']] = '';
			}
		}
		
		return $result;
	}

}

