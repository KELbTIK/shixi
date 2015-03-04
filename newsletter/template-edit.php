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
?><?php include("admin.header.inc.php");
	 if(isset($_COOKIE['inout_sub_admin']))
				{
				   $aid=getAdminId($mysql);
				   $adminname=$mysql->echo_one("select username from  ".$table_prefix."subadmin_details where id=$aid");
				 mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','$adminname attempted unauthorized access to edit email template','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");
				 echo "<br><span class=\"already\">You don't have  access to this page</span>   <a href=\"javascript:history.back(-1);\">Go Back</a><br><br>";
				 include_once("admin.footer.inc.php");
				 exit(0);
				}

$id=$_GET['id'];
phpsafe($id);
$result=mysql_query("select * from ".$table_prefix."email_template where id='$id'");
$row=mysql_fetch_row($result);
if(!isset($_GET['type']))
{
	 if($default_editor==0) 
		$type="noeditor";
	 else
		$type="editor";
}
else
	$type=$_GET['type'];
?>
<style type="text/css">
<!--
.style4 {color: #FF0000}
-->
</style>
<table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center"><br /><a href="createtemplate.php"> New Email Template</a> | <a href="managetemplate.php?action=all">All Email Template</a> </td>
  </tr>
</table>
<form action="saveeditedtemplate.php" method="post" enctype="multipart/form-data" name="form1" onsubmit="return checkNull();">
<input type="hidden" name="id" value="<?php echo $id; ?>">
  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td>&nbsp;</td>
      <td width="34%">&nbsp;</td>
      <td width="64%">&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="2" align="center"><strong><span class="inserted">Please edit  the  template details </span></strong></td>
    </tr>
	 <tr>
      <td>&nbsp;</td>
      <td colspan="2" align="center">&nbsp;</td>
    </tr>
	 <tr>
      <td>&nbsp;</td>
      <td colspan="2" align="center"><?php
	if($type=="editor")
	{
	?>
	<a href="template-edit.php?id=<?php echo $id; ?>&type=noeditor" >Don't need a WYSIWYG Editor</a>
	<?php
	}
	else
		{
		?>
		<a href="template-edit.php?id=<?php echo $id; ?>&type=editor" >Use WYSIWYG Editor</a>
	<?php } ?> &nbsp;</td>
    </tr>
	 <tr>
      <td>&nbsp;</td>
      <td colspan="2" align="center">&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
	<tr>
      <td colspan="3" align="center"><strong> Fields marked <span class="style4">*</span> are compulsory<br> 
      </strong>&nbsp;</td>
      
    </tr>
    <tr>
      <td width="2%">&nbsp;</td>
      <td colspan="2" align="center">&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>Enter the Template Name:&nbsp;</td>
      <td><input type="text" name="template_name" id="template_name" value="<?php echo $row[1]; ?>">&nbsp;<span class="style4">*</span></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>Edit the template content below. </td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
	  
<td colspan="3"><?php
if($type=="editor")
	{
		include(""."FCKeditor/fckeditor.php") ;
		$oFCKeditor = new FCKeditor('body') ;
		$oFCKeditor->BasePath = 'FCKeditor/';
		$oFCKeditor->Value = "".$row[2]."";
		$oFCKeditor->Create() ;
	}
else
	{
$textarea_content= $row[2];
$textarea_content=htmlspecialchars($textarea_content,ENT_QUOTES,$charset_encoding);
	?>
	<textarea name="body" cols="100" rows="15" id="body"><?php echo $textarea_content; ?></textarea>
<?php
	}
?></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
     <td colspan="2"><span class="info">&nbsp;{CONTENT} will be replaced by original email content </span>&nbsp;</td>
    </tr>


    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td><input type="submit" name="Submit" value="Submit Template Details!"> 
&nbsp;      </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  </table>
</form>
<script language="javascript">
function checkNull()
	{
	if(document.getElementById('template_name').value=="")
		{
		alert("Please enter a template name");
		return false;
		}
	else
		return true;
	}
function getSelected()
	{
	var t1=document.getElementById("ex_field").options[document.getElementById("ex_field").selectedIndex].value;
	if(t1!=0)
		{
			//alert(t1);

		document.getElementById('t1').style.display="";
		}
	else
		{
		document.getElementById('t1').style.display="none";
		}
	}
</script>
<?php include("admin.footer.inc.php"); ?>