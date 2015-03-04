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

	 if(isset($_COOKIE['inout_sub_admin']))
				{
				   $aid=getAdminId($mysql);
				   $adminname=$mysql->echo_one("select username from  ".$table_prefix."subadmin_details where id=$aid");
				 mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','$adminname attempted unauthorized  to delete email template','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");
				include_once("admin.header.inc.php");
				 echo "<br><span class=\"already\">You don't have  access to this page</span>   <a href=\"javascript:history.back(-1);\">Go Back</a><br><br>";
				 include_once("admin.footer.inc.php");
				 exit(0);
				}



$id=trim($_GET['id']);
$count=$mysql->echo_one("select count(*) from ".$table_prefix."email_advt_curr_run where email_template='$id'");
if($count==0)
	{
	mysql_query("delete from ".$table_prefix."email_template where id='$id'");
	header("Location:managetemplate.php");
	exit(0);
	}
else
	{
	 include("admin.header.inc.php");
	echo "<br><strong>You cannot delete this template. This is used by $count campaign(s).</strong><br><br><a href=\"javascript:history.back(-1);\">Go Back</a> ";
	include("admin.footer.inc.php");
exit(0);
	}
?>