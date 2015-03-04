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
				 mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','$adminname attempted unauthorized access to delete extra parameter','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");
				 echo "<br><span class=\"already\">You don't have  access to this page</span>   <a href=\"javascript:history.back(-1);\">Go Back</a><br><br>";
				 include_once("admin.footer.inc.php");
				 exit(0);
				}
?>
<center><a href="add_personal_info.php">Add New Extra Field</a> | <a href="manage_personal_info.php">Manage Extra Fields</a></center>
<?php
$fieldid=$_REQUEST['id'];
$fieldname=$mysql->echo_one("select fieldname from ".$table_prefix."extra_personal_info where id='$fieldid'");
$confirm=$_REQUEST['confirm'];
if($mysql->echo_one("select value from  ".$table_prefix."ea_extraparam where name='$fieldname' limit 0,1")!="" && $confirm=="false")
{
?>
<span class="already">&nbsp;&nbsp;<br>The field named <?php echo " '".$fieldname."' "; ?> is already used for some emails.If you continue deleting, the field will be removed from those emails.</span>
<a href="delete-personal-info.php?id=<?php echo $fieldid ?>&confirm=true">Continue Deleting</a>&nbsp;&nbsp;
<a href="javascript:history.back(-1);">Go Back</a><br><br>
<?php
}
else
{
if($script_mode=="demo" && !isset($_GET['force'])) 
{?>
	<span class="info">You cannot delete extra params as you are running online demo. </a></span><br><br>
<?php 
}
else
{
	mysql_query("delete from ".$table_prefix."extra_personal_info where  id= '$fieldid' ");
	mysql_query("delete from ".$table_prefix."ea_extraparam where  name= '$fieldname' ");
	if($log_enabled==1)
	{
	mysql_query("insert into ".$table_prefix."admin_log_info values('','0','Extra Field deleted:".$fieldname."','".time()."','$CST_MLM_EXTRAPARAM')");
	}
?>
<span class="inserted">&nbsp;&nbsp;<br>&nbsp;&nbsp;Extra Field has been deleted successfully.</span><br><br>
<?php 
}

}
include_once("admin.footer.inc.php"); ?>
