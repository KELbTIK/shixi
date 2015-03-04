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
error_reporting(0);
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
?><?php
	 if(isset($_COOKIE['inout_sub_admin']))
				{
				   $aid=getAdminId($mysql);
				   $adminname=$mysql->echo_one("select username from  ".$table_prefix."subadmin_details where id=$aid");
				 mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','$adminname attempted unauthorized access to edit bmh rule','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");
				 include("admin.header.inc.php");
				 echo "<br><span class=\"already\">You don't have  access to this page</span>   <a href=\"javascript:history.back(-1);\">Go Back</a><br><br>";
				 include_once("admin.footer.inc.php");
				 exit(0);
				}

$id=trim($_POST['id']);
$bmh_type=trim($_POST['bmh_type']);
$bmh_rule=trim($_POST['bmh_rule']);
$email_index=intval($_POST['email_index']);
if(!get_magic_quotes_gpc())
		{
			$body=mysql_real_escape_string($body);
			$bmh_rule=mysql_real_escape_string($bmh_rule);
			$email_index=mysql_real_escape_string($email_index);
		}
if($bmh_rule=="")
	{
	 include("admin.header.inc.php");
	echo "<br><strong>Please enter  bmh rule .</strong><br><br><a href=\"javascript:history.back(-1);\">Go Back</a> ";
	include("admin.footer.inc.php");
exit(0);
	}
else
	{
	if(mysql_query("update ".$table_prefix."bmh_rules  set type='$bmh_type',pattern='$bmh_rule',email_index='$email_index' where id='$id'"))
		{
		header("Location:manage-rules.php");
		exit(0);
		}
	}
//echo "insert into ".$table_prefix."email_template  (id,name,content) VALUES ('0','$template_name','$body')";
//echo stripcslashes($body);
?>