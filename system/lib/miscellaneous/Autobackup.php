<?php

class SJB_Autobackup
{
	public function doBackup()
	{
		$settings = SJB_Settings::getSettings();
		if ($settings['autobackup'] && !SJB_System::getSystemSettings('isDemo') && !SJB_System::getIfTrialModeIsOn()) {
			$dirSeparator = DIRECTORY_SEPARATOR;
			$scriptPath = explode(SJB_System::getSystemSettings('SYSTEM_URL_BASE'), __FILE__);
			$scriptPath = array_shift($scriptPath);
			$path = $scriptPath . 'backup' . $dirSeparator;
			$identifier = time();
			$backupsArr = $this->getAllBackups($path);
			$this->deleteBackupAfterExpired($backupsArr);
			if ($this->isAutobackup()) {
				SessionStorage::destroy('backup_' . $identifier);
				SessionStorage::write('backup_' . $identifier, serialize(array('last_time' => time())));
				SJB_Session::unsetValue('restore');
				SJB_Session::unsetValue('error');
				$backupDir = $scriptPath . 'backup' . $dirSeparator;
				if (!is_dir($backupDir)) {
					mkdir($backupDir);
				}
				if (!file_exists($backupDir . '.htaccess')) {
					$handle = fopen($backupDir . '.htaccess', 'a');
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
				$backupType = SJB_System::getSettingByName('backup_type');
				switch ($backupType) {
					case 'full':
						$this->makeFullBackup($identifier, $scriptPath, $dirSeparator);
						break;

					case 'database':
						$this->makeDatabaseBackup($identifier, $dirSeparator, $scriptPath);
						break;

					case 'files':
						$this->makeFilesBackup($identifier, $scriptPath, $dirSeparator);
						break;
				}
				SJB_Settings::updateSetting('last_autobackup', date("Y-m-d H:i:s"));
			}
		}
	}

	private function makeFilesBackup($identifier, $scriptPath, $dirSeparator)
	{
		SessionStorage::write('backup_' . $identifier, serialize(array('last_time' => time())));
		$backupDir = $scriptPath;
		$d = dir($scriptPath);
		$contentDir = array();
		$folders = array('.', '..', 'backup', '.svn', '.settings', '.cache', 'restore');
		while (false !== ($entry = $d->read())) {
			if (!in_array($entry, $folders)) {
				$contentDir[] = $entry;
			}
		}
		$listFilesAndFolders = !empty($contentDir) ? $contentDir : false;
		$backupName = 'backup_' . date('Y_m_d__H_i') . '.tar.gz';
		$exportFilesdirName = '..' . $dirSeparator;
		if (SJB_Backup::archive(false, $listFilesAndFolders, $backupDir, $exportFilesdirName, $backupName, true, $identifier, 'files')) {
			SessionStorage::write('backup_' . $identifier, serialize(array('name' => $backupName)));
		}
		if (SJB_System::getSettingByName('ftp_backup')) {
			$this->sendBackupToFtp($scriptPath . 'backup' . $dirSeparator . $backupName, $backupName);
		}
	}

	private function makeDatabaseBackup($identifier, $dirSeparator, $scriptPath)
	{
		SessionStorage::write('backup_' . $identifier, serialize(array('last_time' => time())));
		$name = 'db.sql';
		$backupName = 'mysqldump_' . date('Y_m_d__H_i') . '.tar.gz';
		$exportFilesDirName = '../backup' . $dirSeparator;
		SJB_Backup::dump($name, $scriptPath, $identifier);
		if (SJB_Backup::archive(false, $name, $scriptPath, $exportFilesDirName, $backupName, false, $identifier, 'database')) {
			SessionStorage::write('backup_' . $identifier, serialize(array('name' => $backupName)));
		}
		if (SJB_System::getSettingByName('ftp_backup')) {
			$this->sendBackupToFtp($scriptPath . 'backup' . $dirSeparator . $backupName, $backupName);
		}
	}

	private function makeFullBackup($identifier, $scriptPath, $dirSeparator)
	{
		SessionStorage::write('backup_' . $identifier, serialize(array('last_time' => time())));
		$backupDir = $scriptPath;
		$name = 'db.sql';
		SJB_Backup::dump($name, $scriptPath, $identifier);
		$d = dir($scriptPath);
		$contentDir = array();
		$folders = array('.', '..', 'backup', '.svn', '.settings', '.cache', 'restore', $name);
		while (false !== ($entry = $d->read())) {
			if (!in_array($entry, $folders)) {
				$contentDir[] = $entry;
			}
		}
		$listFilesAndFolders = !empty($contentDir) ? $contentDir : false;
		$backupName = 'full_backup_' . date('Y_m_d__H_i') . '.tar.gz';
		$exportFilesDirName = '..' . $dirSeparator;
		if (SJB_Backup::archive($name, $listFilesAndFolders, $backupDir, $exportFilesDirName, $backupName, true, $identifier, 'full')) {
			SessionStorage::write('backup_' . $identifier, serialize(array('name' => $backupName)));
		}
		if (SJB_System::getSettingByName('ftp_backup')) {
			$this->sendBackupToFtp($scriptPath . 'backup' . $dirSeparator . $backupName, $backupName);
		}
	}

	private function isAutobackup()
	{
		$backupFrequency = SJB_System::getSettingByName("backup_frequency");
		$date = date("Y-m-d H:i:s");
		$lastBackup = SJB_System::getSettingByName('last_autobackup');
		switch ($backupFrequency) {
			case 'daily':
				if (!empty($lastBackup)) {
					$expiredDate = date('Y-m-d H:i:s', strtotime($lastBackup . " + 1 day"));
					if ($expiredDate < $date) {
						return true;
					}
					return false;
				}
				return true;
				break;
			case 'weekly':
				if (!empty($lastBackup)) {
					$expiredDate = date('Y-m-d H:i:s', strtotime($lastBackup . " + 1 week"));
					if ($expiredDate < $date) {
						return true;
					}
					return false;
				}
				return true;
				break;
			case 'monthly':
				if (!empty($lastBackup)) {
					$expiredDate = date('Y-m-d H:i:s', strtotime($lastBackup . " + 1 month"));
					if ($expiredDate < $date) {
						return true;
					}
					return false;
				}
				return true;
				break;
			default:
				return false;
		}
	}

	private function getAllBackups($path)
	{
		$backupsArr = array();
		if (is_dir($path)) {
			$di = new DirectoryIterator($path);
			$backupsArr = array();
			foreach ($di as $file) {
				$fileName = $file->getFilename();

				if (!$file->isDir() && !$file->isLink() && $fileName != '.htaccess') {
					$cTime = $file->getCTime();
					$backupsArr[$cTime]['name'] = $fileName;
					if (preg_match('/mysqldump/', $fileName)) {
						$backupsArr[$cTime]['type'] = 'Site database only';
					}
					elseif (preg_match('/full_backup/', $fileName)) {
						$backupsArr[$cTime]['type'] = 'Full site backup';
					}
					elseif (preg_match('/backup/', $fileName)) {
						$backupsArr[$cTime]['type'] = 'Site files only';
					} else {
						$backupsArr[$cTime]['type'] = 'Unknown';
					}

					$pattern = '/(\w+)_(\d+)_(\d+)_(\d+)__(\d+)_(\d+).tar.gz/i';
					$replacement = '$2-$3-$4 $5:$6';
					$backupsArr[$cTime]['date'] = preg_replace($pattern, $replacement, $fileName);
				}
			}
			return $backupsArr;
		}
		return $backupsArr;
	}

	private function sendBackupToFtp($backupDirectory, $backupName)
	{
		$host = SJB_System::getSettingByName('backup_ftp_host');
		$user = SJB_System::getSettingByName('backup_ftp_user');
		$password = SJB_System::getSettingByName('backup_ftp_password');
		$ftpDirectory = SJB_System::getSettingByName('backup_ftp_directory');
		$ftpDirectory = $ftpDirectory . $backupName;
		$connect = @ftp_connect($host);
		if ($connect) {
			if (@ftp_login($connect, $user, $password)) {
				ftp_put($connect, $ftpDirectory, $backupDirectory, FTP_BINARY);
				ftp_close($connect);
			} else {
				ftp_close($connect);
			}
		}
	}

	private function deleteBackupAfterExpired($backups)
	{
		$dirSeparator = DIRECTORY_SEPARATOR;
		$scriptPath = explode(SJB_System::getSystemSettings('SYSTEM_URL_BASE'), __FILE__);
		$scriptPath = array_shift($scriptPath);
		$date = date("Y-m-d H:i");
		$expiredPeriod = SJB_System::getSettingByName('backup_expired_period');
		if (!empty($expiredPeriod)) {
			foreach ($backups as $key => $value) {
				$expiredDate = date('Y-m-d H:i', strtotime($value['date'] . " + {$expiredPeriod} day"));
				if ($expiredDate <= $date) {
					if ($value['name']) {
						$backup = $scriptPath . 'backup' . $dirSeparator . $value['name'];
						if (is_file($backup)) {
							SJB_Filesystem::delete($backup);
							if (SJB_System::getSettingByName('ftp_backup')) {
								SJB_Autobackup::deleteFileFromFtp($value['name']);
							}
						}
					}
				}
			}
		}
	}

	public static function deleteFileFromFtp($fileName)
	{
		$host = SJB_System::getSettingByName('backup_ftp_host');
		$user = SJB_System::getSettingByName('backup_ftp_user');
		$password = SJB_System::getSettingByName('backup_ftp_password');
		$ftpDirectory = SJB_System::getSettingByName('backup_ftp_directory');
		$ftpDirectory = $ftpDirectory . $fileName;
		$connect = @ftp_connect($host);
		if ($connect) {
			if (@ftp_login($connect, $user, $password)) {
				if (ftp_size($connect, $ftpDirectory) > 0) {
					ftp_delete($connect, $ftpDirectory);
				}
				ftp_close($connect);
			} else {
				ftp_close($connect);
			}
		}
	}
}
