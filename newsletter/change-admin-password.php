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
.style1 {color: #FF0000}
.style13 {
	font-size: 18px;
	color: #333333;
}
-->
</style>
<style type="text/css">
<!--
.style4 {color: #FF0000}
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
<form name="form1" method="post" action="change-admin-password-action.php">
  <table width="779"  border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td height="42" colspan="4">
	  <table width="100%"  border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td height="35" align="center" valign="bottom" ><span class="style13">Modify Your Password </span></td>
          </tr>
          <tr>
            <th height="14" scope="row">&nbsp;</th>
          </tr>
          <tr>
            <th height="14" scope="row"><span class="inserted">All fields marked <strong><span class="style4">*</span></strong> are compulsory</span></th>
          </tr>
          <tr>
            <th scope="row">&nbsp;</th>
          </tr>
      </table></td>
    </tr>
    <tr>
      <td width="174" align="left">&nbsp;</td>
      <td width="153" align="left">Current Password </td>
      <td width="33">&nbsp;</td>
      <td width="419"> <strong>
        <input name="old" type="password" id="old" size="35">
      <span class="style4">*</span></strong></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td align="right">&nbsp;</td>
      <td align="left">New Password </td>
      <td>&nbsp;</td>
      <td><strong>
        <input name="new" type="password" id="new" size="35">
      <span class="style4"> *</span></strong></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td align="right">&nbsp;</td>
      <td align="left">Confirm Password </td>
      <td>&nbsp;</td>
      <td><strong>
        <input name="confirm" type="password" id="confirm" size="35">
      <span class="style4">*</span></strong></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td><input type="submit" name="Submit" value="Update Password !"></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  </table>
</form>
<?php
include_once("admin.footer.inc.php");
?>

