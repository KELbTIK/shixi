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

?>
<?php include("admin.header.inc.php");

$getListSql="select * from ".$table_prefix."email_advt_category order by name";
if(isset($_COOKIE['inout_sub_admin']))
{
	$subAdminId=getAdminId($mysql);
	$getListSql="SELECT a.*	FROM ".$table_prefix."email_advt_category a inner join 
	( SELECT eid FROM ".$table_prefix."admin_access_control where aid=$subAdminId ) b
	on a.id=b.eid order by a.name";
}

 ?>
<style type="text/css">
<!--
.style1 {color: #FF0000}
-->
</style>
<style type="text/css">
<!--
.style4 {color: #FF0000}
-->
</style>

<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center"><a href="importemails.php?type=single"> Single Email</a> | <a href="importemails.php?type=many"> Multiple Emails</a> | <a href="importemails.php?type=source">Extract from HTML</a> | <a href="emailadvturl.php">Extract from URLs</a> | <a href="importfromdb.php">Import from MySQL</a></td>
  </tr>
</table>
<br>

<form name="form1" method="post" action="extractems.php">
  <table width="100%"  border="0" cellspacing="0" cellpadding="0"> <tr>
    <td width="6%">&nbsp;</td>
    <td width="91%"><strong><?php if($script_mode=="demo"){
?><span class="style1">This feature is disabled in demo version due to security problems. </span><br><?php } ?>
      Select upto 3 Email Lists where you want to add the emails. You can add emails to more lists later. You should select atleast one list here. <br>
        <br>
    </strong>
      <select name="category">
        <option value="" selected>- Select a List - </option>
        <?php $result=mysql_query($getListSql);
	  while($row=mysql_fetch_row($result)){
	  echo '<option value="'.$row[0].'">'.$row[1].'</option>'; 
	  }?>
      </select>
<span class="style4">*</span> <br>
<select name="category2">
  <option value="" selected>- Select a List - </option>
  <?php $result=mysql_query($getListSql);
	  while($row=mysql_fetch_row($result)){
	  echo '<option value="'.$row[0].'">'.$row[1].'</option>'; 
	  }?>
</select>
<br>
<select name="category3" id="category3">
  <option value="" selected>- Select a List - </option>
  <?php $result=mysql_query($getListSql);
	  while($row=mysql_fetch_row($result)){
	  echo '<option value="'.$row[0].'">'.$row[1].'</option>'; 
	  }?>
</select>
<br></td>
    <td width="3%">&nbsp;</td>
  </tr>
    <tr>
      <td>&nbsp;</td>
      <td><strong><br>
        Extract Emails from Single/Multiple URLs </strong><br>
        <br>
        Enter the urls from where you want to extract emails. Place one URL in one line. <br>
      (It is recommended that you do not enter more than 100 urls on one extract. If you do so it may cause increased bandwidth in the time of extraction.)</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><textarea name="urls" cols="55" rows="15" id="urls"></textarea><span class="style4">*</span>
        <br>
        <input type="submit" name="Submit" value="Extract Emails from the URLs"></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  </table>
</form>

<?php include("admin.footer.inc.php"); ?>