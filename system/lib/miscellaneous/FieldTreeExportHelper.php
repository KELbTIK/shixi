<?php

class SJB_FieldTreeExportHelper
{
	private $treeFieldValues = array();
	private $valuesForXml = array();
	private $fieldID;

	/**
	 * @param string $fieldID
	 * @param string $listingTreeFieldValue
	 */
	public function __construct($fieldID, $listingTreeFieldValue)
	{
		$this->fieldID = $fieldID;
		$this->treeFieldValues = explode(',', $listingTreeFieldValue);
	}

	public function getDataToExport()
	{
		$this->prepareAssocArrayForExport();
		return $this->generateXMLString();
	}

	protected function prepareAssocArrayForExport()
	{
		foreach ($this->treeFieldValues as $treeItemSID) {
			$treeItemHierarchyDisplayValues = SJB_ListingFieldTreeManager::getTreeDisplayValuesBySIDForExport($treeItemSID);
			$this->fillAssocArray($treeItemHierarchyDisplayValues, $this->valuesForXml);
		}
	}

	protected function generateXMLString()
	{
		$simpleXML = new SimpleXMLElement('<?xml version="1.0"?><tree></tree>');
		$simpleXML->addAttribute('fieldID', $this->fieldID);
		$this->arrayToXML($this->valuesForXml, $simpleXML);
		return $simpleXML->asXML();
	}

	protected function fillAssocArray($displayValue, &$currentNodeAssocArray)
	{
		$currentItem = array_shift($displayValue);
		$currentItemSID = $currentItem['sid'];
		if (!isset($currentNodeAssocArray[$currentItemSID])) {
			$currentNodeAssocArray[$currentItemSID] = array(
				'caption' => $currentItem['caption'],
				'items' => array(),
			);
		}
		if (!empty($displayValue)) {
			self::fillAssocArray($displayValue, $currentNodeAssocArray[$currentItemSID]['items']);
		}
	}

	protected function arrayToXML($fieldInfo, SimpleXMLElement $xmlFieldInfo)
	{
		foreach($fieldInfo as $treeItemSID => $treeItemInfo) {
			$branch = $xmlFieldInfo->addChild('branch');
			$branch->addChild('sid', $treeItemSID);
			$branch->addChild('caption', XML_Util::replaceEntities($treeItemInfo['caption']));
			if (!empty($treeItemInfo['items'])) {
				$branchItems = $branch->addChild('items');
				$this->arrayToXML($treeItemInfo['items'], $branchItems);
			}
		}
	}
}
