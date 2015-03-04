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
$id=getAdminId($mysql);
$email=$mysql->echo_one("select email from ".$table_prefix."subadmin_details where id='$id'");
?>


<style type="text/css">
<!--
.style5 {font-size: 18}
-->
</style>
<link href="style.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
.style5 {
	font-size: 18px;
	color: #333333;
}
-->
</style>
<table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center"><a href="change-admin-mail.php" >Change Email</a>&nbsp;|&nbsp; <a href="change-admin-password.php" >Change Password</a> </td>
  </tr>
</table>
<form name="form1" method="post" action="change-admin-mail-action.php">
  <table width="779"  border="0" align="center" cellpadding="0" cellspacing="0">
    <tr align="center">
      <td height="50" colspan="3"><span class="styleTitle style5 style5">Modify Your Email Address </span></td>
    </tr>
    <tr>
      <td height="21" colspan="3" align="center" scope="row"><span class="inserted">Please enter your new email address in the below field</span></td>
    </tr>
    <tr>
      <td width="319" height="16" align="right">&nbsp;</td>
      <td width="30">&nbsp;</td>
      <td width="430">&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td align="right">Email Address</td>
      <td>&nbsp;</td>
      <td><input name="email" type="text" id="email" size="35" value="<?php echo $email;?>"></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td><input type="submit" name="Submit" value="Update !"></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  </table>
</form>
<?php
include_once("admin.footer.inc.php");
?>
