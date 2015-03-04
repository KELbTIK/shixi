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

include_once("admin.header.inc.php");
$mode=$_POST['mode'];

$resultstring="";
$result=mysql_query("select * from ".$table_prefix."email_advt_category order by name");
if(isset($_COOKIE['inout_sub_admin']))
{
	$subAdminId=getAdminId($mysql);
	$result=mysql_query("SELECT a.*	FROM ".$table_prefix."email_advt_category a inner join 
	( SELECT eid FROM ".$table_prefix."admin_access_control where aid=$subAdminId ) b
	on a.id=b.eid order by a.name");
}
$i=0;
$j=0;
$listname=array();
while($row=mysql_fetch_row($result))
{
  if(isset($_POST[ "Lis".$i]))
  {
	  $id=$row[0];
	  $resultstring.=$id.",";
	   $listname[$j++]=$row[1];
  }
  $i+=1;
}
  
if($resultstring=="")
{
	echo "<br>Please fill all mandatory fields !!&nbsp;&nbsp;<a href=\"javascript:history.back(-1);\">Go Back</a><br><br>";
	include_once("admin.footer.inc.php");
	exit(0);
}
 	

$rest = substr($resultstring, 0, -1);
//echo $rest;
?>
<style type="text/css">
<!--
.style4 {color: #FF0000}
.style5 {color: #333333}
-->
</style>



<table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>
   <td align="center"><a href="category_addnew.php">Create new List</a>&nbsp;| <a href="category_viewall.php">Manage all Lists</a>&nbsp;| <a href="configurehtml.php">Subscribe HTML Code </a>| <a href="phpcodesub.php">Automatic Subscribtion PHP Code</a>  </td>
  </tr>
</table>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td>&nbsp;</td>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td width="327"><span class="inserted">Preview of HTML subscription code</span></td>
      <td colspan="2">&nbsp;</td>
    </tr>
</table>
	
	
<?php

$currTime = time();
//$currTime = "";
$ret = generatePreview($sub_path,$cid,$currTime,$rest,$subokpath,$mode,$listname,$unsubokpath);
echo $ret["js"];
echo $ret["pre"];
	
function generatePreview($sub_path,$cid,$currTime,$rest,$subokpath,$mode,$listname,$unsubokpath)
{
global  $table_prefix; 
	$jsmethod="
<script language=\"javascript\">
function check_email".$currTime."(emailString) 
{

		var mailPattern = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
   		var matchArray = emailString.match(mailPattern);
   		if (emailString.length == 0)
		 return false;
   
   		if (matchArray == null)
   		{
			return false;
		}
}

function getCheckedValue".$currTime."(radioObj)
{
	if(radioObj!=undefined)
	{
		var len = radioObj.length;
		for(i=0;i<len;i++)
		{
		if(radioObj[i].checked)
 			return radioObj[i].value;
		}

	}
	return 0;
}";

if($mode==2)
{
	$jsmethod.="
	function verifycheckbox".$currTime."(frm)
	{
		";
		
		$lists=explode(",",$rest);
		$i=0;
		 $jsmethod.="
		 for(var i=0;i<".count($lists)."; i++)
		 {
		 
			chbobj=eval('frm.chb'+i);
			//alert(chbobj);
			if(chbobj.checked == true)
			return true;
		 }
		 return false;
	 }
	 ";
}

$jsmethod.="
function verifyFields".$currTime."(frm)
{
	if (check_email".$currTime."(frm.email.value)==false) 
	{
		alert('Your Email Address seems incorrect. Please check to make sure that it is a correct Email Address format.');
		frm.email.focus();
		return false;
	}";
	
if($mode==2)
{
	$jsmethod.="
	if (verifycheckbox".$currTime."(frm)==false) 
	{
		alert('Please select atleast one from the lists.');
		return false;
	}";
}	
$jsmethod.="	
	if(getCheckedValue".$currTime."(frm.radiobutton)==0)
	{


";
//

	///////////
if(isset($_POST['redir1']))
{
$redir1=trim($_POST['redir1']);
	if($redir1=="")
	{
		$redir1=$unsubokpath;
	}
}
	$redir=trim($_POST['redir']);
	if($redir=="")
	{
		$redir=$subokpath;
	}
	$conf_path=trim($_POST['confirm']);
	$conf_path=urlencode($conf_path);
	$redir=urlencode($redir);
	$redir1=urlencode($redir1);

	$preview="<form action=".$sub_path."?redirect=".$redir." method=\"post\" name=\"category_sub\" id=\"category_sub\" onSubmit=\"javascript: return verifyFields".$currTime."(category_sub)\">
	<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
	<tr>
	    <td colspan=\"3\"><span class=\"style5\"><br>All fields marked</span> <span style=\"color:#FF0000\">*</span><span class=\"style5\"> are mandatory <br></span></td>
    </tr>
	<tr>
		<td colspan=\"3\"><br><input type=\"hidden\" name=\"redirect\" value=".$redir."><input type=\"hidden\" name=\"redirect1\" value=".$redir1."><input type=\"hidden\" name=\"confirm\" value=".$conf_path."></td>
	</tr>
	<tr>
    	<td height=\"21\" nowrap>Email </td>
        <td colspan=\"2\">&nbsp;&nbsp;<input type=\"text\" name=\"email\" style=\"width:255px;\"><span style=\"color:#FF0000\">*</span></td>
    </tr>
	<tr>
    	<td colspan=\"3\"><br></td>
    </tr>";
	 if(isset($_REQUEST['List0'])) 
	 {
	     $preview .="
		 <tr>
   		 	<td nowrap>Name</td>
    	 	<td colspan=\"2\">&nbsp;&nbsp;<input type=\"text\" name=\"name\" style=\"width:255px;\">";
	 	 if(isset($_REQUEST['Mandatory0'])) 
		 {
      		 $preview.="<span style=\"color:#FF0000\">*</span>";			
			 $jsmethod.="
			 if (!frm.name.value)
			 {
		  		alert('Please provide your name.');
	 			frm.name.focus();
				return false;
			 }";
	   	 }	
	     $preview .="</td>
  		 </tr>
		 <tr>
   		 	<td colspan=\"3\"><br></td>
	     </tr>";
	 }
 	 $i=1;
	 $extrafields = mysql_query("select * from ".$table_prefix."extra_personal_info order by id ");
	 while($fielddetails=mysql_fetch_row($extrafields))
	 {
 	 	if(isset($_POST[ "List".$i]))
		 {
	   		 $preview .="
			 <tr>
    			 <td nowrap>".$fielddetails[1]."
				 </td>
	 		 <td colspan=\"2\">&nbsp;&nbsp;";
			if($fielddetails[4]==1)
			 {
			 $preview .="<input name=\"extra_personal_info".$fielddetails[0]."\" type=\"text\" id=\"extra_personal_info".$fielddetails[0]."\"  style=\"width:255px;\">";
			 } 
			 else if($fielddetails[4]==2)
			  {
			   $preview .="<textarea name=\"extra_personal_info".$fielddetails[0]."\"  id=\"extra_personal_info".$fielddetails[0]."\" style=\"width:255px;\" rows=\"5\" ></textarea>";
			  } 
		   else
			{
				 $preview .="<select name=\"extra_personal_info".$fielddetails[0]."\"  id=\"extra_personal_info".$fielddetails[0]."\" style=\"width:150px;\">";
				   $options=explode(",",$fielddetails[5]);
		  			
				 for($k=0;$k<count($options);$k++)
				 {
					
					  $preview .='<option value="'.$options[$k].'" >'.$options[$k].'</option>';
					
				 }
				  $preview .="</select>";
				 
			}
				  
				  
	  		 if(isset($_POST["Mandatory".$i])) 
	  		 {
		     	 $preview .= "<span style=\"color:#FF0000\">*</span>";
				 $jsmethod.="
				 if (!frm.extra_personal_info".$fielddetails[0].".value)
				 {
		  			alert('Please provide your ".$fielddetails[1]."');
		 			frm.extra_personal_info".$fielddetails[0].".focus();
					return false;
				 }";
	    	 }	
			 $preview .="</td>
			 </tr>
		 	 <tr>
	       		  <td colspan=\"3\"><br></td>
      		 </tr>";
		}
		$i+=1;
	}
	
	if($mode==1)
	{
		 $preview.="
		 <tr>
     	 	<td></td>
		    <td colspan=\"2\"><input type=\"hidden\" name=\"cid\" value=".$rest."></td>
    	 </tr>";
	}
	if($mode==2)
	{
	$preview .="
			 <tr>
    			 <td nowrap colspan=\"2\"><span class=\"inserted\">Select at least one of the category below.</span>
				 </td>
	 		 <td >&nbsp;&nbsp;</td>
			 </tr>
		 	 <tr>
	       		  <td colspan=\"3\"><br></td>
      		 </tr>";
			 
			 
	$lists=explode(",",$rest);
	$i=0;
	while($i<count($lists))
	 {
	   		 $preview .="
			 <tr>
    			 <td nowrap><input type=\"checkbox\" name=\"chb".$i."\" value=".$lists[$i]." />
				 </td>
	 		 <td colspan=\"2\">".$listname[$i]."</td>
			 </tr>
		 	 <tr>
	       		  <td colspan=\"3\"><br></td>
      		 </tr>";

		$i+=1;
	}
	
	}
	if($mode==3)
	{
	$preview .="
			 <tr>
    			 <td nowrap colspan=\"2\"><span class=\"inserted\">Select at least one of the category below.</span>
				 </td>
	 		 <td >&nbsp;&nbsp;</td>
			 </tr>
		 	 <tr>
	       		  <td colspan=\"3\"><br></td>
      		 </tr>";
			 
			 
	
	   		 $preview .="
			 <tr>
    			 <td nowrap colspan=\"3\"><select name=\"cid\">";
				 $lists=explode(",",$rest);
		$i=0;
		while($i<count($lists))
	 { 	
	$preview .=" <option value=".$lists[$i]." selected>- ".$listname[$i]." -</option>";
	$i+=1;
	}
	$preview .="</select>
				 </td>
	 		
			 </tr>
		 	 <tr>
	       		  <td colspan=\"3\"><br></td>
      		 </tr>";

		
	
	}
	if($_POST['radiobutton']==1)
	{
		  
		 $preview.="
		 <tr>
     	 	<td><input type=\"submit\" name=\"Submit\" value=\"Subscribe !\"></td>
		    <td colspan=\"2\">&nbsp;</td>
    	 </tr>";
	 }
	 else
	 {
		 $preview.="
		 <tr>
			 <td colspan=\"3\"> <input name=\"radiobutton\" type=\"radio\" value=\"0\" checked>Subscribe
       		 <input name=\"radiobutton\" type=\"radio\" value=\"1\" >Unsubscribe &nbsp;&nbsp;<input type=\"submit\" name=\"Submit2\" value=\"Submit !\"></td>
		 </tr>";
	  }
	  $preview.="
	  <tr>
	  	<td></td>
	  	<td width=\"331\">&nbsp;</td>
	  	<td width=\"539\">&nbsp;</td>
      </tr>  
	</table>
	</form>";  
	
	$jsmethod.="
	}
return true;
}
</script>	
";
	$retVal[] = array();
	$retVal["pre"] =$preview;
	$retVal["js"] =$jsmethod;
	return $retVal;
}
?>
		  

<table width="100%" border="0" cellpadding="0" cellspacing="0">  
<tr align="left">
    <td colspan="3" ><p class="pagetable_activecell">Please copy the HTML code displayed in the text area below and paste it into an html page to subscribe to this list. </p>
	</td>
</tr>
<tr align="center">
    <td colspan="3" >
     <textarea name="textfield" cols="100" rows="20">
	<?php 
 	
	 echo htmlentities($ret["js"]); 
	 echo htmlentities($ret["pre"]); 
	?>
	 </textarea>
	</td>
</tr>
<tr>
<td colspan="4">
<?php
echo "<br><a href=\"javascript:history.back(-1);\">Go Back And Modify</a><br><br>";
?>
</td>
</tr>
</table>
<?php
include_once("admin.footer.inc.php");
?>