<?php

class SJB_Pagination
{
	public $sortingField;
	public $sortingOrder;
	public $itemsPerPage;
	public $currentPage = 1;
	protected $restore = 1;
	protected $totalPages = 0;
	protected $itemsCount = 0;

	protected $item;
	protected $numberOfElementsPageSelect = array(1 => 10, 2 => 20, 3 => 50, 4 => 100);
	protected $actionsForSelect =  array();
	protected $translatedText = array();
	protected $fields = array();
	protected $uniqueUrlParams = array();
	protected $countActionsButtons = 0;
	protected $popUp = false;
	protected $isCheckboxes = true;

	public function __construct($defaultSortingField, $defaultSortingOrder, $defaultItemsPerPage)
	{
		$this->sortingField = $this->getPaginationParameter('sortingField', $defaultSortingField);
		$this->sortingOrder = $this->getPaginationParameter('sortingOrder', $defaultSortingOrder);
		$this->itemsPerPage = $this->getPaginationParameter('itemsPerPage', $defaultItemsPerPage);
		$this->currentPage = $this->getPaginationParameter('page', 1);

		$i18N = SJB_I18N::getInstance();
		$this->translatedText['chooseAction'] = $i18N->gettext('Backend', 'Please, choose an action');
		$this->translatedText['chooseItem'] = $i18N->gettext('Backend', 'Please, select ' . $this->item . ' first');
		$this->translatedText['delete'] = $i18N->gettext('Backend', 'Are you sure you want to delete selected ' . $this->item . '?');
	}

	protected function setSortingFieldsToPaginationInfo($fields)
	{
		$fieldsToPaginationInfo = array();
		foreach ($fields as $key => $fieldInfo) {
			$fieldsToPaginationInfo[$key]['name'] = $fieldInfo['name'];
			if (isset($fieldInfo['isVisible']) && $fieldInfo['isVisible'] == false) {
				$fieldsToPaginationInfo[$key]['isVisible'] = false;
			} else {
				$fieldsToPaginationInfo[$key]['isVisible'] = true;
			}
			if (isset($fieldInfo['isSort']) && $fieldInfo['isSort'] == false) {
				$fieldsToPaginationInfo[$key]['isSort'] = false;
			} else {
				$fieldsToPaginationInfo[$key]['isSort'] = true;
			}
		}
		$this->fields = $fieldsToPaginationInfo;
	}

	protected function setActionsForSelect($actionsForSelect)
	{
		$actionsToPaginationInfo = array();
		foreach ($actionsForSelect as $key => $actionInfo) {
			$actionsToPaginationInfo[$key]['name'] = $actionInfo['name'];
			if (isset($actionInfo['isVisible']) && $actionInfo['isVisible'] == false) {
				$actionsToPaginationInfo[$key]['isVisible'] = false;
			} else {
				$actionsToPaginationInfo[$key]['isVisible'] = true;
			}
		}
		$this->actionsForSelect = $actionsToPaginationInfo;
	}

	public function setItemsCount($itemsCount)
	{
		$this->itemsCount = $itemsCount;
	}

	protected function getTotalPages()
	{
		if ($this->itemsPerPage == 'all') {
			$this->totalPages = 1;
		} else {
			$this->totalPages = ceil($this->itemsCount / $this->itemsPerPage);
			if (empty($this->totalPages)) {
				$this->totalPages = 1;
			}
		}
		return $this->totalPages;
	}

	protected function getPages()
	{
		$pages = array();
		for ($i = $this->currentPage - 2; $i < $this->currentPage + 3; $i++) {
			if ($i == $this->totalPages) {
				break;
			} else {
				if ($i > 0) {
					$pages[] = $i;
				}
				if ($i * $this->itemsPerPage > $this->itemsCount) {
					break;
				}
			}
		}

		if (array_search(1, $pages) === false) {
			array_unshift($pages, 1);
		}

		if (array_search($this->totalPages, $pages) === false) {
			array_push($pages, $this->totalPages);
		}
		return $pages;
	}

	protected function getPaginationParameter($paramName, $defaultParam)
	{
		$newSortingParam = SJB_Request::getVar($paramName, null);
		if (empty($newSortingParam)) {
			$sortingInSession = SJB_Session::getValue($paramName . $this->item);
			if (empty($sortingInSession)) {
				SJB_Session::setValue($paramName . $this->item, $defaultParam);
				return $defaultParam;
			}
			return $sortingInSession;
		} else {
			$param = $this->getPossibleParam($paramName, $newSortingParam, $defaultParam);
			SJB_Session::setValue($paramName . $this->item, $param);
			return $param;
		}
	}

	private function getPossibleParam($paramName, $newParam, $defaultParam)
	{
		switch($paramName) {
			case 'sortingField':
				if (array_key_exists($newParam, $this->fields)) {
					return $newParam;
				} else {
					return $defaultParam;
				}
				break;
			case 'sortingOrder':
				if ($newParam == 'DESC' || $newParam == 'ASC') {
					return $newParam;
				} else {
					return $defaultParam;
				}
				break;
			case 'itemsPerPage':
			case 'page':
				if (is_numeric($newParam) && $newParam >= 1) {
					return floor($newParam);
				} elseif (in_array($newParam, $this->numberOfElementsPageSelect)) {
					return $newParam;
				} else {
					return $defaultParam;
				}
				break;
			default:
				return $newParam;
		}
	}

	public function getPaginationInfo()
	{
		return array(
			'item'                          => $this->item,
			'numberOfElementsPageSelect'    => $this->numberOfElementsPageSelect,
			'actionsForSelect'              => $this->actionsForSelect,
			'popUp'                         => $this->popUp,
			'isCheckboxes'                  => $this->isCheckboxes,
			'uniqueUrlParams'               => $this->uniqueUrlParams,
			'countActionsButtons'           => $this->countActionsButtons,
			'currentPage'                   => $this->currentPage,
			'itemsPerPage'                  => $this->itemsPerPage,
			'itemsCount'                    => $this->itemsCount,
			'restore'                       => $this->restore,
			'fields'                        => $this->fields,
			'sortingField'                  => $this->sortingField,
			'sortingOrder'                  => $this->sortingOrder,
			'totalPages'                    => $this->getTotalPages(),
			'pages'                         => $this->getPages(),
			'translatedText'                => $this->translatedText,
		);
	}
}