<?php 

/*--------------------------------------------------+
|													 |
| Copyright © 2006 http://www.inoutscripts.com/      |
| All Rights Reserved.								 |
| Email: contact@inoutscripts.com                    |
|                                                    |
+---------------------------------------------------*/



?>
<?php
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

?>
<?php include("admin.header.inc.php"); ?><table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center"><a href="createcamp.php"> New Campaign</a> | <a href="managecamp.php?action=all">All Campaigns</a> | <a href="managecamp.php?action=active">Active Campaigns</a> | <a href="managecamp.php?action=pending">Pending Campaigns</a> | <a href="managecamp.php?action=inactive">Inactive Campaigns</a></td>
  </tr>
</table>
<?php
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
			mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','$adminname attempted unauthorized access to $str the campaign $entityname(id:".$id.")','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");

	}
	//include_once("admin.header.inc.php");
	?>
	<br><span class="already">&nbsp;&nbsp;You dont have access to this campaign.&nbsp;&nbsp;<a href="javascript:history.back(-1);">Go Back</a></span><br><br>
	<?php
	include_once("admin.footer.inc.php");
	exit(0);
}

?><table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="4%">&nbsp;</td>
    <td width="94%">&nbsp;</td>
    <td width="2%">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>You are about to <?php echo $action; ?> the selected email campaign. Are you sure you want to proceed? </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><form name="form1" method="post" action="editadst.php?id=<?php echo $id; ?>&action=<?php echo $action;  ?>">
	<?php $action{0}=strtoupper($action{0}); ?>
      <h1>
        <input type="submit" name="Submit" value="Yes. <?php echo $action; ?> !">
      </h1>
    </form>&nbsp;<?php if($str=="restart") { ?>
	<form name="form2" method="post" action="editad.php?id=<?php echo $id; ?>&action=<?php echo $action;  ?>">
	<input type="hidden" name="action" value="restart" />
	<?php $action{0}=strtoupper($action{0}); ?>
      <h1>
        <input type="submit" name="Submit" value="Edit and Restart !">
      </h1>
    </form>
	<?php } ?>
	</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>

<?php include("admin.footer.inc.php"); ?>
