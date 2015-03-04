<?php


class I18NFileHelper
{
	/**
	 * @var I18NContext
	 */
	var $context;

	/**
	 * @var SJB_Filesystem
	 */
	var $fileSystem;
	
	function setContext(&$context)
	{
		$this->context =& $context;
	}
	
	function setFileSystem(&$fileSystem)
	{
		$this->fileSystem =& $fileSystem;
	}

	/**
	 * @return array
	 */
	function getLanguageIDs()
	{		
		$path = $this->context->getPathToLanguageFiles();		
		$file_names = $this->fileSystem->getFileNames($path);
		
		$lang_ids = array();
		
		foreach($file_names as $file_name)
		{
			$id = $this->_getID($file_name);
			
			if(!empty($id))	$lang_ids[] = $id;
		}
		
		return $lang_ids;
	}

	function getLanguageIDForFile($file_name)
	{
		$id = $this->_getID($file_name);
		
		if(empty($id)) return false;
		
		return $id;
	}

	function getLanguageIDForImportFile($file_name)
	{
		$id = $this->_getIDFromImportFileName($file_name);

		if(empty($id)) return false;

		return $id;
	}

	function _getID($file_name)
	{				
		$template = $this->context->getFileNameTemplateForLanguageFile();
		$template = preg_replace("/\%s/", "([^\/]+)", $template);
		$template = preg_replace("/\./", "\\.", $template);
		$pattern = sprintf("/%s$/", $template);
		
		if(preg_match($pattern, $file_name, $matches))
		{
			return $matches[1];
		}	
		return null;
	}

	function _getIDFromImportFileName($file_name)
	{
		$template = $this->context->getFileNameTemplateForLanguageExportFile();
		$template = preg_replace("/\%s/", "([^\/]+)", $template);
		$template = preg_replace("/\./", "\\.", $template);
		$pattern = sprintf("/%s$/", $template);

		if(preg_match($pattern, $file_name, $matches))
		{
			return $matches[1];
		}
		return null;
	}

	function createFile($file_path)
	{
		return $this->fileSystem->createFile($file_path);
	}

	/**
	 * @param array $file_path
	 */
	function createFiles($file_path)
	{
		$this->fileSystem->createFile($file_path['languages']);
		$this->fileSystem->createFile($file_path['pages']);
	}
	
	function deleteFile($file_path)
	{
		return $this->fileSystem->deleteFile($file_path);
	}

	/**
	 * delete langauge files
	 *
	 * @param array $file_paths
	 * @return bool
	 */
	function deleteFiles($file_paths)
	{
		foreach ($file_paths as $key => $file_path) {
			$this->fileSystem->deleteFile($file_path);
		}
		return true;
	}
		
	function getFilePathToLangFile($language_id)
	{
		$path = $this->context->getPathToLanguageFiles();
		$file_name = sprintf($this->context->getFileNameTemplateForLanguageFile(), $language_id);
		$file_path = $this->fileSystem->pathCombine($path, $file_name);

		return $file_path;
	}

	/**
	 * @param string $language_id
	 * @return mixed file path
	 */
	public function getFilePathToLangPagesFile($language_id)
	{
		$path = $this->context->getPathToLanguageFiles();
		$file_name = sprintf($this->context->getFileNameTemplateForLanguagePagesFile(), $language_id);
		$file_path = $this->fileSystem->pathCombine($path, $file_name);

		return $file_path;
	}

	/**
	 * retrieve langs files paths (languages, pages)
	 * @param $language_id
	 * @return array
	 */
	function getFilePathToLangFiles($language_id)
	{
		$path = $this->context->getPathToLanguageFiles();
		$file_name = sprintf($this->context->getFileNameTemplateForLanguageFile(), $language_id);

		$file_path = array();
		$file_path['languages'] = $this->fileSystem->pathCombine($path, $file_name);
		$file_path['pages'] = $this->getFilePathToLangPagesFile($language_id);;
		return $file_path;
	}

	function getFileNameForLangExportFile($language_id)
	{
		return sprintf($this->context->getFileNameTemplateForLanguageExportFile(), $language_id);
	}
}
