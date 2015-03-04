<?php 

/*--------------------------------------------------+
|													 |
| Copyright © 2006 http://www.inoutscripts.com/      |
| All Rights Reserved.								 |
| Email: contact@inoutscripts.com                    |
|                                                    |
+---------------------------------------------------*/



?>
<?php
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
.style4 {color: #FF0000}
-->
</style>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center"><a href="importemails.php?type=single"> Single Email</a> | <a href="importemails.php?type=many"> Multiple Emails</a> | <a href="importemails.php?type=source">Extract from HTML</a> | <a href="emailadvturl.php">Extract from URLs</a> | <a href="importfromdb.php">Import from MySQL</a></td>
  </tr>
</table>
<form name="form1" method="post" action="extractems.php">
  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td colspan="3">&nbsp;</td>
      <td width="4%">&nbsp;</td>
    </tr>
    <tr>
      <td><div align="center"></div></td>
      <td colspan="2"><strong><span class="inserted">Import Emails from MySQL Database </span></strong></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td colspan="3">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="2"><strong>Select upto 3 Email Lists where you want to add the emails. You can add emails to more lists later. You should select atleast one list here.</strong></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="2"><strong><br>
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
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td width="6%">&nbsp;</td>
      <td colspan="2">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="2"><strong>Provide Database Info. </strong></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="2">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td width="36%">Database Server </td>
      <td width="54%"><input name="server" type="text" id="server">
<span class="style4">*</span></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="2">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>Database Username </td>
      <td><input name="user" type="text" id="user2">
<span class="style4">*</span></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="2">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>Database Password </td>
      <td><input name="pass" type="text" id="pass2"></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="2">&nbsp;        </td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>Database Name </td>
      <td><input name="db" type="text" id="db2">
<span class="style4">*</span> </td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="2">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>Table Name </td>
      <td><input name="table" type="text" id="table2">
<span class="style4">*</span></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="2">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>Email Field  </td>
      <td><input name="fldem" type="text" id="fldem2">
<span class="style4">*</span></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="2">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>'Full Name' Field / 
      'First Name' Field </td>
      <td><input name="fldname" type="text" id="fldem"></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="2">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>'Second Name' Field </td>
      <td>
        <input name="secname" type="text" id="secname">
      In case you have first name and second name stored separately in the database </td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="2">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>

<?php 
  $extrafields = mysql_query("select * from ".$table_prefix."extra_personal_info order by id ");

while($fielddetails=mysql_fetch_row($extrafields))
  {
   ?>
   		<tr>
		<td>&nbsp;</td>
		 <td ><?php echo $fielddetails[1]." "; ?>Field</td> 
          <td ><input name="<?php echo "extra_personal_info".$fielddetails[0]; ?>" type="text" id="<?php echo "extra_personal_info".$fielddetails[0]; ?>" value="" ></td>
          <td >&nbsp;</td>
		  <td width="0%">&nbsp;</td>
        </tr> 
    <tr>
      <td>&nbsp;</td>
      <td colspan="2">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  <?php
   }
?>
    <tr>
      <td>&nbsp;</td>
      <td colspan="2">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="2"><input type="submit" name="Submit" value="Import Emails!"></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="2">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="2">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  </table>
</form>

<?php include("admin.footer.inc.php"); ?>