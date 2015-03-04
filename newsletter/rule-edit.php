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
?><?php include("admin.header.inc.php");
	 if(isset($_COOKIE['inout_sub_admin']))
				{
				   $aid=getAdminId($mysql);
				   $adminname=$mysql->echo_one("select username from  ".$table_prefix."subadmin_details where id=$aid");
				 mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','$adminname attempted unauthorized access to edit bmh rule','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");
				 echo "<br><span class=\"already\">You don't have  access to this page</span>   <a href=\"javascript:history.back(-1);\">Go Back</a><br><br>";
				 include_once("admin.footer.inc.php");
				 exit(0);
				}

$id=$_GET['id'];
phpsafe($id);
$result=mysql_query("select * from ".$table_prefix."bmh_rules where id='$id'");
$row=mysql_fetch_row($result);
if(!isset($_GET['type']))
	$type="editor";
else
	$type=$_GET['type'];
?>
<table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center"><br />  <a href="create-rule.php"> Add New Rule</a> | <a href="manage-rules.php">Manage Rules </a> </td>
  </tr>
</table>
<form action="save-rule.php" method="post" enctype="multipart/form-data" name="form1" onsubmit="return checkNull();">
<input type="hidden" name="id" value="<?php echo $id; ?>">
<table width="100%" align="center" cellpadding="0" cellspacing="0">
<tr >
  <td height="22">&nbsp;</td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td></td>
</tr>
<tr bgcolor="#CCCCCC">
<td width="12%" height="22"><strong>Type</strong></td>
<td width="64%"><strong>Pattern</strong></td>
<td width="12%"><strong>Email Index </strong></td>
<td width="12%"> </td>
</tr>
<tr>
<td>&nbsp;</td>
<td></td>
<td> </td>
<td > </td>
</tr>
<tr>
<td><select name="bmh_type"><option value="1" <?php if($row[1]==1) echo "selected"; ?>>DSN</option>
<option value="2" <?php if($row[1]==2) echo "selected"; ?>>Body</option>
</select></td>
<td><input type="text" size="70" value="<?php echo $row[2];?>" name="bmh_rule"/></td>
<td> <input type="text"  value="<?php echo $row[3];?>" name="email_index"/></td>
<td > <input type="submit" name="submit" value="Save rule" /></td>
</tr>

<tr>
<td></td>
<td></td>
<td> </td>
<td > </td>
</tr>
</table>
</form>
<script language="javascript">
function checkNull()
	{
	if(document.getElementById('template_name').value=="")
		{
		alert("Please enter a template name");
		return false;
		}
	else
		return true;
	}
function getSelected()
	{
	var t1=document.getElementById("ex_field").options[document.getElementById("ex_field").selectedIndex].value;
	if(t1!=0)
		{
			//alert(t1);

		document.getElementById('t1').style.display="";
		}
	else
		{
		document.getElementById('t1').style.display="none";
		}
	}
</script>
<?php include("admin.footer.inc.php"); ?>