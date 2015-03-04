<?php


class SJB_InfoStorage
{	
	public static function createTranslationFilterStorage()
	{		
		$session = new SJB_Session();
		$storage = new SJB_TranslationFilterStorage();
		$storage->setSession($session);
		return $storage;
	}	
}

