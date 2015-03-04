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

$id=$_REQUEST['id'];
if($id=="")
	$id=-1;
if(!isValidAccess($id,$CST_MLM_LIST,$table_prefix,$mysql))
{
	if($log_enabled==1)
	{
		$aid=getAdminId($mysql);
		$adminname=$mysql->echo_one("select username from  ".$table_prefix."subadmin_details where id=$aid");
		$entityname=$mysql->echo_one("select name from  ".$table_prefix."email_advt_category where id=$id");
		if($entityname!="")
			mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','$adminname attempted unauthorized access to edit the list $entityname','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");
			

	}
	include_once("admin.header.inc.php");
	?>
	<br><span class="already">&nbsp;&nbsp;You dont have access to this list.&nbsp;&nbsp;<a href="javascript:history.back(-1);">Go Back</a></span><br><br>
	<?php
	include_once("admin.footer.inc.php");
	exit(0);
}
$result=mysql_query("select * from ".$table_prefix."email_advt_category where id=$id");
$row=mysql_fetch_row($result);
include_once("admin.header.inc.php");
?><table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center"><a href="category_addnew.php">Create new List</a>&nbsp;| <a href="category_viewall.php">Manage all Lists</a>&nbsp;| <a href="configurehtml.php">Subscribe HTML Code </a>| <a href="phpcodesub.php">Automatic Subscribtion PHP Code</a>  </td>
  </tr>
</table>
<form name="form1" method="post" action="category_edited.php?existingname=<?php echo $row[1];?>">
  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><input name="id" type="hidden" id="id" value="<?php echo $row[0]; ?>">
        <span class="inserted">Edit an Email List <br>
        </span> <span class="info">You can use whitespaces in the name of the list (eg : 'weekly news updates')</span></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td> &nbsp;List Name : 
        <input name="category" type="text" id="category" value="<?php echo $row[1]; ?>">
      <input type="submit" name="Submit" value="Edit ! "></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  </table>
</form>

<?php include_once("admin.footer.inc.php"); ?>