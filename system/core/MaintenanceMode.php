<?php

class SJB_MaintenanceMode
{

	private $remoteIP;
	private $allowedIP = null;
	private $allowed = true;

	public function checkByPattern()
	{
		$aIPParts = explode('.', $this->allowedIP);

		$aNewParts = array();

		foreach ($aIPParts as $part)
		{
			if (strstr($part, '*'))
			{
				$asteriskPosition = strpos($part, '*');
				if ($asteriskPosition === 0)
				{
					$count = 3;
				}
				else
				{
					$count = 3 - $asteriskPosition;
				}
				array_push($aNewParts, str_replace('*', '[\\d]{1,' . $count . '}', $part));
			}
			else
			{
				array_push($aNewParts, $part);
			}
		}

		$pattern = '/^' . implode('\.', $aNewParts) . '$/';
		
		if (preg_match($pattern, $this->remoteIP))
		{
			return true;
		}
		return false;
	}

	public function checkIfSiteIsAvailable()
	{
		if (empty($this->allowedIP)) {
			// turn off site for all visitors
			$this->allowed = false;
		} else {
			if (strstr($this->allowedIP, '*')) {
				$this->allowed = $this->checkByPattern();
			} else if (strcmp($this->allowedIP, $this->remoteIP) !== 0) {
				$this->allowed = false;
			} else {
				$this->allowed = true;
			}
        }
	}

	function __construct($remoteIP)
	{
		if (SJB_Settings::getValue('maintenance_mode'))
		{
			$this->remoteIP = $remoteIP;
            $this->setAllowed();
		}
	}

	private function setAllowed()
    {
        $ips = explode(',', SJB_Settings::getValue('maintenance_mode_ip'));

		foreach ($ips as $ip) {
			$ip = trim($ip);
			$this->allowedIP = $ip;
			$this->checkIfSiteIsAvailable();
			if ($this->allowed) {
				break;
			}
		}
    }

    public function getAllowed()
	{
		return $this->allowed;
	}

}
