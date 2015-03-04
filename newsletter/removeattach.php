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

error_reporting(0);
$id=$_REQUEST['cid'];
$aid=$_REQUEST['id'];
//echo $aid;
//mkdir("attachments/$id/");
include("admin.header.inc.php");?>

<table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center"><a href="createcamp.php">New Email Campaign</a> | <a href="managecamp.php?action=all">All Email Campaigns</a> | <a href="managecamp.php?action=active">Active Email Campaigns</a> | <a href="managecamp.php?action=inactive">Inactive Email Campaigns</a></td>
  </tr>
</table>
<?php if(isset($_REQUEST['new'])){?>
<table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center"><a href="createcamp.php"> </a></td>
  </tr>
  <tr>
    <td align="center">&nbsp;</td>
  </tr>
  <tr>
    <td align="center">1. Email Details <b>&raquo</b> <strong>2. Attach Files</strong> <b>&raquo</b> 3. Preview Campaign <b>&raquo</b> 4. Activate Campaign </td>
  </tr>
</table>
<?php }?><?php
$f=$mysql->echo_one("select name  from ".$table_prefix."ea_attachments where id=$aid");
//echo $f;
unlink("attachments/$id/$f");
mysql_query("delete from ".$table_prefix."ea_attachments where id=$aid");
echo "<br><br> <div align=\"center\"> Selected file [$f] removed successfully.</div>";

?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="12%">&nbsp;</td>
    <td width="85%">&nbsp;</td>
    <td width="3%">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><?php if($script_mode=="demo") {?>
      <span class="red_unsub">In demo version you can attach Text, JPEG or GIF files only.</span>&nbsp;
      <?php } ?>      
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td><strong>Attached Files </strong></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td><?php if($mysql->total("".$table_prefix."ea_attachments","cid=$id")==0) echo "No file attached to this campaign. Please select file(s) below to attach."; ?></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr><?php $result=mysql_query("select * from ".$table_prefix."ea_attachments where cid=$id"); ?>
       <?php while($row=mysql_fetch_row($result)){?> <tr>
          <td colspan="2"><?php echo $row[2]?>
&nbsp;&nbsp;&nbsp; <a href="removeattach.php?id=<?php echo $row[0]?>&cid=<?php echo $row[1]?><?php if(isset($_REQUEST['new'])) echo "&new=yes"; ?>">Remove</a></td>
          <td>&nbsp;</td>
        </tr><?php } ?>
    </table></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><form action="attachfiles.php<?php if(isset($_REQUEST['new'])) echo "?new=yes"; ?>" method="post" enctype="multipart/form-data" name="form1">
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td colspan="2">&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td colspan="2"><strong>Attach Files </strong></td>
          <td width="3%">&nbsp;</td>
        </tr>
        <tr>
          <td width="21%">&nbsp;</td>
          <td width="76%">&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>Attach File 1 </td>
          <td><input name="file" type="file" size="50"></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>Attach File 2 </td>
          <td><input name="file2" type="file" size="50"></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>Attach File 3            </td>
          <td><input name="file3" type="file" size="50"></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>Attach File 4 </td>
          <td><input name="file4" type="file" size="50"></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>Attach File 5 </td>
          <td><input name="file5" type="file" size="50"></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td><input name="id" type="hidden" id="id" value="<?php echo $id?>"></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td><input type="submit" name="Submit" value="Attach to Campaign ! "></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
      </table>
    </form></td>
    <td>&nbsp;</td>
  </tr>
</table>


<?php if(isset($_REQUEST['new'])){?>
<table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center">&nbsp;</td>
    <td align="left">&nbsp;</td>
    <td align="center">&nbsp;</td>
  </tr>
  <tr>
    <td align="center"><a href="createcamp.php"> </a></td>
    <td align="left"><h3><strong>Finished attaching files? </strong></h3></td>
    <td align="center">&nbsp;</td>
  </tr>
  <tr>
    <td width="12%" align="center"><br>      <br></td>
    <td width="85%" align="left">      <a href="previewcampaign.php?id=<?php echo $id;?>&new=yes" class="mainmenu"><strong>Preview Campaign</strong></a> | <a href="editadst.php?id=<?php echo $id;?>&action=activate&new=yes" class="mainmenu">Activate Campaign</a> </td>
    <td width="3%" align="center">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3" align="center">&nbsp;</td>
  </tr>
</table>
<?php }?><?php include("admin.footer.inc.php"); ?>