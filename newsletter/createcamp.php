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
?><?php include("admin.header.inc.php"); ?>
<style type="text/css">
<!--
.style4 {color: #FF0000}
-->
</style>
<table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center"><br /><a href="createcamp.php"> New Campaign</a> | <a href="managecamp.php?action=all">All Campaigns</a> | <a href="managecamp.php?action=active">Active Campaigns</a> | <a href="managecamp.php?action=pending">Pending Campaigns</a> | <a href="managecamp.php?action=inactive">Inactive Campaigns</a></td>
  </tr>
</table>
<table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center"><a href="createcamp.php"> </a></td>
  </tr>
  <tr>
    <td align="center">&nbsp;</td>
  </tr>
  <tr>
    <td align="center"><strong>1. Email Details </strong><b>&raquo</b> 2. Attach Files <b>&raquo</b> 3. Preview Campaign <b>&raquo</b> 4. Activate/Save as Draft </td>
  </tr>
</table>
<br>

<form action="savead.php" method="post" enctype="multipart/form-data" name="form1">
  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td>&nbsp;</td>
      <td width="32%">&nbsp;</td>
      <td width="63%">&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="2" align="center"><strong><span class="inserted">Please fill in the  email details </span></strong></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td width="2%">&nbsp;</td>
      <td colspan="2" align="center"><?php if($default_editor==0) {?><?php if(!isset($_REQUEST['type'])) {?>        <a href="createcamp.php?type=editor">Create Campaign with WYSIWYG HTML Editor</a>        <?php } else {?>        <a href="createcamp.php">Don't need a WYSIWYG HTML Editor to create Campaign </a>        <?php }?> <?php } else if ($default_editor==1) {?><?php if(isset($_REQUEST['type'])) {?>        <a href="createcamp.php">Create Campaign with WYSIWYG HTML Editor</a>        <?php } else {?>        <a href="createcamp.php?type=noeditor">Don't need a WYSIWYG HTML Editor to create Campaign </a>        <?php }?> <?php } ?></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td><strong> Fields marked <span class="style4">*</span> are compulsory<br> 
      </strong></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>Name of Campaign</td>
      <td><input name="cname" type="text" id="cname">
        <span class="info">(OPTIONAL - For Admin Reference)</span></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>Subject of Email </td>
      <td><input name="subject" type="text" id="subject" size="50">
        <span class="style4">*</span></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td><span class="info">You can use the variable(s) {NAME}<?php
	   $extrafields = mysql_query("select * from ".$table_prefix."extra_personal_info order by id ");
		while($fielddetails=mysql_fetch_row($extrafields))
		{
			echo ", ".$fielddetails[3];
		}	
	   ?> here which will be replaced with the actual values added with each email. 
	  If no values are added with the email, default values specified will be used. </span></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
	  
	  <?php if((!isset($_REQUEST['type']) && !$default_editor==1) ||  ( $default_editor==1 && $_REQUEST['type']=="noeditor")) {?>
      <td>Email Body </td>
      <td><textarea name="body" cols="60" rows="15" id="body"></textarea>

        <span class="style4">*</span><br>        
        <span class="info">(You can place HTML here, if you choose HTML format below)</span><br></td>
		
		 <?php } else {?><td width="3%" colspan="3"><?php
		include(""."FCKeditor/fckeditor.php") ;
$oFCKeditor = new FCKeditor('body') ;
$oFCKeditor->BasePath = 'FCKeditor/';
$oFCKeditor->Value = 'Email Body';
$oFCKeditor->Create() ;
?><?php }?></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td valign="top">&nbsp;</td>
      <td><span class="info">Varibles you can use above- (For advanced uses) </span></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td valign="top">&nbsp;</td>
      <td><span class="info">{NAME} - Will be replaced with the  name added with the email <br>
        {UNSUBSCRIBE-LINK} - Will be replaced with the unsubscription link <br>
      {EMAIL} - Will be replaced with the user email<?php
	   $extrafields = mysql_query("select * from ".$table_prefix."extra_personal_info order by id ");
		while($fielddetails=mysql_fetch_row($extrafields))
		{
			echo "<br>".$fielddetails[3]." - Will be replaced with ".$fielddetails[1];
		}	
	   ?> </span></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td valign="top">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td valign="top">&nbsp;</td>
      <td><input name="unsub" type="checkbox" id="unsub" value="yes" checked>
      Add unsubscribe link in the email footer <br>
      <span class="info">It will include unsubscription link in the default format.(You need not tick, if you have used the variable 


 {UNSUBSCRIBE-LINK}

while designing the email body)</span></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td valign="top">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td valign="top">Batch size </td>
      <td><input name="per" type="text" id="per" size="7">
        <span class="style4">*</span><br>
        <span class="info">(eg:100, it means that it takes 10 times to send emails to an email list of 1000 emails)</span></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>Email Format </td>
      <td><input name="html" id="html1" type="radio" value="1" checked onclick="javascript:showaltcontent(1);">
        HTML 
        <input name="html" id="html2" type="radio" value="0" onclick="javascript:showaltcontent(0);">
        Plain Text </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
	 <tr style="display:" id="alt_row">
      <td>&nbsp;</td>
      <td>Enter Alternate Body Content </td>
      <td><textarea rows="15" cols="60" name="alt_body"></textarea> &nbsp;</td>
    </tr>
	 <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>Sender Name</td>
      <td><input name="name" type="text" id="name" size="30">
        <span class="style4">*</span></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>Sender Email Address</td>
      <td><input name="email" type="text" id="email" size="40">
        <span class="style4">*</span></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>Target list 
      <td valign="top">  <select name="category">        
	    <option value="" selected> - Select a list - </option>
		<?php if(!isset($_COOKIE['inout_sub_admin']))
		{?>
		<option value="0">All Emails (
	<?php
								  
								  echo $mysql->total("".$table_prefix."email_advt"," unsubstatus=0");
								  ?>)</option><?php }?>
		<!--<option value="0">All Emails (<?php /*if(isset($_COOKIE['inout_sub_admin']))
								{
	                              
	                              $subAdminId=getAdminId($mysql);
								   $relt="select count(distinct a.id) from ".$table_prefix."email_advt a, ".$table_prefix."ea_em_n_cat b 
		inner join ( SELECT eid FROM ".$table_prefix."admin_access_control where aid=$subAdminId )c on b.cid=c.eid 
		where a.id= b.eid and a.unsubstatus=0 and b.unsubstatus=0";
								   $total=$mysql->echo_one($relt);
								   echo $total;
								  }
								  else
								  {
								  echo $mysql->total("".$table_prefix."email_advt"," unsubstatus=0");
								  }*/ ?>)</option>-->
	    <?php $getListSql="select * from ".$table_prefix."email_advt_category order by name";
              if(isset($_COOKIE['inout_sub_admin']))
						{
							$subAdminId=getAdminId($mysql);
							$getListSql="SELECT a.*	FROM ".$table_prefix."email_advt_category a inner join 
							( SELECT eid FROM ".$table_prefix."admin_access_control where aid=$subAdminId ) b
								on a.id=b.eid  order by a.name";
						}

					$result=mysql_query($getListSql);
	  while($row=mysql_fetch_row($result))
	  {
		echo '<option value="'.$row[0].'">'.$row[1]." (";
  		$tot= mysql_query("select * from ".$table_prefix."email_advt a, ".$table_prefix."ea_em_n_cat b where a.id=b.eid AND a.unsubstatus=0 AND b.unsubstatus=0 AND b.cid=$row[0]");
	    echo mysql_num_rows($tot);
		echo ")</option>"; 
	  }?>
      </select> 
      <span class="style4">*</span><span class="info">&nbsp;<br>
      (Number of emails in brackets. Email List can be changed only for pending campagns or when you restart an active campaign.)</span></td>
    </tr>
	
	<tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
		<tr>
          <td>&nbsp;</td>
          <td>Email Template</td>
          <td>
		   <select name="emailtemplate">        
	    <option value="0" selected> - Select a template - </option>
		  <?php $getEmailTemplate="select * from ".$table_prefix."email_template order by name";
 

					$result=mysql_query($getEmailTemplate);
	  while($row=mysql_fetch_row($result))
	  {
		echo '<option value="'.$row[0].'">'.$row[1];
		echo "</option>"; 
	  }?>
      </select> 
		  &nbsp;</td>
        </tr>

			<tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
		<tr>
		<td>&nbsp;</td>
		 <td >Campaign Firing Criteria (Optional)</td> 
          <td > <select name="ex_field"  id="ex_field" style="width:150px;" onchange="getSelected()">
		  <option value="0" selected="selected">--select--</option>';
		   <option value="name" >Name</option>';
	 <?php
  $extrafields = mysql_query("select * from ".$table_prefix."extra_personal_info order by id ");

while($fielddetails=mysql_fetch_row($extrafields))
  {

					  $str.='<option value="'.$fielddetails[1].'" >'.$fielddetails[1].'</option>';
					
			
   }
    echo $str;
  ?>
				</select> &nbsp;
		  <select id="ext_condition" name="ext_condition">
		  <option value="=">=</option>
		  <option value="!=">!=</option>
		  <option value=">">></option>
		  <option value="<"><</option>
		  <option value=">=">>=</option>
		  <option value="<="><=</option>
		  <option value="LIKE">Pattern</option>
		  </select>
		  &nbsp;&nbsp;<input type="text" name="ext_text"  value=""/></td>
          <td>&nbsp;</td>
        </tr>
  
	
	
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td><span class="info">&nbsp;<br>
     <strong> If you choose 'Pattern', some valid patterns are given below</strong><br>
Starting with 'a' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;=> a% <br>
Ending with 'a' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  => %a <br>
Contains 'a' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;=> %a% <br>
Two letters before 'a' => __a <br>
</span>&nbsp;</td>
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
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td><input type="submit" name="Submit" value="Submit Email Details!"> 
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
function showaltcontent(a)
	{
	if(a==0)
{
				document.getElementById("html2").checked=true;
	document.getElementById("alt_row").style.display="none";
}
	else{
			document.getElementById("html1").checked=true;

				document.getElementById("alt_row").style.display="";
		}
	}
showaltcontent(1)
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