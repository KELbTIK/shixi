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

$id=getAdminId($mysql);
$old=md5(trim($_POST['old']));
//echo $old;
$new=trim($_POST['new']);
//echo $new;
$confirm=trim($_POST['confirm']);
//echo $confirm;
$pass=$mysql->echo_one("select password from ".$table_prefix."subadmin_details where id='$id'");
$name=$mysql->echo_one("select username from ".$table_prefix."subadmin_details where id='$id'");

$msg="";

if($old=="" || $new=="" || $confirm=="")
{
	$msg = "<br><span class=\"already\">Please go back and check whether you fill all manadatory fields!</span><a href=\"javascript:history.back(-1);\">Go Back</a><br><br>"; 
}
else 
	if($old!=$pass)
	{
		$msg = "<span class=\"already\"><br>Wrong old password!</span><a href=\"javascript:history.back(-1);\">Go Back</a><br><br>";
	}
	else if($new!=$confirm)
	{
		$msg = "<br><span class=\"already\">Password and confirm password doesn't match!</span><a href=\"javascript:history.back(-1);\">Go Back</a><br><br>"; 
	}
	else
	{
		$new=md5($new);		
		setcookie("inout_pass",$new);
		mysql_query("update ".$table_prefix."subadmin_details set password='$new' where id='$id'");
		$msg=  "<br><span class=\"inserted\">Your password has been changed successfully!</span><br><br>";
		if($log_enabled==1)
		{
		mysql_query("insert into ".$table_prefix."admin_log_info values('','$id','Administrator password changed:".$name."','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");
		}
		
	}

include("admin.header.inc.php");
?>
<table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center"><a href="change-admin-mail.php" >Change Email</a>&nbsp;|&nbsp; <a href="change-admin-password.php" >Change Password</a> </td>
  </tr>
</table>
<?php
echo $msg;
include_once("admin.footer.inc.php");
?>


