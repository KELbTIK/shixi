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
header("Location:index.php");
exit(0);
}
$inout_username=$_COOKIE['admin'];
$inout_password=$_COOKIE['inout_pass'];
if(isset($_COOKIE['inout_sub_admin']))
{
	$usercount=$mysql->total($table_prefix."subadmin_details","username='$inout_username' and password='$inout_password' and status=1");
	if(0==$usercount)
	{
		header("Location:index.php");
		exit(0);
	}
}
else if(!(($inout_username==md5($username)) && ($inout_password==md5($password))))
{
	header("Location:index.php");
	exit(0);
}

	 include("admin.header.inc.php");

	 if(isset($_COOKIE['inout_sub_admin']))
				{
				   $aid=getAdminId($mysql);
				   $adminname=$mysql->echo_one("select username from  ".$table_prefix."subadmin_details where id=$aid");
				 mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','$adminname attempted unauthorized access to view sub-admins','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");
				 echo "<br><span class=\"already\">You don't have  access to this page</span>   <a href=\"javascript:history.back(-1);\">Go Back</a><br><br>";
				 include_once("admin.footer.inc.php");
				 exit(0);
				}


	 $pageno=1;
if(isset($_REQUEST['page']))
$pageno=$_REQUEST['page'];
$perpagesize = 10;
$result=mysql_query("select * from ".$table_prefix."subadmin_details order by id DESC LIMIT ".(($pageno-1)*$perpagesize).", ".$perpagesize);
$total=$mysql->echo_one("select count(*) from ".$table_prefix."subadmin_details");
	if($total==0)
	{
	echo "<br>- No record to display -<br><br>";
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
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
    <td height="34" colspan="6" valign="bottom"><span class="inserted">Administrator details  are listed below. The administrators which are highlighted in red are blocked.</span> </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td height="19" colspan="6">&nbsp;</td>
  </tr>
  <tr>
    <td width="1%">&nbsp;</td>
    <td colspan="3" ><?php if($total>=1) {?>
      Showing Administrators <span class="inserted"><?php echo ($pageno-1)*$perpagesize+1; ?></span> - <span class="inserted">
      <?php if((($pageno)*$perpagesize)>$total) echo $total; else echo ($pageno)*$perpagesize; ?>
      </span>&nbsp;of <span class="inserted"><?php echo $total; ?></span>&nbsp;
      <?php } ?>
&nbsp;&nbsp; </td>
    <td colspan="3"><?php echo $paging->page($total,$perpagesize,"","manage_sub_admins.php"); ?></td>
  </tr>
   <tr>
    <td>&nbsp;</td>
    <td  colspan="6">&nbsp;</td>
  </tr>
  <tr bgcolor="#CCCCCC">
    <td width="1%">&nbsp;</td>
    <td width="20%" height="30" align="left"><strong>Administrator Name</strong></td>
    <td width="28%"><strong>Email</strong></td>
    <td colspan="4"><strong>Action</strong></td>
  </tr>
  <?php

$single=0;

while($row=mysql_fetch_row($result))
{
?>
  <tr <?php if($row[4]!=1) echo 'bgcolor="#F8B9AF"';?> <?php if(($single%2)==0) { ?>bgcolor="#EFEFEF"<?php }?>>
    <td style="border-bottom:1px solid #CCCCCC; ">&nbsp;</td>
    <td height="25" align="left" style="border-bottom:1px solid #CCCCCC; "><?php echo $row[1];?></td>
    <td style="border-bottom:1px solid #CCCCCC; "><?php echo $row[3];?></td>
    <td colspan="2" style="border-bottom:1px solid #CCCCCC; "><a href="edit-admin-details.php?id=<?php echo "$row[0]";?>">Change Email</a> | <?php if($row[4]==1) echo '<a href="change-admin-status.php?action=block&id='.$row[0].'">Block</a>';  else echo '<a href="change-admin-status.php?action=activate&id='.$row[0].'">Activate</a>';?></td>
    <td width="15%" style="border-bottom:1px solid #CCCCCC; "><a href="sub-admin-resetpass.php?id=<?php echo "$row[0]";?>">Reset Password</a></td>
    <td width="13%" style="border-bottom:1px solid #CCCCCC; "><a href="sub-admin-access.php?id=<?php echo "$row[0]";?>">Access Control </a></td>
  </tr>
  <?php
$single+=1;
}

?>
  <tr>
    <td>&nbsp;</td>
    <td colspan="3" >&nbsp; </td>
    <td colspan="3">&nbsp;</td>
  </tr>
  <tr>
    <td width="1%">&nbsp;</td>
    <td colspan="3" ><?php if($total>=1) {?>
      Showing Advertisers <span class="inserted"><?php echo ($pageno-1)*$perpagesize+1; ?></span> - <span class="inserted">
      <?php if((($pageno)*$perpagesize)>$total) echo $total; else echo ($pageno)*$perpagesize; ?>
      </span>&nbsp;of <span class="inserted"><?php echo $total; ?></span>&nbsp;
      <?php } ?>
&nbsp;&nbsp; </td>
    <td colspan="3"><?php echo $paging->page($total,$perpagesize,"","manage_sub_admins.php"); ?></td>
  </tr>
</table>
<?php include_once("admin.footer.inc.php"); ?>
