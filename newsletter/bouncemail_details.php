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
	 if(isset($_COOKIE['inout_sub_admin']))
				{
				   $aid=getAdminId($mysql);
				   $adminname=$mysql->echo_one("select username from  ".$table_prefix."subadmin_details where id=$aid");
				 mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','$adminname attempted unauthorized access to view bounced email details','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");
				 echo "<br><span class=\"already\">You don't have  access to this page</span>   <a href=\"javascript:history.back(-1);\">Go Back</a><br><br>";
				 include_once("admin.footer.inc.php");
				 exit(0);
				}

$pagesize=50;
$pageno=1;
if(isset($_GET['page']))
	$pageno=$_GET['page'];
	
//echo "select id,email,bouncetype ".$table_prefix." from bounce_mail_details";
$bouncemail1=mysql_query("select id,email,bouncetype ,eid  from ".$table_prefix."bounce_mail_details");
 $bouncemail=mysql_query("select id,email,bouncetype ,eid  from ".$table_prefix."bounce_mail_details  LIMIT ".(($pageno-1)*$pagesize).", $pagesize");
$total=mysql_num_rows($bouncemail1);
$cnt=mysql_num_rows($bouncemail);
if(mysql_num_rows($bouncemail)>0)
{





?>
<form name="form1" method="post" action="massedit.php">
 <input type="hidden" name="param" id="param" value="<?php echo $_SERVER['REQUEST_URI'];?>">
<table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr >
    <td align="center"> </td>
  </tr>
</table>
<br>
<span class="inserted">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;All Bounced Email Are Listed Below</span> <br>
<br>

<table width="95%" align="center" cellpadding="0" cellspacing="0">
<?php
if($total>=1)
	{
	?>
  <tr  height="25">
  <td colspan="3"  >

		Showing Bounced Emails <span class="inserted"><?php echo ($pageno-1)*$pagesize+1; ?></span> -
        <span class="inserted"> <?php if(($pageno*$pagesize)>$total) echo $total; else echo $pageno*$pagesize; ?>
        </span>&nbsp; of &nbsp;<span class="inserted"><?php echo $total; ?></span>&nbsp;&nbsp;	   </td>
    <td align="right" ><?php echo $paging->page($total,$pagesize,"","bouncemail_details.php?action=$_REQUEST[action]".$cc.$searchappnd); ?></td>
  </tr>
  
  <?php
  }
  
?> 
<tr >
  <td height="30" colspan="4"><a href="javascript:checkAll('document.form1.C',<?php echo $cnt;?>);">Check all</a> | <a href="javascript:uncheckAll('document.form1.C',<?php echo $cnt;?>);">Uncheck all</a> | <a href="javascript:switchAll('document.form1.C',<?php echo $cnt;?>);">Switch Selection</a></td>
  <td></td>
</tr>
<tr bgcolor="#CCCCCC">
  <td width="3%">&nbsp;</td>
<td width="30%" height="30">&nbsp;&nbsp;<strong>Email Address</strong></td>
<td width="31%"><strong>Bounce Type</strong></td>
<td width="36%"><strong>Action</strong></td>
<td width="0%"> </td>
</tr>
<?php
$i=0;
$fst=1000;
$lst=0;
		
while($bouncemairow=mysql_fetch_row($bouncemail))
{
if($i==0)
			$fst=$bouncemairow[0];
if($i==$pagesize)
			 break;
?>
<tr height="25">
  <td height="31" style="border-bottom:1px solid #CCCCCC; "><input type="checkbox" name="C<?php echo $i; ?>" id="C<?php echo $i; ?>"  value="<?php echo $bouncemairow[3];?>"/></td>
  <td style="border-bottom:1px solid #CCCCCC; "><?php echo $bouncemairow[1];?></td>
  <td style="border-bottom:1px solid #CCCCCC; "><?php echo $bouncemairow[2];?></td>
  <td style="border-bottom:1px solid #CCCCCC; "><a href="editem.php?action=delete&id=<?php echo $bouncemairow[3]; ?>&param=<?php echo urlencode($_SERVER['REQUEST_URI']);?>">Delete</a> &nbsp;|&nbsp;<a href="editem.php?action=unsub&id=<?php echo $bouncemairow[3]; ?>&param=<?php echo urlencode($_SERVER['REQUEST_URI']);?>">Unsubscribe</a></td>
</tr>

 



<?php
    $i+=1; 
	$lst=$bouncemairow[0];
}
?>
 <tr >
  <td height="30" colspan="4"><a href="javascript:checkAll('document.form1.C',<?php echo $cnt;?>);">Check all</a> | <a href="javascript:uncheckAll('document.form1.C',<?php echo $cnt;?>);">Uncheck all</a> | <a href="javascript:switchAll('document.form1.C',<?php echo $cnt;?>);">Switch Selection</a></td>
  <td></td>
</tr>

<tr height="25">
  <td height="31" colspan="4" >
	<?php
	 if(!isset($_COOKIE['inout_sub_admin']))
		  	{
			?>
	 <input type="submit" name="Submit" value="Delete !">&nbsp;|&nbsp;
	 <?php 
	 }
	 ?>
	   <?php 
	  if(!isset($_COOKIE['inout_sub_admin']))
	  {
	  ?>
	  	<input name="Submit" type="submit" id="Submit" value="Unsubscribe !">&nbsp;
	  <?php
	  }
	  ?></td>
  </tr>

<?php
	if($total>=1)
	{
	?>
  <tr  height="25">
  <td colspan="3"  >

		Showing Bounced Emails <span class="inserted"><?php echo ($pageno-1)*$pagesize+1; ?></span> -
        <span class="inserted"> <?php if(($pageno*$pagesize)>$total) echo $total; else echo $pageno*$pagesize; ?>
        </span>&nbsp; of &nbsp;<span class="inserted"><?php echo $total; ?></span>&nbsp;&nbsp;	   </td>
    <td align="right" ><?php echo $paging->page($total,$pagesize,"","bouncemail_details.php?action=$_REQUEST[action]".$cc.$searchappnd); ?></td>
  </tr>
  
  <?php
  }
  
?>
</table>
</form>
<?php
}
else
{
echo "<br>-No Bounced Emails-<br><br>";

}


 include_once("admin.footer.inc.php"); ?>