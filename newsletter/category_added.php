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

include_once("admin.header.inc.php");?>
<table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center"><a href="category_addnew.php">Create new List</a>&nbsp;| <a href="category_viewall.php">Manage all Lists</a>&nbsp;| <a href="configurehtml.php">Subscribe HTML Code </a>| <a href="phpcodesub.php">Automatic Subscribtion PHP Code</a> </td>
  </tr>
</table>
<?php
$cat=trim($_POST['category']);
phpSafe($cat);		
if($cat!=="")
{
	if($mysql->total("".$table_prefix."email_advt_category","name='$cat'")==0)
	{
		mysql_query("insert into ".$table_prefix."email_advt_category values('','$cat','".time()."')");
		$aid=0;
		if(isset($_COOKIE['inout_sub_admin']))
		{
		    $aid=getAdminId($mysql);
			$id=$mysql->echo_one("select id from ".$table_prefix."email_advt_category  where name='$cat'");
			$uid=getAdminId($mysql);
			mysql_query("insert into ".$table_prefix."admin_access_control values('','$uid','$id')");
		}
		if($log_enabled==1)
		{
		mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','List created:".$cat."','".time()."','$CST_MLM_LIST')");
		}
		echo "&nbsp;&nbsp;<br><br>&nbsp;&nbsp;<span class=\"inserted\">New email list '$cat' has been added successfully.</span><br><br>";
		include_once("admin.footer.inc.php");
		exit(0);
	}
	else
		echo "<br><br>&nbsp;&nbsp;<span class=\"already\">ERROR!!! Email List Exists!!!&nbsp;<a href=\"javascript:history.back(-1);\">Go Back</a></span><br><br>";
}
else
	echo "<br><span class=\"already\">Provide a list name.&nbsp;<a href=\"javascript:history.back(-1);\">Go Back</a></span><br><br>";
include_once("admin.footer.inc.php");
?>