<?php

class SJB_ManagePhrasesPagination extends SJB_Pagination
{
	public function __construct()
	{
		$this->item = 'phrases';
		$this->numberOfElementsPageSelect = array(1 => 50, 2 => 100, 3 => 200, 4 => 'all');

		parent::__construct(null, null, 50);
	}

	public function setUniqueUrlParams($request)
	{
		$this->uniqueUrlParams['action'] = array('value' => 'search_phrases');;
		if (isset($request['language'])) {
			$this->uniqueUrlParams['language'] = array('value' => $request['language'], 'escape' => 'url');
		}
		if (isset($request['phrase_id'])) {
			$this->uniqueUrlParams['phrase_id'] = array('value' => $request['phrase_id'], 'escape' => 'url');
		}
		if (isset($request['domain'])) {
			$this->uniqueUrlParams['domain'] = array('value' => $request['domain'], 'escape' => 'url');
		}
	}
}

