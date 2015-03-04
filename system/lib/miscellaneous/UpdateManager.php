<?php

class SJB_UpdateManager {

	protected $updateName = '';
	protected $updateDir = '';
	protected $updateFilesInfo = array();
	protected $archivePath = '';
	protected $updateLogFile = '';
	const UPDATE_DB_FILE = 'update.php';


	public function __construct($updateName)
	{
		$this->updateName = $updateName;

		$updatesDir = SJB_System::getSystemSettings('SJB_UPDATES_DIR');
		$this->updateDir = $updatesDir . $updateName . DIRECTORY_SEPARATOR;

		$serializedFilesInfo   = file_get_contents($this->updateDir . DIRECTORY_SEPARATOR . 'files_info_serialized.info');
		$this->updateFilesInfo = unserialize($serializedFilesInfo);

		$this->updateLogFile = $updatesDir . $updateName . ".log";
	}

	public static function updateDatabase()
	{
		if (SJB_Settings::getSettingByName('patchDatabaseOnlyUsingSJB') || !$patchList = self::getPatchList()) {
			return;
		}
		
		$originalMaxExecutionTime = ini_get('max_execution_time');
		ini_set('max_execution_time', 0);
		
		$patchCode = '';
		foreach ($patchList as $patch) {
			if (!$patchCode) {
				$patchCode = $patch;
 			} else {
				if ($patchCode != '*') {
					$patched = SJB_Settings::getValue('db-patch-' . $patchCode);
				} else {
					$patched = false;
				}
				
				if (empty($patched)) {
					$patch();
					
					if (SJB_DB::isErrorExist()) {
						self::logMysqlErrors($patchCode);
					}
					else if ($patched !== false) {
						SJB_Settings::addSetting('db-patch-' . $patchCode, 'patched');
					}
				}
				
				$patchCode = '';
			}
 		}
		
		ini_set('max_execution_time', $originalMaxExecutionTime);
	}

	public static function updateDatabasePerPatch()
	{
		if (!$patchList = self::getPatchList()) {
			return false;
		}
		
		ini_set('memory_limit', -1);
		$originalMaxExecutionTime = ini_get('max_execution_time');
		ini_set('max_execution_time', 0);
		SJB_DB::hideMysqlErrors();
		SJB_DB::cleanMysqlErrors();

		$patchList  = include SJB_BASE_DIR . self::UPDATE_DB_FILE;
		$patchFound = false;
		
		$patchCode = '';
		foreach ($patchList as $patch) {
			if (!$patchCode) {
				$patchCode = $patch;
			} else {
				$patched = SJB_Settings::getValue('db-patch-' . $patchCode);
				if (empty($patched)) {
					$patch();
					
					if (SJB_DB::isErrorExist()) {
						self::logMysqlErrors($patchCode);
						$patchFound = 'Can\'t install patch ' . $patchCode;
					} else {
						SJB_Settings::addSetting('db-patch-' . $patchCode, 'patched');
						$patchFound = true;
					}
					
					break;
				}
				
				$patchCode = '';
			}
		}
		
		ini_set('max_execution_time', $originalMaxExecutionTime);
		
		return $patchFound;
	}

	public static function updateDatabasePercentagePerPatch()
	{
		$patchList = self::getPatchList();
		
		$patchesCount       = sizeof($patchList) / 2;
		$percentagePerPatch = $patchesCount ? 100 / $patchesCount : 100;
		return floor($percentagePerPatch * 10) / 10;
	}

	private static function getPatchList()
	{
		if (!file_exists(SJB_BASE_DIR . self::UPDATE_DB_FILE)) {
			return array();
		}
		
		return include SJB_BASE_DIR . self::UPDATE_DB_FILE;
	}

	public static function removeDatabasePatchFile()
	{
		@unlink(SJB_BASE_DIR . self::UPDATE_DB_FILE);
	}

	/**
	 * @param string|bool $patchCode
	 */
	private static function logMysqlErrors($patchCode)
	{
		$mysqlErrors = SJB_DB::getMysqlError();
		$mysqlErrors = implode('<br />', $mysqlErrors);
		$errorMessage = 'Can\'t install patch &quot;' . $patchCode . '&quot;<br /><b>reason:</b>' . $mysqlErrors . '<br />';
		SJB_Error::writeToLog(array( array('level' => 'E_WARNING', 'message' => $errorMessage, 'file' => self::UPDATE_DB_FILE)));
	}

	public function checkPermissionsForFiles($selectedFilesList, $ignorePaths)
	{
		$errors    = array();
		$filesInfo = $this->updateFilesInfo;

		$oldUmask = umask(0); // will set mode for mkdir in octal form (0XXX)
		// check every file info and update it if needed
		foreach ($filesInfo as $key => $updateFile) {
			$filepath = $updateFile['filepath'];
			$status   = $updateFile['status'];
			$sourceFilename = $this->updateDir . $filepath;
			$destFilename   = SJB_BASE_DIR . $filepath;
			foreach ($ignorePaths as $ignorePath) {
				if (strpos($filepath, $ignorePath) !== false) {
					$ignorePath = SJB_BASE_DIR . $ignorePath;
					if (!file_exists($ignorePath)) {
						continue 2;
					}
				}
			}
			try {
				switch ($status) {
					case 'A':
						$dirname = dirname($destFilename);
						if (file_exists($dirname) && !is_writable($dirname)) {
							throw new Exception("Directory '<b>" . $dirname . "</b>' is not writable. Check permissions.");
						}
						break;

					case 'D':
						if (is_dir($destFilename) && strpos($destFilename, 'cache/updates') === false) {
							if (file_exists($destFilename) && !is_readable($destFilename)) {
								throw new Exception("Directory '<b>" . $destFilename . "</b>' not readable.");
							} else {
								$this->checkPermissionsForRemoveDir($destFilename);
							}
						} else {
							$dirname = dirname($destFilename);
							if (file_exists($dirname) && !is_readable($dirname)) {
								throw new Exception("Directory '<b>" . $destFilename . "</b>' not readable.");
							}
						}
						break;

					case 'M':
						// check incoming array. If selected 'use update file' - replace it.
						if (in_array($key, $selectedFilesList)) {
							if (!is_writable($destFilename)) {
								if (is_dir($destFilename)) {
									throw new Exception("Directory '<b>" . $destFilename . "</b>' is not writable.");
								} else {
									if (!file_exists($destFilename)) {
										if (!is_writable(dirname($destFilename))) {
											throw new Exception("Directory '<b>" . dirname($destFilename) . "</b>' is not writable.");
										}
									} else {
										throw new Exception("File '<b>" . $destFilename . "</b>' is not writable. Unable to update this file.");
									}
								}
							}
						}
						break;

					default:
						break;
				}

			} catch (Exception $e) {
				echo "<p class=\"error\">" . $e->getMessage() . "</p>\n";
				$errors[] = $e->getMessage();
			}
		}

		// if no errors - include DB update file
		if (empty($errors)) {
			$dbUpdateFile = $this->updateDir . self::UPDATE_DB_FILE;
			if (file_exists($dbUpdateFile) && !is_readable($dbUpdateFile)) {
				echo "<p class=\"error\">File '" . self::UPDATE_DB_FILE . "' is not readable.</p>\n";
				$errors[] = "File '" . self::UPDATE_DB_FILE . "' is not readable.";
			}
		}

		umask($oldUmask);

		return $errors;
	}
	
	public function checkPermissionsForRemoveDir($dir)
	{
		$oDir = dir($dir);
		$dir_separator = DIRECTORY_SEPARATOR;
		while (false !== ($entry = $oDir->read())) {
			if (!in_array($entry, array('.', '..'))) {
				$child = $dir.$dir_separator.$entry;
				if (is_dir($child)) {
					if (file_exists($child) && !is_readable($child)) {
						throw new Exception("Directory '<b>" . $child . "</b>' not readable.");
					} else {
						$this->checkPermissionsForRemoveDir($child);
					}
				} else {
					if (file_exists($child) && !is_writable($child)) {
						throw new Exception("File '<b>" . $child . "</b>' is not writable. Unable to delete this file.");
					}
				}
			}
		}
		$oDir->close();
	}

	public function startSjbUpdateForFiles($selectedFilesList, $ignorePaths)
	{

		$errors    = array();
		$filesInfo = $this->updateFilesInfo;

		$oldUmask = umask(0); // will set mode for mkdir in octal form (0XXX)

		// check every file info and update it if needed
		foreach ($filesInfo as $key => $updateFile) {
			$filepath = $updateFile['filepath'];
			$status   = $updateFile['status'];

			$sourceFilename = $this->updateDir . $filepath;
			$destFilename   = SJB_BASE_DIR . $filepath;

			foreach ($ignorePaths as $ignorePath) {
				if (strpos($filepath, $ignorePath) !== false) {
					$ignorePath = SJB_BASE_DIR . $ignorePath;
					if (!file_exists($ignorePath)) {
						continue 2;
					}
				}
			}
			
			try {
				switch ($status) {
					case 'A':
						if (is_dir($sourceFilename)) {
							if (!file_exists($destFilename)) {
								mkdir($destFilename, 0777, true);

								if (!file_exists($destFilename)) {
									throw new Exception("Directory '<b>" . $destFilename . "</b>' cannot be created.");
								} elseif (!is_writable($destFilename)) {
									throw new Exception("Directory '<b>" . $destFilename . "</b>' not writable.");
								}
							}
						} else {
							$dirname = dirname($destFilename);
							if (!file_exists($dirname)) {
								mkdir($dirname, 0777, true);
							}
							$result = copy($sourceFilename, $destFilename);
							if (!$result) {
								throw new Exception("Unable to copy update file '<b>" . $sourceFilename . "</b>' to directory '<b>" . $destFilename . "</b>'. Check permissions.");
							}
						}
						break;

					case 'D':
						if (file_exists($destFilename) && strpos($destFilename, 'cache/updates') === false) {
							if (is_dir($destFilename)) {
								$result = $this->removeDir($destFilename);
							} else {
								$result = unlink($destFilename);
							}
							if (!$result) {
								throw new Exception("Unable to delete old file <b>'" . $destFilename . "</b>'. Check permissions.");
							}
						}
						break;

					case 'M':
						// check incoming array. If selected 'use update file' - replace it.
						if (in_array($key, $selectedFilesList)) {
							// replace old file by new
							$result = copy( $sourceFilename, $destFilename);
							if (!$result) {
								throw new Exception("Unable to copy update file '<b>" . $sourceFilename . "</b>' to '<b>" . $destFilename . "</b>'. Check permissions.");
							}
						}
						break;

					default:
						break;
				}

			} catch (Exception $e) {
				echo "<p class=\"error\">" . $e->getMessage() . "</p>\n";
				$errors[] = $e->getMessage();
			}
		}
		umask($oldUmask);

		return $errors;
	}

	public function removeDir($dir)
	{
		$oDir = dir($dir);
		$dir_separator = DIRECTORY_SEPARATOR;
		while (false !== ($entry = $oDir->read())) {
			if (!in_array($entry, array('.', '..'))) {
				$child = $dir.$dir_separator.$entry;
				if (is_dir($child)) {
					$this->removeDir($child);
				} else {
					unlink($child);
				}
			}
		}
		$oDir->close();
		return rmdir($dir);
	}


	public function createZipArchiveWithFiles($selectedFilesList, $allFilesInfo)
	{
		$errors   = array();
		$this->archivePath = $this->updateDir . $this->updateName . "-configured-by-admin.zip";

		$zip = new ZipArchive;
		$res = $zip->open($this->archivePath, ZipArchive::OVERWRITE);
		if ($res === TRUE) {
			$chosenFilesInfo = array();
			// check every file info and update it if needed
			foreach ($allFilesInfo as $key => $updateFile) {
				if (!in_array($key, $selectedFilesList)) {
					continue;
				}

				$filepath  = $updateFile['filepath'];
				$status    = $updateFile['status'];

				$sourceFilename = $this->updateDir . $filepath;

				try {
					switch ($status) {
						case 'A':
						case 'M':
							if (!is_dir($sourceFilename) && file_exists($sourceFilename) && is_readable($sourceFilename)) {
								$zip->addFile($sourceFilename, $filepath);
							} else {
								$updatesDir = SJB_System::getSystemSettings('SJB_UPDATES_DIR');
								if (is_writable($updatesDir)) {
									file_put_contents($this->updateLogFile, date("Y-m-d H:i:s") . "\tFile {$sourceFilename} not exists in update {$this->updateName}\n", FILE_APPEND);
								}
							}
							break;

						default:
							break;
					}
				} catch (Exception $e) {
					echo "<p class=\"error\">" . $e->getMessage() . "</p>\n";
					$errors[] = $e->getMessage();
				}
				$chosenFilesInfo[] = $updateFile;
			}

		    $save = $zip->close();
			if ($save === false) {
				$errors[] = "Unable to save zip archive. Check permissions.";
			}

		} else {
			$errors[] = "Unable to create zip archive. Check permissions.";
		}

		return $errors;

	}


	public function getArchivePath()
	{
		return $this->archivePath;
	}



	public function sendArchiveToUser()
	{
		$file = $this->archivePath;
		if (empty($file)) {
			return false;
		}
		for ($i = 0; $i < ob_get_level(); $i++) {
			ob_end_clean();
		}
		header ("Content-Type: application/octet-stream");
		header ("Accept-Ranges: bytes");
		header ("Content-Length: ".filesize($file));
		header ("Content-Disposition: attachment; filename=".basename($file));
		readfile($file);
	}
}
