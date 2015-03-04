<?php 

/*--------------------------------------------------+
|													 |
| Copyright � 2006 http://www.inoutscripts.com/      |
| All Rights Reserved.								 |
| Email: contact@inoutscripts.com                    |
|                                                    |
+---------------------------------------------------*/



?><?php


// Please login to your inoutscripts account and create a new license key and paste it here
$license_key="f8fd47509f0d8714a4bcb87cef3a2e4f";


// Mysql Information

$mysql_server="localhost";		// MySql Server Name
$mysql_username="shixi_inoutnews"; 	// MySql Username
$mysql_password="YnhNcJxGq01A";		// MySql Password
$mysql_dbname="shixi_inoutnews";		// MySql DataBase name that need to be selected.

//ADMIN INFO
$username="citizencorp@gmail.com";
$password="citylove888";


$dirpath="http://newsletter.shixi.com/"; // eg:http://www.mysite.com/mail_list/   if you upload the files to mail_list directory

$subokpath="http://newsletter.shixi.com/thanks.html"; // The page to be displayed after users subscribe their email address. Script will redirect to this page after it adds the new email to the database. You can specify any of your site pages.

$unsubokpath="http://newsletter.shixi.com/unsub.html"; // The page to be displayed after users unsubscribe their email address. Script will redirect to this page after it unsubcribes the new email from the database. You can specify any of your site pages. 

$time_interval=0; // Time interval(in seconds) between consecutive emails

$defaultname="friend"; //Default name of an user(Used to address the user in case if his/her name is not added.)

$admin_general_notification_email="citizencorp@gmail.com"; ///email to which subscription/unsubscription notice should be sent.If you leave this blank, no notification will be sent.

$confirm_subscription=1;   // 1/0 for  ON/OFF confirmation Email. Edit the file confirm.inc.php for subscription email settings.

$default_editor=1;        // 1 for WYSIWYG editor and 0 for manual editing.

$log_enabled=1;         //1 for enabling activity logs and 0 for disabling.

$enable_web_page_preview=0; // Make this 1 if you want to prefix a link to all your html campaigns which says "Email not displaying correctly ? View it in your browser"

$charset_encoding="UTF-8"; //Until version 4.2 this configuration was not available and default encoding was "ISO-8859-1" . So if you are upgrading from 4.2 or lower version you may need to set this to "ISO-8859-1"


// ADVANCED CONFIGURATIONS
// DO ONLY IF REQUIRED. 


$tableprefix=""; 	// Add a  prefix to every table in your database.
$mailserver_type=0;        // Set it to 1 in case you are using any Qmail variations. Use 0 by default. Wrong type may cause headers to show on Emails.



// SMTP MAIL SETTINGS

$smtpmailer = 0; // 0 for false and 1 for true
$smtp_host= "localhost"; // specify SMTP mail server  eg: smtp.gmail.com or localhost
$smtp_port = "25"; // specify SMTP Port eg: 465 or 25
$smtp_user = ""; //Full SMTP username
$smtp_pass =""; //SMTP password
$smtp_secure = "notls"; // the service options ( tls, notls, ssl, etc.), default is 'notls'
$smtp_auth="false";

//Bounce Mail Handler Settings (you can configure the default settings here or configure directly from admin area when required)

$bmh_mailhost= ''; // your mail server  eg: imap.gmail.com
$bmh_mailbox_username= ''; // your mailbox username
$bmh_mailbox_password= ''; // your mailbox password
$bmh_port= 143; // the port to access your mailbox, default is 143 
$bmh_service= 'imap'; // the service to use (imap or pop3), default is 'imap'
$bmh_service_option= 'notls'; // the service options (none, tls, notls, ssl, etc.), default is 'notls'
$bmh_boxname= 'INBOX'; // the mailbox to access, default is 'INBOX'


//You DON'T have to configure anything below. Site may stop working if you edit anything below. Its for inoutscripts programmers.
ob_start();
include_once("script.inc.php");
include_once("constants.php");
include_once("functions.php");

?>