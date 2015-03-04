<html>
<head>
	<title>SmartJobBoard Installation Script</title>
	<style>

		body {
			font-family: helvetica;
			font-size: 80%;
			background-color: white;
			margin: 30px;
		}

		.info {
			color: navy;
		}

		.error {
			color: red;
			font-weight: bold;
		}

		.warning {
			color: #3878DB;
			font-weight: bold;
		}

		h1 {
			text-align: center;
		}

		table {
			align: center
		}

		td.label {
			text-align: right;
			vertical-align: top;
		}

		td {
			padding: 0.2em;
			font-size: 80%;
		}

		.description {
			padding: 0 0 1em 0;
			font-family: verdana;
			font-size: 80%;
		}

		.button {
			font-weight: bold;
			background: #e1e1e1;
		}

	</style>
</head>
<body>

<?php

use Zend\Db\Adapter\Adapter;

error_reporting(0);
define ("ERROR", 1);
define ("WARNING", 2);
define ("INFO", 3);

/**
 * unquoting data
 *
 * Function unquotes data
 *
 * @param array $arr array of data to unquote
 */
function unquote(& $arr)
{
	foreach ($arr as $index => $value) {
		if (is_array($arr[$index]))
			unquote($arr[$index]);
		else
			$arr[$index] = stripslashes($arr[$index]);
	}
}

function logging($level, $message)
{
	switch ($level) {
		case ERROR:
			echo "<div class=\"error\">{$message}</div>";
			break;

		case WARNING:
			echo "<div class=\"warning\">{$message}</div>";
			break;

		case INFO:
			echo "<div class=\"info\">{$message}</div>";
			break;
	}
}

class Requirements
{
	var $requirement_phpversion;
	var $requirement_extensions;

	function Requirements()
	{
		$this->requirement_phpversion = "5.3";
		$this->requirement_extensions = array(
			'gd' => array(
				'name' => 'gd',
				'version' => '',
				'warn_errmessage' => '&quot;GD&quot;library was not found. You can lose some functionality.',
				'mandatory' => 0,
			),
			'iconv' => array(
				'name' => 'iconv',
				'version' => '',
				'warn_errmessage' => '&quot;iconv&quot; extension is not found. Import from MS Excel files will not be available',
				'mandatory' => 0,
			),
			'curl' => array(
				'name' => 'curl',
				'version' => '',
				'warn_errmessage' => '&quot;CURL&quot; library was not found. PayPal payment functionality will not work',
				'mandatory' => 0,
			),
			'db' => array(
				'name' => array('mysqli', 'pdo_mysql'),
				'version' => '',
				'warn_errmessage' => '&quot;mysqli&quot; or &quot;pdo_mysql&quot; library was not found. Database will not work',
				'mandatory' => 0,
			),
		);

	}

	/**
	 * checking requirements
	 *
	 * Function checks requirements
	 *
	 * @return bool 'true' if it satisfies or 'false' otherwise
	 */
	function check_requirements()
	{
		if ($this->check_requirement_php_version() === false) {
			logging(ERROR, "PHP version is lower than required. SmartJobBoard will work under PHP version " . $this->requirement_phpversion . " or higher");
			return false;
		}
		if ($this->check_requirement_extensions() === false)
			return false;
		return true;
	}

	/**
	 * checking php version
	 *
	 * Function checks if PHP version is 4.x.x. or higher
	 *
	 * @return bool 'true' if PHP version satisfies its requirement or 'false' otherwise
	 */
	function check_requirement_php_version()
	{
		logging(INFO, "Checking PHP Version...");
		$req_phpversion = explode(".", $this->requirement_phpversion);
		$phpversion = explode(".", phpversion());
		foreach ($req_phpversion as $key => $value) {
			if (!isset ($phpversion[$key]))
				return false;
			if ($phpversion[$key] < $req_phpversion[$key])
				return false;
			if ($phpversion[$key] > $req_phpversion[$key]) {
				logging(INFO, "OK");
				return true;
			}
		}
		logging(INFO, "OK");
		return true;
	}

	/**
	 * checking existing of extensions
	 *
	 * Function checks for existing of necessary extensions
	 *
	 * @return bool 'true' if they exist or 'false' otherwise
	 */
	function check_requirement_extensions()
	{
		logging(INFO, "Checking extensions...");
		foreach ($this->requirement_extensions as $name => $cur_ext) {
			if (is_array($cur_ext['name'])) {
				$exLoaded = false;
				foreach ($cur_ext['name'] as $name) {
					if (extension_loaded($name)) {
						$exLoaded = true;
						break;
					}
				}
				if (!$exLoaded) {
					if ($cur_ext['mandatory']) {
						if ($cur_ext['warn_errmessage'] != '') {
							logging(ERROR, $cur_ext['warn_errmessage']);
						}
						
						return false;
					}
					
					if ($cur_ext['warn_errmessage'] != '') {
						logging(WARNING, $cur_ext['warn_errmessage']);
					}
				}
			} else {
				if (!extension_loaded($cur_ext['name'])) {
					if ($cur_ext['mandatory']) {
						if ($cur_ext['warn_errmessage'] != '') {
							logging(ERROR, $cur_ext['warn_errmessage']);
						}
						
						return false;
					}
					
					if ($cur_ext['warn_errmessage'] != '') {
						logging(WARNING, $cur_ext['warn_errmessage']);
					}
				}
			}
		}
		logging(INFO, "OK");
		return true;
	}
}

class Form
{

	var $is_form_submitted;
	var $valid_form_fields;

	function Form($params)
	{
		$this->formfields = array(
			'httphost' => 'localhost',
			'dbhost' => 'localhost',
			'dbname' => '',
			'dbuser' => '',
			'dbpswd' => '',
			'adminname' => '',
			'adminpswd' => '',
			'system_email' => '',
			'timezone' => '',
			'ftphost' => 'localhost',
			'ftpdir' => getcwd(),
			'ftpuser' => '',
			'ftppswd' => '',
		);

		$this->fdescriptions = array(
			'httphost' => 'Hostname or IP address of the Web-site server',
			'dbhost' => 'Hostname or IP address of the MySQL server',
			'dbname' => 'Name of the database to be used for this SmartJobBoard installation',
			'dbuser' => 'MySQL user name. The user must have all the database privileges enabled for the SmartJobBoard installation to work properly',
			'dbpswd' => 'Database user password',
			'adminname' => 'User name which will be used for administration purposes',
			'adminpswd' => 'Password for the administrator',
			'system_email' => 'Default SmartJobBoard email address used to send system-generated messages and emails, for ex., ads@yourdomain.com',
			'timezone' => 'The default timezone used by all date/time functions',

			'ftphost' => 'FTP host',
			'ftpdir' => 'FTP path to installation directory',
			'ftpuser' => 'FTP user name',
			'ftppswd' => 'FTP password',
		);

		foreach ($this->formfields as $cfield => $cvalue) {
			$this->$cfield = '';
			if (isset ($params[$cfield]))
				$this->$cfield = $params[$cfield];
			else
				$this->$cfield = $cvalue;
		}
		$this->is_form_submitted = false;
		$this->valid_form_fields = false;
		$this->errmess = '';
		if ($params['action'] == 'check_form_data')
			$this->is_form_submitted = true;
	}

	function check_form_data()
	{
		if (!$this->is_form_submitted)
			return $this->valid_form_fields = false;
		//logging (INFO, "Checking Form Data");
		$this->valid_form_fields = true;
		if ($this->check_mysql_connection() === false) {
			logging(ERROR, "Database configuration is invalid");
			return $this->valid_form_fields = false;
		}
		if ($this->check_admin_config() === false) {
			logging(ERROR, "Administrator configuration is invalid");
			return $this->valid_form_fields = false;
		}
		if ($this->check_timezone() === false) {
			return $this->valid_form_fields = false;
		}
		return $this->valid_form_fields;
	}

	function check_mysql_connection()
	{
		logging(INFO, "Checking Database configuration...");
		set_include_path('system/ext/' . PATH_SEPARATOR );
		require_once 'Zend/Loader/Autoloader.php';
		$loader = Zend_Loader_Autoloader::getInstance();
		$loader->registerNamespace('Zend');
		$db = new Adapter( array(
			'driver'   => $this->getDBAdapter(),
			'host'     => $this->dbhost,
			'database' => $this->dbname,
			'username' => $this->dbuser,
			'password' => $this->dbpswd
		));
		
		try {
			$connection = $db->driver->getConnection();
			$connection->connect();
			Zend_Registry::set('db', $db); 
		} catch (Exception $e) {
			logging(ERROR, 'Cannot establish connection to MySQL server: #' . $e->getCode() . ' - ' . $e->getMessage());
			return false;
		}
		
		return true;
	}

	/**
	 * @return null|string
	 */
	public function getDBAdapter()
	{
		$extensions = get_loaded_extensions();
		if (in_array('pdo_mysql', $extensions)) {
			return 'Pdo_Mysql';
		}
		else if (in_array('mysqli', $extensions)) {
			return 'Mysqli';
		}
		
		return null;
	}

	function check_admin_config()
	{
		logging(INFO, "Checking Administrator configuration...");
		if (empty ($this->adminname) || empty ($this->adminpswd)) {
			logging(ERROR, "Administrator name and password cannot be empty");
			return false;
		}
		if (strpos($this->adminname, ' ') || strpos($this->adminpswd, ' ')) {
			logging(ERROR, "Administrator name and password cannot involve spaces");
			return false;
		}
		return true;
	}

	function check_timezone()
	{
		logging(INFO, "Checking Timezone Value...");
		if (empty ($this->timezone)) {
			logging(ERROR, "Timezone cannot be empty");
			return false;
		}
		return true;
	}

	function output_form($timezones)
	{
		echo '
		<h1>SmartJobBoard Configuration</h1>
		<form method=post action="install.php">
		<input type="hidden" name="action" value="check_form_data">
		<table border=0>
		<tr>
			<td class=label>HTTP Host</td>
			<td><input type="text" name="httphost" value="' . $this->httphost . '" size=40>
				<div class=description>' . $this->fdescriptions['httphost'] . '</div>
			</td>
		</tr>
		<tr>
			<td class=label>MySQL Database Host</td>
			<td><input type="text" name="dbhost" value="' . $this->dbhost . '" size=40	>
				<div class=description>' . $this->fdescriptions['dbhost'] . '</div>
			</td>
		</tr>
		<tr>
			<td class=label>MySQL Database Name</td>
			<td>
			<input type="text" name="dbname" value="' . $this->dbname . '" >
			<div class=description>' . $this->fdescriptions['dbname'] . '</div></td>
		</tr>
		<tr>
			<td class=label>MySQL Database User</td>
			<td>
			<input type="text" name="dbuser" value="' . $this->dbuser . '" >
			<div class=description>' . $this->fdescriptions['dbuser'] . '</div></td>
		</tr>
		<tr>
			<td class=label>MySQL Database Password</td>
			<td>
			<input type="text" name="dbpswd" value="' . $this->dbpswd . '" >
			<div class=description>' . $this->fdescriptions['dbpswd'] . '</div></td>
		</tr>
		<tr>
			<td class=label>Administrator Username</td>
			<td>
			<input type="text" name="adminname" value="' . $this->adminname . '" >
			<div class=description>' . $this->fdescriptions['adminname'] . '</div></td>
		</tr>
		<tr>
			<td class=label>Administrator Password</td>
			<td>
			<input type="text" name="adminpswd" value="' . $this->adminpswd . '" >
			<div class=description>' . $this->fdescriptions['adminpswd'] . '</div></td>
		</tr>
		<tr>
			<td class=label>SmartJobBoard System Email</td>
			<td>
			<input type="text" name="system_email" value="' . $this->system_email . '"  size=40>
			<div class=description>' . $this->fdescriptions['system_email'] . '</div></td>
		</tr>
		<tr>
			<td class=label>Timezone</td>
			<td>
			<select name="timezone">
				<option value="">Select Timezone</option>
				' . $timezones . '
			</select>
			<div class=description>' . $this->fdescriptions['timezone'] . '</div></td>
		</tr>
		<tr>
			<td class=label>FTP Host</td>
			<td>
			<input type="text" name="ftphost" value="' . $this->ftphost . '"  size=40>
			<div class=description>' . $this->fdescriptions['ftphost'] . '</div></td>
		</tr>
		<tr>
			<td class=label>FTP Initial Directory</td>
			<td>
			<input type="text" name="ftpdir" value="' . $this->ftpdir . '"  size=40>
			<div class=description>' . $this->fdescriptions['ftpdir'] . '</div></td>
		</tr>
		<tr>
			<td class=label>FTP User</td>
			<td>
			<input type="text" name="ftpuser" value="' . $this->ftpuser . '">
			<div class=description>' . $this->fdescriptions['ftpuser'] . '</div></td>
		</tr>
		<tr>
			<td class=label>FTP Password</td>
			<td>
			<input type="text" name="ftppswd" value="' . $this->ftppswd . '">
			<div class=description>' . $this->fdescriptions['ftppswd'] . '</div></td>
		</tr>
		<tr>
			<td><br /></td>
			<td align=left><input type="submit" value="Configure" class="button"></td>
		</tr>
		</table>
		</form>
		';
	}
}


openDirFileFolder($path, $result);
function openDirFileFolder($patch, &$result)
{
	if ($dir = @opendir($patch)) {
		while (($file = readdir($dir)) !== false) {
			if ($file != '.' && $file != '..') {
				$result[] = $patch . '/' . $file;
				if (is_dir($patch . '/' . $file)) {
					openDirFileFolder($patch . '/' . $file, $result);
				}
			}
		}
		closedir($dir);
		return $result;
	}
}

class Installation
{
	var $installation_dir;
	var $step;
	var $config_files;
	var $writable_files;
	var $writable_dirs;
	var $ftp_connection;
	var $mysql_charset;

	var $baseurl;
	var $realUrl;

	var $httphost;

	var $dbhost;
	var $dbuser;
	var $dbpswd;
	var $dbname;

	var $adminname;
	var $adminpswd;

	var $system_email;

	var $timezone;

	var $ftphost;
	var $ftpdir;
	var $ftpuser;
	var $ftppswd;

	var $ftp_resource;

	function Installation($params)
	{

		foreach ($params as $key => $value)
			$this->$key = $value;

		$this->step = 'define_baseurl';
		$this->installation_dir = ".";
		$this->config_files = array('.htaccess', 'admin/.htaccess');
		$this->writable_files = array();
		$this->writable_dirs = array('cron/log', 'files/files', 'files/video', 'files/pictures', 'temp', 'languages', "./");

		$result = array();
		openDirFileFolder('templates', $result);
		openDirFileFolder('temp', $result);
		openDirFileFolder('files', $result);
		openDirFileFolder('languages', $result);

		$GLOBALS['writable_files'] = $result;

		if (isset ($GLOBALS['writable_files'])) {
			$this->writable_files = array_unique(array_merge($this->writable_files, $GLOBALS['writable_files']));
		}

		$cashe = array();
		openDirFileFolder('system/cache', $cashe);

		$GLOBALS['writable_dirs'] = $cashe;

		if (isset($GLOBALS['writable_dirs'])) {
			$this->writable_dirs = array_unique(array_merge($this->writable_dirs, $GLOBALS['writable_dirs']));
		}

		$this->ftp_resource = NULL;
	}

	function output_continue_button()
	{
		$object = htmlspecialchars(serialize($this));
		echo '<p></p>
		<form method=post action="install.php">
		<input type="hidden" name="action" value="continue">
		<input type="hidden" name="installation_object" value="' . $object . '">
		<input type="submit" value="Continue" class="button">
		</form>
		';
	}

	function Install()
	{
		switch ($this->step) {
			case 'define_baseurl':
				if ($this->define_baseurl() === false)
					return false;
			case 'connect_2ftp':
				if ($this->connect_2ftp() === false)
					logging(WARNING, "You have to access rights for config and template files manually");
			case 'grant_files_write_access':
				if ($this->grant_files_write_access() === false) {
				}
			case 'check_files_write_access':
				if ($this->check_files_write_access() === false)
					return false;
			case 'grant_dirs_write_access':
				if ($this->grant_dirs_write_access() === false) {
				}
			case 'check_dirs_write_access':
				if ($this->check_dirs_write_access() === false)
					return false;
			case 'set_charset':
				$this->set_charset();
			case 'create_base_tables':
				if ($this->create_base_tables() === false)
					return false;
			case 'forming_config_files':
				if ($this->forming_config_files() === false)
					return false;
			case 'write_admin_db_config':
				if ($this->write_admin_db_config() === false)
					return false;
			case 'write_timezone':
				if ($this->write_timezone() === false)
					return false;
			case 'write_www_htaccess':
				if ($this->write_www_htaccess() === false)
					return false;
			case 'write_admin_htaccess':
				if ($this->write_admin_htaccess() === false)
					return false;
			case 'deny_files_write_access':
				if ($this->deny_files_write_access() === false) {
					$config_files_list = "<div>" . implode("</div><div>", $this->config_files) . "</div>";
					logging(WARNING, "Please change the access mode to non Apache-writable (644|go-w) for the following files: $config_files_list");
				}
		}
		return true;
	}

	/**
	 * defining base URL
	 *
	 * Function defines base URL
	 *
	 * @return string base URL
	 */
	function define_baseurl()
	{
		logging(INFO, "Defining Base URL...");
		$p = pathinfo($_SERVER['SCRIPT_NAME']);
		$p['dirname'] = str_replace("\\", "/", $p['dirname']);
		if ($p['dirname'] == "/")
			$p['dirname'] = "";
		$this->realUrl = 'http://' . $_SERVER['HTTP_HOST'] . $p['dirname'];
		return $this->baseurl = $p['dirname'];
	}

	function connect_2ftp()
	{
		$this->step = 'connect_2ftp';
		logging(INFO, "Connecting to FTP-Server...");
		if (empty($this->ftpuser)) {
			logging(WARNING, "FTP user is not specified");
			return $this->ftp_connection = false;
		}
		if (!function_exists("ftp_connect")) {
			logging(WARNING, "Your server does not support PHP's FTP-functions");
			return $this->ftp_connection = false;
		}
		if (!$this->ftp_resource = @ftp_connect($this->ftphost)) {
			logging(WARNING, "Cannot connect to FTP-Server");
			return $this->ftp_connection = false;
		}
		if ($logged = @ftp_login($this->ftp_resource, $this->ftpuser, $this->ftppswd) === false) {
			logging(WARNING, "FTP-Authorization failed");
			return $this->ftp_connection = false;
		}

		if (!empty($this->ftpdir) && $this->ftpdir[strlen($this->ftpdir) - 1] != '/') {
			$this->ftpdir .= '/';
		}
		
		return $this->ftp_connection = true;
	}


	/**
	 * granting write-access to files by all
	 *
	 * Function changes files' permissions via FTP
	 *
	 * @return bool 'true' if operation succeeded or 'false' otherwise
	 */
	function grant_files_write_access()
	{
		$this->step = 'grant_files_write_access';
		if (!$this->ftp_connection)
			return false;
		logging(INFO, "Changing mode of files to 666...");
		$mode = 666;

		foreach ($this->config_files as $filename) {
			if (!@ftp_site($this->ftp_resource, "CHMOD $mode $filename")) {
				logging(WARNING, "Cannot change modes of files via FTP");
				return false;
			}
		}
		return true;
	}

	/**
	 * checking permissions for files
	 *
	 * Function checks permission for wriring files
	 *
	 * @return bool 'true' if it's permitted to write files or 'false' otherwise
	 */
	function check_files_write_access()
	{
		$this->step = 'check_files_write_access';
		logging(INFO, "Checking mode of files...");
		$no_permissions = array();
		foreach ($this->config_files as $filename) {
			if (!is_writable($filename))
				array_push($no_permissions, "/" . $filename);
		}
		if (count($no_permissions)) {
			$no_permissions = implode("<br />", $no_permissions);
			logging(ERROR, "Permissions denied for the following config file(s): <div>$no_permissions</div>. Please set file access modes to 666 (chmod a+w)");
			return false;
		}

		foreach ($this->writable_files as $filename) {
			if (!is_writable($filename))
				array_push($no_permissions, "/" . $filename);
		}
		if (count($no_permissions)) {
			$no_permissions = implode("<br />", $no_permissions);
			logging(WARNING, "Permissions denied for the following file(s): <div>$no_permissions</div>. Please set file access modes to 666 (chmod a+w)");
		}
		return true;
	}

	/**
	 * granting write-access to files by all
	 *
	 * Function changes files' permissions via FTP
	 *
	 * @return bool 'true' if operation succeeded or 'false' otherwise
	 */
	function grant_dirs_write_access()
	{
		$this->step = 'grant_dirs_write_access';
		if (!$this->ftp_connection)
			return false;
		logging(INFO, "Changing mode of directories to 777...");
		$mode = 777;
		$files = array_unique(array_merge($this->writable_dirs, $this->writable_files));
		foreach ($files as $dirname) {
			if (!@ftp_site($this->ftp_resource, "CHMOD $mode $dirname")) {

				logging(WARNING, "Cannot change modes of directories via FTP");
				return false;
			}
		}
		return true;
	}

	/**
	 * checking permissions for files
	 *
	 * Function checks permission for wriring files
	 *
	 * @return bool 'true' if it's permitted to write files or 'false' otherwise
	 */
	function check_dirs_write_access()
	{
		$this->step = 'check_dirs_write_access';
		logging(INFO, "Checking mode of directories...");
		$no_permissions = array();
		foreach ($this->writable_dirs as $dirname) {
			if (!is_writable($dirname))
				array_push($no_permissions, "/" . $dirname);
		}
		if (count($no_permissions)) {
			$no_permissions = implode("<br />", $no_permissions);
			logging(ERROR, "Permissions denied for the following directory(es): <div>$no_permissions</div> Please set CHMOD for the directory(es) to 777");
			return false;
		}
		return true;
	}


	/**
	 * setting mysql character set
	 *
	 * Function sets MySQL character set to utf8 and defines MYSQL_CHARSET constant
	 *
	 * @return bool 'true' if operation succeeded or 'false' otherwise
	 */
	function set_charset()
	{
		logging(INFO, "Setting character set...");
		$this->step = 'set_charset';
		if ($this->sql_version_above41() && $this->current_character_set() != 'utf8' && $this->is_utf8_available()) {
			$this->set_character_set($this->dbname, 'utf8');
		}
		
		$this->mysql_charset = $this->current_character_set();
		return true;
	}

	/**
	 * creating base tables
	 *
	 * Function creates base tables
	 *
	 * @return bool 'true' if operation succeeded or 'false' otherwise
	 */
	function create_base_tables()
	{
		logging(INFO, 'Creating base tables...');
		$this->step = 'create_base_tables';
		if (!$sql_file = @fopen("db.sql", "r")) {
			logging(ERROR, "Cannot read SQL dump: 'db.sql'");
			return false;
		}
		$sql_query = fread($sql_file, filesize("db.sql"));
		fclose($sql_file);
		
		set_time_limit(0);
		$db = Zend_Registry::get('db'); 
		$commands = array();
		$this->set_character_set_cc('utf8');
		$this->PMA_splitSqlFile($commands, $sql_query);
		$connectedUsingMysqli = $this->getDBAdapter() == 'Mysqli';
		foreach ($commands as $command) {
			if ($command['empty'] || empty ($command['query']) || ($connectedUsingMysqli && strpos($command['query'], 'LOCK TABLES') !== false)) {
				continue;
			}
			
			$command['query'] = trim($command['query']);
			try {
				$db->createStatement($command['query'])->execute();
			} catch (Exception $e) {
				logging(ERROR, "Cannot execute MySQL query: #" . $e->getCode() . " - " . $e->getMessage());
				return false;
			}
		}
		return true;
	}

	/**
	 * forming configuration files
	 *
	 * Function forms configuration files for administator and www part
	 *
	 * @return bool 'true' if operation succeeded or 'false' otherwise
	 */
	function forming_config_files()
	{
		logging(INFO, 'Forming config files...');
		$this->step = 'forming_config_files';
		
		$www_config_vars = array();
		$www_config_vars['HTTPHOST'] = '\'' . str_replace('\'', '\\\'', $this->httphost) . '\'';
		$www_config_vars['BASEURL'] = '\'' . str_replace('\'', '\\\'', $this->baseurl) . '\'';
		$www_config_vars['DBHOST'] = '\'' . str_replace('\'', '\\\'', $this->dbhost) . '\'';
		$www_config_vars['DBNAME'] = '\'' . str_replace('\'', '\\\'', $this->dbname) . '\'';
		$www_config_vars['DBUSER'] = '\'' . str_replace('\'', '\\\'', $this->dbuser) . '\'';
		$www_config_vars['DBPASSWORD'] = '\'' . str_replace('\'', '\\\'', $this->dbpswd) . '\'';
		$www_config_vars['DBADAPTER'] = '\'' . str_replace('\'', '\\\'', $this->getDBAdapter()) . '\'';
		if (!empty ($this->mysql_charset)) {
			$www_config_vars['MYSQL_CHARSET'] = '\'' . $this->mysql_charset . '\'';
		}
		
		$www_config_vars['USER_SITE_URL'] =
		$www_config_vars['SITE_URL'] = '$protocol . $_SERVER[\'HTTP_HOST\'] . \'' . $this->baseurl . '\'';
		$www_config_vars['ADMIN_SITE_URL'] = '$protocol . $_SERVER[\'HTTP_HOST\'] . \'' . $this->baseurl . '/admin' . '\'';
		
		$www_config_file_content = "<?php\r\n\r\n"
				. "\$protocol = 'http://';\r\n"
				. "if (!empty(\$_SERVER['HTTPS']) && \$_SERVER['HTTPS'] == 'on')\r\n"
				. "\t\$protocol = 'https://';\r\n"
				. "return array(\r\n";
		foreach ($www_config_vars as $key => $value) {
			$www_config_file_content .= "\t'{$key}' => {$value},\r\n";
		}
		$www_config_file_content .= ");";
		
		if (($www_config_file = @fopen('config.php', "w")) === false) {
			logging(ERROR, "Cannot open www config file for writing");
			return false;
		}
		rewind($www_config_file);
		if (fwrite($www_config_file, $www_config_file_content) == -1) {
			logging(ERROR, "Cannot write to www config file");
			return false;
		}
		fclose($www_config_file);
		return true;
	}

	/**
	 * @return null|string
	 */
	public function getDBAdapter()
	{
		$extensions = get_loaded_extensions();
		if (in_array('pdo_mysql', $extensions)) {
			return 'Pdo_Mysql';
		}
		else if (in_array('mysqli', $extensions)) {
			return 'Mysqli';
		}
		
		return null;
	}
	/**
	 * writing administrator's username and password
	 *
	 * Function writes to db administrator's username and password
	 *
	 * @return bool 'true' if operation succeeded or 'false' otherwise
	 */
	function write_admin_db_config()
	{
		logging(INFO, "Writing administrator configuration...");
		$this->step = 'write_admin_db_config';
		$db  = Zend_Registry::get('db'); 
		$sql = "TRUNCATE TABLE `administrator`";
		try {
			$db->createStatement($sql)->execute();
		} catch (Exception $e) {
			logging(ERROR, 'Cannot execute MySQL query: #' . $e->getCode() . ' - ' . $e->getMessage());
			return false;
		}
		unset ($sql);
		$sql = "INSERT INTO `administrator`(`username`, `password`) VALUES('" . $this->quote($this->adminname) . "', '" . md5($this->quote($this->adminpswd)) . "')";
		try {
			$db->createStatement($sql)->execute();
		} catch (Exception $e) {
			logging(ERROR, 'Cannot execute MySQL query: #' . $e->getCode() . ' - ' . $e->getMessage());
			return false;
		}

		$sql = "UPDATE settings SET value = '" . $this->quote($this->system_email) . "' WHERE name = 'system_email'";
		try {
			$db->createStatement($sql)->execute();
		} catch (Exception $e) {
			logging(ERROR, 'Cannot execute MySQL query: #' . $e->getCode() . ' - ' . $e->getMessage());
			return false;
		}

		return true;
	}

	/**
	 * writing timezone default value
	 *
	 * Function write to db timezone value
	 *
	 * @return bool 'true' if operation succeeded or 'false' otherwise
	 */
	function write_timezone()
	{
		logging(INFO, "Writing timezone value...");
		$this->step = 'write_timezone';
		$db = Zend_Registry::get('db'); 
		$sql = "DELETE FROM `settings` WHERE `name`='timezone'";
		try {
			$db->createStatement($sql)->execute();
		} catch (Exception $e) {
			logging(ERROR, 'Cannot execute MySQL query: #' . $e->getCode() . ' - ' . $e->getMessage());
			return false;
		}
		unset ($sql);
		
		$sql = "INSERT INTO `settings` (`name`,`value`) VALUES ('timezone', '" . $this->quote($this->timezone) . "')";
		try {
			$db->createStatement($sql)->execute();
		} catch (Exception $e) {
			logging(ERROR, 'Cannot execute MySQL query: #' . $e->getCode() . ' - ' . $e->getMessage());
			return false;
		}

		return true;
	}

	/**
	 * writing configuration to .htaccess
	 *
	 * Function writes string of 'RewriteBase' parameter to file .htaccess
	 *
	 * @return bool 'true' if operation succeeded or 'false' otherwise
	 */
	function write_www_htaccess()
	{
		logging(INFO, "Writing .htaccess file...");
		$this->step = 'write_www_htaccess';
		$path_2_htaccess = ".htaccess";
		$rewrite_base_path = '';
		$url_params = parse_url($this->realUrl);
		if (isset ($url_params['path']))
			$rewrite_base_path = $url_params['path'];
		$rewrite_base_path = trim($rewrite_base_path);
		if (!$strings = @file($path_2_htaccess)) {
			logging(ERROR, "Cannot find .htaccess file. Please check file existing");
			return false;
		}
		if (!$htaccess = @fopen($path_2_htaccess, 'w')) {
			logging(ERROR, "Cannot open .htaccess file to write.");
			return false;
		}

		$newLine = defined('PHP_EOL') ? PHP_EOL : "\r\n";

		$resultStrings = array();
		foreach ($strings as $index => $cstr) {
			if (strpos($strings[$index], "RewriteBase") !== false) {
				$strings[$index] = "RewriteBase " . $rewrite_base_path . "/" . $newLine;
			}
			fputs($htaccess, $strings[$index]);
			$resultStrings[$index] = $strings[$index];
		}
		fclose($htaccess);

		// add cache settings for apache mod_expires
		$cacheBlock = '<IfModule mod_expires.c>' . $newLine
				. "\t" . '<FilesMatch ".(ico|gif|jpe?g|png|swf|css|js|txt)$">' . $newLine
				. "\t" . "\t" . 'ExpiresActive On' . $newLine
				. "\t" . "\t" . 'ExpiresDefault "access plus 1 day"' . $newLine
				. "\t" . "\t" . 'ExpiresByType image/gif "modification plus 1 day"' . $newLine
				. "\t" . '</FilesMatch>' . $newLine
				. '</IfModule>' . $newLine;

		// add cache directives to htaccess
		$rewriteBlock = implode($resultStrings, '');
		$htaccessContent = $cacheBlock . $newLine . $rewriteBlock;

		file_put_contents($path_2_htaccess, $htaccessContent);
		return true;
	}

	/**
	 * writing configuration to admin/.htaccess
	 *
	 * Function writes string of 'RewriteBase' parameter to file admin/.htaccess
	 *
	 * @return bool 'true' if operation succeeded or 'false' otherwise
	 */
	function write_admin_htaccess()
	{
		logging(INFO, "Writing admin/.htaccess file...");
		$this->step = 'write_admin_htaccess';
		$path_2_htaccess = "admin/.htaccess";
		$rewrite_base_path = '';
		$url_params = parse_url($this->realUrl);
		if (isset ($url_params['path']))
			$rewrite_base_path = $url_params['path'];
		$rewrite_base_path = trim($rewrite_base_path);
		if (!$strings = @file($path_2_htaccess)) {
			logging(ERROR, "Cannot find admin/.htaccess file. Please check file existing");
			return false;
		}
		if (!$htaccess = fopen($path_2_htaccess, 'w')) {
			logging(ERROR, "Cannot open admin/.htaccess file to write");
			return false;
		}
		foreach ($strings as $index => $cstr) {
			if (strpos($strings[$index], "RewriteBase") !== false)
				$strings[$index] = "RewriteBase " . $rewrite_base_path . "/admin/\r\n";
			fputs($htaccess, $strings[$index]);
		}
		fclose($htaccess);
		return true;
	}

	/**
	 * denying write-access to files by group and others
	 *
	 * Function changes files' permissions via FTP
	 *
	 * @return bool 'true' if operation succeeded or 'false' otherwise
	 */
	function deny_files_write_access()
	{
		$this->step = 'deny_files_write_access';
		if (!$this->ftp_connection)
			return false;
		logging(INFO, "Changing mode of files to 644...");
		$mode = 644;

		foreach ($this->config_files as $filename) {
			if (!@ftp_site($this->ftp_resource, "CHMOD $mode $filename")) {
				logging(WARNING, "Cannot change modes of files via FTP");
				return false;
			}
		}
		return true;
	}


	// AUXILIARY FUNCTIONS

	/**
	 * converting absolute path to relative path
	 *
	 * Function converts absolute path to relative to path defined by $relate
	 *
	 * @param string $path path to convert
	 * @param string $relate relative path
	 */
	function abs2relative(&$path, $relate)
	{
		$relate = preg_replace("/\//", "\\/", $relate);
		$path = preg_replace('/^' . $relate . '/', '', $path);
	}

	/**
	 * checking existing of data for file in array
	 *
	 * Function checks if data for file exists based on its full path
	 * and compare control summs of 2 files if it exists
	 *
	 * @param array $file information about file
	 * @param array $directory data for content of directory
	 * @return integer -1 if it doesn't exists, 0 if exists but not coinside, 1 if exists and coincide
	 */
	function check_file_existing($file, $directory)
	{
		$result = -1;
		foreach ($directory as $k => $cfile) {
			if ($file['path'] == $cfile['path']) {
				$result++;
				if ($file['csum'] == $cfile['csum']) {
					$result++;
				}
			}
		}
		return $result;
	}

	//-------------------------------------------------------------//

	function sql_version_above41()
	{
		$sql    = "show variables like 'version';";
		$result = $this->getQueryResult($sql);
		
		if (!empty($result)) {
			$result = array_pop($result);
		} else {
			$result['Value'] = '';
		}
		
		$version_num = $result['Value'];
		$parts = preg_split('@[/.-]@', $version_num);
		return !($parts[0] < 4 || $parts[0] == 4 && $parts[1] < 1);
	}

	function is_utf8_available()
	{
		$sql     = "show character set;";
		$result = $this->getQueryResult($sql);
		
		$isUtf8 = false;
		if (!empty($result)) {
			foreach ($result as $value) {
				$isUtf8 = $value['Charset'] == 'utf8';
				if ($isUtf8) {
					break;
				}
			}
		}
		
		return $isUtf8;
	}

	function current_character_set()
	{
		$sql    = "show variables like 'character_set_database';";
		$result = $this->getQueryResult($sql);
		
		if (!empty($result)) {
			$result = array_pop($result);
		} else {
			$result['Value'] = '';
		}
		
		return $result['Value'];
	}

	/**
	 * @param  string $sql
	 * @return array
	 */
	function getQueryResult($sql)
	{
		$values = array();
		$db     = Zend_Registry::get('db'); 
		try {
			$result = $db->getDriver()->getConnection()->execute($sql);
			if ($result->isQueryResult()) {
				$results = new Zend\Db\ResultSet\ResultSet();
				$values  = $results->initialize($result)->toArray();
			}
		} catch (Exception $e) {
			$values = array();
		}
		
		return $values;
	}

	function set_character_set_cc($charset)
	{
		$db  = Zend_Registry::get('db'); 
		$sql = "set names '" . $this->quote($charset) . "';";
		try {
			$db->createStatement($sql)->execute();
			return true;
		} catch (Exception $e) {
			return false;
		}
	}


	function set_character_set($dbname, $charset)
	{
		$db  = Zend_Registry::get('db'); 
		$sql = "alter database `" . $this->quote($dbname) . "` character set '" . $this->quote($charset) . "';";
		try {
			$db->createStatement($sql)->execute();
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function quote($string)
	{
		$platform = Zend_Registry::get('db')->getPlatform();
		return substr($platform->quoteValue($string), 1, -1);
	}


	//-------------------------------------------------------------//


	/**
	 * Removes comment lines and splits up large sql files into individual queries
	 *
	 * Last revision: September 23, 2001 - gandon
	 *
	 * @param   array    the splitted sql commands
	 * @param   string   the sql commands
	 * @param   integer  the MySQL release number (because certains php3 versions
	 *                   can't get the value of a constant from within a function)
	 *
	 * @return  boolean  always true
	 *
	 * @access  public
	 */
	function PMA_splitSqlFile(&$ret, $sql, $release = 3)
	{
		// do not trim, see bug #1030644
		//$sql          = trim($sql);
		$sql = rtrim($sql, "\n\r");
		$sql_len = strlen($sql);
		$char = '';
		$string_start = '';
		$in_string = FALSE;
		$nothing = TRUE;
		$time0 = time();

		for ($i = 0; $i < $sql_len; ++$i) {
			$char = $sql[$i];

			// We are in a string, check for not escaped end of strings except for
			// backquotes that can't be escaped
			if ($in_string) {
				for (; ;) {
					$i = strpos($sql, $string_start, $i);
					// No end of string found -> add the current substring to the
					// returned array
					if (!$i) {
						$ret[] = array('query' => $sql, 'empty' => $nothing);
						return TRUE;
					}
					// Backquotes or no backslashes before quotes: it's indeed the
					// end of the string -> exit the loop
					else if ($string_start == '`' || $sql[$i - 1] != '\\') {
						$string_start = '';
						$in_string = FALSE;
						break;
					}
					// one or more Backslashes before the presumed end of string...
					else {
						// ... first checks for escaped backslashes
						$j = 2;
						$escaped_backslash = FALSE;
						while ($i - $j > 0 && $sql[$i - $j] == '\\') {
							$escaped_backslash = !$escaped_backslash;
							$j++;
						}
						// ... if escaped backslashes: it's really the end of the
						// string -> exit the loop
						if ($escaped_backslash) {
							$string_start = '';
							$in_string = FALSE;
							break;
						}
						// ... else loop
						else {
							$i++;
						}
					} // end if...elseif...else
				} // end for
			} // end if (in string)

			// lets skip comments (/*, -- and #)
			else if (($char == '-' && $sql_len > $i + 2 && $sql[$i + 1] == '-' && $sql[$i + 2] <= ' ') || $char == '#' || ($char == '/' && $sql_len > $i + 1 && $sql[$i + 1] == '*')) {
				$i = strpos($sql, $char == '/' ? '*/' : "\n", $i);
				// didn't we hit end of string?
				if ($i === FALSE) {
					break;
				}
				if ($char == '/') $i++;
			}

			// We are not in a string, first check for delimiter...
			else if ($char == ';') {
				// if delimiter found, add the parsed part to the returned array
				$ret[] = array('query' => substr($sql, 0, $i), 'empty' => $nothing);
				$nothing = TRUE;
				$sql = ltrim(substr($sql, min($i + 1, $sql_len)));
				$sql_len = strlen($sql);
				if ($sql_len) {
					$i = -1;
				} else {
					// The submited statement(s) end(s) here
					return TRUE;
				}
			} // end else if (is delimiter)

			// ... then check for start of a string,...
			else if (($char == '"') || ($char == '\'') || ($char == '`')) {
				$in_string = TRUE;
				$nothing = FALSE;
				$string_start = $char;
			} // end else if (is start of string)

			elseif ($nothing) {
				$nothing = FALSE;
			}

			// loic1: send a fake header each 30 sec. to bypass browser timeout
			$time1 = time();
			if ($time1 >= $time0 + 30) {
				$time0 = $time1;
				header('X-pmaPing: Pong');
			} // end if
		} // end for

		// add any rest to the returned array
		if (!empty($sql) && preg_match('@[^[:space:]]+@', $sql)) {
			$ret[] = array('query' => $sql, 'empty' => $nothing);
		}

		return TRUE;
	} // end of the 'PMA_splitSqlFile()' function
}

// turning of 'magic_quotes_runtime' (for outputting information)
@ini_set('magic_quotes_runtime', false);

// unquoting request data if 'get_magic_quotes_gpc' is turned on
if (@ini_get('magic_quotes_gpc'))
	unquote($_REQUEST);

if (!isset ($_REQUEST['action']))
	$_REQUEST['action'] = 'check_minimal_requirements';

/* Checking minimal requirements. If server does not satisfy minimal requirements then it will stopped */
if ($_REQUEST['action'] == 'check_minimal_requirements') {
	$requirements = new Requirements ();
	if ($requirements->check_requirements() === false) {
		logging(ERROR, "Server configuration doesn't satisfy minimal requirements");
		return;
	}
}

// Checking if installation has not been processed yet
if ($_REQUEST['action'] != 'continue') {
	/**
	 * Outputting form and checking it's data. If form data is invalid then it will
	 * stopped asking user to input valid data
	 **/
	$form = new Form ($_REQUEST);
	if ($form->check_form_data() === false) {
		$timezones = '';
		foreach (timezone_identifiers_list() as $timezonesItem) {
			if ($timezonesItem == $_REQUEST['timezone']) {
				$timezones .= "<option ='{$timezonesItem}' selected='selected'>{$timezonesItem}</option>";
			} else {
				$timezones .= "<option ='{$timezonesItem}'>{$timezonesItem}</option>";
			}
		}
		$form->output_form($timezones);
		return;
	}
	$installation = new Installation ($_REQUEST);
}
else
	$installation = unserialize($_REQUEST['installation_object']);

// Installation of system
if (!$installation->Install()) {
	// If installation failed then output 'Continue' button
	$installation->output_continue_button();
	return;
}
$ffmpegOutput = null;
exec('ffmpeg -L', $ffmpegOutput);
if (empty($ffmpegOutput)) {
	echo 'FFmpeg is not supported by your server. Video upload feature will not work.<br>';
}
// Congratulations
echo '<p style="color:green">Congratulations!!! The installation was successful!</p>';
echo '<a href="' . $installation->realUrl . '/admin/">Go to the admin panel</a>&nbsp;&nbsp;';
echo '<a href="' . $installation->realUrl . '/">Go to the front end area</a>';
echo '<p>Now, please remove the <strong>install.php</strong> file from the root of the installation folder.<br>'
		. 'Failure to do so may allow third persons to re-install your software and gain control over it.</p>';
?>
</body>
</html>
