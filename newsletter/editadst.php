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

?><?php include("admin.header.inc.php"); ?>
<table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center"><a href="createcamp.php"> New Campaign</a> | <a href="managecamp.php?action=all">All Campaigns</a> | <a href="managecamp.php?action=active">Active Campaigns</a> | <a href="managecamp.php?action=pending">Pending Campaigns</a> | <a href="managecamp.php?action=inactive">Inactive Campaigns</a></td>
  </tr>
  <tr>
    <td align="center">&nbsp;</td>
  </tr>
</table>
<?php if(isset($_REQUEST['new'])){?>
<table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center">1. Email Details <b>&raquo</b> 2. Attach Files <b>&raquo</b> 3. Preview Campaign <b>&raquo</b> <strong>4. Activate Campaign </strong></td>
  </tr>
  <tr>
    <td align="center">&nbsp;</td>
  </tr>
  <tr>
    <td align="center">&nbsp;</td>
  </tr>
</table>
<?php }?>
<?php
$aid=0;
if(isset($_COOKIE['inout_sub_admin']))
{
$aid=getAdminId($mysql);
}
$str="";
$action=$_REQUEST['action']; 
if($action=="restart")
{
$str="restart";
}
else if($action=="inactivate")
{
$str="inactivate";
}
else if($action=="delete")
{
$str="delete";
}
else
{
$str="activate";
}
$id=$_REQUEST['id'];
if($id=="")
	$id=-1;
if(!isValidAccess($id,$CST_MLM_CAMPAIGN,$table_prefix,$mysql))
{
	if($log_enabled==1)
	{
		$aid=getAdminId($mysql);
		$adminname=$mysql->echo_one("select username from  ".$table_prefix."subadmin_details where id=$aid");
		$entityname=$mysql->echo_one("select cname from  ".$table_prefix."email_advt_curr_run where id=$id");
		if($entityname!="")
			mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','$adminname attempted unauthorized access to $str   the campaign $entityname(id:".$id.")','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");

	}
	//include_once("admin.header.inc.php");
	?>
	<br><span class="already">&nbsp;&nbsp;You dont have access to this campaign.&nbsp;&nbsp;<a href="javascript:history.back(-1);">Go Back</a></span><br><br>
	<?php
	include_once("admin.footer.inc.php");
	exit(0);
}
$cam=$mysql->echo_one("select cname from ".$table_prefix."email_advt_curr_run where id=$id");

if($_REQUEST['action']=="delete")
{
$sql="delete from ".$table_prefix."ea_cnc where campid=$id";
mysql_query($sql);
$sql="delete from ".$table_prefix."email_advt_curr_run where id=$id";
$logstr="insert into ".$table_prefix."admin_log_info values('','$aid','Campaign deleted:".$cam."(id:".$id.")','".time()."','$CST_MLM_CAMPAIGN')";
$str="Email campaign has been successfully deleted!&nbsp;&nbsp;<br><br><a href='managecamp.php?action=all'>View all Email Campaigns</a>"; 
}

if($_REQUEST['action']=="restart")
{
$sql="update ".$table_prefix."email_advt_curr_run set lastid=0, status=1,sent=0 where id=$id";
mysql_query($sql);
//$sql="update ".$table_prefix."email_advt_curr_run set status=1 where id=$id";
$logstr="insert into ".$table_prefix."admin_log_info values('','$aid','Campaign restarted:".$cam."(id:".$id.")','".time()."','$CST_MLM_CAMPAIGN')";
$str="Email campaign has been successfully restarted and activated. Emails are ready to be sent now!  &nbsp;&nbsp; <br><br><a href='managecamp.php?action=all'>View all Email Campaigns</a> "; 
}

if($_REQUEST['action']=="activate")
{
$sql="update ".$table_prefix."email_advt_curr_run set status=1 where id=$id";
$logstr="insert into ".$table_prefix."admin_log_info values('','$aid','Campaign activated:".$cam."(id:".$id.")','".time()."','$CST_MLM_CAMPAIGN')";
$str="<span class=\"inserted\">Email campaign has been successfully  activated! Emails are ready to be sent now.</span> &nbsp;&nbsp; <br><br><a href='managecamp.php?action=all'>View all Email Campaigns</a><br><br>"; 
}

if($_REQUEST['action']=="inactivate")
{
$sql="update ".$table_prefix."email_advt_curr_run set status=0 where id=$id";
$logstr="insert into ".$table_prefix."admin_log_info values('','$aid','Campaign inactivated:".$cam."(id:".$id.")','".time()."','$CST_MLM_CAMPAIGN')";
$str="Email  campaign has been successfully inactivated! No emails will be sent for this email campaign unless you Activate/Restart the email campaign again. &nbsp;&nbsp; <br><br><a href='managecamp.php?action=all'>View all Email Campaigns</a>"; 
}
mysql_query($sql);
if($_REQUEST['action']=="delete")
{
	mysql_query("delete from ".$table_prefix."campaign_access_control where cid=$id");
}
if($log_enabled==1)
{
	mysql_query($logstr);
}		

?>
<?php echo $str; ?><?php include("admin.footer.inc.php"); ?>