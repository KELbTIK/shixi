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

include_once("admin.header.inc.php");?>
<table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center"><a href="category_addnew.php">Create new List</a>&nbsp;| <a href="category_viewall.php">Manage all Lists</a>&nbsp;| <a href="configurehtml.php">Subscribe HTML Code </a> </td>
  </tr>
</table>
<form name="form1" method="post" action="category_added.php">

<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="3%">&nbsp;</td>
    <td width="95%">&nbsp;</td>
    <td width="2%">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><span class="inserted">Create a new email list <br>
    </span> <span class="info">You can use whitespaces in the name of the list (eg : 'weekly news updates')</span></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td> Name of the list:
        <input name="category" type="text" id="category">
        <input type="submit" name="Submit" value="Create List!"></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table></form>
<br>
<span class="inserted">&nbsp;&nbsp; &nbsp;&nbsp;All email lists are shown below</span> <br>
<br><table width="95%" align="center">
<?php
$result=mysql_query("select * from ".$table_prefix."email_advt_category");
while($row=mysql_fetch_row($result))
{
echo "<tr><td><strong> $row[1] </strong> &nbsp;&nbsp;&nbsp;</td><td><a href=\"category_editname.php?id=$row[0]\">Edit</a> &nbsp;&nbsp;&nbsp;</td><td><a href=\"category_delete.php?id=$row[0]\">Delete</a> &nbsp;&nbsp;&nbsp;</td><td>  <a href=\"viewems.php?cid=$row[0]\">View all E-Mails in this List</a> </td><td>&nbsp;&nbsp;&nbsp;<a href=\"getsubhtml.php?cid=$row[0]\">Subscribe to '$row[1]' HTML code</a></td></tr>";
}?></table>
<?php
include_once("admin.footer.inc.php");
?>
