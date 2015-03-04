<script language="javascript" type="text/javascript">
function showFieldTypeOptions()
{
if(document.forms[0].radiobutton[0].checked)
	val=1;
else
	val=0;
	
//alert(val);
if(val==0)
document.getElementById('dropdowntable').rows["dropdownrow1"].style.display="";
else
document.getElementById('dropdowntable').rows["dropdownrow1"].style.display="none";
}
</script>
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

include_once("admin.header.inc.php");
?>

<table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>
   <td align="center"><a href="category_addnew.php">Create new List</a>&nbsp;| <a href="category_viewall.php">Manage all Lists</a>&nbsp;| <a href="configurehtml.php">Subscribe HTML Code </a>| <a href="phpcodesub.php">Automatic Subscribtion PHP Code</a>  </td>
  </tr>
</table>
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
 $extrafields = mysql_query("select * from ".$table_prefix."extra_personal_info order by id ");

?>
<style type="text/css">
<!--
.style3 {
	font-size: 16px;
	font-weight: bold;
}
.style5 {color: #FF0000}
-->
</style>



<center>
<form name="form1" method="post" action="preview_subscription_html.php" enctype="multipart/form-data">
  <table width="100%"  border="0" cellspacing="0" cellpadding="0" id="dropdowntable">
    <tr>
      <td></td>
      <td colspan="5"></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="5"><span class="inserted">Please select the lists to which you want to subscribe the email.</span> </td>
      <td>&nbsp;</td>
    </tr>
	    <tr>
      <td></td>
      <td colspan="5"></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td scope="row">&nbsp;</td>
      <td height="30" colspan="5" valign="center" bgcolor="#CCCCCC"><strong> Email Lists</strong> (<a href="javascript:checkAll('document.form1.Lis',<?php echo mysql_num_rows($result);?>)">All</a> , <a href="javascript:uncheckAll('document.form1.Lis',<?php echo mysql_num_rows($result);?>)">None</a>) <span class="style3 style1 style5">*</span> </td>
      <td>&nbsp;</td>
    </tr>
     <?php 
	$i=0;
		while($row=mysql_fetch_row($result))
	{
	?>
    <tr>
	<td>&nbsp;</td>
      <td width="2%" valign="center"  <?php if($i%2==1){echo 'bgcolor="#EFEFEF"'; }?> height="25" style="border-bottom:1px solid #CCCCCC; ">&nbsp;</td>
      
      <td width="30%" align="left" valign="center"  <?php if($i%2==1){echo 'bgcolor="#EFEFEF"'; }?> style="border-bottom:1px solid #CCCCCC; "><input name="<?php echo "Lis".$i; ?>" type="checkbox"  id="Lis<?php echo $i; ?>" value="<?php echo $row[0]; ?>">
        <?php echo $row[1]; ?> </td>
      <td align="left" valign="center"  <?php if($i%2==1){echo 'bgcolor="#EFEFEF"'; }?> style="border-bottom:1px solid #CCCCCC; ">&nbsp;</td>
      <td align="left" valign="center"  <?php if($i%2==1){echo 'bgcolor="#EFEFEF"'; }?> style="border-bottom:1px solid #CCCCCC; ">&nbsp;</td>
      <td align="left" valign="center"  <?php if($i%2==1){echo 'bgcolor="#EFEFEF"'; }?> style="border-bottom:1px solid #CCCCCC; ">&nbsp;</td>
      <td >&nbsp;</td>
    </tr>
    <?php $i+=1; 
	}
	?>
    <tr>
      <td width="4%">&nbsp;</td>
      <td colspan="5">&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="5"><span class="inserted">Please select fields you wants to display in HTML code along with email.</span></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="5">&nbsp;</td>
    </tr>
    <tr>
	<td>&nbsp;</td>
      <td colspan=5>
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
          <tr>
		    <td height="30" width="38" align="left" bgcolor="#CCCCCC"><strong> &nbsp;&nbsp;</strong></td>
            <td width="425" align="left" bgcolor="#CCCCCC"><strong>Field Name</strong></td>
            <td height="30" width="410" align="left" bgcolor="#CCCCCC"><strong> Select</strong>(<a href="javascript:checkAll('document.form1.List',<?php echo mysql_num_rows($extrafields);?>)">All</a> , <a href="javascript:uncheckAll('document.form1.List',<?php echo mysql_num_rows($extrafields);?>)">None</a>) </td>
			<td height="30" width="354" align="left" bgcolor="#CCCCCC"><strong> Mandatory Check</strong>(<a href="javascript:checkAll('document.form1.Mandatory',<?php echo mysql_num_rows($extrafields);?>)">All</a> , <a href="javascript:uncheckAll('document.form1.Mandatory',<?php echo mysql_num_rows($extrafields);?>)">None</a>)</td>
          </tr>
          <tr >
            <td align="left" style="border-bottom:1px solid #CCCCCC; " height="25">&nbsp;</td>
            <td align="left" style="border-bottom:1px solid #CCCCCC; " height="25">Name</td>
            <td align="left" style="border-bottom:1px solid #CCCCCC; " height="25">
              <input name="List0" type="checkbox" id="name3" value="true">
            </td>
            <td align="left" style="border-bottom:1px solid #CCCCCC; " height="25">
              <input name="Mandatory0" type="checkbox" id="name13" value="true">
            </td>
          </tr>
          <?php
 
$i=1;
while($fielddetails=mysql_fetch_row($extrafields))
  {
   ?>
          <tr <?php if($i%2==1){echo 'bgcolor="#EFEFEF"'; }?>>
            <td align="left" style="border-bottom:1px solid #CCCCCC; " height="25">&nbsp;&nbsp;</td>
            <td align="left" style="border-bottom:1px solid #CCCCCC; "><?php echo $fielddetails[1]; ?></td>
            <td align="left" style="border-bottom:1px solid #CCCCCC; ">
              <input type="checkbox" name="<?php echo "List".$i; ?>" value="true">
            </td>
            <td align="left" style="border-bottom:1px solid #CCCCCC; ">
              <input type="checkbox" name="<?php echo "Mandatory".$i; ?>" value="true">
            </td>
          </tr>
          <?php
		  $i+=1; 
   }
  ?>
      </table></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="5">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="5"><span class="inserted">Type of HTML Subscription </span></td>
      <td width="4%">&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="2">&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="2"><input name="radiobutton"  type="radio"  value="1" checked onClick="javascript:showFieldTypeOptions();" >        Subscribe only </td>
      <td width="10%">&nbsp;</td>
      <td width="23%">&nbsp;</td>
      <td width="27%"><input name="radiobutton"  type="radio" value="0"  onClick="javascript:showFieldTypeOptions();" >
        Subscribe/Unsubscribe</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="2">&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="5"><span class="inserted">Mode of HTML Subscription </span></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="2">&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="4"><select name="mode">
        <option value="1" selected>- Add to all list -</option>
        <option value="2">- Allow users to select multiple list -</option>
        <option value="3">- Allow users to select single list -</option>
      </select></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="2" align="right">&nbsp;</td>
      <td align="right">&nbsp;</td>
      <td align="right">&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <?php if(!isset($_REQUEST['action'])) {?>
    <tr>
      <td>&nbsp;</td>
      <td colspan="5"><span class="inserted">Advanced Options </span></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="5">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="5">In case if you want to manage newsletters of your other sites from your main domain, then you can specify the page to where people need to be redirected after subscribing in the list. If you don't specify that below, people will get redirected to the default page that you set in the config file.<br>
          <br>
      Page to be redirected after subscription(absolute path)<br>
      <input name="redir" type="text" id="redir3" size="80" value="<?php echo $subokpath; ?>" onfocus="if(this.value=='<?php echo $subokpath; ?>') this.value='';" onblur="if(this.value=='') this.value='<?php echo $subokpath; ?>';">
      <br>
      (eg:http://www.mysite.com/thanks.html)<br>
      </td>
      <td>&nbsp;</td>
    </tr>
    <?php } ?>
    <tr>
      <td>&nbsp;</td>
      <td colspan="5" align="center">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr id="dropdownrow1" style="display:none">
      <td>&nbsp;</td>
      <td colspan="5" align="left"> Page to be redirected after unsubscription(absolute path)<br>
	   <input name="redir1" type="text"  size="80" value="<?php echo $unsubokpath; ?>" onfocus="if(this.value=='<?php echo $unsubokpath; ?>') this.value='';" onblur="if(this.value=='') this.value='<?php echo $unsubokpath; ?>';">
	   <br>
      (eg:http://www.mysite.com/thanks.html)<br></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="5" align="center">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
<?php if($confirm_subscription==1) {?>
    <tr>
      <td>&nbsp;</td>
      <td colspan="5" align="left"> Page to be redirected after confirming(absolute path)<br>
	   <input name="confirm" type="text"  size="80" value="<?php echo $dirpath."confirm.html"; ?>" onfocus="if(this.value=='<?php echo $dirpath."confirm.html"; ?>') this.value='';" onblur="if(this.value=='') this.value='<?php echo $dirpath."confirm.html"; ?>';">
       <br>
       (eg:http://www.mysite.com/thanks.html)<br></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="5" align="center">&nbsp;</td>
      <td>&nbsp;</td> 
    </tr>
<?php } ?>
    <tr>
      <td>&nbsp;</td>
      <td colspan="5" align="center"><input type="submit" name="Submit" value="Preview"></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="5">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  </table>
</form>
</center>
<?php
include_once("admin.footer.inc.php");
?>
<script language="javascript" type="text/javascript">
showFieldTypeOptions();
</script>