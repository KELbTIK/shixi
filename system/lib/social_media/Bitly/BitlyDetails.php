<?php

class SJB_BitlyDetails extends SJB_ObjectDetails
{
	function SJB_BitlyDetails($bitlyInfo)
	{
		$detailsInfo = SJB_BitlyDetails::getDetails();
		foreach ($detailsInfo as $detailInfo) {
			if (isset($bitlyInfo[$detailInfo['id']])) {
				$detailInfo['value'] = $bitlyInfo[$detailInfo['id']];
			} else {
				$detailInfo['value'] = '';
			}
			$this->properties[$detailInfo['id']] = new SJB_ObjectProperty($detailInfo);
		}
	}

	public static function getDetails()
	{
		return array(
			array(
				'id'            => 'bitlyTokenId',
				'caption'       => 'Bitly Token ID',
				'type'          => 'string',
				'length'        => '40',
				'table_name'    => 'settings',
				'is_required'   => true,
				'is_system'     => false,
			),
		);
	}
}
