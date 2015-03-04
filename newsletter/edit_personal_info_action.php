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
				 mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','$adminname attempted unauthorized access to edit extra parameter','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");
				 echo "<br><span class=\"already\">You don't have  access to this page</span>   <a href=\"javascript:history.back(-1);\">Go Back</a><br><br>";
				 include_once("admin.footer.inc.php");
				 exit(0);
				}
?>
<style type="text/css">
<!--
.style4 {color: #FF0000}
-->
</style>
<?php
$msg="";
$fieldname=trim($_POST['info']);
phpSafe($fieldname);
$type=$_POST['select'];
if($type==1 || $type==2)
$defaultvalue=trim($_POST['value']);
else
$defaultvalue="";

if($type==3)
$dvalue=trim($_POST['values']);
else
$dvalue="";
if($type==3 && $dvalue=="")
{
?>
<span class="already"><br><br>&nbsp;&nbsp;Please fill all mandatory fields !</span>
<?php
echo "<a href=\"javascript:history.back(-1);\">Go Back</a><br><br>";
 include_once("admin.footer.inc.php");
 exit (0);
}
$existingfldid=$_REQUEST['existingfldid'];
$existingfldname=$mysql->echo_one("select fieldname from ".$table_prefix."extra_personal_info where id='$existingfldid'");
$information="{".str_replace(" ","",strtoupper($fieldname))."}";
if($fieldname!="")
{
if(strtolower(trim($fieldname))!='name' && $mysql->total("".$table_prefix."extra_personal_info","id <>'$existingfldid' AND fieldname='$fieldname' ")==0)
{
 mysql_query("update ".$table_prefix."extra_personal_info set fieldname='$fieldname',defaultvalue='$defaultvalue',variablename='$information',fieldtype='$type',fieldvalue='$dvalue' where id='$existingfldid'");
 mysql_query("update ".$table_prefix."ea_extraparam set name='$fieldname' where name='$existingfldname'");
 if($log_enabled==1)
 {
  mysql_query("insert into ".$table_prefix."admin_log_info values('','0','Extra Field edited:".$existingfldname."->".$fieldname."','".time()."','$CST_MLM_EXTRAPARAM')");
  }
}
else
{
?><span class="already"><br><br>&nbsp;&nbsp;ERROR!!! Field NAME Exists!!!</span>   <?php echo  "<a href=\"javascript:history.back(-1);\">Go Back</a><br><br>";
 include_once("admin.footer.inc.php"); 
exit(0);
}
}
else
{
$msg.="Please fill all the necessary fields and click save button!"."  <a href=\"javascript:history.back(-1);\">Go Back</a><br>";
}
?>

<form name="form1" method="post" action="">
  <table width="100%"  border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td colspan="2" align="center" scope="row"><a href="add_personal_info.php">Add New Extra Field</a> | <a href="manage_personal_info.php">Manage Extra Fields</a></td>
    </tr>
    <tr>
      <td align="center" scope="row">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <th align="left" scope="row">
	  <?php
	  if($msg=="")
	  {
	  $msg.="Selected information field has been edited successfully!<br>";
	  ?>
	  <span class="inserted"><?php echo $msg;?> </span> </th>
	  <?php
	  }
	  else
	  {
	  ?>
	  <span class="already"><?php echo $msg;?> </span> </th>
	  <?php
	  }
	  ?>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <th scope="row">&nbsp;</th>
      <td>&nbsp;</td>
    </tr>
  </table>
</form>
<?php include_once("admin.footer.inc.php"); ?>