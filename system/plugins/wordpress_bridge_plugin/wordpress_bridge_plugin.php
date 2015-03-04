<?php

$c = new Zend_Http_Client();
$c->setCookieJar();

class WordPressBridgePlugin extends SJB_PluginAbstract
{
	function pluginSettings ()
	{
		return array( 
			array (
				'id'			=> 'blog_path',
				'caption'		=> 'WordPress Path',
				'type'			=> 'string',
				'comment'		=> '* e.g. /blog',
				'length'		=> '50',
				'order'			=> null,
			),
			array (
				'id'			=> 'display_blog_on_homepage',
				'caption'		=> 'Display on homepage',
				'type'			=> 'boolean',
				'length'		=> '50',
				'order'			=> null,
			),
			array (
				'id'			=> 'rss_url_for_wordpress',
				'caption'		=> 'RSS url',
				'type'			=> 'string',
				'comment'		=> '* e.g. http://rss_url.com',
				'length'		=> '50',
				'order'			=> null,
			)
		);
	}
	
	public static function login($request)
	{
		$blogPath = SJB_Settings::getSettingByName('blog_path');
		
		if (empty($blogPath))
			return;
		$username = $request['username'];
		$password = $request['password'];
		$userInfo = SJB_UserManager::getUserInfoByUserName($username);
		$userInfo = $userInfo?base64_encode(serialize($userInfo)):false;
		$url = SJB_System::getSystemSettings('SITE_URL') . $blogPath . '/wp-login.php';
		$client = new Zend_Http_Client($url,
		    array(
		    	'useragent' => SJB_Request::getUserAgent(),
		        'maxredirects' => 0
		    )
		);
		$client->setCookieJar();
        $client->setCookie($_COOKIE);
        $client->setMethod(Zend_Http_Client::POST);
        $client->setParameterPost(
            array(
                'log' => $username,
                'pwd' => $password,
            	'noSJB' => 1,
            	'userInfo' => $userInfo,
                'wp-submit' => 'Log in'
            )
        );

        try {
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
			$_SESSION['wp_cookie_jar'] = @serialize($client->getCookieJar());
		}
		catch (Exception $ex) {};
	}
	
	public static function logout()
	{
		$blogPath = SJB_Settings::getSettingByName('blog_path');
		if (empty($blogPath))
			return;
		$url = SJB_System::getSystemSettings('SITE_URL') . $blogPath . '/';
		$client = new Zend_Http_Client($url,
		    array(
		   		'useragent' => SJB_Request::getUserAgent(),
		        'maxredirects' => 0
		    )
		);
		if (isset($_SESSION['wp_cookie_jar']))
		    $client->setCookieJar(@unserialize($_SESSION['wp_cookie_jar']));
		try {
			$response = $client->request();
			$matches = array();
			if (preg_match('/_wpnonce=([\w\d]+)"/', $response->getBody(), $matches)) {
				$wpnonce = $matches[1];
				$url = $url . 'wp-login.php?action=logout&_wpnonce=' . $wpnonce.'&noSJB=1';
				$client->setUri($url);
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
	
	public static function displayBlogContent()
	{
		if (SJB_System::getSettingByName('display_blog_on_homepage') && SJB_System::getSettingByName('rss_url_for_wordpress') !== '') {
			$url = SJB_System::getSettingByName('rss_url_for_wordpress');
			try {
			    $feed = Zend_Feed::import($url);
			}
			catch (Exception $ex ) {
			    $feed = array();
			}
			
			$i18n = SJB_I18N::getInstance();
			$items = array();
						
			foreach ($feed as $feedItem) {
			    $date = new Zend_Date($feedItem->pubDate(), DATE_RSS, 'en_US');
				$item = array(
						'date' => $feedItem->pubDate()?$i18n->getDate($date->toString()):'',
				        'title' => $feedItem->title(),
				        'description' => $feedItem->description(),
						'link' => $feedItem->link()
				    );
				$items[] = $item;
			}
			return $items;
		}
	}
}