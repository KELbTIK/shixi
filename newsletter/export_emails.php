<?php 

/*--------------------------------------------------+
|													 |
| Copyright © 2006 http://www.inoutscripts.com/      |
| All Rights Reserved.								 |
| Email: contact@inoutscripts.com                    |
|                                                    |
+---------------------------------------------------*/



?><?php


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


<style type="text/css">
<!--
.style4 {color: #FF0000}
-->
</style>
<table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center"><a href="export_emails.php">Export
		as IEF</a> | <a href="export_emails_csv.php">Export as CSV</a></td>
  </tr>
</table>
<br>
<?php 
$getListSql="select * from ".$table_prefix."email_advt_category order by name";
if(isset($_COOKIE['inout_sub_admin']))
{
	$subAdminId=getAdminId($mysql);
	$getListSql="SELECT a.*	FROM ".$table_prefix."email_advt_category a inner join 
	( SELECT eid FROM ".$table_prefix."admin_access_control where aid=$subAdminId ) b
	on a.id=b.eid order by a.name";
}

$result=mysql_query($getListSql);
if(mysql_num_rows($result)==0)
{
	echo "<br>-No Email Lists Found-<br><br>";
	include_once("admin.footer.inc.php");
	exit(0);
}
$i=0;
//$result=mysql_query("select * from ".$table_prefix."email_advt_category");


?>
<form name="form1" method="post" action="export_data_run.php?id=el">
  <table width="99%" cellpadding="5" cellspacing="0">
   <tr>
     <td height="35" colspan="3" valign="top"><span class="inserted">Select the data you want to export</span></td>
    </tr>
   <tr bgcolor="#CCCCCC">
      <td width="532" height="30" valign="middle" bgcolor="#CCCCCC"><strong>Export Lists</strong> (<a href="javascript:checkListsAll();">All</a> , <a href="javascript:uncheckListsAll();">None</a>) <span class="style4">*</span></td>
      <td valign="middle" bgcolor="#CCCCCC"><strong>Number of Emails</strong></td>
      <td valign="middle" bgcolor="#CCCCCC">&nbsp;</td>
   </tr>
    <?php

while($row=mysql_fetch_row($result))
{

?>
   
    <tr <?php if($i%2==1){echo 'bgcolor="#EFEFEF"'; }?>>
      <td height="25" style="border-bottom:1px solid #CCCCCC; "><input name="<?php echo "List".$i; ?>" type="checkbox"  id="List<?php echo $i; ?>" value="<?php echo $row[0]; ?>">        <?php echo $row[1]; ?> <input name="ListName<?php echo $i; ?>" type="hidden" value="<?php echo $row[1]; ?>"></td>
      <td width="363" style="border-bottom:1px solid #CCCCCC; ">&nbsp;<?php echo $mysql->echo_one("select count(*) from ".$table_prefix."ea_em_n_cat where  cid =".$row[0]); ?>
      <input name="<?php echo "Email".$i; ?>" type="hidden"  id="Email<?php echo $i; ?>" value="<?php echo $row[0]; ?>">      </td>
      <td width="464" style="border-bottom:1px solid #CCCCCC; "><input name="status<?php echo $i; ?>" type="radio" value="1" checked>
        All
          <input name="status<?php echo $i; ?>" type="radio" value="2">
      Subscribed
      <input name="status<?php echo $i; ?>" type="radio" value="3">
      Unsubscribed</td>
    </tr>
    <?php $i+=1; 
	
	}

?>
<?php if(!isset($_COOKIE['inout_sub_admin']))

{
?>
   <!-- <tr>
	
      <td  valign="top">
          <input name="Emailnolist" type="checkbox" id="Emailnolist" value="1">
        Export emails which are not in any list  </td>
	  <td width="363">&nbsp;<?php //echo $mysql->echo_one("select count(*) from ".$table_prefix."email_advt ") - $mysql->echo_one("select count(distinct(eid)) from ".$table_prefix."ea_em_n_cat") ;?></td>
      <td width="464"><input name="status" type="radio" value="1" checked>
        All
          <input name="status" type="radio" value="2">
      Subscribed
      <input name="status" type="radio" value="3">
      Unsubscribed</td>
	  
	  <input name="completed" type="hidden" value="0" ></td>
    </tr>-->
	<?php
	}
	?>
	    <tr>
      <td colspan="3"   valign="top">        
        <em>[Exporting may take a few minutes depending on the size of the list.]</em> <br><br>
          <input type="submit" name="Submit" value="Export Data !">
          </p>
      </td>
	  </tr>
  </table>
</form>
<?php
include_once("admin.footer.inc.php");
?>
