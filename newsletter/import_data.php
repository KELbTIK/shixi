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

<link href="style.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
.style3 {color: #666666}
-->
</style>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center"><a href="importfromfile.php">Import from CSV</a> | <a href="import_data.php">Import from IEF</a></td>
  </tr>
</table>

<table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <th width="2%" scope="row">&nbsp;</th>
    <td width="78%">&nbsp;</td>
    <td width="20%">&nbsp;</td>
  </tr>
  <tr>
    <th height="19" align="left" scope="row">&nbsp;</th>
    <td><span class="inserted">Import Data From Your IEF File </span></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <th scope="row">&nbsp;</th>
    <td><span class="style3">Please select your IEF file and click the Proceed botton. </span></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <th scope="row">&nbsp;</th>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <th scope="row">&nbsp;</th>
    <td><form action="import_data_middle_page.php" method="post" enctype="multipart/form-data" name="form1">
      <input name="file" type="file" size="40">
      <br>
      <br>
      <input type="submit" name="Submit" value="Proceed !">    
        </form></td>
    <td>&nbsp;</td>
  </tr>
</table>

<?php
include_once("admin.footer.inc.php");
?>
