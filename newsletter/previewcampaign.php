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


<table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center"><a href="createcamp.php"> New Campaign</a> | <a href="managecamp.php?action=all">All Campaigns</a> | <a href="managecamp.php?action=active">Active Campaigns</a> | <a href="managecamp.php?action=pending">Pending Campaigns</a> | <a href="managecamp.php?action=inactive">Inactive Campaigns</a></td>
  </tr>
</table>
<?php if(isset($_REQUEST['new'])){?>
<table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center"><a href="createcamp.php"> </a></td>
  </tr>
  <tr>
    <td align="center">&nbsp;</td>
  </tr>
  <tr>
    <td align="center">1. Email Details <b>&raquo</b> 2. Attach Files <b>&raquo</b> <strong>3. Preview Campaign</strong> <b>&raquo</b> 4. Activate Campaign </td>
  </tr>
  <tr>
    <td align="center">&nbsp;</td>
  </tr>
</table>
<?php }?>
<br>

  <input name="id" type="hidden" id="id" value="<?php echo $id; ?>">
  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
     <tr>
     
      <td align="center" colspan="3"><span class="inserted">Preview the Campaign </span></td>
  
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
      <td><?php 
		if($mysql->total("".$table_prefix."ea_cnc","campid=$id")!=0){
		
		echo $mysql->echo_one("select ".$table_prefix."email_advt_category.name from ".$table_prefix."email_advt_category,".$table_prefix."ea_cnc where ".$table_prefix."email_advt_category.id=".$table_prefix."ea_cnc.catid and ".$table_prefix."ea_cnc.campid=$id")."<span class=\"info\"> [Mailing List]</span>";
		}
		else {
		echo "All Emails";
		}
		
  
  ?>&nbsp;</td>
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
      <td colspan="2"><?php if($row[8]==1) {?><iframe width="700" height="300" src="viewbody.php?id=<?php echo $id?>"></iframe><?php }else {?><?php 
	 $contant=$row[5];
	$final_str="";
	  	if($row[16]!=0)
							{
								$final_str=$mysql->echo_one("select content from ".$table_prefix."email_template where id='$row[16]'");
								$final_str=str_replace("{CONTENT}",$contant,$final_str);
							}
						else
							{
								$final_str=$contant;
							}
	  echo nl2br(htmlspecialchars($final_str)); 
	  
	  }?></td>
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
      <td colspan="2" align="center">&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="2" align="center">&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td colspan="2" align="center"><?php if(!isset($_REQUEST['new'])) {?><?php if($row[9]==0 ) {?>      
            <form name="form1" method="post" action="editadst.php?action=activate&id=<?php echo $row[0]; ?>">
              <input type="submit" name="Submit" value="Activate the Campaign !">
            </form>            <?php }else { ?>            <form name="form2" method="post" action="editadst.php?action=inactivate&id=<?php echo $row[0]; ?>">
              <input type="submit" name="Submit2" value="Inactivate the Campaign !">
      </form>            <?php } } else {?> 
	  
	  
	  
	  <table width="100%"  border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td align="left">&nbsp;</td>
          <td align="center">&nbsp;</td>
        </tr>
        <tr>
          <td align="left"><h3><strong>More actions on this campaign </strong></h3></td>
          <td align="center">&nbsp;</td>
        </tr>
        <tr>
          <td width="85%" align="left"> <a href="attach.php?id=<?php echo $id?>&new=yes" class="mainmenu"><strong>Attach Files</strong></a> | <a href="editadst.php?id=<?php echo $id;?>&action=activate&new=yes" class="mainmenu"><strong>Activate Campaign</strong></a></td>
          <td width="3%" align="center">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="2" align="center">&nbsp;</td>
        </tr>
      </table>
	  <?php }?>            &nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  </table>
<?php include("admin.footer.inc.php"); ?>