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
error_reporting(0);
include("admin.header.inc.php");
?>
<table width="90%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="4%">&nbsp;</td>
    <td width="93%">&nbsp;</td>
    <td width="3%">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><strong>About - Inout Mailing List Manager </strong></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td> <p>Inout Mailing List Manager is developed by <a href="http://www.inoutscripts.com/">InoutScripts</a>. All Rights reserved to Inoutscripts only. You can see the <a href="http://www.inoutscripts.com/terms.php">terms of use</a> in the corresponding link from the inoutscripts.com site footer.<br> 
      Some icons used in the script are published by 


          <a href="http://icon-king.com/">DAVID VIGNONI</a> under LGPL License.Thank you David.</p>
      <p>We use <a href="http://www.fckeditor.net/">FCKeditor</a> in this script which is also published under LGPL License.Thank you FCKeditor.<br>
        <br>
        We also use LGPL licensed PHPMailer and PHPMailer - BMH from 


 <a href="http://worxware.com">worxware.com</a>. Thank you Worxware.<br>
        <br>
    You can always use our <a href="http://www.inoutscripts.com/support/">Support Desk</a> for your questions and help requests. </p>      </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><a href="main.php" class="mainmenu">Back to Admin Area Home</a> </td>
    <td>&nbsp;</td>
  </tr>
</table>
<?php include("admin.footer.inc.php"); ?>