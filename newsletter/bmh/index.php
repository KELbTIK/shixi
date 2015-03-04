<?php 

/*--------------------------------------------------+
|													 |
| Copyright © 2006 http://www.inoutscripts.com/      |
| All Rights Reserved.								 |
| Email: contact@inoutscripts.com                    |
|                                                    |
+---------------------------------------------------*/



?><?php
include("../config.inc.php");
if(!isset($_COOKIE['admin']))
{
header("Location:../index.php"); exit(0);
}
$inout_username=$_COOKIE['admin'];
$inout_password=$_COOKIE['inout_pass'];
if(isset($_COOKIE['inout_sub_admin']))
{
	$usercount=$mysql->total($table_prefix."subadmin_details","username='$inout_username' and password='$inout_password' and status=1");
	if(0==$usercount)
	{
		header("Location:../index.php"); exit(0);
	}
}
else if(!(($inout_username==md5($username)) && ($inout_password==md5($password))))
{
	header("Location:../index.php"); exit(0);
}

include("../admin.header.inc.php");
	 if(isset($_COOKIE['inout_sub_admin']))
				{
				   $aid=getAdminId($mysql);
				   $adminname=$mysql->echo_one("select username from  ".$table_prefix."subadmin_details where id=$aid");
				 mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','$adminname attempted unauthorized access to process bounced emails','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");
				 echo "<br><span class=\"already\">You don't have  access to this page</span>   <a href=\"javascript:history.back(-1);\">Go Back</a><br><br>";
				 include_once("../admin.footer.inc.php");
				 exit(0);
				}

if(phpversion()<5)
			{
				echo "<br><strong>Your system is having php version less than 5. BMH wont be available</strong><br><br><a href=\"javascript:history.back(-1);\">Go Back</a> ";
				 include("../admin.footer.inc.php");
				 exit(0);
			}
/*~ index.php
.---------------------------------------------------------------------------.
|  Software: PHPMailer-BMH (Bounce Mail Handler)                            |
|   Version: 5.0.0rc1                                                       |
|   Contact: codeworxtech@users.sourceforge.net                             |
|      Info: http://phpmailer.codeworxtech.com                              |
| ------------------------------------------------------------------------- |
|    Author: Andy Prevost andy.prevost@worxteam.com (admin)                 |
| Copyright (c) 2002-2009, Andy Prevost. All Rights Reserved.               |
| ------------------------------------------------------------------------- |
|   License: Distributed under the General Public License (GPL)             |
|            (http://www.gnu.org/licenses/gpl.html)                         |
| This program is distributed in the hope that it will be useful - WITHOUT  |
| ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or     |
| FITNESS FOR A PARTICULAR PURPOSE.                                         |
| ------------------------------------------------------------------------- |
| This is a update of the original Bounce Mail Handler script               |
| http://sourceforge.net/projects/bmh/                                      |
| The script has been renamed from Bounce Mail Handler to PHPMailer-BMH     |
| ------------------------------------------------------------------------- |
| We offer a number of paid services:                                       |
| - Web Hosting on highly optimized fast and secure servers                 |
| - Technology Consulting                                                   |
| - Oursourcing (highly qualified programmers and graphic designers)        |
'---------------------------------------------------------------------------'
Last updated: January 21 2009 13:38 EST

/*
 * This is an example script to work with PHPMailer-BMH (Bounce Mail Handler).
 */

$time_start = microtime_float();
if(!isset($_POST['flag']))
	{
	 ?>
<style type="text/css">
<!--
.style3 {font-family: Arial, Helvetica, sans-serif; font-size: 18px;}
-->
</style>

	<form name="setval" method="post" enctype="multipart/form-data" action="index.php">
	<table width="100%" align="center">
		<tr><td width="284">&nbsp;</td>
		<td width="278">&nbsp;</td>
		</tr>
		<tr>
		  <td colspan="2" align="center" class="style3">Detect bounced emails </td>
	  </tr>
		<tr>
		  <td>&nbsp;</td>
		  <td>&nbsp;</td>
	  </tr>
		<tr>
		  <td>Mail server</td>
		  <td><input type="text" name="bmh_mailhost" value="<?php echo $bmh_mailhost; ?>" />&nbsp;</td></tr>
		<tr>
		  <td>Mailbox username</td>
		  <td><input type="text" name="bmh_mailbox_username" value="<?php echo $bmh_mailbox_username; ?>" />&nbsp;</td></tr>
		<tr>
		  <td>Mailbox password</td>
		  <td><input type="password" name="bmh_mailbox_password" value="<?php echo $bmh_mailbox_password; ?>" />&nbsp;</td></tr>
		<tr>
		  <td>Port to access your mailbox</td>
		  <td><input type="text" name="bmh_port" value="<?php echo $bmh_port; ?>" />&nbsp;</td></tr>
		<tr>
		  <td>Service to use (imap or pop3)</td>
		  <td><input type="text" name="bmh_service" value="<?php echo $bmh_service; ?>" />&nbsp;</td></tr>
		<tr>
		  <td>Service options (none, tls, notls, ssl, etc.)</td>
		  <td><input type="text" name="bmh_service_option" value="<?php echo $bmh_service_option; ?>" />&nbsp;</td></tr>
		<tr>
		  <td>Mailbox to access, default is 'INBOX'</td>
		  <td><input type="text" name="bmh_boxname" value="<?php echo $bmh_boxname; ?>" />&nbsp;</td></tr>
		<tr>
		  <td>Delete bounce report emails after detection</td>
		  <td><input type="checkbox" name="delete_status" />&nbsp;</td>
	  </tr>
		<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
		<tr><td align="right"><input type="submit" name="submit" value="Proceed" />&nbsp;</td><td>&nbsp;</td></tr>
		<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
	</table>
	<input type="hidden" name="flag" value="1" />
	</form>
	<?php

	}
else
	{
	$bmh_mailhost1=$_POST['bmh_mailhost'];
	$bmh_mailbox_username1=$_POST['bmh_mailbox_username'];
	$bmh_mailbox_password1=$_POST['bmh_mailbox_password'];
	$bmh_port1=$_POST['bmh_port'];
	$bmh_service1=$_POST['bmh_service'];
	$bmh_service_option1=$_POST['bmh_service_option'];
	$bmh_boxname1=$_POST['bmh_boxname'];
	$bhm_disable_delete=$_POST['delete_status'];
	
	// Use ONE of the following -- all echo back to the screen
	require_once('callback samples/callback_echo.php');
	//require_once('callback samples/callback_database.php'); // NOTE: Requires modification to insert your database settings
	//require_once('callback samples/callback_csv.php'); // NOTE: Requires creation of a 'logs' directory and making writable
	
	// determine the current directory
	$dirTmp = getcwd();
	// define the "base" directory of the application
	if (!defined('_PATH_BMH')) {
	  $dirTmp = $_SERVER['DOCUMENT_ROOT'] . '/' . $dirTmp;
	  if ( strlen( substr($dirTmp,strlen($_SERVER['DOCUMENT_ROOT']) + 1) ) > 0 ) {
		define('_PATH_BMH', substr($dirTmp,strlen($_SERVER['DOCUMENT_ROOT']) + 1) . "/");
	  } else {
		define('_PATH_BMH', '');
	  }
	}
	
	// END determine the current directory
	include(_PATH_BMH . 'class.phpmailer-bmh.php');
	
	// testing examples
	$bmh = new BounceMailHandler();
	//$bmh->action_function    = 'callbackAction'; // default is 'callbackAction'
	//$bmh->verbose            = VERBOSE_SIMPLE; //VERBOSE_REPORT; //VERBOSE_DEBUG; //VERBOSE_QUIET; // default is VERBOSE_SIMPLE
	//$bmh->use_fetchstructure = true; // true is default, no need to speficy
	//$bmh->testmode           = false; // false is default, no need to specify
	//$bmh->debug_body_rule    = false; // false is default, no need to specify
	//$bmh->debug_dsn_rule     = false; // false is default, no need to specify
	//$bmh->purge_unprocessed  = false; // false is default, no need to specify
	//$bmh->disable_delete     = false; // false is default, no need to specify
	
	/*
	 * for local mailbox (to process .EML files)
	 */
	//$bmh->openLocalDirectory('/home/email/temp/mailbox');
	//$bmh->processMailbox();
	
	/*
	 * for remote mailbox
	 */
	 $bmh_rule_result=mysql_query("select * from ".$table_prefix."bmh_rules");
	$bmh->mailhost           = $bmh_mailhost1; //'imap.gmail.com'; // your mail server
	$bmh->mailbox_username   = $bmh_mailbox_username1; //'test@nesote.com'; // your mailbox username
	$bmh->mailbox_password   = $bmh_mailbox_password1; //'test123'; // your mailbox password
	$bmh->port               = $bmh_port1; //993; // the port to access your mailbox, default is 143
	$bmh->service            = $bmh_service1; //'imap'; // the service to use (imap or pop3), default is 'imap'
	$bmh->service_option     = $bmh_service_option1; //'ssl'; // the service options (none, tls, notls, ssl, etc.), default is 'notls'
	$bmh->boxname            = $bmh_boxname1; //'INBOX'; // the mailbox to access, default is 'INBOX'
	if($bhm_disable_delete==true)
		$bmh->disable_delete            = false;
	else
		$bmh->disable_delete            = true;
	//$bmh->moveHard           = true; // default is false
	//$bmh->hardMailbox        = 'INBOX.hardtest'; // default is 'INBOX.hard' - NOTE: must start with 'INBOX.'
	//$bmh->moveSoft           = true; // default is false
	//$bmh->softMailbox        = 'INBOX.softtest'; // default is 'INBOX.soft' - NOTE: must start with 'INBOX.'
	//$bmh->deleteMsgDate      = '2009-01-05'; // format must be as 'yyyy-mm-dd'

/*
 * rest used regardless what type of connection it is
 */
$bmh->openMailbox();
$bmh->processMailbox();

echo '<hr style="width:200px;" />';
$time_end = microtime_float();
$time     = $time_end - $time_start;
echo "Seconds to process: " . $time . "<br />";

}
function microtime_float() {
  list($usec, $sec) = explode(" ", microtime());
  return ((float)$usec + (float)$sec);
}
?>
<?php include("../admin.footer.inc.php"); ?>