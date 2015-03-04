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
error_reporting(0);
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

$per=trim($_POST['per']);
$total=0;
$subject=trim($_POST['subject']);
$body=trim($_POST['body']);
$alt_body=trim($_POST['alt_body']);
$name=trim($_POST['name']);
$email=trim($_POST['email']);
$cid=$_POST['category'];
$emailtemplate=$_POST['emailtemplate'];
phpsafe($name);
phpsafe($email);
phpsafe($per);
if($_POST['ex_field']!="0")
	{
		$ex_field=trim($_POST['ex_field']);
		phpsafe($ex_field);
		$ext_condition=trim($_POST['ext_condition']);
		//phpsafe($ext_condition);
		$ext_text=trim($_POST['ext_text']);
		phpsafe($ext_text);
	}
	else
		{
		$ex_field="";
		$ext_condition="";
		$ext_text="";
		}
if($per=="" || $subject=="" || $body=="" || $name=="" || $email==""){
header("Location:goback.php?action=goback");
exit(0);
}
if(!is_valid_email($email))
{
 include("admin.header.inc.php");
echo "<br>The email you have entered is not valid.<br><br><a href=\"javascript:history.back(-1);\">Go Back</a> ";
include("admin.footer.inc.php");
exit(0);
}
$id=$_POST['id'];
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
			mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','$adminname attempted unauthorized access to edit the campaign $entityname(id:".$id.")','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");

	}
	include_once("admin.header.inc.php");
	?>
	<br><span class="already">&nbsp;&nbsp;You dont have access to this campaign.&nbsp;&nbsp;<a href="javascript:history.back(-1);">Go Back</a></span><br><br>
	<?php
	include_once("admin.footer.inc.php");
	exit(0);
}
$cname="";
if(isset($_POST['cname']))
{
$cname=$_POST['cname'];
phpSafe($cname);
}
//echo $cname;

			//phpSafe($subject);
if(!get_magic_quotes_gpc())
		{
			$subject=mysql_real_escape_string($subject);
		}
if(!get_magic_quotes_gpc())
		{
			$body=mysql_real_escape_string($body);
			$alt_body=mysql_real_escape_string($alt_body);
		}
		
$existingcname=$_REQUEST['existingname'];
if(mysql_query("UPDATE `".$table_prefix."email_advt_curr_run` set total=$total, emailsperrun=$per, sendername='$name', senderemail='$email', subject='$subject', body='$body', html=$_POST[html], cname='$cname',extra_field ='$ex_field',ex_condition='$ext_condition' ,ex_value='$ext_text',email_template='$emailtemplate',alt_body='$alt_body' where id=$id"))
{

if($cid!=0){
	//echo "update ".$table_prefix."ea_cnc set catid=$cid where campid=$id";
	$updated_rows=mysql_query("update ".$table_prefix."ea_cnc set catid=$cid where campid=$id");
	if(mysql_affected_rows($updated_rows)==0)
		mysql_query("insert into ".$table_prefix."ea_cnc values('','$id','$cid')");
	}
else
	{
	mysql_query("delete from ".$table_prefix."ea_cnc where campid=$id");
	}
$aid=0;
if(isset($_COOKIE['inout_sub_admin']))
{
$aid=getAdminId($mysql);

}
if($log_enabled==1)
		{
		mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','Campaign edited:".$existingcname."->".$cname."(".$id.")','".time()."','$CST_MLM_CAMPAIGN')");
		}
	if($_REQUEST['action']=="restart")
	{
		$sql="update ".$table_prefix."email_advt_curr_run set lastid=0, status=1,sent=0 where id=$id";
		mysql_query($sql);
		$logstr="insert into ".$table_prefix."admin_log_info values('','$aid','Campaign restarted:".$cam."(id:".$id.")','".time()."','$CST_MLM_CAMPAIGN')";
		//$str="Email campaign has been successfully restarted and activated. Emails are ready to be sent now!  &nbsp;&nbsp; <br><br><a href='managecamp.php?action=all'>View all Email Campaigns</a> "; 
	}
}
else{
header("Location:goback.php?action=interror");
exit(0);
}
?><?php include("admin.header.inc.php"); ?>
<table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center"><a href="createcamp.php">New Email Campaign</a> | <a href="managecamp.php?action=all">All Email Campaigns</a> | <a href="managecamp.php?action=active">Active Email Campaigns</a> | <a href="managecamp.php?action=inactive">Inactive Email Campaigns</a></td>
  </tr>
</table>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="center">Email campaign has been edited successfully. &nbsp;<a href="previewcampaign.php?id=<?php echo $id;?>">Preview Campaign</a><br></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
<?php include("admin.footer.inc.php"); ?>