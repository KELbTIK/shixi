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
    <td align="center"><a href="importfromfile.php">Import from CSV</a> | <a href="import_data.php">Import from IEF</a></td>
  </tr>
</table>
<form action="extractems.php" method="post" enctype="multipart/form-data" name="form1">
  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td height="25" colspan="3">
	  	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
        	 <tr>
         		 <td>&nbsp;</td>
          		<td class="inserted">&nbsp;</td>
				<td>&nbsp;</td>
       		 </tr>
        	<tr>
          		<td width="6%">&nbsp;</td>
          		<td width="94%" class="inserted"><strong>Import Email Addresses from Files </strong></td>
				<td>&nbsp;</td>
        	</tr>
      	</table>
		
	  </td>
      <td width="4%">&nbsp;</td>
    </tr>
    <tr>
      <td width="6%">&nbsp;</td>
      <td width="90%">&nbsp;</td>
      <td width="0%">&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><strong>Select upto 3 Email Lists where you want to add the emails. You can add emails to more lists later. You should select atleast one list here.<br>
      </strong>
        <br>
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
<input name="file" type="hidden" id="file" value="yes">
<br>
<select name="category3" id="category3">
  <option value="" selected>- Select a List - </option>
  <?php $result=mysql_query($getListSql);
	  while($row=mysql_fetch_row($result)){
	  echo '<option value="'.$row[0].'">'.$row[1].'</option>'; 
	  }?>
</select></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><strong>Select the File<br>
      </strong>        <input name="file" type="file" size="50">
      <span class="style4">*</span> <p>Please browse your csv or text based file and click extract. If the file contains extra fields, then it should be maintained in the following format <strong>email : name<?php 
	  		$extrafields = mysql_query("select * from ".$table_prefix."extra_personal_info order by id ");
			while($fielddetails=mysql_fetch_row($extrafields))
			{
			 echo " : ".$fielddetails[1];
			}
			?></strong><br>
	  <span class="already">Colon should not be used in extra field values .</span> Comma may be used instead of colon. <br>
	  	  You may use  new line as separator after each email.</p>     </td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><input type="submit" name="Submit" value="Extract Emails !"></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
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