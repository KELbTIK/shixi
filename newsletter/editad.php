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

$id=$_REQUEST['id'];
if($id=="")
	$id=-1;
if(!isValidAccess($id,$CST_MLM_CAMPAIGN,$table_prefix,$mysql))
{
	if($log_enabled==1)
	{
		$aid=getAdminId($mysql);
		$adminname=$mysql->echo_one("select username from  ".$table_prefix."subadmin_details where id=$aid");
		$entityname=$mysql->echo_one("select cname from  ".$table_prefix."email_advt_curr_run where id=$id");
		if($entityname!="")
			mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','$adminname attempted unauthorized access to edit the campaign $entityname(id:".$id.")','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");

	}
	include_once("admin.header.inc.php");
	?>
	<br><span class="already">&nbsp;&nbsp;You dont have access to this campaign.&nbsp;&nbsp;<a href="javascript:history.back(-1);">Go Back</a></span><br><br>
	<?php
	include_once("admin.footer.inc.php");
	exit(0);
}
$result=mysql_query("select * from ".$table_prefix."email_advt_curr_run where id=$id");
$row=mysql_fetch_row($result);
$act=$_REQUEST['action'];
$status=$row[9];
?><?php include("admin.header.inc.php"); ?>

<style type="text/css">
<!--
.style4 {color: #FF0000}
-->
</style>
<table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center"><a href="createcamp.php"> New Campaign</a> | <a href="managecamp.php?action=all">All Campaigns</a> | <a href="managecamp.php?action=active">Active Campaigns</a> | <a href="managecamp.php?action=pending">Pending Campaigns</a> | <a href="managecamp.php?action=inactive">Inactive Campaigns</a></td>
  </tr>
</table><br>

<form name="form1" method="post" action="edited.php?existingname=<?php echo $row[11];?>">
<input type="hidden" name="action" value="<?php echo $_REQUEST['action']; ?>" />
  <input name="id" type="hidden" id="id" value="<?php echo $id; ?>">
  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
     <tr>
     
      <td align="center" colspan="3"><strong>Edit an Email Campaign</strong></td>
    </tr>
     <tr>
       <td align="center" colspan="3">&nbsp;</td>
     </tr>
    <tr>
      
      <td align="center" colspan="3"><?php if($default_editor==0) {?>
        <?php if(!isset($_REQUEST['type'])) {?>
        <a href="editad.php?type=editor&id=<?php echo $id;?>">Edit Campaign with WYSIWYG HTML Editor</a>
        <?php } else {?>
        <a href="editad.php?id=<?php echo $id;?>">Don't need a WYSIWYG HTML Editor to edit Campaign </a>
        <?php }?>
        <?php } else if ($default_editor==1) {?>
        <?php if(isset($_REQUEST['type'])) {?>
        <a href="editad.php?id=<?php echo $id;?>">Edit Campaign with WYSIWYG HTML Editor</a>
        <?php } else {?>
        <a href="editad.php?type=noeditor&id=<?php echo $id;?>">Don't need a WYSIWYG HTML Editor to edit Campaign </a>
        <?php }?>
        <?php } ?></td>
    </tr>
   
    <tr>
      <td width="0%">&nbsp;</td>
      <td width="29%">&nbsp;</td>
      <td width="68%">&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td><strong>All fields marked <span class="style4">*</span> are compulsory<br> 
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
      <td><input name="cname" type="text" id="cname" value="<?php echo  $row[11];  ?>">
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
      <td><input name="subject" type="text" id="subject" value="<?php echo htmlspecialchars($row[4],ENT_QUOTES); ?>">
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
        <td width="3%" colspan="3">&nbsp;</td>
      </tr>
      <tr>
      <td>&nbsp;</td>
	  
	  <?php if((!isset($_REQUEST['type']) && !$default_editor==1) ||  ( $default_editor==1 && $_REQUEST['type']=="noeditor")) {
$textarea_content= $row[5];
$textarea_content=htmlspecialchars($textarea_content,ENT_QUOTES,$charset_encoding);
?>
      <td>Email Body </td>
      <td><textarea name="body" cols="60" rows="15" id="body"><?php echo $textarea_content; ?></textarea>

        <span class="style4">*</span><br>
        (You can place HTML here<span class="info">, if you choose HTML format below)</span><br></td>
		
		 <?php } else {?><td colspan="3"><?php
		include("FCKeditor/fckeditor.php") ;
$oFCKeditor = new FCKeditor('body') ;
$oFCKeditor->BasePath = 'FCKeditor/';
$oFCKeditor->Value = $row[5];
$oFCKeditor->Create() ;
?><?php }?></td>
    </tr>
	<tr>
        <td colspan="3"></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>Varibles you can use in body and alternate body- (For advanced uses) </td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>{NAME} - Will be replaced with the user name<br>
          {UNSUBSCRIBE-LINK} - Will be replaced with the unsubscription link <br>
{EMAIL} - Will be replaced with the user email
<?php
	   $extrafields = mysql_query("select * from ".$table_prefix."extra_personal_info order by id ");
		while($fielddetails=mysql_fetch_row($extrafields))
		{
			echo "<br>".$fielddetails[3]." - Will be replaced with ".$fielddetails[1]." ";
		}	
	   ?> </td>
      </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>Email Format </td>
      <td><input name="html" id ="html1" type="radio" value="1" <?php if($row[8]=="1") echo "checked"; ?> onclick="javascript:showaltcontent(1);">
        HTML 
        <input name="html" id ="html2"  type="radio" value="0" <?php if($row[8]=="0") echo "checked"; ?> onclick="javascript:showaltcontent(0);">
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
<?php
$textarea_content= $row[17];
$textarea_content=htmlspecialchars($textarea_content,ENT_QUOTES,$charset_encoding);
?>
      <td><textarea rows="15" cols="60" name="alt_body"><?php echo $textarea_content; ?></textarea> &nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td valign="top">Batch size </td>
      <td><input name="per" type="text" id="per" value="<?php echo $row[2]; ?>">
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
      <td>Sender Name</td>
      <td><input name="name" type="text" id="name" value="<?php echo $row[6]; ?>">
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
      <td><input name="email" type="text" id="email" value="<?php echo $row[7]; ?>" size="35">
        <span class="style4">*</span></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td><?php  $cid=$mysql->echo_one("select `catid` from ".$table_prefix."ea_cnc where campid='$row[0]'"); ?>&nbsp;</td>
    </tr>
	 <tr>
      <td>&nbsp;</td>
      <td>Target list<td valign="top">  <?php if($status==-1 || $act=="restart") { ?><select name="category">        
	    <option value="" > - Select a List - </option>
		<?php if(!isset($_COOKIE['inout_sub_admin']))
		{?>
		<option value="0" selected="selected">All Emails (
	<?php
								  
								  echo $mysql->total("".$table_prefix."email_advt"," unsubstatus=0");
								  ?>)</option><?php }?>
	    <?php $getListSql="select * from ".$table_prefix."email_advt_category order by name";
              if(isset($_COOKIE['inout_sub_admin']))
						{
							$subAdminId=getAdminId($mysql);
							$getListSql="SELECT a.*	FROM ".$table_prefix."email_advt_category a inner join 
							( SELECT eid FROM ".$table_prefix."admin_access_control where aid=$subAdminId ) b
								on a.id=b.eid  order by a.name";
						}

					$result=mysql_query($getListSql);
					
					
	  while($row1=mysql_fetch_row($result))
	  {
	  $msg="";
	  if($cid==$row1[0])
	  	$msg="selected";
		echo '<option value='.$row1[0].' '.$msg.'>'.$row1[1]." (";
  		$tot= mysql_query("select * from ".$table_prefix."email_advt a, ".$table_prefix."ea_em_n_cat b where a.id=b.eid AND a.unsubstatus=0 AND b.unsubstatus=0 AND b.cid=$row1[0]");
	    echo mysql_num_rows($tot);
		echo ")</option>"; 
	  }?>
      </select> 
      <span class="style4">*</span><span class="info">&nbsp;<br>
      (Number of emails in brackets. Email List cannot be changed later)</span><?php } else { 
	 $res=mysql_query("select name from ".$table_prefix."email_advt_category a,".$table_prefix."ea_cnc b where b.catid=a.id and b.campid='$id'");
echo mysql_error();
	 $res=mysql_fetch_row($res);
	 if($res[0]!="")
		 echo "<strong>".$res[0]."</strong>";
	 else
	 		 echo "<strong>All Emails</strong>";
	//  echo "select name from email_advt_category a,ea_cnc b where b.catid=a.id and b.campid='$id'";
	 // echo $row[12]; ?>
	<input type="hidden" name="category" value="<?php echo $cid; ?>">
<?php } ?>
</td>
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
		  <?php $getEmailTemplate="select * from ".$table_prefix."email_template order by name";
 

					$result1=mysql_query($getEmailTemplate);
	 ?>
		   <select name="emailtemplate"  id="emailtemplate" style="width:150px;">
		  <option value="0" <?php if($row[16]==0) echo "selected"; ?>>--select--</option>';
	 <?php
	   while($row1=mysql_fetch_row($result1))
	  {
		if($row[16]==$row1[0])
			{
			$str.='<option value='.$row1[0].' selected>'.$row1[1].'</option>';
			}
		else
			{
			$str.='<option value='.$row1[0].'>'.$row1[1].'</option>';
			}
		}

    echo $str;
  ?>
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
          <td >
		  <?php if($status==-1 || $act=="restart") { ?>
		   <select name="ex_field"  id="ex_field" style="width:150px;" onchange="getSelected()">
		  <option value="0" selected="selected">--select--</option>;
	 <?php
  $extrafields = mysql_query("select * from ".$table_prefix."extra_personal_info order by id ");
  $str="";
if($row[13]=="name")
	{
	$str.='<option value=name selected>name</option>';
	}
while($fielddetails=mysql_fetch_row($extrafields))
  {
  	  $msg="";
	  if($row[13]==$fielddetails[1])
	  	$msg="selected";

					  $str.='<option value='.$fielddetails[1].'  '.$msg.'>'.$i.$fielddetails[1].'</option>';
			
   }
    echo $str;
  ?>
				</select> &nbsp;
				
		  <select id="ext_condition" name="ext_condition">
		  <option value=">" <?php if($row[14]==">") echo "selected"; ?>>></option>
		  <option value="<" <?php if($row[14]=="<") echo "selected"; ?>><</option>
		  <option value=">=" <?php if($row[14]==">=") echo "selected"; ?>>>=</option>
		  <option value="<=" <?php if($row[14]=="<=") echo "selected"; ?>><=</option>
		  <option value="==" <?php if($row[14]=="=") echo "selected"; ?>>=</option>
		  <option value="!=" <?php if($row[14]=="!=") echo "selected"; ?>>!=</option>
		  <option value="LIKE" <?php if($row[14]=="LIKE") echo "selected"; ?>>Pattern</option>
		  </select>
		 &nbsp;&nbsp;<input type="text" name="ext_text"  value="<?php echo $row[15]; ?>"/>
		 <?php } else {
		 
			 $extrafields = mysql_query("select * from ".$table_prefix."extra_personal_info order by id ");
				$flag=0;
				while($fielddetails=mysql_fetch_row($extrafields))
					  {
						  $msg="";
						  if($row[13]==$fielddetails[1])
							{
							$flag=1;
							echo "<strong>".$fielddetails[1];
							?>
							<input type="hidden" name="ex_field" value="<?php echo $fielddetails[1]; ?>" />
							<?php
							}
					
						}
						if($flag==0) 
							{
							if($row[13]=="")
								{
								echo "<strong>No campaign Firing Criteria</strong>";
								}
							else
								{
								echo "<strong>Name";
								?>
								<input type="hidden" name="ex_field" value="name" /><?php 
								}
							}
						
							
				?>
				<?php echo $row[14]; ?>&nbsp;<?php echo$row[15]; ?>&nbsp;</strong>
		<input type="hidden" name="ext_condition" value="<?php echo $row[14]; ?>" />
		<input type="hidden" name="ext_text" value="<?php echo$row[15]; ?>" />
			<?php } ?>		 </td>
          <td>&nbsp;</td>
        </tr>
		 <tr>
		   <td>&nbsp;</td>
		   <td>&nbsp;</td>
		   <td><?php if($row[13]!="")
								{ ?><span class="info">&nbsp;<br>
     <strong> You can give patterns like this </strong><br>
Starting with 'a' = a% <br>
Ending with 'a'   = %a <br>
'a' in the middle = %a% <br>
Two letters before 'a' = __a <br>
</span><?php } ?>&nbsp;</td>
    </tr>
		 <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td><input type="submit" name="Submit" value="Edit & Save the Email Settings"></td>
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
<script language="javascript">
function showaltcontent(a)
	{
//alert(a);
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
showaltcontent(<?php echo $row[8]; ?>);
function getSelected()
	{
	var t1=document.getElementById("ex_field").options[document.getElementById("ex_field").selectedIndex].value;
	if(t1!=0)
		{
		if(document.getElementById('t1'))
			document.getElementById('t1').style.display="";
		}
	else
		{
		if(document.getElementById('t1'))
			document.getElementById('t1').style.display="none";
		}
	}
	getSelected();
</script>
<?php include("admin.footer.inc.php"); ?>