<?php

class SJB_Backup
{
	public static function dump($name, $backupDir, $identifier)
	{
		$dir_separator 	= DIRECTORY_SEPARATOR;
		$fp = fopen($backupDir . 'backup'.$dir_separator.$name, 'w');
	       			
		$tables = array();
	    $result = SJB_DB::query('SHOW TABLES');
	
	    foreach ($result as $row) {
	   		$tables[] = array_pop($row);
	    }
	
	    $tabinfo[0] = 0;
		$result = SJB_DB::query('SHOW TABLE STATUS');
		foreach ($result as $item){
			if(in_array($item['Name'], $tables)) {
				$item['Rows'] = empty($item['Rows']) ? 0 : $item['Rows'];
				$tabinfo[$item['Name']] = $item['Rows'];
			}
		}
		
		foreach ($tables AS $table) {
			SessionStorage::write('backup_'.$identifier, serialize(array('last_time' => time())));
			$result = SJB_DB::query("SHOW CREATE TABLE `{$table}`");
			$tab = array_pop($result);	
			$tab = preg_replace('/(default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP|DEFAULT CHARSET=\w+|COLLATE=\w+|character set \w+|collate \w+)/i', '/*!40101 \\1 */', $tab);

			fwrite($fp, "DROP TABLE IF EXISTS `{$table}`;\n{$tab['Create Table']};\n\n");
			$result = SJB_DB::query("SHOW COLUMNS FROM `{$table}`");
			$field = 0;
			$NumericColumn = array();
			$dateColumn = array();
			foreach ($result as $col) {
				$col = array_values($col);
				$NumericColumn[$field] = preg_match("/^(\w*int|float|double|year)/", $col[1]) ? 1 : 0;
				$dateColumn[$field] = preg_match("/^(\w*date)/", $col[1]) ? 1 : 0;
				$field++;
			}
			$countRows = $tabinfo[$table];
			if ($tabinfo[$table] > 0) {
				for ($i=0; $i<$countRows; $i = $i+1000) {
					$from = $i;
					$limit = 1000;
					fwrite($fp, "INSERT INTO `{$table}` VALUES");
					$result = SJB_DB::query("SELECT * FROM `{$table}` LIMIT {$from}, {$limit}");

					foreach ($result as $k => $row) {
						$row = array_values($row);
						foreach ($row as $key => $value) {
					        if ($NumericColumn[$key])
		               		    $row[$key] = ($value !== '' && $value !== NULL) ? $value : "NULL";
		               		elseif($dateColumn[$key])
		               			$row[$key] = ($value !== '' && $value !== NULL) ? "'" . SJB_DB::quote($value) . "'" : "NULL";
		               		else
		               			$row[$key] = ($value !== '' && $value !== NULL) ? "'" . SJB_DB::quote($value) . "'" : "''";
						}
	               		fwrite($fp, ($k == 0 ? "" : ",") . "\n(" . implode(", ", $row) . ")");
					}
	      			fwrite($fp, ";\n\n");
				}
			}
    	}
		fclose($fp);
	}
	
	public static function archive($name = false, $listFilesAndFolders, $export_files_dir, $export_files_dir_name, $backupName, $move = false, $identifier, $type)
	{
		if (empty($export_files_dir))
			return;
		$dir_separator 	= DIRECTORY_SEPARATOR;
		$backupName = 'backup' . $dir_separator . $backupName;
		$installFilePath = 'system' . $dir_separator . 'admin-scripts' . $dir_separator . 'miscellaneous' . $dir_separator;
		$dbSQLFilePath = 'backup' . $dir_separator;
		$old_path = getcwd();						
		chdir($export_files_dir);
		$tar = new Archive_Tar($backupName, 'gz');
		if (SJB_System::getIfTrialModeIsOn()) {
			$tar->setIgnoreList(array('system/plugins/mobile', 'system/plugins/facebook_app', 'templates/mobile', 'templates/Facebook'));
		}

		SessionStorage::write('backup_'.$identifier, serialize(array('last_time' => time())));
		switch ($type) {
			case 'full':
				$tar->addModify("{$installFilePath}install.php", '', $installFilePath);
				$tar->addModify($dbSQLFilePath.$name, '', $dbSQLFilePath);
				$tar->addModify($listFilesAndFolders, '');
				SJB_Filesystem::delete($export_files_dir.$dbSQLFilePath.$name);
				break;
			case 'files':
				$tar->addModify("{$installFilePath}install.php", '', $installFilePath);
				$tar->addModify($listFilesAndFolders, '');
				break;
			case 'database':
				$tar->addModify($dbSQLFilePath.$listFilesAndFolders, '', $dbSQLFilePath);
				SJB_Filesystem::delete($export_files_dir.$dbSQLFilePath.$listFilesAndFolders);
				break;
		}
		chdir($old_path);
		return true;
	}
	
	public static function sendArchiveFile($backupName, $archive_file_path)
	{
		for ($i = 0; $i < ob_get_level(); $i++) {
			ob_end_clean();
		}
		header('Content-type: application/octet-stream');
		header("Content-disposition: attachment; filename={$backupName}");  
		header('Content-Length: ' . filesize($archive_file_path));
		readfile($archive_file_path);
		exit();
	}

	public static function restore_base_tables($file)
	{
		$sql_query = file_get_contents($file);
		$commands = array();
		self::set_character_set_cc('utf8');
		$connectedUsingMysqli = SJB_System::getSystemSettings('DBADAPTER') == 'Mysqli';
		self::PMA_splitSqlFile($commands, $sql_query);
		foreach ($commands as $command) {
			if ($command['empty'] || empty ($command['query']) || ($connectedUsingMysqli && strpos($command['query'], 'LOCK TABLES') !== false)) {
				continue;
			}
			$command['query'] = trim($command['query']);
			if (!SJB_DB::query($command['query'])) {
				return false;
			}
		}
		return true;
	}
	
	public static function set_character_set_cc($charset)
	{
		$sql = "set names '".SJB_DB::quote($charset)."';";
		if(!SJB_DB::query($sql))
			return false;
		return true;
	}

	public static function PMA_splitSqlFile(&$ret, $sql, $release = 3)
	{
	    $sql          = rtrim($sql, "\n\r");
	    $sql_len      = strlen($sql);
	    $string_start = '';
	    $in_string    = FALSE;
	    $nothing      = TRUE;
	    $time0        = time();

	    for ($i = 0; $i < $sql_len; ++$i) {
	        $char = $sql[$i];
	        if ($in_string) {
	            for (;;) {
	                $i         = strpos($sql, $string_start, $i);
	                if (!$i) {
	                    $ret[] = array('query' => $sql, 'empty' => $nothing);
	                    return TRUE;
	                }
	                else if ($string_start == '`' || $sql[$i-1] != '\\') {
	                    $string_start      = '';
	                    $in_string         = FALSE;
	                    break;
	                }
	                else {
	                    $j                     = 2;
	                    $escaped_backslash     = FALSE;
	                    while ($i-$j > 0 && $sql[$i-$j] == '\\') {
	                        $escaped_backslash = !$escaped_backslash;
	                        $j++;
	                    }
	                    if ($escaped_backslash) {
	                        $string_start  = '';
	                        $in_string     = FALSE;
	                        break;
	                    }
	                    else {
	                        $i++;
	                    }
	                } 
	            }
	        } 
	        else if (($char == '-' && $sql_len > $i + 2 && $sql[$i + 1] == '-' && $sql[$i + 2] <= ' ') || $char == '#' || ($char == '/' && $sql_len > $i + 1 && $sql[$i + 1] == '*')) {
	            $i = strpos($sql, $char == '/' ? '*/' : "\n", $i);
	            if ($i === FALSE) {
	                break;
	            }
	            if ($char == '/') $i++;
	        }
	        else if ($char == ';') {
	            $ret[]      = array('query' => substr($sql, 0, $i), 'empty' => $nothing);
	            $nothing    = TRUE;
	            $sql        = ltrim(substr($sql, min($i + 1, $sql_len)));
	            $sql_len    = strlen($sql);
	            if ($sql_len) {
	                $i      = -1;
	            } else {
	                return TRUE;
	            }
	        } 
	        else if (($char == '"') || ($char == '\'') || ($char == '`')) {
	            $in_string    = TRUE;
	            $nothing      = FALSE;
	            $string_start = $char;
	        } 
	        elseif ($nothing) {
	            $nothing = FALSE;
	        }
	        $time1     = time();
	        if ($time1 >= $time0 + 30) {
	            $time0 = $time1;
	            header('X-pmaPing: Pong');
	        }
	    }

	    if (!empty($sql) && preg_match('@[^[:space:]]+@', $sql)) {
	        $ret[] = array('query' => $sql, 'empty' => $nothing);
	    }

	    return TRUE;
	}
}