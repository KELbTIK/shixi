<?php

require_once 'Translation2/Admin/Container/xml.php';
require_once 'Translation2/Admin/Container/xml_ex.php';

class I18NAdminRepository
{
	/**
	 * @var Translation2AdminFactory
	 */
	var $adminFactory;

	/**
	 * @var I18NFileHelper
	 */
	var $fileHelper;

	/**
	 * @var array
	 */
	var $repository = array();

	/**
	 * @param Translation2AdminFactory $adminFactory
	 */
	function setAdminFactory(Translation2AdminFactory $adminFactory)
	{
		$this->adminFactory = $adminFactory;
	}

	/**
	 * @param I18NFileHelper $fileHelper
	 */
	function setFileHelper(I18NFileHelper $fileHelper)
	{
		$this->fileHelper = $fileHelper;
	}
	
	function load()
	{
		$language_ids = $this->fileHelper->getLanguageIDs();
		
		foreach($language_ids as $language_id)
		{
			$this->repository[$language_id] = null;
		}
	}

	/**
	 * @param string $language_id
	 * @return object|Translation2_Admin
	 */
	function &create($language_id)
	{
//		$file_path = $this->fileHelper->getFilePathToLangFile($language_id);
		$file_paths = $this->fileHelper->getFilePathToLangFiles($language_id);

		$this->fileHelper->createFiles($file_paths);
		
		$trAdmin = $this->adminFactory->createTrAdmin(realpath($file_paths['languages']), true, true, realpath($file_paths['pages']));
		
		$this->repository[$language_id] = $trAdmin;
		
		return $trAdmin;
	}
	
	public function get($language_id)
	{
		if (!isset($this->repository[$language_id]))
		{		
			$file_paths = $this->fileHelper->getFilePathToLangFiles($language_id);

			// загрузим переводы (languages & pages)
			$adminMode = SJB_System::getSystemSettings('SYSTEM_ACCESS_TYPE') === 'admin';
			$loadTrPages = $adminMode || $language_id === $this->fileHelper->context->langSettings->getCurrentLangID();

			if ($loadTrPages) { // генерим данные с pages & languages
				$file_path 				= $file_paths['pages'];
				$cache_path 			= SJB_System::getSystemSettings('CACHE_DIR') . DIRECTORY_SEPARATOR . basename($file_path) . '.cache';
				$conf_file_path 		= $file_paths['languages'];
				$generateTr = !file_exists($cache_path) || filemtime($file_path) >= filemtime($cache_path)
						|| filemtime($conf_file_path) >= filemtime($cache_path);

				if ($generateTr) {
					// генерим languages data
					$trAdmin = $this->getLanguagesTrAdmin($file_paths);

					// генерим pages data
					$trAdmin->getLanguagePages();
					$this->createCache($cache_path, $trAdmin);
				}
				else {
					$trAdmin = $this->getTrAdminFromCache($cache_path);
				}
			}
			else {
				// генерим только данные с languages
				$trAdmin = $this->getLanguagesTrAdmin($file_paths);
			}
			$this->repository[$language_id] = $trAdmin;
		}
		else
			$trAdmin = $this->repository[$language_id];
		
		return $trAdmin;
	}

	/**
	 * @param array $file_paths
	 * @return mixed|object|Translation2_Admin
	 */
	private function getLanguagesTrAdmin($file_paths)
	{
		return $this->adminFactory->createTrAdmin($file_paths['languages'], false, true, $file_paths['pages']);
	}

	/**
	 * @param string $cache_path
	 * @return Translation2_Admin_Container_xml_ex
	 */
	private function getTrAdminFromCache($cache_path)
	{
		$h = fopen($cache_path, 'r');
		$trAdmin = unserialize(fread($h, filesize($cache_path)));
		fclose($h);
		return $trAdmin;
	}

	public function createCache($cache_path, $trAdmin)
	{
		$h = fopen($cache_path, 'w+');
		flock($h, LOCK_EX);
		fwrite($h, serialize($trAdmin));
		flock($h, LOCK_UN);
		fclose($h);
	}

	function remove($language_id)
	{
		$file_paths = $this->fileHelper->getFilePathToLangFiles($language_id);
		
		unset($this->repository[$language_id]);
				
		return $this->fileHelper->deleteFiles($file_paths);
	}
	
	function getLangList()
	{		
		return array_keys($this->repository);
	}
}
