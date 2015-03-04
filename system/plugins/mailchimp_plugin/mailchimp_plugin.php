<?php

require_once 'MCAPI.class.php';

class MailChimpPlugin extends SJB_PluginAbstract
{
	public static function init()
	{
		$moduleManager = SJB_System::getModuleManager();

		$miscellaneous = $moduleManager->modules['miscellaneous']['functions'];
		$newMiscellaneous = array(
			'mailchimp' => array(
				'display_name'	=> 'Subscribe to newsletter',
				'script'		=> 'mailchimp.php',
				'type'			=> 'user',
				'access_type'	=> array('user'),
			),
		);

		$allFunctions = array_merge( $miscellaneous, $newMiscellaneous );
		$moduleManager->modules['miscellaneous']['functions'] = $allFunctions;

		if(SJB_Settings::getSettingByName('mc_subscribe_new_users')) {
			SJB_Event::handle('onAfterUserCreated', array('MailChimpPlugin', 'subscribeUser'));
		}
	}

	function pluginSettings ()
	{
		return array(
			array (
				'id'			=> 'mc_subscribe_new_users',
				'caption'		=> 'Automatically subscribe newly registered users',
				'type'			=> 'boolean',
				'length'		=> '50',
				'order'			=> null,
			),
			array (
				'id'			=> 'mc_apikey',
				'caption'		=> 'API Key',
				'type'			=> 'string',
				'comment'		=> 'Please check this MC page for more info: <a href="https://us4.admin.mailchimp.com/account/api">https://us4.admin.mailchimp.com/account/api</a></p>',
				'length'		=> '50',
				'order'			=> null,
			),
			array (
				'id'			=> 'mc_listId',
				'caption'		=> 'List ID',
				'type'			=> 'string',
				'length'		=> '50',
				'comment'		=> 'MailChimp Account &gt; Lists &gt; List Settings &gt; List Settings & Unique ID',
				'order'			=> null,
			),
		);
	}
	
	public static function subscribeUser($user = '', $email = '', $name = '', &$error = '')
	{
		if(!empty($user)) {
			$email = $user->getUserEmail();
			$name  = $user->getUserName();
		}

		$apikey = SJB_Settings::getSettingByName('mc_apikey');
		$listId = SJB_Settings::getSettingByName('mc_listId');

		$api = new MCAPI($apikey);

		$merge_vars = array(
			'FNAME'		=>	$name,
		);

		// By default this sends a confirmation email - you will not see new members
		// until the link contained in it is clicked!
		$api->listSubscribe( $listId, $email, $merge_vars );
		if ($api->errorCode) {
			$error = $api->errorMessage;
			return false;
		} else {
			return true;
		}

	}
}