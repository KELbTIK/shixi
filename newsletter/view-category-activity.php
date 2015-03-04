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
	 if(isset($_COOKIE['inout_sub_admin']))
				{
				   $aid=getAdminId($mysql);
				   $adminname=$mysql->echo_one("select username from  ".$table_prefix."subadmin_details where id=$aid");
				 mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','$adminname attempted unauthorized access to view logs','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");
				 echo "<br><span class=\"already\">You don't have  access to this page</span>   <a href=\"javascript:history.back(-1);\">Go Back</a><br><br>";
				 include_once("admin.footer.inc.php");
				 exit(0);
				}

?>
<style type="text/css">
<!--
.style1 {	color: #666666;
	font-weight: bold;
}
.style2 {	color: #006600;
	font-weight: bold;
}
-->
</style>
 <table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td colspan="2" align="center" scope="row"><a href="view-time-activity.php">Time Based Logs</a> | <a href="view-admin-activity.php">Administrator Logs</a> | <a href="view-category-activity.php">Categorized Logs</a></td>
    </tr>
    <tr>
      <td colspan="2" align="left" scope="row">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="2" align="left" scope="row"><span class="style2">Category  based logs </span></td>
    </tr>
    <tr>
      <td colspan="2" align="left" scope="row">&nbsp;</td>
    </tr>
    <tr>
      <td width="55%" align="left" scope="row">
	    <form name="form1" method="get" action="view-category-activity.php">
	    Show details of     
	      <select name="admin" id="admin">
		  <?php if(!isset($_GET['admin']))
     	 {  	
		 ?>
		   <option value="1" selected>Email List Logs</option>
		   
		 <?php
		 }
		 else
		 {
		 ?>
		     <option value="1" <?php 
			  				  if($_GET['admin']==1) echo "selected";			  
			  ?>>Email List Logs</option>      
		  <?php
		
	 	 }
		 ?>
		  
		<option value="2" <?php 
			  				  if($_GET['admin']==2) echo "selected";			  
			  ?>>Email Address Logs</option>
		  <option value="3" <?php 
			  				  if($_GET['admin']==3) echo "selected";			  
			  ?>>Email Campaign Logs</option>
		  <option value="4" <?php 
			  				  if($_GET['admin']==4) echo "selected";			  
			  ?>>Extra Parameter Logs</option>
		  <option value="5" <?php 
			  				  if($_GET['admin']==5) echo "selected";			  
			  ?>>Public Logs</option>
		  <option value="6" <?php 
			  				  if($_GET['admin']==6) echo "selected";			  
			  ?>>Administrator Logs</option>
	      </select>
         <input type="submit" name="Submit" value="View logs !">
      </form></td>
    
    </tr>
  </table>
<?php
	       $perpagesize=50;
          $pageno=1;
         if(isset($_GET['page']))
	     $pageno=$_GET['page'];
		 if(!isset($_GET['admin']))
		 $type=1;
		 else
		 $type=$_GET['admin'];
		 $total=$mysql->echo_one("select count(*) from ".$table_prefix."admin_log_info where type='$type'");
		  $result=mysql_query("select * from ".$table_prefix."admin_log_info  where type='$type' order by time desc LIMIT ".(($pageno-1)*$perpagesize).", ".$perpagesize);
		  $no=mysql_num_rows($result);
		 if($total>0)
           {
		  ?>
 
    

	   <table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td colspan="2" align="left" scope="row">&nbsp;</td>
        <td colspan="2" align="left" scope="row">&nbsp;</td>
      </tr>
      <tr>
      <td colspan="2" align="left" scope="row"><strong>Activity logs sorted by time are listed below </strong></td>
      <td colspan="2" width="53%" align="left" scope="row">&nbsp;</td>
      </tr>
    <tr>
      <td colspan="2" align="left" scope="row">&nbsp;</td>
      <td align="left" scope="row">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="2" scope="row"><?php if($total>=1) {?>
Showing Activities <span class="inserted"><?php echo ($pageno-1)*$perpagesize+1; ?></span> - <span class="inserted">
<?php if((($pageno)*$perpagesize)>$total) echo $total; else echo ($pageno)*$perpagesize; ?>
</span>&nbsp;of <span class="inserted"><?php echo $total; ?></span>&nbsp;
<?php } ?></td>
      <td colspan="2" align="right"scope="row"><?php echo $paging->page($total,$perpagesize,"","view-category-activity.php?admin=$type"); ?></td>
    </tr>
    <tr>
      <td colspan="2" scope="row">&nbsp;</td>
      <td colspan="2"scope="row">&nbsp;</td>
    </tr>
  </table>
  <table width="100%"  border="0" cellpadding="0" cellspacing="0">

    <tr bgcolor="#CCCCCC">
      <td width="1%" height="30" align="left">      
      <td width="22%" align="left"><span class="style1">Time<br>
        </span>
      <td width="19%" align="left"><span class="style1">Administrator<br>
      </span></td>
      <td width="41%"><span class="style1">Activity</span></td>
      <td width="17%">&nbsp;</td>
    </tr>
    <?PHP
	$single=0;
		  while($row=mysql_fetch_row($result))
          {
		 
		  ?>
    <tr <?php if(($single%2)==0) { ?>bgcolor="#EFEFEF"<?php }?>>
      <td align="left" style="border-bottom:1px solid #CCCCCC; ">&nbsp;</td>
      <td height="25" align="left" style="border-bottom:1px solid #CCCCCC; "><?php $today = date("M j, Y, g:i a",$row[3]);
	  echo $today;
	  ?></td>
      <td height="25" align="left" style="border-bottom:1px solid #CCCCCC; "><?php 
	  $str="";
	  if($row[1]==0)
	  $str="Super Administrator";
	  else if($row[1]==-1)
	  $str="- System -";
	   else
	  $str=$mysql->echo_one("select username from ".$table_prefix."subadmin_details where id='$row[1]'");
	  echo $str;
	  ?></td>
      <td height="25" colspan="2" align="left" style="border-bottom:1px solid #CCCCCC; "><?php echo $row[2];?></td>
    </tr>

	<?php
	$single+=1;
	 } ?>
	    <tr>
	      <td colspan="3" scope="row">&nbsp;</td>
	      <td colspan="2" align="right"scope="row">&nbsp;</td>
    </tr>
	    <tr>
      <td colspan="3" scope="row"><?php if($total>=1) {?>
    Showing Activities <span class="inserted"><?php echo ($pageno-1)*$perpagesize+1; ?></span> - <span class="inserted">
    <?php if((($pageno)*$perpagesize)>$total) echo $total; else echo ($pageno)*$perpagesize; ?>
    </span>&nbsp;of <span class="inserted"><?php echo $total; ?></span>&nbsp;
    <?php } ?></td>
      <td colspan="2" align="right"scope="row"><?php echo $paging->page($total,$perpagesize,"","view-category-activity.php?admin=$type"); ?></td>
    </tr>
  </table>

    <?php 
		   }
		  else
		  {
		  echo "<br>- There is no record to display -<br><br>";
		  include_once("admin.footer.inc.php");
		  exit(0);
		  }
		  ?>
<?php include_once("admin.footer.inc.php"); ?>
