<?php

class SJB_ScreeningQuestionnairesDisplayListController extends SJB_DisplayListController
{
	function SJB_ScreeningQuestionnairesDisplayListController($input_data)
	{
		parent::SJB_DisplayListController($input_data, new SJB_ScreeningQuestionnairesFieldManager, new SJB_ScreeningQuestionnairesListItemManager);
	}
}
