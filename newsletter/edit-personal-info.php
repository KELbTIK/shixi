<script language="javascript" type="text/javascript">
function showFieldTypeOptions()
{
val=document.getElementById('select').value;

	if(val==3)
	{
		document.getElementById('dropdowntable').rows["dropdownrow1"].style.display="";
		//document.getElementById('dropdowntable').rows["dropdownrow2"].style.display="none";
		
	 
	}
	else
	{
		document.getElementById('dropdowntable').rows["dropdownrow1"].style.display="none";
		document.getElementById('dropdowntable').rows["dropdownrow2"].style.display="";
	}
	
}
</script>
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

include("admin.header.inc.php");
if(isset($_COOKIE['inout_sub_admin']))
				{
				   $aid=getAdminId($mysql);
				   $adminname=$mysql->echo_one("select username from  ".$table_prefix."subadmin_details where id=$aid");
				 mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','$adminname attempted unauthorized access to edit extra parameter','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");
				 echo "<br><span class=\"already\">You don't have  access to this page</span>   <a href=\"javascript:history.back(-1);\">Go Back</a><br><br>";
				 include_once("admin.footer.inc.php");
				 exit(0);
				}
?>
<center><a href="add_personal_info.php">Add New Extra Field</a> | <a href="manage_personal_info.php">Manage Extra Fields</a></center>
<?php
$fieldid=$_REQUEST['id'];
$fieldname=$mysql->echo_one("select fieldname from ".$table_prefix."extra_personal_info where id='$fieldid'");
$confirm=$_REQUEST['confirm'];
if($mysql->total($table_prefix."ea_extraparam","name='$fieldname'")>0 && $confirm=="false")
{
	?>
	<span class="already">&nbsp;&nbsp;<br>The field named <?php echo " '".$fieldname."' "; ?> is already used for some emails.If you continue editing, the fieldname will be changed for those emails.</span>
	<a href="edit-personal-info.php?id=<?php echo $fieldid ?>&confirm=true">Continue Editing</a>&nbsp;&nbsp;
	<a href="javascript:history.back(-1);">Go Back</a><br><br>
	<?php
 	include_once("admin.footer.inc.php");
	exit(0);
}
$result=mysql_query("select * from ".$table_prefix."extra_personal_info where id='$fieldid'");
$row=mysql_fetch_row($result);
?>
<style type="text/css">
<!--
.style4 {color: #FF0000}
-->
</style>
<form name="form1" method="post" action="edit_personal_info_action.php?<?php echo "existingfldid=$row[0]";?>">
  <table width="100%"  border="0" cellspacing="0" cellpadding="0" id="dropdowntable">
    <tr align="left">
      <td colspan="3" scope="row"><span class="inserted"><br>Edit Existing  Extra Fields</span></td>
    </tr>
    <tr>
      <td height="26" colspan="2">Please Fill the Following Fields and Click the Below Button </td>
      <td width="15%">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="2"><span class="pagetable_activecell">All fields marked <span class="style4">*</span> are mandatory </span></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td width="48%">Info. field name&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>
      <td width="48%"><input name="info" type="text" id="info2" value="<?php echo $row[1];?>">
  <span class="style4">*</span>
  </td>
  <td>&nbsp;</td>
  </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Field Type </td>
      <td><?php if($row[4]==1)
	  {
	  ?><select name="select" id="select" style="width:175px;" onChange="javascript:showFieldTypeOptions();">
	  
	  <option value="1" selected>Text Field</option>
	  
	  	
		  	  <option value="3" >Dropdown List</option>
			  	 
    </select>
	<?php } if($row[4]==2) {?><select name="select" id="select" style="width:175px;" onChange="javascript:showFieldTypeOptions();">
	  
	  <option value="1" >Text Field</option>
	  
	  	
		  	  <option value="3" >Dropdown List</option>
			  	 
    </select>
	<?php } if($row[4]==3) {?><select name="select" id="select" style="width:175px;" onChange="javascript:showFieldTypeOptions();">
	  
	  <option value="1" >Text Field</option>
	  
	  	 
		  	  <option value="3" selected>Dropdown List</option>
			  	 
    </select><?php }?></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr id="dropdownrow1">
      <td>Comma separated values </td>
      <td><input type="text" name="values" id="values" style="width:175px" value="<?php echo $row[5];?>">
      <span class="style4">*</span>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr id="dropdownrow2">
    <td>Default Value &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>
    <td><input name="value" type="text" id="value" value="<?php echo $row[2];?>">
&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><input type="submit" name="Submit" value="Save Changes !"></td>
    <td>&nbsp;</td>
  </tr>
  </table>
</form>
<?php
include_once("admin.footer.inc.php");
?>
<script language="javascript" type="text/javascript">
 showFieldTypeOptions();
</script>