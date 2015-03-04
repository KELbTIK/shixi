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
      <td colspan="2" align="left" scope="row"><span class="style2">Administrator based logs </span></td>
    </tr>
    <tr>
      <td colspan="2" align="left" scope="row">&nbsp;</td>
    </tr>
    <tr>
      <td width="55%" align="left" scope="row">
	    <form name="form1" method="get" action="view-admin-activity.php">
	    Show activities of  
	      <select name="admin" id="admin">
		  <?php if(!isset($_GET['admin']))
     	 {  	
		 ?>
		   <option value="0" selected>Super Admin</option>
		   
		 <?php
		 }
		 else
		 {
		 ?>
		    <option value="0" <?php 
			  				  if($_GET['admin']==0) echo "selected";			  
			  ?>>Super Admin</option>
		           
		<?php
		
	 	 }
		 $rslt=mysql_query("select * from ".$table_prefix."subadmin_details");
		while($rw=mysql_fetch_row($rslt))
		{
		  echo '<option value="'.$rw[0].'" '; 
	 	  if($_GET['admin']==$rw[0])
		  	 echo 'selected';			  
		  echo '>'.$rw[1].'</option>'; 
	 	 }
	  	 	 ?>
  
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
     	 $admin=0;
		 else
		 $admin=$_GET['admin'];
		 $total=$mysql->echo_one("select count(*) from ".$table_prefix."admin_log_info where aid='$admin'");
		  $result=mysql_query("select * from ".$table_prefix."admin_log_info  where aid='$admin' order by time desc LIMIT ".(($pageno-1)*$perpagesize).", ".$perpagesize);
		  $no=mysql_num_rows($result);
		 if($total>0)
           {
		  ?>
 
    
<form name="form1" method="post" action="">

	   <table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td colspan="2" align="left" scope="row">&nbsp;</td>
        <td colspan="2" align="left" scope="row">&nbsp;</td>
      </tr>
      <tr>
      <td colspan="4" align="left" scope="row"><strong>Activity logs sorted by time are listed below </strong></td>
      </tr>
    <tr>
      <td colspan="2" align="left" scope="row">&nbsp;</td>
      <td colspan="2" align="left" scope="row">&nbsp;</td>
    </tr>
    <tr>
      <td width="46%" scope="row"><?php if($total>=1) {?>
        Showing Activities <span class="inserted"><?php echo ($pageno-1)*$perpagesize+1; ?></span> - <span class="inserted">
<?php if((($pageno)*$perpagesize)>$total) echo $total; else echo ($pageno)*$perpagesize; ?>
</span>&nbsp;of <span class="inserted"><?php echo $total; ?></span>&nbsp;
<?php } ?></td>
      <td colspan="3" align="right" scope="row"><?php echo $paging->page($total,$perpagesize,"","view-admin-activity.php"); ?></td>
      </tr>
    <tr>
      <td height="19" colspan="2" scope="row">&nbsp;</td>
      <td colspan="2" scope="row">&nbsp;</td>
    </tr>
  </table>
  <table width="100%"  border="0" cellpadding="0" cellspacing="0">

    <tr bgcolor="#CCCCCC">
      <td width="1%" height="30" align="left">&nbsp;</td>
      <td width="23%" align="left"><span class="style1">Time<br>
      </span></td>
      
      <td colspan="2"><span class="style1">Activity</span></td>
    </tr>
    <?PHP $single=0;
		  while($row=mysql_fetch_row($result))
          {
		 
		  ?>
    <tr <?php if(($single%2)==0) { ?>bgcolor="#EFEFEF"<?php }?>>
      <td align="left" style="border-bottom:1px solid #CCCCCC; ">&nbsp;</td>
      <td height="25" align="left" style="border-bottom:1px solid #CCCCCC; "><?php $today = date("M j, Y, g:i a",$row[3]);
	  echo $today;
	  ?></td>
      <td colspan="2" height="25" align="left" style="border-bottom:1px solid #CCCCCC; "><?php echo $row[2];?></td>
    </tr>
	<?php 
	$single+=1;

	} ?>
	
	    <tr>
	      <td colspan="4" align="left">&nbsp;</td>
    </tr>
	    <tr>
      <td colspan="3" align="left"><?php if($total>=1) {?>
Showing Activities <span class="inserted"><?php echo ($pageno-1)*$perpagesize+1; ?></span> - <span class="inserted">
<?php if((($pageno)*$perpagesize)>$total) echo $total; else echo ($pageno)*$perpagesize; ?>
</span>&nbsp;of <span class="inserted"><?php echo $total; ?></span>&nbsp;
<?php } ?></td>
      <td width="54%" align="right"><?php echo $paging->page($total,$perpagesize,"","view-admin-activity.php"); ?></td>
    </tr>
  </table>
</form>

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
