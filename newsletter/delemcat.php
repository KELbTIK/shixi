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
//$cat=$_POST['category'];
$eid=$_REQUEST['eid'];
$cid=$_REQUEST['cid'];
$param=$_REQUEST['param'];
if($cid=="")
	$cid=-1;
if(!isValidAccess($cid,$CST_MLM_LIST,$table_prefix,$mysql))
{
	if($log_enabled==1)
	{
		$aid=getAdminId($mysql);
		$adminname=$mysql->echo_one("select username from  ".$table_prefix."subadmin_details where id=$aid");
		$entityname=$mysql->echo_one("select name from  ".$table_prefix."email_advt_category where id='$cid'");
		if($entityname!="")
			mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','$adminname attempted unauthorized access to remove email from the list $entityname','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");

	}
	//include_once("admin.header.inc.php");
	?>
<link href="style.css" rel="stylesheet" type="text/css">

	<br><span class="already">&nbsp;&nbsp;You dont have access to this list.&nbsp;&nbsp;<a href="javascript:history.back(-1);">Go Back</a></span><br><br>
	<?php
	include_once("admin.footer.inc.php");
	exit(0);
}


mysql_query("DELETE from ".$table_prefix."ea_em_n_cat where  eid=$eid and cid=$cid");
$mail=$mysql->echo_one("select email from ".$table_prefix."email_advt where id='$eid'");
$cat=$mysql->echo_one("select name from ".$table_prefix."email_advt_category where id='$cid'");
$aid=0;
if(isset($_COOKIE['inout_sub_admin']))
{
$aid=getAdminId($mysql);
}
if($log_enabled==1)
		{
mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','".$mail." removed from the list ".$cat."','".time()."','$CST_MLM_LIST')");
}
//mysql_query("update category set name='$cat' where id=$id");
?><span class="inserted"><br>Email has been deleted from the selected category.</span>
<a href="<?php echo "http://".$_SERVER['HTTP_HOST'].$param;?>">View All Emails</a><br><br>


<?php include_once("admin.footer.inc.php"); ?>