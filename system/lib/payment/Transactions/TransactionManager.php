<?php

class SJB_TransactionManager extends SJB_ObjectManager
{
	public static function saveTransaction($transaction)
	{
		parent::saveObject('transactions', $transaction);
		$date = $transaction->getPropertyValue('date');
		if (empty($date)){
			SJB_DB::query('UPDATE `transactions` SET `date`= NOW() WHERE `sid`=?n', $transaction->getSID());
		}
	}

	public static function getTransactionInfoBySID($trans_sid)
	{
		$trans_info = parent::getObjectInfoBySID('transactions', $trans_sid);
		return $trans_info;
    }

	public static function getObjectBySID($trans_sid)
	{
    	$trans_info = SJB_TransactionManager::getTransactionInfoBySID($trans_sid);
    	if (is_null($trans_info)){
    		return null;
	    }
    	$transaction = new SJB_Transaction($trans_info);
		$transaction->setSID($trans_sid);
		return $transaction;
    }

	public static function getTransactionsByInvoice($invoice_sid)
	{
		return SJB_DB::query("SELECT * FROM  `transactions` WHERE `invoice_sid` = ?n", $invoice_sid);
	}

	public static function getTransactionsSIDs($limit = 'all', $numRows = false, $sortingField = 'date', $sortingOrder = 'ASC')
	{
		return SJB_DB::query("SELECT `tr`.`sid` FROM  `transactions` as `tr` ORDER BY `?w` ?w  LIMIT ?w, ?w", $sortingField, $sortingOrder, $limit, $numRows);
	}

	public static function deleteTransactionBySID($transaction_sid)
	{
		return SJB_InvoiceManager::deleteObject('transactions', $transaction_sid);
	}
}


