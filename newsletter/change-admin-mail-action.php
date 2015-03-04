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
?>
<table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center"><a href="change-admin-mail.php" >Change Email</a>&nbsp;|&nbsp; <a href="change-admin-password.php" >Change Password</a> </td>
  </tr>
</table>
<?php

$id=getAdminId($mysql);
$email=trim($_POST['email']);
$name=$mysql->echo_one("select username from ".$table_prefix."subadmin_details where id='$id'");
if($email=="")
{
echo "<span class=\"already\"><br>Email field can't be blank! </span><a href=\"javascript:history.back(-1);\">Go Back</a><br><br>";
include_once("admin.footer.inc.php");
exit(0);
}
mysql_query("update ".$table_prefix."subadmin_details set email='$email' where id='$id'");
if($log_enabled==1)
{
mysql_query("insert into ".$table_prefix."admin_log_info values('','$id','Administrator email changed:".$name."','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");
}
echo "<br><span class=\"inserted\">Your email address has ben changed successfully!</span><br><br>";
include_once("admin.footer.inc.php");
?>


