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
$categId=-1;
if(isset($_REQUEST['cid']))
	$categId=$_REQUEST['cid'];

?><?php include("admin.header.inc.php"); ?>
<?php 
$param=$_REQUEST['param'];
$act="";
if($_REQUEST['action']!="edit")
	$act="edit";
if($_REQUEST['action']=="delete")
	$act="delete";
if($_REQUEST['action']=="unsub")
	$act="unsubscribe";
	
$id=$_REQUEST['id'];
if($id=="")
	$id=-1;
if(!isValidEmailAccess($id,$table_prefix,$mysql))
{
	if($log_enabled==1)
	{
		$aid=getAdminId($mysql);
		$adminname=$mysql->echo_one("select username from  ".$table_prefix."subadmin_details where id=$aid");
		$entityname=$mysql->echo_one("select email from  ".$table_prefix."email_advt where id=$id");
		if($entityname!="")
			mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','$adminname attempted unauthorized access to $act the email $entityname','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");

	}
	?>
	<br><span class="already">&nbsp;&nbsp;You dont have access to this email.&nbsp;&nbsp;<a href="javascript:history.back(-1);">Go Back</a></span><br><br>
	<?php
	include_once("admin.footer.inc.php");
	exit(0);
}

if($_REQUEST['action']!="edit")
{
if($_REQUEST['action']=="delete")
{
$name= $mysql->echo_one("select email from ".$table_prefix."email_advt where id=$id");
$sql="delete from ".$table_prefix."ea_em_n_cat where eid=$id";
mysql_query($sql);
$sql="delete from ".$table_prefix."email_advt where id=$id";
mysql_query($sql);
$sql="delete from ".$table_prefix."ea_extraparam where eid=$id";
$sql1="delete from ".$table_prefix."bounce_mail_details where eid=$id";
mysql_query($sql1);
$str="Email address has been deleted successfully!"; 
if($log_enabled==1)
		{
		mysql_query("insert into ".$table_prefix."admin_log_info values('','0','Email deleted :".$name."','".time()."','$CST_MLM_EMAIL')");
		}
}
if($_REQUEST['action']=="unsub")
{
$sql="update ".$table_prefix."email_advt set unsubstatus=1 where id=$id";
mysql_query("update ".$table_prefix."ea_em_n_cat set unsubstatus=1 where eid=$id");
$str="Email address has been successfully marked as unsubscribed!&nbsp;&nbsp;";
$name= $mysql->echo_one("select email from ".$table_prefix."email_advt where id=$id");
$sql1="delete from ".$table_prefix."bounce_mail_details where eid=$id";
mysql_query($sql1);




if($log_enabled==1)
		{
		mysql_query("insert into ".$table_prefix."admin_log_info values('','0','Email unsubscribed :".$name."','".time()."','$CST_MLM_EMAIL')");
		}
}

mysql_query($sql);

?>
<?php echo $str; ?> 
<a href="<?php echo "http://".$_SERVER['HTTP_HOST'].$param;?>">View All Emails</a><br><br>
<?php include("admin.footer.inc.php");
}
else
{
$id=$_REQUEST['id'];
$result=mysql_query("select * from ".$table_prefix."email_advt where id=$id");
$row=mysql_fetch_row($result);?>
<style type="text/css">
<!--
.style4 {color: #FF0000}
-->
</style>
 <table width="100%"  border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td><span class="inserted">Edit Details </span></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td><span class="info">The current details are shown below. Please edit and click on 'Edit' button. </span></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
 </table>
 <form name="form1" method="post" action="emedited.php<?php if($categId!=-1) echo "?cid=$categId"?>">
 <table width="100%"  border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td>&nbsp;Email<input name="id" type="hidden" id="id" value="<?php echo $row[0]; ?>"><input name="param" type="hidden" id="param" value="<?php echo $param; ?>">
          </td>
      <td>&nbsp;
		  <input name="email" type="text" id="email" value="<?php echo $row[1]; ?>"style="width:255px;"><strong><span class="style4">*</span></strong>
          </td>
    </tr>
    <tr>
  <td colspan ="2"><br></td>
    </tr>
	<tr>
      <td >&nbsp;Name</td>
      <td >&nbsp;
	  <input name="name" type="text" id="name" value="<?php echo $mysql->echo_one("select value  from ".$table_prefix."ea_extraparam where eid = '$id' and name='name'"); ?>"style="width:255px;">
	  </td>
    </tr>
    <tr>
      <td colspan ="2"><br></td>
    </tr>
    <?php 
	$extrafields = mysql_query("select * from ".$table_prefix."extra_personal_info order by id ");
	while($fielddetails=mysql_fetch_row($extrafields))
	{?>
    <tr>
      <td>&nbsp;<?php echo $fielddetails[1]; ?></td>
      <td>&nbsp;
	  <?PHP if($fielddetails[4]==1) {?><input name="<?php echo "extra_personal_info".$fielddetails[0]; ?>" type="text" id="<?php echo "extra_personal_info".$fielddetails[0]; ?>" value="<?php echo $mysql->echo_one("select value from ".$table_prefix."ea_extraparam where eid = '$id' and name='$fielddetails[1]'"); ?>" style="width:255px;"><?PHP } else if($fielddetails[4]==2) {?><textarea name="<?php echo "extra_personal_info".$fielddetails[0]; ?>"  id="<?php echo "extra_personal_info".$fielddetails[0]; ?>" style="width:255px;" rows="5"><?php echo $mysql->echo_one("select value from ".$table_prefix."ea_extraparam where eid = '$id' and name='$fielddetails[1]'"); ?></textarea><?php } else {?> <select name="<?php echo "extra_personal_info".$fielddetails[0]; ?>"  id="<?php echo "extra_personal_info".$fielddetails[0]; ?>" style="width:150px;">
	  <?php  $options=explode(",",$fielddetails[5]);
		  $str="";
		  $selected=$mysql->echo_one("select value from ".$table_prefix."ea_extraparam where eid = '$id' and name='$fielddetails[1]'");
				 for($k=0;$k<count($options);$k++)
				 {
					
					  if($selected==$options[$k])
					  $str.='<option value="'.$options[$k].'" selected>'.$options[$k].'</option>';
					 else
					  $str.='<option value="'.$options[$k].'">'.$options[$k].'</option>';
					
				 }
				 echo $str;?></select><?php }?>
	 
</td>
    </tr>
    <tr>
      <td colspan ="2"><br></td>
    </tr>
    <?php 
	}
	?>
	<tr>
	<td></td>
	<td>&nbsp;&nbsp;<input type="submit" name="Submit" value="Update ! "></td>
	</tr>
  </table>
</form>
<?php  include("admin.footer.inc.php"); 
}?>
