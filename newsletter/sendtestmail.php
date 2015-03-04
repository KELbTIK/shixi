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
$result=mysql_query("select * from ".$table_prefix."email_advt_curr_run where id=$id");
$row=mysql_fetch_row($result);
?><?php include("admin.header.inc.php"); ?>
<style type="text/css">
<!--
.style1 {
font-family: Arial, Helvetica, sans-serif; font-size: 18px;color:#000000}
-->
</style>


<table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center"><a href="createcamp.php"> New Campaign</a> | <a href="managecamp.php?action=all">All Campaigns</a> | <a href="managecamp.php?action=active">Active Campaigns</a> | <a href="managecamp.php?action=pending">Pending Campaigns</a> | <a href="managecamp.php?action=inactive">Inactive Campaigns</a></td>
  </tr>
</table>

  
  <form action="testsend.php" name="testsend" method="post" enctype="multipart/form-data">
  <input name="id" type="hidden" id="id" value="<?php echo $id; ?>">
  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="2%">&nbsp;</td>
      <td width="16%">&nbsp;</td>
      <td width="82%">&nbsp;</td>
    </tr>
     <tr>
     
      <td align="center" colspan="3"><span class="style1">Sent test email </span></td>
  
    </tr>

    <tr>
      <td width="2%">&nbsp;</td>
      <td width="16%">&nbsp;</td>
      <td width="82%">&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><strong>From: </strong></td>
      <td><?php echo $row[6]; ?>&nbsp;&lt;<?php echo $row[7]; ?>&gt; </td>
    </tr>
     <tr>
       <td>&nbsp;</td>
       <td>&nbsp;</td>
       <td>&nbsp;</td>
     </tr>
     <tr>
      <td>&nbsp;</td>
      <td><strong>To:</strong></td>
      <td><input type="text" name="toemail" value="" maxlength="70" size="50">&nbsp;</td>
    </tr> 
     <tr>
       <td>&nbsp;</td>
       <td>&nbsp;</td>
       <td>&nbsp;</td>
     </tr>
    <tr>
      <td>&nbsp;</td>
      <td><strong>Subject:</strong></td>
      <td><?php echo $row[4]; ?></td>
    </tr>
  
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td valign="top"><strong>Attachments:</strong></td>
      <td valign="top"><?php if($mysql->total("".$table_prefix."ea_attachments","cid=$id")==0) echo "No Attachments"; ?><?php $result=mysql_query("select * from ".$table_prefix."ea_attachments where cid=$id"); ?>
       <?php while($ro=mysql_fetch_row($result)){?>  
        <?php echo $ro[2]?><br>
<?php } ?>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="2"><strong>Email Body<br>
        <br> 
      </strong></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="2"><?php if($row[8]==1) {?><iframe width="750" height="300" src="viewbody.php?id=<?php echo $id?>"></iframe><?php }else {
	  ?><?php 
	  $contant=$row[5];
	  	if($row[16]!=0)
							{
								$final_str=$mysql->echo_one("select content from ".$table_prefix."email_template where id='$row[16]'");
								$final_str=str_replace("{CONTENT}",$contant,$final_str);
							}
						else
							{
								$final_str=$contant;
							}
	   echo nl2br(htmlspecialchars($final_str)); }?></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="2"><hr width="100%" size="1"></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="2"><span class="info">Note: Emails can't be unsubscribed in the preview mode. Unsubscribe link is dynamically generated for each email address  at the time of sending emails. </span></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="2" align="center"><input type="submit" name="submit" value="Send mail">&nbsp;</td>
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
  </table> 
</form>

<?php include("admin.footer.inc.php"); ?>