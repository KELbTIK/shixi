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
				 mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','$adminname attempted unauthorized access to modify  sub-admin details','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");
				 echo "<br><span class=\"already\">You don't have  access to this page</span>   <a href=\"javascript:history.back(-1);\">Go Back</a><br><br>";
				 include_once("admin.footer.inc.php");
				 exit(0);
				}
				
				
$id=$_REQUEST['id'];
phpSafe($id);
$email=$mysql->echo_one("select email from $table_prefix"."subadmin_details where id ='$id'");
	 if(""==$email )
	  {
	  echo "<br><span class=\"already\">Please select an administrator.<a href=\"javascript:history.back(-1);\">Go Back</a></span><br><br>";
	  include("admin.footer.inc.php");
	  exit(0);
	}

				
	 ?>
	<style type="text/css">
<!--
.style1 {color: #FF0000}
.style2 {
	font-size: 18px;
	color: #333333;
}
-->
    </style>
<table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center"><a href="create_new_sub_admin.php" >Create New  Administrator</a>&nbsp;|&nbsp; <a href="manage_sub_admins.php" >Manage  Administrators</a></td>
  </tr>
</table>
	
<form name="form1" method="post" action="edit_sub_admin_action.php">
  <table width="779"  border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td width="13%">&nbsp;</td>
      <td width="78%">&nbsp; </td>
      <td width="9%">&nbsp;</td>
    </tr>
    <tr align="center">
      <td height="35" colspan="3"><span class="styleTitle style2"> Edit Administrator Details! </span></td>
    </tr>
    <tr valign="top">
      <td height="25" colspan="3" align="center"><div align="center"></div>
          <p class="inserted"> <strong>All fields marked <span class="style1">*</span> are compulsory</strong></p></td>
    </tr>
    <tr>
      <td colspan="3"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="27%" height="17">&nbsp;</td>
            <td width="26%">&nbsp;</td>
            <td width="3%">&nbsp;</td>
            <td width="44%">&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>Username</td>
            <td>&nbsp;</td>
            <td><input name="id" type="hidden" value="<?php echo $id ;?>">
               <?php echo  $mysql->echo_one("select username from $table_prefix"."subadmin_details where id ='$id'"); ?> </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td> Email</td>
            <td>&nbsp;</td>
            <td><input name="email" type="text" id="email2" value="<?php echo $email ;?>">
                <strong><span class="style1">*</span></strong></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td><input type="submit" name="Submit" value="Update!"></td>
          </tr>
      </table></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td align="center"><strong>Note:</strong>You can later configure the access control for this administrator.</td>
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
