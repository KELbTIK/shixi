<?php

class SJB_Payment_PaymentCompleted extends SJB_Function
{
	public function execute()
	{
        $tp = SJB_System::getTemplateProcessor();
        $tp->display('payment_completed.tpl');
    }
}