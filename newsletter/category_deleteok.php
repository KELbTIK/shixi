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
			mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','$adminname attempted unauthorized access to delete the list $entityname','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");

	}
	include_once("admin.header.inc.php");
	?>
	<br><span class="already">&nbsp;&nbsp;You dont have access to this list.&nbsp;&nbsp;<a href="javascript:history.back(-1);">Go Back</a></span><br><br>
	<?php
	include_once("admin.footer.inc.php");
	exit(0);
}
include_once("admin.header.inc.php");
?><table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center"><a href="category_addnew.php">Create new List</a>&nbsp;| <a href="category_viewall.php">Manage all Lists</a>&nbsp;| <a href="configurehtml.php">Subscribe HTML Code </a>| <a href="phpcodesub.php">Automatic Subscribtion PHP Code</a> </td>
  </tr>
</table>
<br>
<?php
//echo $mysql->total($table_prefix."ea_cnc","catid=$id");
if(isset($_COOKIE['inout_sub_admin']))
{
	$aid=getAdminId($mysql);
	$cnt=$mysql->echo_one("select count(distinct(a.id)) from ".$table_prefix."campaign_access_control a inner join (select * from ".$table_prefix."ea_cnc where catid =$id ) b on a.cid=b.campid  where a.aid='$aid' "); 
	if($cnt!=0)
	{
		echo "<span class=\"already\"> $cnt campaign(s) is(are) fired on this list. You should delete those campaigns which are fired on this list in order to delete the email list. &nbsp;&nbsp;&nbsp;<a href=\"managecamp.php\">See all Campaigns</a></span><br><br>";
		include_once("admin.footer.inc.php");
		exit(0);
	}
}
else
{
	if($mysql->total($table_prefix."ea_cnc","catid=$id")!=0)
	{
		echo "<span class=\"already\">".$mysql->total($table_prefix."ea_cnc","catid=$id")." campaign(s) is(are) fired on this list. You should delete those campaigns which are fired on this list in order to delete the email list. &nbsp;&nbsp;&nbsp;<a href=\"managecamp.php\">See all Campaigns</a></span><br><br>";
		include_once("admin.footer.inc.php");
		exit(0);
	}
}
$aid=0;
if(isset($_COOKIE['inout_sub_admin']))
{
	$aid=getAdminId($mysql);
	$name=$mysql->echo_one("select username from ".$table_prefix."subadmin_details where id='$aid'");

	$cat=$mysql->echo_one("select name from ".$table_prefix."email_advt_category where id=$id");
	//mysql_query("delete from ".$table_prefix."email_advt_category where id=$id");
	//mysql_query("delete from ".$table_prefix."ea_em_n_cat where cid=$id");
	mysql_query("delete from ".$table_prefix."admin_access_control where eid=$id and aid=$aid");
	if($log_enabled==1)
	{
		mysql_query("insert into ".$table_prefix."admin_log_info values('',0,'$name removed self-access to the list ".$cat."','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");
	}

}
else
{
	$cat=$mysql->echo_one("select name from ".$table_prefix."email_advt_category where id=$id");
	mysql_query("delete from ".$table_prefix."email_advt_category where id=$id");
	mysql_query("delete from ".$table_prefix."ea_em_n_cat where cid=$id");
	mysql_query("delete from ".$table_prefix."admin_access_control where eid=$id");
	if($log_enabled==1)
	{
		mysql_query("insert into ".$table_prefix."admin_log_info values('',0,'List deleted:".$cat."','".time()."','$CST_MLM_LIST')");
	}
}
?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td>&nbsp;</td>
      <td>
        <span class="inserted">Email List  has been  deleted successfully. <a href="category_viewall.php">View All Lists</a>
      </span> </td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  </table>

<?php include_once("admin.footer.inc.php"); ?>