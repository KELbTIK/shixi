<?php
/**
 * Description of TreeHelper
 *
 * @author still
 */
class SJB_TreeHelper
{
	/**
	 * describes what type of TreeHelper is used
	 * 'user' | 'listing'
	 * @var string
	 */
	private $_type;

	/**
	 *
	 * @var object SJB_UserProfileFieldTreeManager | SJB_ListingFieldTreeManager
	 */
	private $_treeManager;

	/**
	 * field SID
	 * old "id"
	 * @var int
	 */
	private $_fieldSID;
	private $_checked = array();
	private $_search;

	/**
	 * old "name"
	 * @var string
	 */
	private $_fieldName;

	/*
	 * Tree Properties
	 */
	private $_sortByAlphabet = false;
	private $_displayAsSelectBoxes = false;


	private $_treeValues = array();
	private $_translatedCaptions = array();

	/**
	 *
	 * @param string $type  'user' | 'listing'
	 */
	function __construct($type)
	{
		$this->_type = $type;
		switch ($this->_type) {
			case 'user':
				$this->_treeManager = new SJB_UserProfileFieldTreeManager();
				break;

			case 'listing':
				$this->_treeManager = new SJB_ListingFieldTreeManager();
				break;

			default:
				break;
		}
	}

	public function init()
	{
		$this->_fieldSID = SJB_Request::getInt('id');
		$this->_fieldName = SJB_Request::getString('name');
		$this->_search = SJB_Request::getString('search');
		$this->_displayAsSelectBoxes = $this->defineTreeProperty('display_as_select_boxes', $this->_fieldSID);
		$this->_checked = $this->prepareCheckedItems();
		if ($this->_displayAsSelectBoxes && !empty($this->_checked[0]) && count($this->_checked) === 1) {
			$this->_checked = $this->getCheckedItems($this->_checked[0]);
		}
		$this->_sortByAlphabet = $this->defineTreeProperty('sort_by_alphabet', $this->_fieldSID);
	}
	/**
	 * @return array
	 */
	private function prepareCheckedItems()
	{
		$check = SJB_Request::getVar('check', array(), 'GET');
		if ($check) {
			$check = explode(',', $check);
			if (!$this->_displayAsSelectBoxes)
				return $check;
			foreach($check as $checked)
				if (!empty($checked))
					return array($checked);
		}
		return array();
	}

	/**
	 * @param int $checkedItem
	 * @param array $checked
	 * @return array
	 */
	public function getCheckedItems($checkedItem, &$checked = array())
	{
		array_unshift($checked, $checkedItem);
		$parentChecked = $this->_treeManager->getParentSID($checkedItem);
		if ($parentChecked) {
			$this->getCheckedItems($parentChecked, $checked);
		}
		return $checked;
	}

	public function displayAsSelectBoxes()
	{
		$parentSID = SJB_Request::getInt('parentSID');
		$this->_treeValues = $this->_treeManager->getTreeItemsByParentSIDAndFieldSID($this->_fieldSID, $parentSID);
		if (!empty($this->_treeValues)) {
			$level = $this->_treeValues[$parentSID][0]['level'];
			$aLevel = $this->_treeManager->getTreePropertyByTreeSID($this->_fieldSID);
			$levelCaption = SJB_Array::get($aLevel, 'level_' . $level);
		}
		else {
			return false;
		}

		// TRANSLATE & Order BY Alphabet
		$this->translateAndMakeOrderTreeValues();
		$this->prepareValuesForDisplayAsTreeSearchForm();
		$tp = SJB_System::getTemplateProcessor();
		$tp->assign('tree_values', $this->_treeValues);
		$tp->assign('levelName', $levelCaption);
		$tp->assign('level', $level);
		$tp->assign('has_child', true);
		$tp->assign('fieldSID', $this->_fieldSID);
		$tp->assign('name', $this->_fieldName);
		$tp->assign('checked', $this->_checked);
		$tp->display('tree_display_as_select.tpl');
	}

	public function prepareValuesForDisplayAsTreeSearchForm()
	{
		$this->_treeValues = array_shift($this->_treeValues);
	}


	public function displayAsTree()
	{
		$this->_treeValues = $this->_treeManager->getTreeValuesBySID($this->_fieldSID);
		$this->translateAndMakeOrderTreeValues();
		
		$tp = SJB_System::getTemplateProcessor();
		$tp->assign('treeValues', $this->implodeTree($this->_treeValues));
		$tp->assign('checked', SJB_Request::getVar('check', ''));
		$tp->assign('choiceLimit', SJB_Request::getVar('choiceLimit', 'undefined'));
		$tp->assign('fieldId', SJB_Request::getVar('name', ''));
		$tp->assign('fieldSid', SJB_Request::getVar('id', ''));
		$tp->display('tree_display_as_tree.tpl');
	}

	/**
	 * @param  array $tree
	 * @return string
	 */
	private function implodeTree($tree)
	{
		$treeHtml = '';
		$i18n     = SJB_I18N::getInstance();
		$domain   = SJB_I18N::getInstance()->getDefaultDomain();
		
		$count = 0;
		$treeCount = count($tree); 
		foreach ($tree as $branchSid => $branchValues) {
			if (is_array($branchValues)) {
				$countValues = 0;
				$branchValuesCount = count($branchValues);
				$treeHtml .= "{$branchSid}:[";
				foreach ($branchValues as $value) {
					$caption = $i18n->gettext($domain, $value['caption']);
					$caption = $this->escapeJavaScript($caption);
					$treeHtml .= "new Object({ 'sid':{$value['sid']},'caption':'{$caption}' }";
					
					$countValues++;
					$treeHtml .= $countValues == $branchValuesCount ? ')' : '),';
				}
			}
			
			$count++;
			$treeHtml .= $count == $treeCount ? ']' : '],';
		}
		
		return $treeHtml;
	}

	/**
	 * @param  string $selectedSids
	 * @return array|string
	 */
	public function createTreeObjects($selectedSids)
	{
		if (empty($selectedSids)) {
			return array();
		}
		
		$selectedSids .= $this->getParentsSid($selectedSids);
		
		$table      = in_array('users', SJB_PageManager::getPageModule()) || SJB_Navigator::getURI() == '/registration-social/' ? 'user_profile_field_tree' : 'listing_field_tree';
		$treeValues = SJB_DB::query("SELECT *, sid AS id FROM `{$table}` WHERE sid IN (?w) ORDER BY `order`", $selectedSids);
		
		$result = array();
		foreach ($treeValues as $treeValue) {
			$treeValue['value'] = in_array($treeValue['sid'], $treeValues) ? $treeValue['sid'] : '';
			$result[$treeValue['parent_sid']][] = $treeValue;
		}
		
		return $this->implodeTreeToObjects($result, 0);
	}

	/**
	 * @param  string $selectedSids
	 * @return string
	 */
	private function getParentsSid($selectedSids)
	{
		$table  = in_array('users', SJB_PageManager::getPageModule()) || SJB_Navigator::getURI() == '/registration-social/' ? 'user_profile_field_tree' : 'listing_field_tree';
		$result = SJB_DB::query("SELECT `parent_sid`, `level` FROM `{$table}` WHERE `sid` IN (?w)", $selectedSids);
		
		$parentSids = '';
		$allSids    = '';
		foreach ($result as $value) {
			$allSids .= ',' . $value['parent_sid'];
			if ($value['level'] > 2) {
				$parentSids .= empty($parentSids) ? $value['parent_sid'] : ',' . $value['parent_sid'];
			}
		}
		
		if (!empty($parentSids)) {
			$allSids .= $this->getParentsSid($parentSids);
		}
		
		return $allSids;
	}

	/**
	 * @param  array $tree
	 * @param  int   $id
	 * @return string
	 */
	private function implodeTreeToObjects($tree, $id)
	{
		$treeHtml = '';
		$i18n     = SJB_I18N::getInstance();
		$domain   = SJB_I18N::getInstance()->getDefaultDomain();
		
		$count         = 0;
		$elementsCount = count($tree[$id]);
		if (!is_array($tree[$id])) {
			return $treeHtml;
		}
		foreach ($tree[$id] as $element) {
			$parent  = isset($tree[$element['sid']]);
			$caption = $i18n->gettext($domain, $element['caption']);
			$caption = $this->escapeJavaScript($caption);
			$level   = (int) $element['level'] - 1;
			$value   = $parent ? '' : $element['sid'];
			$treeHtml .= "new Object({ 'sid':{$element['sid']},'caption':'{$caption}','level':'{$level}','value':'{$value}' })";
			
			if ($parent) {
				$treeHtml .= ',' . $this->implodeTreeToObjects($tree, $element['sid']);
			}
			
			$count++;
			if ($count != $elementsCount) {
				$treeHtml .= ',';
			}
		}
		
		return $treeHtml;
	}

	/**
	 * @param  string $value
	 * @return string
	 */
	private function escapeJavaScript($value)
	{
		return strtr($value, array('\\' => '\\\\', "'" => "\\'", '"' => '\\"', "\r" => '\\r', "\n" => '\\n', '</' => '<\/'));
	}

	/**
	 *
	 * @param string $sProperty
	 * @param int $fieldSID
	 * @return boolean
	 */
	public function defineTreeProperty($sProperty, $fieldSID)
	{
		$aProperty = $this->_treeManager->getTreePropertyByTreeSID($fieldSID);
		return SJB_Array::get($aProperty, $sProperty);
	}

	/**
	 *
	 * @param int $fieldSID
	 * @return array
	 */
	public function getTreeValuesBySID($fieldSID)
	{
		return $this->_treeManager->getTreeValuesBySID($this->_fieldSID);
	}

	public function translateAndMakeOrderTreeValues()
	{
		$i18n = SJB_I18N::getInstance();
		foreach ($this->_treeValues as $key => $val) {
			foreach ($val as $s_key => $s_val) {
				$trans = $i18n->gettext('', $s_val['caption'], 'default');
				$this->_treeValues[$key][$s_key]['caption'] = $trans;
				$this->_translatedCaptions[$key][] = $trans;
			}
		}

		if ($this->_sortByAlphabet)
			$this->sortTreeItemsByAlphabetOrder();
	}

	public function sortTreeItemsByAlphabetOrder()
	{
		foreach ($this->_treeValues as $key => $val) {
			$captions_lower = array_map('strtolower', $this->_translatedCaptions[$key]);
			array_multisort($captions_lower, SORT_ASC, SORT_STRING, $this->_treeValues[$key]);
		}
	}

	public function get_sortByAlphabet()
	{
		return $this->_sortByAlphabet;
	}

	public function get_displayAsSelectBoxes()
	{
		return $this->_displayAsSelectBoxes;
	}

	public function get_treeValues()
	{
		return $this->_treeValues;
	}
}
