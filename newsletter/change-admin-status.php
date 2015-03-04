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

	 include("admin.header.inc.php");
	 if(isset($_COOKIE['inout_sub_admin']))
				{
				   $aid=getAdminId($mysql);
				   $adminname=$mysql->echo_one("select username from  ".$table_prefix."subadmin_details where id=$aid");
				 mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','$adminname attempted unauthorized access to change status of administrator','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");
				 echo "<br><span class=\"already\">You don't have  access to this page</span>   <a href=\"javascript:history.back(-1);\">Go Back</a><br><br>";
				 include_once("admin.footer.inc.php");
				 exit(0);
				}
	
	 ?>
	 <table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center"><a href="create_new_sub_admin.php" >Create New  Administrator</a>&nbsp;|&nbsp; <a href="manage_sub_admins.php" >Manage  Administrators</a></td>
  </tr>
</table>
	<?php

$uid=$_GET['id'];
$action=$_GET['action'];
$name=$mysql->echo_one("select username from ".$table_prefix."subadmin_details where id='$uid'");
if($action=="block")
{
//echo "update ppc_users set status=0 where uid=$uid;";
mysql_query("update ".$table_prefix."subadmin_details set status=0 where id=$uid;");
if($log_enabled==1)
{
mysql_query("insert into ".$table_prefix."admin_log_info values('','0','Administrator blocked:".$name."','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");
}
}
if($action=="activate")
{
mysql_query("update ".$table_prefix."subadmin_details set status=1 where id=$uid;");
if($log_enabled==1)
{
mysql_query("insert into ".$table_prefix."admin_log_info values('','0','Administrator activated:".$name."','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");
}
}

?>
<br>

Administrator status has been successfully changed.

<a href="manage_sub_admins.php">View All Admins</a> <br>

<br>
<?php include_once("admin.footer.inc.php"); ?>