<?php

class PhpBBBridgePlugin extends SJB_PluginAbstract
{
	function pluginSettings()
	{
		return array( 
			array (
				'id'			=> 'forum_path',
				'caption'		=> 'PhpBB Path',
				'type'			=> 'string',
				'comment'		=> '* e.g. /forum',
				'length'		=> '50',
				'order'			=> null,
			)
		);
	}
	
	static function login($request)
	{
		$forumPath = SJB_Settings::getSettingByName('forum_path');
		if (empty($forumPath))
			return;
		$username = $request['username'];
		$password = $request['password'];

		$url = SJB_System::getSystemSettings('SITE_URL') . $forumPath . '/ucp.php?mode=login';
	    $client = new Zend_Http_Client($url, array('useragent' => SJB_Request::getUserAgent()));
        $client->setCookie($_COOKIE);
        $client->setMethod(Zend_Http_Client::POST);
        $client->setParameterPost(
            array(
            	'username'  => $username,
            	'password'  => $password,
            	'login'	    => 'Login',
            	'autologin' => '',
            	'viewonline'=> ''
            )
        );

        $client->setCookieJar();
        try {
    	    $ret = $client->request();
	        foreach ($ret->getHeaders() as $key => $header) {
	            if ('set-cookie' == strtolower($key)) {
	                if (is_array($header)) {
    	                foreach ($header as $val)
                            header("Set-Cookie: " . $val, false);
	                }
	                else {
	                    header("Set-Cookie: " . $header, false);
	                }
	            }
	        }
        }
        catch (Exception $ex) {}
	}
	
	static function logout()
	{
		SessionStorage::destroy(SJB_Session::getSessionId());
		$forumPath = SJB_Settings::getSettingByName('forum_path');
		if (empty($forumPath))
			return;
		$url = SJB_System::getSystemSettings('SITE_URL') . $forumPath . '/';
	    $client = new Zend_Http_Client($url, array('useragent' => SJB_Request::getUserAgent()));
        $client->setCookie($_COOKIE);
        $client->setCookieJar();
        try {
            $response = $client->request();
            $matches = array();
            if (preg_match('/\.\/ucp.php\?mode=logout\&amp;sid=([\w\d]+)"/', $response->getBody(), $matches)) {
                $sid = $matches[1];
        	    $client->setUri($url . 'ucp.php?mode=logout&sid=' . $sid);
        	    $response = $client->request();
    	        foreach ($response->getHeaders() as $key => $header) {
    	            if ('set-cookie' == strtolower($key)) {
    	                if (is_array($header)) {
        	                foreach ($header as $val)
                                header("Set-Cookie: " . $val, false);
    	                }
    	                else {
    	                    header("Set-Cookie: " . $header, false);
    	                }
    	            }
    	        }
            }
        }
        catch (Exception $ex) {}
	}

	public static function sessionDecode($sessionData)
	{
		if (empty($sessionData)) {
			return false;
		}
		if (strpos($sessionData, '|') !== false) {
			$sessionData = explode('|', $sessionData);
			$result = unserialize(array_pop($sessionData));
			return $result;
		}
		return false;
	}

	public static function getUserSessionBySessionId($sessionId)
	{
		$userSession = SJB_DB::query('SELECT * FROM `session` WHERE `session_id` = ?s', $sessionId);
		if ($userSession) {
			return array_pop($userSession);
		}
		return null;
	}
}