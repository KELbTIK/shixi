<?php 

/*--------------------------------------------------+
|													 |
| Copyright © 2006 http://www.inoutscripts.com/      |
| All Rights Reserved.								 |
| Email: contact@inoutscripts.com                    |
|                                                    |
+---------------------------------------------------*/



?><?php

$file="export_data";

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

include_once("admin.header.inc.php");?>
<style type="text/css">
<!--
.style3 {color: #FF0000}
.style4 {color: #666666}
-->
</style>

<link href="style.css" rel="stylesheet" type="text/css">
<table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>
   <td align="center"><a href="category_addnew.php">Create new List</a>&nbsp;| <a href="category_viewall.php">Manage all Lists</a>&nbsp;| <a href="configurehtml.php">Subscribe HTML Code </a>| <a href="phpcodesub.php">Automatic Subscribtion PHP Code</a>  </td>
  </tr>
</table>
<?php

	$getListSql="select * from ".$table_prefix."email_advt_category order by name";
	if(isset($_COOKIE['inout_sub_admin']))
	{
		$subAdminId=getAdminId($mysql);
		$getListSql="SELECT a.*	FROM ".$table_prefix."email_advt_category a inner join 
		( SELECT eid FROM ".$table_prefix."admin_access_control where aid=$subAdminId ) b
		on a.id=b.eid order by a.name";
	}

	$result=mysql_query($getListSql);
	
if(mysql_num_rows($result)==0)
{
	echo "<br>-No Email Lists Found-<br><br>";
	include_once("admin.footer.inc.php");
	exit(0);
}
 $extrafields = mysql_query("select * from ".$table_prefix."extra_personal_info order by id ");

?>

<form name="form1" method="post" action="phpcodesub_action.php">
  <table width="100%"  border="0" cellpadding="0" cellspacing="0">
    <tr>
      <th width="49" scope="row">&nbsp;</th>
      <td colspan="2">&nbsp;</td>
      <td width="51">&nbsp;</td>
    </tr>
    <tr>
      <th height="26" scope="row">&nbsp;</th>
      <td colspan="2" class="inserted">Automatic subscription PHP code !!!</td>
      <td></td>
    </tr>
	 <tr>
	 <td>&nbsp;</td>
      <td colspan="2" >
	  Already have a form in your website containing email field ?
	  This feature enables you to automatically subsrcibe emails which are entered through that form to any desired list. </td>
	  <td>&nbsp;</td>
    </tr>
	<tr>
	  <td colspan="4"><br></td>
    </tr>
    <tr>
      <th scope="row">&nbsp;</th>
      <td colspan="2"><span class="pagetable_activecell">All fields marked <span class="style3">*</span> are mandatory </span></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <th scope="row">&nbsp;</th>
      <td colspan="2">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <th scope="row">&nbsp;</th>
      <td width="502">Enter your Email field name&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;          </td>
      <td width="823"><input name="email" type="text" id="email">
        <span class="style3">*<span class="style4">(Should not contain space) </span></span></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <th scope="row">&nbsp;</th>
      <td colspan="2">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <th scope="row">&nbsp;</th>
      <td>Enter Name field name&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;      </td>
      <td><input name="name" type="text" id="name2"> <span class="style3"><span class="style4">&nbsp; (Should not contain space) </span></span></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td scope="row">&nbsp;</td>
      <td colspan="2">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
	<?php 
 	 $extrafields = mysql_query("select * from ".$table_prefix."extra_personal_info order by id ");
	while($fielddetails=mysql_fetch_row($extrafields))
  	{
   	?>
	    <tr>
    		<th scope="row">&nbsp;</th>
      		<td>Enter <?php echo " ".$fielddetails[1]." "; ?> field name  </td>
      		<td><input name="<?php
			 echo "extra_personal_info".$fielddetails[0]; ?>" type="text" id="<?php echo "extra_personal_info".$fielddetails[0]; ?>"> <span class="style3"><span class="style4">&nbsp; (Should not contain space) </span></span></td>
      		<td>&nbsp;</td>
	    </tr>
    	<tr>
	      <td scope="row">&nbsp;</td>
    	  <td colspan="2">&nbsp;</td>
	      <td>&nbsp;</td>
    	</tr>
	<?php
	}
	?>
    <tr>
      <td scope="row">&nbsp;</td>
      <td colspan="2">Please select the lists to which you want to add the email. </td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td scope="row">&nbsp;</td>
      <td colspan="2">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td scope="row">&nbsp;</td>
     <td height="30" colspan="2" valign="center" bgcolor="#CCCCCC"><strong>&nbsp; Email Lists</strong> (<a href="javascript:checkListsAll();">All</a> , <a href="javascript:uncheckListsAll();">None</a>) <span class="style3">*</span> </td>
      <td>&nbsp;</td>
    </tr>
	<?php 
	$i=0;
	while($row=mysql_fetch_row($result))
	{
	?>
    <tr>
	<td >&nbsp;</td>
      <td height="25" colspan="2" valign="center" style="border-bottom:1px solid #CCCCCC; "  <?php if($i%2==1){echo 'bgcolor="#EFEFEF"'; }?>><input name="<?php echo "List".$i; ?>" type="checkbox"  id="List<?php echo $i; ?>" value="<?php echo $row[0]; ?>">        <?php echo $row[1]; ?> </td>
      
      <td >&nbsp;</td>
    </tr>
    <?php $i+=1; 
	}
	?>
    <tr>
      <td scope="row">&nbsp;</td>
      <td colspan="2">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td height="35" align="right" valign="top"  >&nbsp;</td>
      <td height="35" align="left" valign="top"  ><input type="submit" name="Submit" value="Generate PHP Code !"></td>
      <td>&nbsp;</td>
    </tr>
  </table>
</form>
<?php
include_once("admin.footer.inc.php");
?>
