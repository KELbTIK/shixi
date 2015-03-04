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


$id=$_REQUEST['id']?>

	<?php

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
	<?php
	$id=trim($_POST['id']);
$email=trim($_POST['email']);
phpSafe($id);		
phpSafe($email);		
if($id=="" || $email=="")
{
echo "<br><span class=\"already\">Please go back and check whether you fill all manadatory fields!</span><a href=\"javascript:history.back(-1);\">Go Back</a><br><br>"; 
include_once("admin.footer.inc.php");
exit(0);
}
		$valid=is_valid_email($email);
		if($valid==false)
		{
		echo "<br><span class=\"already\">Please enter valid email!</span><a href=\"javascript:history.back(-1);\">Go Back</a><br><br>"; 
include_once("admin.footer.inc.php");
exit(0);
		}

	mysql_query("update ".$table_prefix."subadmin_details set email='$email' where id='$id'");
	echo mysql_error();
    $msg.="<br>Administrator email has been updated successfully!<br><br>";
	if($log_enabled==1)
	{
	$username=$mysql->echo_one("select username from $table_prefix"."subadmin_details where id ='$id'");
	mysql_query("insert into ".$table_prefix."admin_log_info values('','0','Sub administrator email modified:".$username."','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");
	}
	?>
    <span class="inserted"><?php echo $msg;?> </span> <?php

?>
<?php include_once("admin.footer.inc.php"); ?>
