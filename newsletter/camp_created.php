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
	 ?>



<table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center"><a href="createcamp.php"> New Email Campaign</a> | <a href="managecamp.php?action=all">All Email Campaigns</a> | <a href="managecamp.php?action=active">Active Email Campaigns</a> | <a href="managecamp.php?action=inactive">Inactive Email Campaigns</a></td>
  </tr>
</table>
<table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center"><a href="createcamp.php"> </a></td>
  </tr>
  <tr>
    <td align="center">&nbsp;</td>
  </tr>
  <tr>
    <td align="center"><strong>1. Email Details </strong><b>&raquo</b> 2. Attach Files <b>&raquo</b> 3. Preview Campaign <b>&raquo</b> 4. Activate Campaign </td>
  </tr>
</table>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="10%" rowspan="3">&nbsp;</td>
    <td width="88%"><br><br>
      <span class="inserted">Email campaign has been successfully created.</span><br>
     The campaign is <span class="already">inactive</span> and you can manage it from the 'inactive campaign' area. <br>
     <br>     </td>
    <td width="2%" rowspan="3">&nbsp;</td>
  </tr>
  <tr>
    <td><h3><strong>Manage the Campaign Now </strong></h3></td>
  </tr>
  <tr>
    <td><a href="attach.php?id=<?php echo $id?>&new=yes" class="mainmenu"><strong>Attach Files </strong></a> | <a href="previewcampaign.php?id=<?php echo $id;?>&new=yes" class="mainmenu"><strong>Preview Campaign</strong></a> | <a href="editadst.php?id=<?php echo $id;?>&action=activate&new=yes" class="mainmenu">Activate Campaign</a> </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
<?php include("admin.footer.inc.php"); ?>