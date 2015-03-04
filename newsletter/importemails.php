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


?>
<style type="text/css">
<!--
.style4 {color: #FF0000}
-->
</style>
<?php include("admin.header.inc.php");

$getListSql="select * from ".$table_prefix."email_advt_category order by name";
if(isset($_COOKIE['inout_sub_admin']))
{
	$subAdminId=getAdminId($mysql);
	$getListSql="SELECT a.*	FROM ".$table_prefix."email_advt_category a inner join 
	( SELECT eid FROM ".$table_prefix."admin_access_control where aid=$subAdminId ) b
	on a.id=b.eid order by a.name";
}

 ?>




<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center"><a href="importemails.php?type=single"> Single Email</a> | <a href="importemails.php?type=many"> Multiple Emails</a> | <a href="importemails.php?type=source">Extract  from HTML</a> | <a href="emailadvturl.php">Extract  from URLs</a> | <a href="importfromdb.php">Import from MySQL</a></td>
  </tr>
</table>
<center>
<form name="form1" method="post" action="extractems.php<?php if($_REQUEST['type']=="source") {echo "?html=true";}?>">
  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="6%">&nbsp;</td>
      <td colspan="3">&nbsp;</td>
      <td width="5%">&nbsp;</td>
    </tr>
    <tr>
    <td>&nbsp;</td>
    <td colspan="3"><strong>Select upto 3 Email Lists where you want to add the emails. You can add emails to more lists later. You should select atleast one list here. <br>
        <br>
        </strong>
      <select name="category">
	    <option value="" selected>- Select a List - </option>
	    <?php
		 $result=mysql_query($getListSql);
	  while($row=mysql_fetch_row($result)){
	  echo '<option value="'.$row[0].'">'.$row[1].'</option>'; 
	  }?>
      </select>     <span class="style4">*</span> <br>
    <select name="category2">
	  <option value="" selected>- Select a List - </option>
	  <?php $result=mysql_query($getListSql);
	  while($row=mysql_fetch_row($result)){
	  echo '<option value="'.$row[0].'">'.$row[1].'</option>'; 
	  }?>
      </select>
    <br>
    <select name="category3" id="category3">
	  <option value="" selected>- Select a List - </option>
	  <?php $result=mysql_query($getListSql);
	  while($row=mysql_fetch_row($result)){
	  echo '<option value="'.$row[0].'">'.$row[1].'</option>'; 
	  }?>
      </select>
    <br></td>
    <td>&nbsp;</td>
  </tr>
  <?php if($_REQUEST['type']=="source") {
  ?><tr>
    <td>&nbsp;</td>
    <td colspan="3"><strong><br>
      Extract email addresses from HT</strong><strong>ML source </strong><br>      
      <br>
      Place the source code in the  field below. 
      <br></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="3"><textarea name="emails" cols="60" rows="15" id="emails"></textarea><span class="style4">*</span>
      <br>
      You can right click on a webpage and click on the View Source to see the source of the webpage. Copy the entire source and paste in the field above and click the 'Extract Emails' button. All emails in the source code will be automatically identified and added to the database. <br>
      <input type="submit" name="Submit" value="Extrat Emails!"></td>
    <td>&nbsp;</td>
  </tr><?php } ?>  <?php if($_REQUEST['type']=="single") {?><tr>
    <td>&nbsp;</td>
    <td colspan="3"><strong><br>
      Add a Single Email Address</strong><br>
      <br>
      Please enter the email address below.</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="3"><input name="emails" type="text" id="emails" value="" size="60">
    <span class="style4">*</span> </td>
	<td>&nbsp;</td>
</tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td width="0%">&nbsp;</td>
  </tr>
  <tr>
<td>&nbsp;</td>
        <td width="16%">Name</td> 
		<td width="52%"><input name="name" type="text" id="name2">
		  (Optional)
		    <input name="extra" type="hidden" id="extra2" value="single">	      <br>
	    </td>
		<td>&nbsp;</td>
		<td width="5%">&nbsp;</td>
		<td>&nbsp;</td>
    <td width="0%">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <?php
  $extrafields = mysql_query("select * from ".$table_prefix."extra_personal_info order by id ");

while($fielddetails=mysql_fetch_row($extrafields))
  {
   ?>
   		<tr>
		<td>&nbsp;</td>
		 <td width="16%"><?php echo $fielddetails[1]; ?></td> 
          <td width="52%"><?PHP if($fielddetails[4]==1) {?><input name="<?php echo "extra_personal_info".$fielddetails[0]; ?>" type="text" id="<?php echo "extra_personal_info".$fielddetails[0]; ?>" value="" style="width:300px;"><?PHP } else if($fielddetails[4]==2) {?><textarea name="<?php echo "extra_personal_info".$fielddetails[0]; ?>"  id="<?php echo "extra_personal_info".$fielddetails[0]; ?>" style="width:300px;" rows="5"></textarea><?php } else {?> <select name="<?php echo "extra_personal_info".$fielddetails[0]; ?>"  id="<?php echo "extra_personal_info".$fielddetails[0]; ?>" style="width:150px;"><?php  $options=explode(",",$fielddetails[5]);
		  $str="";
				 for($i=0;$i<count($options);$i++)
				 {
					
					  $str.='<option value="'.$options[$i].'" selected>'.$options[$i].'</option>';
					
				 }
				 echo $str;?></select><?php }?> (Optional)</td>
          <td width="21%">&nbsp;</td>
		  <td>&nbsp;</td>
        </tr> 
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
  <?php
   }
  ?>
  <tr>
  <td>&nbsp;</td>
    <td colspan="3"><br>
      If the entered email address is not in the database already, it will be added to the database when you click on the 'Add' button. <br>
      <input type="submit" name="Submit" value="Add!"></td>
	  <td>&nbsp;</td>
 </tr>	  
  <?php } ?>  <?php if($_REQUEST['type']=="many" && !isset($_REQUEST['add'])) {?>
  <tr>
    <td>&nbsp;</td>
    <td colspan="3"><br> 
      <strong>Add multiple email addresses</strong>  ( <a href="?type=many&add=name">Multiple Input Mode</a> ) <p>Please enter the email addresses and optional fields below. Add optional fields after ech email address separated by a colon(:) in the following format. Comma separation can also be used instead of colon.
	  <br> <strong>email : name<?php 
	  	$extrafields = mysql_query("select * from ".$table_prefix."extra_personal_info order by id ");
			while($fielddetails=mysql_fetch_row($extrafields))
			{
			 echo " : ".$fielddetails[1];
			}
			?></strong><br>
			<span class="already">Colon should not be used in extra field values .</span>
	  <br>
	  You may use  new line as  separator after each email.</p>      </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="3"><p>
        <textarea name="emails" cols="60" rows="15" id="emails"></textarea><span class="style4">*</span>
        <br>
      Click on the button below after you finish entering email addresses </p>
      <p>
        <input name="Submit2" type="submit" id="Submit" value="Add Email Addresses !"> 
      </p></td>
    <td>&nbsp;</td>
  </tr><?php } ?><?php if($_REQUEST['type']=="many" && $_REQUEST['add']=="name") {?><tr>
    <td>&nbsp;</td>
    <td colspan="3" rowspan="2" valign="top"><p> 
          <strong>Add multiple email addresses with details </strong> (<a href="importemails.php?type=many"> Single Input Mode</a>)<br>      
          <br>
      Please enter the email addresses and corresponding details below 
	   <table width="100%"  border="0" align="left" cellpadding="0" cellspacing="0">
           <?php for($i=0;$i<5;$i+=1) {?>
		<tr>
          <td colspan="2"><strong>Email Address <?php if($i==0) {?><span class="style4">*</span> <?php }?></strong></td>
          <td width="54%"><input name="email<?php echo $i?>" type="text" id="email<?php echo $i?>" value="" size="40">
					</td>
        </tr>
     
        <tr>
          <td width="1%">&nbsp;</td>
          <td width="45%">&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td colspan="2"><strong>Name</strong></td>
          <td><input name="name<?php echo $i?>" type="text" id="name<?php echo $i?>" value="" size="40">
            (Optional)</td>
        </tr> 
 
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
		<?php
		
		$extrafields = mysql_query("select * from ".$table_prefix."extra_personal_info order by id ");
		while($fielddetails=mysql_fetch_row($extrafields))
  		{
  		?>
        <tr>
          <td colspan="2"><strong><?php echo $fielddetails[1]; ?></strong></td>
          <td><?PHP if($fielddetails[4]==1) {?><input name="<?php echo "extra_personal_info".$fielddetails[0]; ?>" type="text" id="<?php echo "extra_personal_info".$fielddetails[0]; ?>" value="" style="width:255px;"><?PHP } else if($fielddetails[4]==2) {?><textarea name="<?php echo "extra_personal_info".$fielddetails[0]; ?>"  id="<?php echo "extra_personal_info".$fielddetails[0]; ?>" style="width:255px;" rows="5"></textarea><?php } else {?> <select name="<?php echo "extra_personal_info".$fielddetails[0]; ?>"  id="<?php echo "extra_personal_info".$fielddetails[0]; ?>" style="width:150px;"><?php  $options=explode(",",$fielddetails[5]);
		  $str="";
				 for($k=0;$k<count($options);$k++)
				 {
					
					  $str.='<option value="'.$options[$k].'" selected>'.$options[$k].'</option>';
					
				 }
				 echo $str;?></select><?php }?> (Optional)</td>
        </tr>
		        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
		<?php
		}
		?>
        <tr>
          		<td colspan =4><hr></hr></td>
        </tr>
		 <tr>
          		<td colspan =4><br></td>
        </tr>
		 <?php } ?>
        <tr>
          <td>&nbsp;</td>
          <td colspan="2"> 
            <p>
              <input name="Submit22" type="submit" id="Submit2" value="Add Emails!">
              <input name="add" type="hidden" id="add" value="name">
</p></td>
          </tr>
      </table></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td valign="top">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
	
	
	<?php } ?></td>
   
  <tr>
    <td>&nbsp;</td>
    <td colspan="3">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
</form>
</center>
<?php include("admin.footer.inc.php"); ?>