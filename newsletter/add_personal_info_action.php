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

include_once("admin.header.inc.php");
if(isset($_COOKIE['inout_sub_admin']))
				{
				   $aid=getAdminId($mysql);
				   $adminname=$mysql->echo_one("select username from  ".$table_prefix."subadmin_details where id=$aid");
				 mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','$adminname attempted unauthorized access to add extra parameter','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");
				 echo "<br><span class=\"already\">You don't have  access to this page</span>   <a href=\"javascript:history.back(-1);\">Go Back</a><br><br>";
				 include_once("admin.footer.inc.php");
				 exit(0);
				}
?>
<?php
$info=trim($_POST['info']);
$value=trim($_POST['value']);
$type=$_POST['select'];
$dvalue=trim($_POST['values']);
phpSafe($info);
phpSafe($value);
phpSafe($dvalue);

if($type==3 && $dvalue=="")
{
?>
<span class="already"><br><br>&nbsp;&nbsp;Please fill all mandatory fields !</span>
<?php
echo "<a href=\"javascript:history.back(-1);\">Go Back</a><br><br>";
 include_once("admin.footer.inc.php");
 exit (0);
}
$information="{".str_replace(" ","",strtoupper($info))."}";
if($info=="")
{
?> <br><span class="already">Go back and fill all mandatory fields</span>
<?php
echo "&nbsp;&nbsp;<a href=\"javascript:history.back(-1);\">Go Back</a><br><br>";

}
else if(strtolower(trim($info))=='name')
{
?>
<span class="already"><br><br>&nbsp;&nbsp;ERROR!!! Field NAME Exists!!! </span><?php 
 echo "<a href=\"javascript:history.back(-1);\">Go Back</a><br><br>";
 include_once("admin.footer.inc.php");
 exit (0);
}
else
{
if($mysql->total("".$table_prefix."extra_personal_info","fieldname='$info'")==0)
{
mysql_query("insert into ".$table_prefix."extra_personal_info values('','$info','$value','$information','$type','$dvalue')");
if($log_enabled==1)
{
mysql_query("insert into ".$table_prefix."admin_log_info values('','0','Extra field created:".$info."','".time()."','$CST_MLM_EXTRAPARAM')");
}
?>
<center><a href="add_personal_info.php">Add New Extra Field</a> | <a href="manage_personal_info.php">Manage Extra Fields</a></center>
<span class="inserted">&nbsp;&nbsp;<br>&nbsp;&nbsp;New extra field has been just added successfully!</span><br><br><?php 
}
else
{
 ?><span class="already"><br><br>&nbsp;&nbsp;ERROR!!! Field NAME Exists!!! </span><?php 
 echo "<a href=\"javascript:history.back(-1);\">Go Back</a><br><br>";
}
}
?>

<?php include_once("admin.footer.inc.php"); ?>