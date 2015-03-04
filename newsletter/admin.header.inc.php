<?php 

/*--------------------------------------------------+
|													 |
| Copyright  2006 http://www.inoutscripts.com/      |
| All Rights Reserved.								 |
| Email: scripts@inoutscripts.com                    |
|                                                    |
+---------------------------------------------------*/



?>
<?php error_reporting(0); ?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"><html>
<head>
<title>Inout Mailing List Manager Premium - The Ultimate Email List Management Solution.</title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $charset_encoding; ?>">
<link href="<?php echo $dirpath; ?>style.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
.style1 {
	color: #FFFFFF;
	font-weight: bold;
	font-size: 14px;
}
.style2 {color: #FFFFFF}
body {
	background-color: #666666;
}
-->
</style><?php if($file=="viewems") {?><SCRIPT LANGUAGE="JavaScript">
 
function checkAll() { 
for (var j = 0; j <=(document.form1.elements.length-5); j++) {
box = eval("document.form1.C" + j); 
//alert(box);
if (box.checked == false) box.checked = true;
   }
}

function uncheckAll() {

for (var j = 0; j <= (document.form1.elements.length-5); j++) {
box = eval("document.form1.C" + j); 
if (box.checked == true) box.checked = false;
   }
}

function switchAll() {
for (var j = 0; j <= (document.form1.elements.length-5); j++) {
box = eval("document.form1.C" + j); 
box.checked = !box.checked;
   }
}
  </script>
<?php } ?>

<?php 
if($file=="export_data") 
{?><SCRIPT LANGUAGE="JavaScript">
 
function checkListsAll() { 
for (var j = 0; j <=(document.form1.elements.length); j++) {
box = eval("document.form1.List" + j); 
//alert(box);
if (box.checked == false) box.checked = true;
   }
}

function uncheckListsAll() {

for (var j = 0; j <= (document.form1.elements.length-5); j++) {
box = eval("document.form1.List" + j); 
if (box.checked == true) box.checked = false;
   }
}
function switchAll(elementname,count) {
for (var j = 0; j <= count; j++) {
box = eval(elementname + j); 
box.checked = !box.checked;
   }
}
function checkAll(elementname,count) { 
//alert(elementname);
//alert(count);
for (var j = 0; j <=count; j++) {
box = eval(elementname + j); 
//alert(box);
if (box.checked == false) box.checked = true;
   }
}
function uncheckAll(elementname,count) { 
for (var j = 0; j <=count; j++) {
box = eval(elementname + j); 
//alert(box);
if (box.checked == true) box.checked = false;
   }
}


function checkEmailsAll() { 
for (var j = 0; j <=(document.form1.elements.length); j++) {
box = eval("document.form1.Email" + j); 
//alert(box);
if (box.checked == false) box.checked = true;
   }
}

function uncheckEmailsAll() {

for (var j = 0; j <= (document.form1.elements.length-5); j++) {
box = eval("document.form1.Email" + j); 
if (box.checked == true) box.checked = false;
   }
}

  </script>
<?php 
}
 ?>


</head>

<body <?php if($file=="viewems") {?> onLoad="javascript:uncheckAll();" <?php } ?>>
<table width="800" bgcolor="#FFFFFF" border="0" align="center" cellpadding="0" cellspacing="0" >
  <tr>
    <td>
<div style="padding:0px 10px ">
	<table width="100%"   border="0" cellpadding="5" cellspacing="0" >
      <tr height="50px">
        <td width="64%"  rowspan="2" align="left">&nbsp;<a href="<?php echo $dirpath; ?>main.php"><img src="<?php echo $dirpath; ?>images/inoutmlmpremium.png" alt="Admin Area Home"  border="0"></a></td>
        <td width="36%" align="right" valign="top"><?php if($script_mode=="demo") {?><span class="info">You are running online demo : <a href="http://www.inoutscripts.com/products/inout_mailing_list_manager_premium/buy.php">Buy Now</a></span><?php }?> </td>
      </tr>
      <tr height="35px">
        <td align="right" valign="bottom">&lt; <a href="<?php echo $dirpath; ?>tutor1.php" class="header">First Time User Guide</a> &gt;</td>
      </tr>
    </table>
  <table width="100%"  border="0" align="center" cellpadding="3" cellspacing="0" style="background-color:#F0F0F0;border:1px solid #CCCCCC ">
    <tr height="25px">
      <td colspan="2" align="center"><img src="<?php echo $dirpath."/"; ?>images/folder_home.gif" width="22" height="22" align="absmiddle"><a href="<?php echo $dirpath; ?>main.php" class="header">Admin  Home</a> | <img src="<?php echo $dirpath; ?>images/kate.gif" width="22" height="22" align="absmiddle"><a href="<?php echo $dirpath; ?>category_viewall.php" class="header">Email Lists</a> | <img src="<?php echo $dirpath; ?>images/edit_emails.gif" width="22" height="22" align="absmiddle"><a href="<?php echo $dirpath; ?>viewems.php" class="header">Email Addresses</a> | <img src="<?php echo $dirpath; ?>images/add_small.gif" width="22" height="22" align="absmiddle"><a href="<?php echo $dirpath; ?>importemails.php?type=many" class="header">Add Emails</a> |  <img src="<?php echo $dirpath; ?>images/kmail.gif" width="22" height="22" align="absmiddle"><a href="<?php echo $dirpath; ?>managecamp.php?action=all" class="header">Email Campaigns</a> |<img src="<?php echo $dirpath; ?>images/send_emails.gif" width="22" height="22" align="absmiddle"><a href="<?php echo $dirpath; ?>sendmails.php" class="header">Send Emails</a> | <img src="<?php echo $dirpath; ?>images/logout.gif" width="22" height="22" align="absmiddle"><a href="<?php echo $dirpath; ?>logout.php" class="header">Logout</a> </td>
      <td>&nbsp;</td>
    </tr>
  </table>
</div>
<div style="padding:10px 10px ">
