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
if(isset($_COOKIE['inout_sub_admin']))
				{
				   $aid=getAdminId($mysql);
				   $adminname=$mysql->echo_one("select username from  ".$table_prefix."subadmin_details where id=$aid");
				 mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','$adminname attempted unauthorized access to manage  extra parameter','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");
				 echo "<br><span class=\"already\">You don't have  access to this page</span>   <a href=\"javascript:history.back(-1);\">Go Back</a><br><br>";
				 include_once("admin.footer.inc.php");
				 exit(0);
				}
?>
<style type="text/css">
<!--
.style1 {	color: #666666;
	font-weight: bold;
}
.style2 {color: #333333}
-->
</style>
  <table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td colspan="2" align="center" scope="row"><a href="add_personal_info.php">Add New Extra Field</a> | <a href="manage_personal_info.php">Manage Extra Fields</a></td>
    </tr>
    <tr>
      <td colspan="2" align="center" scope="row">&nbsp;</td>
    </tr>
  </table>
    <?php
		  $result=mysql_query("select * from ".$table_prefix."extra_personal_info order by id");
		  		  ?>
<form name="form1" method="post" action="">

	   <table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
      <td colspan="2" align="center" scope="row">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="2" align="left" scope="row"><span class="inserted">Extra fields and corresponding default values are listed below. You can edit/delete existing fields </span></td>
    </tr>
    <tr>
      <td colspan="2" scope="row">&nbsp;</td>
    </tr>
  </table>
  <table width="100%"  border="0" cellpadding="0" cellspacing="0">

    <tr bgcolor="#CCCCCC">
      <td width="23%" align="left" height="30"><strong>Field Name <br>
       </strong>
      <td width="23%" align="left" height="30"><strong>Default Value <br>
     </strong></td>
      <td width="17%" height="30">&nbsp;</td>
      <td width="12%" height="30">&nbsp;</td>
    </tr>
	<tr>
      <td height="25" align="left" style="border-bottom:1px solid #CCCCCC; ">Name</td>
       <td height="25" align="left" style="border-bottom:1px solid #CCCCCC; "><?php echo $defaultname;?></td>
      <td height="25" align="left" style="border-bottom:1px solid #CCCCCC; "><span class="style2"> -- </span></td>
      <td height="25" align="left" style="border-bottom:1px solid #CCCCCC; "><span class="style2"> -- </span></td>
    </tr>
    <?PHP
	$single=0;
		  while($row=mysql_fetch_row($result))
          {
		  ?>
    <tr <?php if(($single%2)==0) { ?>bgcolor="#EFEFEF"<?php }?>>
      <td height="25" align="left" style="border-bottom:1px solid #CCCCCC; "> <?php echo $row[1];?></td>
      <td height="25" align="left" style="border-bottom:1px solid #CCCCCC; "><?php echo $row[2];?></td>
      <td height="25" align="left" style="border-bottom:1px solid #CCCCCC; "><a href="edit-personal-info.php?id=<?php echo $row[0] ?>&confirm=false">Edit</a></td>
      <td height="25" align="left" style="border-bottom:1px solid #CCCCCC; "><a href="delete-personal-info.php?id=<?php echo $row[0] ?>&confirm=false">Delete</a></td>
	 
    </tr>
	 <?php
	$single+=1;
	 } ?>
  </table>
</form>
<?php include_once("admin.footer.inc.php"); ?>
