<?php

class SJB_Admin_Miscellaneous_Backup extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('create_and_restore_backups');
		return parent::isAccessible();
	}

	public function execute()
	{
		ini_set('max_execution_time', 0);
		$errors = array();
		$tp = SJB_System::getTemplateProcessor();
		$action = SJB_Request::getVar('action', false);
		$dir_separator = DIRECTORY_SEPARATOR;
		$script_path = explode(SJB_System::getSystemSettings('SYSTEM_URL_BASE'), __FILE__);
		$script_path = array_shift($script_path);
		$identifier = SJB_Request::getVar('identifier', time());
		$filename = SJB_Request::getVar('filename', false);
		$settings = array();
		if ($filename) {
			SJB_Backup::sendArchiveFile($filename, $script_path . 'backup' . $dir_separator . $filename);
		}
		if (SJB_Request::getVar('action') == "save") {
			$expPeriod = SJB_Request::getVar('backup_expired_period');
			if (!empty($expPeriod) && (!is_numeric($expPeriod) || ($expPeriod < 0))) {
				$errors[] = 'EXP_PERIOD_NOT_VALID';
			}
			$ftpValid = $this->isFTPDataValid();
			if (SJB_Request::getVar('autobackup', false) && SJB_Request::getVar('ftp_backup', false) && !$ftpValid) {
				$errors[] = 'FTP_DETAILS_NOT_VALID';
			}
			if (empty($errors)) {
				$backupSettings = $_REQUEST;
				foreach ($backupSettings as $setting => $value) {
					if (!SJB_Settings::saveSetting($setting, $value)) {
						$errors['SETTINGS_SAVED_WITH_PROBLEMS'] = "SETTINGS_SAVED_WITH_PROBLEMS";
					}
				}
				if (empty($errors)) {
					$tp->assign('successSaveMessage', true);
				}
			} else {
				$settings = $_REQUEST;
			}
		}

		switch ($action) {

			case 'backup':
				if (SJB_System::getSystemSettings('isDemo')) {
					$i18N = SJB_I18N::getInstance();
					$str = $i18N->gettext('Backend', 'Error: You don\'t have permissions for it. This is a Demo version of the software.');
					SJB_Session::setValue('error', $str);
					break;
				}

				if (SJB_System::getIfTrialModeIsOn() && $_SERVER['REMOTE_ADDR'] != "91.205.51.231") {
					$i18N = SJB_I18N::getInstance();
					$str = $i18N->gettext('Backend', 'Error: You don\'t have permissions for it. This is a Trial version of the software.');
					SJB_Session::setValue('error', $str);
					break;
				}

				SessionStorage::destroy('backup_' . $identifier);
				SessionStorage::write('backup_' . $identifier, serialize(array('last_time' => time())));
				SJB_Session::unsetValue('restore');
				SJB_Session::unsetValue('error');
				$backup_type = SJB_Request::getVar('backup_type');
				$backupDir = $script_path . 'backup' . $dir_separator;
				
				try {
					$this->prepareBackupDir($backupDir);
				} catch (Exception $e) {
					SJB_Session::setValue('error', $e->getMessage());
					exit();
				}

				switch ($backup_type) {

					case 'full':
						SessionStorage::write('backup_' . $identifier, serialize(array('last_time' => time())));
						$backupDir = $script_path;
						$name = 'db.sql';

						SJB_Backup::dump($name, $script_path, $identifier);
						$d = dir($script_path);
						$contentDir = array();
						$folders = array('.', '..', 'backup', '.svn', '.settings', '.cache', 'restore', $name);
						while (false !== ($entry = $d->read())) {
							if (!in_array($entry, $folders)) {
								$contentDir[] = $entry;
							}
						}
						$listFilesAndFolders = !empty($contentDir) ? $contentDir : false;
						$backupName = 'full_backup_' . date('Y_m_d__H_i') . '.tar.gz';
						$export_files_dir_name = '..' . $dir_separator;
						if (SJB_Backup::archive($name, $listFilesAndFolders, $backupDir, $export_files_dir_name, $backupName, true, $identifier, 'full'))
							SessionStorage::write('backup_' . $identifier, serialize(array('name' => $backupName)));
						exit();
						break;

					case 'database':
						SessionStorage::write('backup_' . $identifier, serialize(array('last_time' => time())));
						$name = 'db.sql';
						$backupName = 'mysqldump_' . date('Y_m_d__H_i') . '.tar.gz';
						$export_files_dir_name = '../backup' . $dir_separator;
						SJB_Backup::dump($name, $script_path, $identifier);
						if (SJB_Backup::archive(false, $name, $script_path, $export_files_dir_name, $backupName, false, $identifier, 'database'))
							SessionStorage::write('backup_' . $identifier, serialize(array('name' => $backupName)));
						exit();
						break;

					case 'files':
						SessionStorage::write('backup_' . $identifier, serialize(array('last_time' => time())));
						$backupDir = $script_path;
						$d = dir($script_path);
						$contentDir = array();
						$folders = array('.', '..', 'backup', '.svn', '.settings', '.cache', 'restore');
						while (false !== ($entry = $d->read())) {
							if (!in_array($entry, $folders))
								$contentDir[] = $entry;
						}
						$listFilesAndFolders = !empty($contentDir) ? $contentDir : false;
						$backupName = 'backup_' . date('Y_m_d__H_i') . '.tar.gz';
						$export_files_dir_name = '..' . $dir_separator;
						if (SJB_Backup::archive(false, $listFilesAndFolders, $backupDir, $export_files_dir_name, $backupName, true, $identifier, 'files'))
							SessionStorage::write('backup_' . $identifier, serialize(array('name' => $backupName)));
						exit();
						break;
				}
				break;

			case 'restore':

				if (SJB_System::getSystemSettings('isDemo')) {
					SJB_Session::setValue('error', 'Error: You don\'t have permissions for it. This is a Demo version of the software.');
					exit();
				}

				if (SJB_System::getIfTrialModeIsOn()) {
					SJB_Session::setValue('error', 'Error: You don\'t have permissions for it. This is a Trial version of the software.');
					exit();
				}

				SJB_Session::unsetValue('restore');
				SJB_Session::unsetValue('error');
				$error = false;
				$restoreDir = $script_path . 'restore' . $dir_separator;
				try {
					$fileName = $this->moveUploadedFile($restoreDir);
					$tar = new Archive_Tar($restoreDir . $fileName, 'gz');
					$tar->_error_class = 'SJB_PEAR_Exception';
					$tar->extractList('db.sql', $restoreDir);
					$tar->extract($script_path);
					if (is_file($restoreDir . 'db.sql')) {
						SJB_Backup::restore_base_tables($restoreDir . 'db.sql');
					}
                    SJB_Cache::getInstance()->clean();
				}
				catch (Exception $ex) {
					$error = $ex->getMessage();
				}
				SJB_Filesystem::delete($restoreDir);
				if (is_file($script_path . 'install.php'))
					SJB_Filesystem::delete($script_path . 'install.php');
				if ($error)
					SJB_Session::setValue('error', $error);
				else
					SJB_Session::setValue('restore', 1);
				exit();
				break;

			case 'send_archive':
				$name = SJB_Request::getVar('name', false);
				$archive_file_path = SJB_Path::combine(SJB_BASE_DIR.'backup' . $dir_separator, $name);
				if ($name)
					SJB_Backup::sendArchiveFile($name, $archive_file_path);
				break;

			case 'check':
				$sessionBackup = SessionStorage::read('backup_' . $identifier);
				$sessionBackup = $sessionBackup ? unserialize($sessionBackup) : array();
				$sessionRestore = SJB_Session::getValue('restore');
				$sessionError = SJB_Session::getValue('error');
				if (!empty($sessionBackup['name'])) {
					$name = $sessionBackup['name'];
					SessionStorage::destroy('backup_' . $identifier);
					echo SJB_System::getSystemSettings('SITE_URL') . "/backup/?action=send_archive&name={$name}";
					exit();
				}
				elseif (!empty($sessionRestore)) {
					SJB_Session::unsetValue('restore');
					echo SJB_System::getSystemSettings('SITE_URL') . '/backup/#restore';
					exit();
				}
				elseif (!empty($sessionError)) {
					echo 'Error';
					if (SJB_System::getSystemSettings('isDemo')) {
						echo ': You don\'t have permissions for it. This is a Demo version of the software.';
					}
					if (SJB_System::getIfTrialModeIsOn()) {
						echo ': You don\'t have permissions for it. This is a Trial version of the software.';
					}
					exit();
				}
				elseif (!empty($sessionBackup['last_time'])) {
					$period = (time() - $sessionBackup['last_time']) / 60;
					if ($period < 5)
						echo 1;
					else {
						SJB_Session::setValue('error', 'The backup generation process was unexpectedly interrupted. Please try again.');
						echo 'error';
					}
					exit();
				}
				else
					echo 1;
				exit();
				break;

			case 'delete_backup':
				$name = SJB_Request::getVar('name', false);
				if ($name) {
					$backup = $script_path . 'backup' . $dir_separator . $name;
					if (is_file($backup)) {
						SJB_Filesystem::delete($backup);
						SJB_Autobackup::deleteFileFromFtp($name);
					}
					else {
						$errors['FILE_NOT_FOUND'] = 1;
					}
				}
				$tp->assign('errors', $errors);
				$tp->assign('delBackup', 1);

			case 'created_backups':
				$path = $script_path . 'backup' . $dir_separator;

				if (is_dir($path)) {
					$di = new DirectoryIterator($path);
					$backupsArr = array();

					foreach ($di as $file) {
						$fileName = $file->getFilename();

						if (!$file->isDir() && !$file->isLink() && $fileName != '.htaccess') {
							$cTime = $file->getCTime();
							$backupsArr[$cTime]['name'] = $fileName;
							if (preg_match('/mysqldump/', $fileName))
								$backupsArr[$cTime]['type'] = 'Site database only';
							elseif (preg_match('/full_backup/', $fileName))
								$backupsArr[$cTime]['type'] = 'Full site backup';
							elseif (preg_match('/backup/', $fileName))
								$backupsArr[$cTime]['type'] = 'Site files only';
							else
								$backupsArr[$cTime]['type'] = 'Unknown';

							$pattern = '/(\w+)_(\d+)_(\d+)_(\d+)__(\d+)_(\d+).tar.gz/i';
							$replacement = '$2-$3-$4 $5:$6';
							$backupsArr[$cTime]['date'] = preg_replace($pattern, $replacement, $fileName);
						}
					}
					krsort($backupsArr);
					$tp->assign('created_backups', $backupsArr);
				}
				$tp->display('created_backups.tpl');
				exit();
				break;

			case 'error':
				$sessionError = SJB_Session::getValue('error');
				if (!is_null($sessionError)) {
					echo '<p class="error">' . $sessionError . '</p>';
					exit;
				}
				break;
		}
		if(empty($settings)) {
			$settings = SJB_Settings::getSettings();
		}
		$tp->assign('errors', $errors);
		$tp->assign('settings', $settings);
		$tp->assign('identifier', $identifier);
		$tp->assign("uploadMaxFilesize", SJB_UploadFileManager::getIniUploadMaxFilesize());
		$tp->display('backup.tpl');
	}

	public function moveUploadedFile($restoreDir)
	{
		$restoreFileError = SJB_Array::get($_FILES['restore_file'], 'error');
		if (!empty($restoreFileError)) {
			switch ($restoreFileError) {
				case UPLOAD_ERR_INI_SIZE:
					$errorMessage = 'File size exceeds system limit. Please check the file size limits on your hosting or upload another file.';
					break;
				case UPLOAD_ERR_FORM_SIZE:
					$errorMessage = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';					break;
				case UPLOAD_ERR_PARTIAL:
					$errorMessage = 'The uploaded file was only partially uploaded';
					break;
				case UPLOAD_ERR_NO_FILE:
					$errorMessage = 'No file was uploaded';
					break;
				case UPLOAD_ERR_NO_TMP_DIR:
					$errorMessage = 'Missing a temporary folder';
					break;
				case UPLOAD_ERR_CANT_WRITE:
					$errorMessage = 'Failed to write file to disk';
					break;
				case UPLOAD_ERR_EXTENSION:
					$errorMessage = 'A PHP extension stopped the file upload. PHP does not provide a way to ascertain which extension caused the file upload to stop; examining the list of loaded extensions with phpinfo() may help';
					break;
			}
			throw new Exception($this->getTranslatedMessage($errorMessage));
		}

		$file = $_FILES['restore_file']['tmp_name'];

		if (!is_uploaded_file($file)) {
			$message = $this->getTranslatedMessage('Is not uploaded file');
			throw new Exception($message);
		}

		$fileName = $_FILES['restore_file']['name'];

		$this->prepareDir($restoreDir);

		if (!move_uploaded_file($file, $restoreDir . $fileName)) {
			$message = $this->getTranslatedMessage('Failed to move uploaded file', $file . ' > ' . $restoreDir . $fileName);
			throw new Exception ($message);
		}

		return $fileName;
	}

	private function prepareBackupDir($backupDir)
	{
		$this->prepareDir($backupDir);
		
		$htAccessFilePath = $backupDir . '.htaccess';
		if (!file_exists($htAccessFilePath)) {
			$handle = fopen($htAccessFilePath, 'a');
			$text = '# Apache 2.4
<IfModule mod_authz_core.c>
	<FilesMatch ".*">
		Require all denied
	</FilesMatch>
</IfModule>

# Apache 2.2
<IfModule !mod_authz_core.c>
	<FilesMatch ".*">
		Order Allow,Deny
		Deny from all
	</FilesMatch>
</IfModule>';
			fwrite($handle, $text);
			fclose($handle);
		}
		if (!file_exists($htAccessFilePath)) {
			$message = $this->getTranslatedMessage('Could not create .htaccess file for backup directory', $htAccessFilePath);
			throw new Exception($message);
		}
	}

	private function prepareDir($path)
	{
		if (!is_dir($path)) {
			mkdir($path, 0777);
		}
		if (!is_dir($path)) {
			$message = $this->getTranslatedMessage('Could not create directory', $path);
			throw new Exception($message);
		}
	}

	public function getTranslatedMessage($message, $param = '')
	{
		$message = SJB_I18N::getInstance()->gettext('Backend', $message);
		if (!empty($param)) {
			$message .= ' ' . $param;
		}
		return $message;
	}

	public function isFTPDataValid()
	{
		$host = SJB_Request::getVar('backup_ftp_host');
		$user = SJB_Request::getVar('backup_ftp_user');
		$password = SJB_Request::getVar('backup_ftp_password');
		$directory = SJB_Request::getVar('backup_ftp_directory');
		if (substr($host, -1) == '/') {
			$host = substr($host, 0, strlen($host)-1);
		}
		if (substr($directory, -1) != '/') {
			$directory .= '/';
		}
		if (substr($directory, 0, 1) != '/') {
			$directory = '/' . $directory;
		}
		$_REQUEST['backup_ftp_host'] = $host;
		$_REQUEST['backup_ftp_directory'] = $directory;
		$connect = @ftp_connect($host);
		if ($connect) {
			if (@ftp_login($connect, $user, $password) && @ftp_chdir($connect, $directory)) {
				ftp_close($connect);
				return true;
			}
			ftp_close($connect);
		}
		return false;
	}

}