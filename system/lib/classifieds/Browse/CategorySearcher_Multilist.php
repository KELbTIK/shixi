<?php

class SJB_CategorySearcher_MultiList extends SJB_AbstractCategorySearcher 
{
	public function SJB_CategorySearcher_MultiList($field)
	{
		$this->field = $field;
		parent::SJB_AbstractCategorySearcher($field);
	}

	protected function _decorateItems($items)
	{
		$listingFieldListItemManager = SJB_ObjectMother::createListingFieldListItemManager();
		$values = $listingFieldListItemManager->getHashedListItemsByFieldSID($this->field['sid']);
		$values = $this->getSortedValues($values);

		$listData = array();
		foreach ($values as $id => $value) {
			$listData[$value] = isset($items[$id]) ? $items[$id] : 0;
		}

		return $listData;
	}

	protected function _get_Captions_with_Counts_Grouped_by_Captions($request_data, array $listingSids = array())
	{
		if (SJB_Settings::getValue('enableBrowseByCounter')) {
			$res = parent::_get_Captions_with_Counts_Grouped_by_Captions($request_data, $listingSids);
			
			foreach ($res as $key => $value) {
				if (strpos($key, ',')) {
					unset($res[$key]);
					$newKeys = explode(',', $key);
					foreach ($newKeys as $newKey) {
						$res[$newKey] = isset($res[$newKey]) ? $res[$newKey] + $value : $value;
					}
				}
			}
		} else {
			$sql = "select `value` as caption from `listing_field_list` where `field_sid`=?n";
			$res = SJB_DB::query($sql,$this->field['sid']);
		}
		
		return $res;
	}
	
	
	/**
	 * Check 'sort_by_alphabet' flag for field, and sort values if needed
	 * 
	 * @param $values
	 */
	private function getSortedValues($values)
	{
		$fieldInfo = SJB_ListingFieldManager::getFieldInfoBySID($this->field['sid']);
		if (SJB_Array::get($fieldInfo, 'sort_by_alphabet') > 0) {
			$i18n = SJB_I18N::getInstance();
			
			// translate captions to current language
			$translates = array();
			foreach ($values as $value) {
				$translates[] = $i18n->gettext('', $value);
			}
			
			// we need to recover keys for $values after array_multisort
			$keys = array_keys($values);
			
			// sort $keys and $values order by $translates sort
			array_multisort($translates, SORT_STRING, $keys, $values);
			// restore keys for $values
			$values = array_combine($keys, $values);
		}
		return $values;
	}

}
