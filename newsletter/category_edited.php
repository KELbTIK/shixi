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
?>
<table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center"><a href="category_addnew.php">Create new List</a>&nbsp;| <a href="category_viewall.php">Manage all Lists</a>&nbsp;| <a href="configurehtml.php">Subscribe HTML Code </a>| <a href="phpcodesub.php">Automatic Subscribtion PHP Code</a>  </td>
  </tr>
</table>
<br><br>
<?php
$cat=trim($_POST['category']);
phpSafe($cat);		
$existingcatname =$_REQUEST['existingname'];
phpsafe($existingcatname);
if($cat=="") 
{
echo "<span class=\"already\">List name cannot be blank. &nbsp;&nbsp;<a href=\"javascript:history.back(-1);\">Go Back</a></span><br><br>";
include_once("admin.footer.inc.php"); 
exit(0);
}
$id=$_POST['id'];
if($id=="")
	$id=-1;
if(!isValidAccess($id,$CST_MLM_LIST,$table_prefix,$mysql))
{
	if($log_enabled==1)
	{
		$aid=getAdminId($mysql);
		$adminname=$mysql->echo_one("select username from  ".$table_prefix."subadmin_details where id=$aid");
		$entityname=$mysql->echo_one("select name from  ".$table_prefix."email_advt_category where id='$id'");
		if($entityname!="")
			mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','$adminname attempted unauthorized access to edit the list $entityname','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");

	}
	//include_once("admin.header.inc.php");
	?>
	<br><span class="already">&nbsp;&nbsp;You dont have access to this list.&nbsp;&nbsp;<a href="javascript:history.back(-1);">Go Back</a></span><br><br>
	<?php
	include_once("admin.footer.inc.php");
	exit(0);
}
if($mysql->total("".$table_prefix."email_advt_category","name <>'$existingcatname' AND name='$cat' ")==0)
{
$aid=0;
if(isset($_COOKIE['inout_sub_admin']))
{
$aid=getAdminId($mysql);
}
mysql_query("update ".$table_prefix."email_advt_category set name='$cat' where id=$id");
if($log_enabled==1)
{
mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','List edited:".$existingcatname."->".$cat."','".time()."','$CST_MLM_LIST')");
}
?>
<span class="inserted">&nbsp;&nbsp;List name edited successfully.&nbsp;<a href="category_viewall.php">View All Lists</a></span><br><br>
 <?php
 
}
else
{
echo "<span class=\"already\">List name already exists. &nbsp;&nbsp;<a href=\"javascript:history.back(-1);\">Go Back</a></span><br><br>";
}
include_once("admin.footer.inc.php"); ?>