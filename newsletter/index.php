<?php include_once("config.inc.php");
if(isset($_COOKIE['admin']) && isset($_COOKIE['inout_pass']) )
{
$inout_username=$_COOKIE['admin'];
$inout_password=$_COOKIE['inout_pass'];
if(isset($_COOKIE['inout_sub_admin']))
{
	$usercount=$mysql->total($table_prefix."subadmin_details","username='$inout_username' and password='$inout_password' and status=1");
	if(1==$usercount)
	{
		header("Location:main.php"); exit(0);
	}
}
else if($inout_username==md5($username) && $inout_password==md5($password))
	{
	header("Location:main.php");
	exit(0);
	}
}
//setcookie("admin","");
//setcookie("inout_pass","");
//setcookie("inout_sub_admin","");
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"><html>
<head>
<title>Inout Mailing List Manager Premium- The Ultimate Email List Management Solution.</title>
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
.style2 
{
	color: #FF0000;
	font-weight: bold;
}
-->
</style>
<link href="style.css" rel="stylesheet" type="text/css">
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
          <td colspan="3" bgcolor="#000000"><span class="style1">&nbsp;&nbsp;</span><span class="style1">Admin Login </span></td>
          </tr>
        <tr>
          <td colspan="3">&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td colspan="2"><strong>Inout Mailing List  Manager Premium ADMIN Login! </strong></td>
          </tr>
		<?php
		if(isset($_REQUEST['invalid']) && $_REQUEST['invalid']==true)
		{
		?>
		 <tr>
          <td width="9%">&nbsp;</td>
          <td colspan="2"><span class="style2">Invalid username or password.</span></td>
		 <?php
		}
		?> 
        </tr>
		 <tr>
          <td colspan="3"><br></td>
        </tr>

        <tr>
          <td>&nbsp;</td>
          <td width="29%">Username</td>
          <td width="62%">&nbsp;<input name="username" type="text" <?php if($script_mode=="demo") echo "value=\"demo\"" ?> id="username">
          </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>Password</td>
          <td>&nbsp;<input name="password" type="password" <?php if($script_mode=="demo") echo "value=\"demo\"" ?> id="password">
            </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;    </td>
          <td><input type="checkbox" name="checkbox" value="1"  <?php if($script_mode=="demo") echo "checked=\"checked\"" ?> >
Super admin </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;<input type="submit" name="Submit" value="Login!"></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td colspan="2">Please enter your username and password above and click Login! 
            <a href="http://www.inoutscripts.com/"> </a></td>
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
