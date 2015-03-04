<?php

class SJB_CategorySearcher_Tree extends SJB_AbstractCategorySearcher
{
	public $tree_values;
	public $parent;

	public function SJB_CategorySearcher_Tree($field)
	{
		parent::SJB_AbstractCategorySearcher($field);
		$this->tree_values = SJB_ListingFieldTreeManager::getTreeValuesAsArrayWithChildBySID($field['sid']);
		$this->parent = array_keys($this->tree_values);
		$this->field = $field;
	}

	protected static function _getCountsByItems($items)
	{
		$res = array();
		$parentsSIDs = array();
		foreach($items as $item) {
			$itemArray = explode(',', $item['caption']);
			foreach ($itemArray as $val) {
				$res[$val] = isset($res[$val]) ? $res[$val] + 1 : 1;
				$parentSID = SJB_ListingFieldTreeManager::getParentSID($val);
				while ($parentSID != 0) {
					if (!isset($parentsSIDs[$parentSID]) && $parentSID != 0) {
						$parentsSIDs[$parentSID] = 1;
					}
					$parentSID = SJB_ListingFieldTreeManager::getParentSID($parentSID);
				}
			}
			foreach($parentsSIDs as $key => $parentSIDValue) {
				$res[$key] = isset($res[$key]) ? $res[$key] + 1 : 1;
			}
			$parentsSIDs = array();
		}
		return $res;
	}

	public function getPositionSID($itemSID, &$result, $count)
	{
		if (isset($this->tree_values[$itemSID])) {
			if (isset($this->tree_values[$itemSID]['countListings'])) {
				$this->tree_values[$itemSID]['countListings'] += $count;
			} else {
				$this->tree_values[$itemSID]['countListings'] = $count;
			}

			if ($this->tree_values[$itemSID]['parent_sid'] != 0 && in_array($this->tree_values[$itemSID]['parent_sid'], $this->parent)) {
				if (isset($result[$itemSID])) {
					$result[$this->tree_values[$itemSID]['parent_sid']][$itemSID] = $result[$itemSID];
					unset($result[$itemSID]);
				} else {
					$result[$this->tree_values[$itemSID]['parent_sid']][$itemSID] = 1;
				}
			}
		}
	}

	protected function _get_Captions_with_Counts_Grouped_by_Captions($request_data, array $listingSids = array())
	{
		$items  = array();
		if (!empty($request_data[$this->field['field']]['tree'])) {
			$this->parent = $items = explode(',', $request_data[$this->field['field']]['tree']);
			$treeValues   = $this->tree_values;
			$this->tree_values = array();
		}

		if (SJB_Settings::getValue('enableBrowseByCounter')) {
			$request_data['access_type'] = array(
				'accessible' => SJB_UserManager::getCurrentUserSID(),
			);
			$criteria       = SJB_SearchFormBuilder::extractCriteriaFromRequestData($request_data);
			$sqlTranslator  = new SJB_SearchSqlTranslator('listings');
			$whereStatement = $sqlTranslator->_getWhereStatement($criteria);

			$sql = "`{$this->field['field']}` != ''";
			if (count($items) >1) {
				$sql = " AND (";
				foreach ($items as $key => $item) {
					if ($key == 0) {
						$sql .= " FIND_IN_SET('{$item}',`{$this->field['field']}`) ";
					} else {
						$sql .= " OR FIND_IN_SET('{$item}',`{$this->field['field']}`) ";
					}
					if (isset($treeValues[$item])) {
						$this->tree_values[$item] = $treeValues[$item];
					}
				}
				$sql .= ")";
			}
			if (!empty($listingSids)) {
				$sql .= ' AND `listings`.`sid` IN (' . implode(',', $listingSids) . ')';
			}

			$sql    = "SELECT `?w` AS caption, `sid` AS object_sid FROM `listings` {$whereStatement} AND {$sql}";
			$result = SJB_DB::query($sql, $this->field['field']);
			$result = self::_getCountsByItems($result);
		} else {
			if (count($items) >1) {
				foreach ($items as $item) {
					$this->tree_values[$item] = $treeValues[$item];
				}
			}
			$result  = self::getTreeItems($this->tree_values, SJB_ListingFieldTreeManager::getTreeDepthBySID($this->field['sid']));
		}
		return $result;
	}

	public function getTreeItems($treeItems, $level)
	{
		foreach($treeItems as $id => $item) {
			if ($item['level'] == $level) {
				if (isset($treeItems[$item['parent_sid']])) {
					$treeItems[$item['parent_sid']]['nodes'][$id] = $item;
					unset($treeItems[$id]);
				}
			}
		}
		if ($level > 2) {
			$treeItems = self::getTreeItems($treeItems, $level-1);
		}
		return $treeItems;
	}

	protected function _decorateItems($items)
	{
		if (SJB_Settings::getValue('enableBrowseByCounter')) {
			$this->fieldSid = $this->getFieldSID($this->field['field']);

			$counts = $this->_getCountsByItemsDecorate($items);
			$result = array();
			foreach ($counts as $itemSID => $count) {
				self::getPositionSID($itemSID, $result, $count);
			}

			$result = $this->getSortedResults($result);
			return self::_decorate($result);
		}
		$items = $this->getSortedValues($items);
		return self::_decorate($items);
	}

	protected function _getCountsByItemsDecorate($items)
	{
		$res = array();
		foreach($items as $key => $value) {
			$res[$key] = $value;
		}
		return $res;
	}

	protected function _decorate($items)
	{
		$html = '';
		if ($items) {
			$i18n   = SJB_I18N::getInstance();

			if ($this->field['homepage'] == 0) {
				$html .= '<div style="width:50%; float: left;" >';
			}
			$countItemsAtColumn = ceil(count($items)/2);
			$countItems = 0;
			foreach ($items as $id => $value) {
				if ($this->field['homepage'] == 0 && $countItems == $countItemsAtColumn) {
					$html .= '</div><div style="width:50%; float: left;">';
				}
				$html .= "\n<ul class='browse_tree' id='browse_tree_ul_{$id}'><li id='browse_tree_li_{$id}'><div>";

				$showCounter = false;
				if (SJB_Settings::getValue('enableBrowseByCounter')){
					$caption     = $this->tree_values[$id]['caption'];
					$count       = "({$this->tree_values[$id]['countListings']})";
					$showCounter = true;
				}
				else {
					$caption = $value['caption'];
				}
				$translatedCaption = $i18n->gettext('', $caption);

				if ($showCounter) {
					// with counter we have array of keys and '1' values
					$class = !empty($value) ? 'arrow collapsed' : 'arrow';
					$html .= "<div class='{$class}' onclick='openLevel(\"{$id}\")' id='browse_tree_arrow_{$id}' ></div><label><a href='".SJB_System::getSystemSettings('SITE_URL')."/browse-by-".strtolower($this->field['field'])."/{$id}/".str_replace(" ", "-", htmlentities($caption, ENT_QUOTES, "UTF-8"))."'>". htmlentities($translatedCaption, ENT_QUOTES, "UTF-8").$count."</a></label></div>";
				} else {
					// without counter we have multidimensional array with 'children' value.
					$class = $value['children'] > 0 ? 'arrow collapsed' : 'arrow';
					$html .= "<div class='{$class}' onclick='openLevel(\"{$id}\")' id='browse_tree_arrow_{$id}' ></div><label><a href='".SJB_System::getSystemSettings('SITE_URL')."/browse-by-".strtolower($this->field['field'])."/{$id}/".str_replace(" ", "-", htmlentities($caption, ENT_QUOTES, "UTF-8"))."'>". htmlentities($translatedCaption, ENT_QUOTES, "UTF-8") . "</a></label></div>";
				}
				self::_decorateResults($value, $html);
				$html .= "\n</li>";
				$html .= "\n</ul>";
				$countItems++;
			}
			if ($this->field['homepage'] == 0)
				$html .= '</div>';
		}
		return $html;
	}

	protected function _decorateResults($itemArr, &$html)
	{
		$i18n   = SJB_I18N::getInstance();

		if (SJB_Settings::getValue('enableBrowseByCounter')) {
			if (is_array($itemArr)) {
				foreach ($itemArr as $item => $val ) {
					$caption    = $i18n->gettext('', $this->tree_values[$item]['caption']);
					$countItems = "({$this->tree_values[$item]['countListings']})";
					if ($this->tree_values[$item]['children']) {
						$html .= "\n<ul style='display: none;' class='browse_tree' id='browse_tree_ul_{$item}'><li id='browse_tree_li_{$item}'><div>";
						$html .= "<div class='arrow collapsed' onclick='openLevel(\"{$item}\")' id='browse_tree_arrow_{$item}'></div><label><a href='".SJB_System::getSystemSettings('SITE_URL')."/browse-by-".strtolower($this->field['field'])."/{$item}/".str_replace(" ", "-", htmlentities($this->tree_values[$item]['caption'], ENT_QUOTES, "UTF-8"))."'>". htmlentities($caption, ENT_QUOTES, "UTF-8") . $countItems."</a></label></div>";
						self::_decorateResults($val, $html);
						$html .= "\n</li>";
						$html .= "\n</ul>";
					}
					else {
						$html .= "\n<ul style='display: none;' class='browse_tree' id='browse_tree_ul_{$item}'><li id='browse_tree_li_{$item}'><div><div class='arrow'></div><label><a href='".SJB_System::getSystemSettings('SITE_URL')."/browse-by-".strtolower($this->field['field'])."/{$item}/".str_replace(" ", "-", htmlentities($this->tree_values[$item]['caption'], ENT_QUOTES, "UTF-8"))."'>". htmlentities($caption, ENT_QUOTES, "UTF-8") . $countItems."</a></label></div>";
						self::_decorateResults($val, $html);
						$html .= "\n</li>";
						$html .= "\n</ul>";
					}
				}
			}
		} else {
			if (isset($itemArr['nodes'])) {
				foreach ($itemArr['nodes'] as $item => $val ) {
					$caption = $i18n->gettext('', $val['caption']);
					if ($val['children']) {
						$html .= "\n<ul style='display: none;' class='browse_tree' id='browse_tree_ul_{$item}'><li id='browse_tree_li_{$item}'><div>";
						$html .= "<div class='arrow collapsed' onclick='openLevel(\"{$item}\")' id='browse_tree_arrow_{$item}'></div><label><a href='".SJB_System::getSystemSettings('SITE_URL')."/browse-by-".strtolower($this->field['field'])."/{$item}/".str_replace(" ", "-", htmlentities($val['caption'], ENT_COMPAT, "UTF-8"))."'>". htmlentities($caption, ENT_QUOTES, "UTF-8") . "</a></label></div>";
						self::_decorateResults($val, $html);
						$html .= "\n</li>";
						$html .= "\n</ul>";
					} else {
						$html .= "\n<ul style='display: none;' class='browse_tree' id='browse_tree_ul_{$item}'><li id='browse_tree_li_{$item}'><div><div class='arrow'></div><label><a href='".SJB_System::getSystemSettings('SITE_URL')."/browse-by-".strtolower($this->field['field'])."/{$item}/".str_replace(" ", "-", htmlentities($val['caption'], ENT_COMPAT, "UTF-8"))."'>". htmlentities($caption, ENT_QUOTES, "UTF-8")."</a></label></div>";
						$html .= "\n</li>";
						$html .= "\n</ul>";
					}
				}
			}
		}
	}

	private function getSortedValues($values)
	{
		if (!empty($values)) {
			$fieldInfo = SJB_ListingFieldManager::getFieldInfoBySID($this->field['sid']);
			if (SJB_Array::get($fieldInfo, 'sort_by_alphabet') > 0) {
				$i18n   = SJB_I18N::getInstance();

				$parents = array();
				// in this place we have 'caption' with ids of children items, like '300,301,302,305' etc.
				// need to get real caption value (for children and parent) and sort by it
				foreach ($values as $key => $item) {
					$parents[] = $i18n->gettext('', $item['caption']);
					if ($item['children'] > 0) {
						// sort children by alphabet
						$sids     = array_keys($item['nodes']);
						$children = array();
						foreach ($item['nodes'] as $child) {
							$children[] = $i18n->gettext('', $child['caption']);
						}
						array_multisort($children, SORT_STRING, $sids, $item['nodes']);
						$values[$key]['nodes'] = array_combine($sids, $item['nodes']);
					}
				}
				// sort parent by alphabet
				$keys = array_keys($values);
				array_multisort($parents, SORT_STRING, $keys, $values);
				$values = array_combine($keys, $values);
			}
		}
		return $values;
	}

	private function getSortedResults($values)
	{
		if (!empty($values)) {
			$fieldInfo = SJB_ListingFieldManager::getFieldInfoBySID($this->field['sid']);
			if (SJB_Array::get($fieldInfo, 'sort_by_alphabet') > 0) {
				$i18n   = SJB_I18N::getInstance();

				$parents = array();
				// in this place we have 'caption' with ids of children items, like '300,301,302,305' etc.
				// need to get real caption value (for children and parent) and sort by it
				foreach ($values as $parent => $items) {
					if (!is_array($items)) {
						$items = array();
					}
					$sids       = array_keys($items);
					$parentSid  = SJB_ListingFieldTreeManager::getParentSID($parent);
					$parentInfo = SJB_ListingFieldTreeManager::getTreeItemInfoBySID($parentSid);
					$parents[]  = $i18n->gettext('', $parentInfo['caption']);

					// sort children by alphabet
					$children = array();
					foreach ($sids as $sid) {
						$info       = SJB_ListingFieldTreeManager::getTreeItemInfoBySID($sid);
						$children[] = $i18n->gettext('', $info['caption']);
					}
					array_multisort($children, SORT_STRING, $sids);
					$sids = array_fill_keys($sids, 1);

					// set to sorted sids
					$values[$parent] = $sids;
				}
				// sort parent by alphabet
				$keys = array_keys($values);
				array_multisort($parents, SORT_ASC, SORT_STRING, $keys, $values);
				$values = array_combine($keys, $values);
			}
		}
		return $values;
	}
}
