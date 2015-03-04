<?php

class SJB_GuestAlert extends SJB_Object
{
	const STATUS_ACTIVE = 'active';
	const STATUS_INACTIVE = 'inactive';
	const STATUS_UNCONFIRMED = 'unconfirmed';
	const STATUS_UNSUBSCRIBED = 'unsubscribed';

	function __construct($detailsInfo = array())
	{
		$this->db_table_name = 'guest_alerts';
		$this->details = new SJB_GuestAlertDetails($detailsInfo);
	}

	public function save()
	{
		$this->addConfirmationKeyProperty();
		SJB_ObjectDBManager::saveObject($this->db_table_name, $this);
		SJB_DB::query('UPDATE `guest_alerts` SET `subscription_date` = NOW() WHERE `sid` = ?n', $this->getSID());
	}

	public function update()
	{
		return SJB_ObjectDBManager::saveObject($this->db_table_name, $this);
	}

	public function addDataProperty($requested_data)
	{
		$this->details->addDataProperty($requested_data);
	}

	public function addConfirmationKeyProperty($value = '')
	{
		if (empty($value))
			$value = $this->createUniqueKey();
		$this->details->addConfirmationKeyProperty($value);
	}

	public function addSubscriptionDateProperty($value = '')
	{
		$this->details->addSubscriptionDateProperty($value);
	}

	protected function createUniqueKey()
	{
		$symbols = array_merge(range('a','z'), range('0','9'));
		shuffle($symbols);
		return join('', $symbols);
	}

	public function getVerificationKeyForEmail()
	{
		$data = array (
			'sid' => $this->getSID(),
			'key' => $this->getVerificationKey(),
		);

		return base64_encode(serialize($data));
	}

	private function getVerificationKey()
	{
		$SID = $this->getSID();
		$alertEmail = $this->getAlertEmail();
		$propertyValue = $this->getPropertyValue('confirmation_key');

		return md5($SID . $alertEmail . $propertyValue);
	}

	/**
	 * @return string|null
	 */
	public function getAlertEmail()
	{
		$emailValue = $this->getPropertyValue('email');
		if (is_array($emailValue))
			$emailValue = array_pop($emailValue);
		return $emailValue;
	}

	/**
	 * @param string $key
	 * @return array
	 */
	public static function getDataFromKey($key)
	{
		$data = @unserialize(base64_decode($key));
		if (!isset($data['sid'], $data['key'])) {
			$data = array('sid' => 0, 'key' => '');
		}
		return $data;
	}

	/**
	 * @param $keyReceived
	 * @return bool
	 * @throws Exception
	 */
	public function validateReceivedKey($keyReceived)
	{
		$validKey = $this->getVerificationKey();
		if (strcmp($keyReceived, $validKey) !== 0)
			throw new Exception('INVALID_CONFIRMATION_KEY');
		return true;
	}

	public function addStatusProperty($value='')
	{
		$this->details->addStatusProperty($value);
	}

	public function addListingTypeIDProperty($value='')
	{
		$this->details->addListingTypeIDProperty($value);
	}

	public function setStatusActive()
	{
		$this->setStatus(self::STATUS_ACTIVE);
	}

	public function setStatusUnSubscribed()
	{
		$this->setStatus(self::STATUS_UNSUBSCRIBED);
	}

	public function setStatusInactive()
	{
		$this->setStatus(self::STATUS_INACTIVE);
	}

	protected function setStatus($status)
	{
		$this->setPropertyValue('status', $status);
	}

	public function setStatusActiveFromUnconfirmed()
	{
		if ($this->getPropertyValue('status') !== SJB_GuestAlert::STATUS_UNCONFIRMED) {
			throw new Exception('Cant mark current Guest Alert as confirmed');
		}
		$this->setStatus(SJB_GuestAlert::STATUS_ACTIVE);
	}
}
