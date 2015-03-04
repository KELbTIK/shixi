<?php 

/*--------------------------------------------------+
|													 |
| Copyright © 2006 http://www.inoutscripts.com/      |
| All Rights Reserved.								 |
| Email: contact@inoutscripts.com                    |
|                                                    |
+---------------------------------------------------*/



?><?php
include("config.inc.php");
if(!isset($_COOKIE['admin']))
{
header("Location:index.php"); exit(0);
}
$inout_username=$_COOKIE['admin'];
$inout_password=$_COOKIE['inout_pass'];
if(isset($_COOKIE['inout_sub_admin']))
{
	$usercount=$mysql->total($table_prefix."subadmin_details","username='$inout_username' and password='$inout_password' and status=1");
	if(0==$usercount)
	{
		header("Location:index.php"); exit(0);
	}
}
else if(!(($inout_username==md5($username)) && ($inout_password==md5($password))))
{
	header("Location:index.php"); exit(0);
}

?><?php include("admin.header.inc.php");
	 if(isset($_COOKIE['inout_sub_admin']))
				{
				   $aid=getAdminId($mysql);
				   $adminname=$mysql->echo_one("select username from  ".$table_prefix."subadmin_details where id=$aid");
				 mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','$adminname attempted unauthorized access to view settings page','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");
				 echo "<br><span class=\"already\">You don't have  access to this page</span>   <a href=\"javascript:history.back(-1);\">Go Back</a><br><br>";
				 include_once("admin.footer.inc.php");
				 exit(0);
				}
 ?>

<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td><p><strong>The configuration file allows you to manage some important settings. </strong></p>
      <ul>
        <li>Configure MySQL Details .</li>
        <li>          Super Administrator Username and Password .</li>
        <li>Time interval between consecutive emails . </li>
        <li>          Default redirection page after subscription.</li>
<li>          Default redirection page after unsubscription.</li>
        <li>Default name(eg: user) in case there is no name attached with an email and you are using the {NAME} variable in a campaign. </li>
    <li>Admin general notification email [This is the email to which subscription/unsubscription notice should be sent.If you leave this blank, no notification will be sent.] </li>
        <li>Confirmation Email Enable/Disable</li>
        <li>Default editor [ WYSIWYG Editor / Manual Editor ] </li>
 <li>Enable/Disable Activity logs</li>
 <li>Enable web preview for email campaigns</li>
        <li><?php
		if(phpversion()>=5)
			{
			include_once("smtp_code.php");
			}
		else
			echo "Your system is having php version less than 5. SMTP and BMH wont be available";
		?></li>
        <br>
          <br>
          <a href="main.php">Back to Home</a> </li>
      </ul></td>
  </tr>
</table>

<?php include("admin.footer.inc.php"); ?>