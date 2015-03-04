<?php

define("DS", DIRECTORY_SEPARATOR);

/**
 * This class provides basic implementation of different filesystem functions
 * @package SystemClasses
 * @subpackage Errors
 */

class SJB_Filesystem
{
	/**
	 * Makes a copy of the file source to dest. Returns TRUE on success or FALSE on failure
	 * @param string $source source file or directory
	 * @param string $destination destination
	 */
	public static function copy($source, $destination)
	{
		if (preg_match("/cvs$/", strtolower($source)))
			return true;
		if (is_dir($source)) {
			if (!mkdir($destination))
				return false;
			else {
				$res = true;
				if ($dh = opendir($source)) {
					while (($file_or_directory=readdir($dh)) !== false)
						if ($file_or_directory != '.' && $file_or_directory != '..')
							if (!SJB_Filesystem :: copy($source.'/'.$file_or_directory, $destination.'/'.$file_or_directory))
								$res = false;
					closedir($dh);
				}
				return $res;
			}
		}
		else {
			if (copy($source, $destination)) {
				$old = umask(0);
				chmod($destination, 0777);
				umask($old);
				return true;
			}
			return false;
		}
	}

	/**
	 * Deletes $delete file. Returns TRUE on success or FALSE on failure.
	 * @param string $delete File or directory name
	 */
	public static function delete($delete)
	{
		if (is_dir($delete)) {
			$res = true;
			if ($dh = opendir($delete)) {
				while (($file_or_directory=readdir($dh)) !== false)
					if ($file_or_directory != '.' && $file_or_directory != '..')
						if (!SJB_Filesystem :: delete($delete . '/' . $file_or_directory))
							$res = false;
				closedir($dh);
			}
			rmdir($delete);
			return $res && !is_dir($delete);
		}
		else {
			unlink($delete);
			return !is_file($delete);
		}
	}

	/**
	 * Write a string to a file. This function is binary-safe.
	 * @param string $filename Destination file.
	 * @param string $contents Data to write.
	 */
	public static function file_put_contents($filename, $contents)
	{
	    if (!$handle = fopen($filename, 'w'))
	    	return false;

	    // Write $contents to our opened file.
	    if (!fwrite($handle, $contents))
	    	return false;

	    fclose($handle);
		return true;
	}

	/**
	 * Attempts to create all directories specified by pathname.
	 * @param string $directory Pathname.
	 */
	public static function mkpath($directory)
	{
		$tokens = explode('/', $directory);
		$path = '';
		foreach($tokens as $token) {
			$path .= $token .'/';
			if(!is_dir($path) && !mkdir($path))
				return false;
		}
		return is_dir($directory);
	}

	public static function createFile($file_path)
	{
		return self::file_put_contents($file_path, '');
	}

	public static function deleteFile($file_path)
	{
		$fh = fopen($file_path, 'w');
		fclose($fh);
		return unlink($file_path);
	}

	public static function pathCombine()
	{
		$args = func_get_args();
		return call_user_func_array(array('SJB_Path', 'combine'), $args);
	}

	public static function getFileNames($dir)
	{
		$file_names = array();
		$iterator = new DirectoryIterator($dir);
		foreach ($iterator as $dirItem) {
			if ($dirItem->isFile())
				$file_names[] = $dirItem->getFilename();
		}
		return $file_names;
	}

	public static function getFiles($dir, $filter = ".", $recursive = true, $exclude = array(".svn", "CVS", ".", ".."))
	{
		$files = array();
		
		if (is_dir($dir)) {
			$dh = opendir($dir);
			if ($dh !== false) {
				while (($dirItem = readdir($dh)) !== false) {
					$file = $dir . DS . $dirItem;
					if (is_dir($file)) {
						if ($recursive && !in_array($dirItem, $exclude))
							$files = array_merge($files, SJB_Filesystem::getFiles($file, $filter, $recursive, $exclude));
					}
					else {
						if (preg_match("/{$filter}/", $dirItem) === 1)
							$files[] = $file;
					}					
				}
			}
			else {
				SJB_Error::add("Open directory error '{$dir}'");
			}
		}
		else {
			SJB_Error::add("Trying to take files from directory {$dir}");
		}
		
		return $files;
	}

	public static function getFileContents($fileName)
	{
		return file_get_contents($fileName);
	}
	
}
