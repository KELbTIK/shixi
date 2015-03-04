<?php

class SJB_InvoiceCriteriaSaver extends SJB_CriteriaSaver
{
	function SJB_InvoiceCriteriaSaver()
	{
		parent::SJB_CriteriaSaver('InvoiceSearcher', new SJB_InvoiceManager);
	}
}
