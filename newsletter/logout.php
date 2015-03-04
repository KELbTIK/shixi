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
$flag=0;
$inout_username=$_COOKIE['admin'];
$inout_password=$_COOKIE['inout_pass'];
if(isset($_COOKIE['inout_sub_admin']))
{
	$usercount=$mysql->total($table_prefix."subadmin_details","username='$inout_username' and password='$inout_password' and status=1");
	if(0==$usercount)
	{
		header("Location:index.php"); exit(0);
	}
	$flag=1;
}
else if(!(($inout_username==md5($username)) && ($inout_password==md5($password))))
{
	header("Location:index.php"); exit(0);
}
if($flag==0)
{
        if($log_enabled==1)
		     {
			     				   
			       mysql_query("insert into ".$table_prefix."admin_log_info values('','0','Super administrator logged out','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");
		     }
}
else
{
            if($log_enabled==1)
		     {
			     		  $aid=getAdminId($mysql);		
						  $name=$mysql->echo_one("select username from ".$table_prefix."subadmin_details where id='$aid'");   
			       mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','".$name." logged out','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");
		     }
}
setcookie("admin","");
setcookie("inout_pass","");
setcookie("inout_sub_admin","");

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"><html>
<head>
<title>Inout Mailing List Manager Premium - The Ultimate Email Management Solution.</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
.style1 {
	color: #FFFFFF;
	font-weight: bold;
}
-->
</style>
<link href="style.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
.style2 {
	color: #FF0000;
	font-weight: bold;
}
-->
</style>
</head>

<body>
<form name="form1" method="post" action="main.php">
  <br>
  <br>
  <br>
  <table width="40%"  border="1" align="center" cellpadding="0" cellspacing="0" bordercolor="#000000" bgcolor="#FFFFFF" class="bannerbox">
    <tr>
      <td><table width="100%"  border="0" align="center">
        <tr>
          <td colspan="3" bgcolor="#000000"><span class="style1">&nbsp;&nbsp;</span><span class="style1">Admin Login</span></td>
          </tr>
        <tr>
          <td colspan="3">&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td colspan="2"><span class="style2">You have successfully logged out. </span></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td colspan="2"><strong>You can login again below!</strong></td>
          </tr>
        <tr>
          <td width="9%">&nbsp;</td>
          <td width="29%">&nbsp;</td>
          <td width="62%">&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>Username</td>
          <td>&nbsp;<input name="username" type="text" id="username">
          </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>Password</td>
          <td>&nbsp;<input name="password" type="password" id="password">
            </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp; </td>
          <td><input type="checkbox" name="checkbox" value="1" >
    Super admin </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;<input type="submit" name="Submit" value="Login!"></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td colspan="2">Please enter your username and password above and click Login! <a href="http://www.inoutscripts.com/"> </a></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td colspan="2" align="left"><a href="http://www.inoutscripts.com/">InoutScripts Home </a></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td colspan="2">&nbsp;</td>
        </tr>
      </table></td>
    </tr>
  </table>
</form>
</body>
</html>
