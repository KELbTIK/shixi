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
error_reporting(0);
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

include("admin.header.inc.php");?>


<?php 


$cc="";
if(isset($_REQUEST['cid']))
{
$ccid=$_REQUEST['cid'];
//echo  $ccid;
}
else
$ccid=0;

if($ccid==0)
$cc="";
else
$cc="&cid=$_REQUEST[cid]"; 
	
if(isset($_REQUEST['id']))
{
	$id=$_REQUEST['id'];
}	
else
{
	$lstrow=$mysql->select_last_row("".$table_prefix."email_advt","id");
	$id=$lstrow[0]+1; 
}

//echo $id;
$str="";
$str1="";
if($_REQUEST['action']=="unsub")
{
$str="and  a.unsubstatus=1";
$str1="and  b.unsubstatus=1";
}
if($_REQUEST['action']=="active")
{
$str="and  a.unsubstatus=0";
$str1="and  b.unsubstatus=0";
}
//echo $str;
$pagesize=50;
$pageno=1;
if(isset($_GET['page']))
	$pageno=$_GET['page'];

$searchappnd="";

if(isset($_GET['search']))
{
	$search=$_GET['search'];
	$radio=2;
	if(isset($_GET['radio']))
		$radio=$_GET['radio'];
	$searchappnd="&search=$search";
	
	if($radio==1)
	{
		$getEmailSql="select * from ".$table_prefix."email_advt a where a.id='".$search."' ";
		if(isset($_COOKIE['inout_sub_admin']))
		{
			$subAdminId=getAdminId($mysql);
			$getEmailSql="SELECT a.* FROM ".$table_prefix."email_advt a, ".$table_prefix."ea_em_n_cat b 
			inner join ( SELECT eid FROM ".$table_prefix."admin_access_control where aid=$subAdminId )c on b.cid=c.eid 
			where a.id= b.eid  AND a.id='".$search."' $str $str1 group by a.id ";
		}
		$result=mysql_query($getEmailSql);
		$total=mysql_num_rows($result);
	}
	else
	{
		$getEmailCntSql="select count(*) from ".$table_prefix."email_advt a where a.id<>0 $str AND a.email like '%".$search."%'  order by a.id desc;";
		$getEmailSql="select * from ".$table_prefix."email_advt a where a.id<>0 $str AND a.email like '%".$search."%' order by a.id desc  LIMIT ".(($pageno-1)*$pagesize).", $pagesize";
		if(isset($_COOKIE['inout_sub_admin']))
		{
			$subAdminId=getAdminId($mysql);
			$getEmailCntSql="select count(distinct a.id) from ".$table_prefix."email_advt a, ".$table_prefix."ea_em_n_cat b 
			inner join ( SELECT eid FROM ".$table_prefix."admin_access_control where aid=$subAdminId )c on b.cid=c.eid 
			where a.id= b.eid  AND a.email like '%".$search."%' $str $str1";
			$getEmailSql="SELECT a.* FROM ".$table_prefix."email_advt a, ".$table_prefix."ea_em_n_cat b 
			inner join ( SELECT eid FROM ".$table_prefix."admin_access_control where aid=$subAdminId )c on b.cid=c.eid 
			where a.id= b.eid  AND a.email like '%".$search."%' $str $str1 group by a.id order by a.id desc LIMIT ".(($pageno-1)*$pagesize).", $pagesize";
		}
		$total=$mysql->echo_one($getEmailCntSql);
		$result=mysql_query($getEmailSql);
	}
	//echo $getEmailCntSql;
	  
}
else if($ccid==0)
{
	  $getEmailCntSql="select count(*) from ".$table_prefix."email_advt a where a.id<>0 $str ;";
	  $getEmailSql="select * from ".$table_prefix."email_advt a where a.id<>0 $str order by a.id desc LIMIT ".(($pageno-1)*$pagesize).", $pagesize";
	  if(isset($_COOKIE['inout_sub_admin']))
	  {
		$subAdminId=getAdminId($mysql);
		$getEmailCntSql="select count(distinct a.id) from ".$table_prefix."email_advt a, ".$table_prefix."ea_em_n_cat b 
		inner join ( SELECT eid FROM ".$table_prefix."admin_access_control where aid=$subAdminId )c on b.cid=c.eid 
		where a.id= b.eid  $str $str1 ";
		$getEmailSql="SELECT a.* FROM ".$table_prefix."email_advt a, ".$table_prefix."ea_em_n_cat b 
		inner join ( SELECT eid FROM ".$table_prefix."admin_access_control where aid=$subAdminId )c on b.cid=c.eid 
		where a.id= b.eid  $str $str1 group by a.id order by a.id desc LIMIT ".(($pageno-1)*$pagesize).", $pagesize";
		
	  }
	  $total=$mysql->echo_one($getEmailCntSql);
	  $result=mysql_query($getEmailSql);
//echo $getEmailSql;
}
else
{//echo "hii";
	  if(isset($_COOKIE['inout_sub_admin']))
	  {
		$subAdminId=getAdminId($mysql);
		$flag=false;
		$resultlist=mysql_query("select eid from ".$table_prefix."admin_access_control where aid =$subAdminId");
		while($row=mysql_fetch_row($resultlist))
		{
		//echo $row[0];
			if($row[0]==$ccid)
			{
			 $flag=true;
			 break;
			}
		}
		if($flag==false)	  
		{
			if($mysql->total($table_prefix."ea_em_n_cat", "cid=$ccid")>0)
			{
				echo "<br><span class=\"already\">You dont have access to this list.</span><a href=\"javascript:history.back(-1);\">Go Back</a><br><br>";
				if($log_enabled==1)
				{
					$aid=getAdminId($mysql);
					$adminname=$mysql->echo_one("select username from  ".$table_prefix."subadmin_details where id=$aid");
					$entityname=$mysql->echo_one("select name from  ".$table_prefix."email_advt_category where id=$ccid");
					if($entityname!="")
						mysql_query("insert into ".$table_prefix."admin_log_info values('','$aid','$adminname attempted unauthorized access to view the list $entityname','".time()."','$CST_MLM_ADMIN_MANAGEMENT')");
				}
				include("admin.footer.inc.php");
				exit(0);
			}	
		}
	  }
	  $getEmailCntSql="select count(*) from ".$table_prefix."email_advt a,".$table_prefix."ea_em_n_cat b where b.eid=a.id 
	  and b.cid=$ccid and a.id<>0  $str1 order by a.id desc;";
	  $getEmailSql="select * from ".$table_prefix."email_advt a,".$table_prefix."ea_em_n_cat b where b.eid=a.id 
	  and b.cid=$ccid and a.id<$id  $str1 order by a.id desc  LIMIT ".(($pageno-1)*$pagesize).", $pagesize;";

	  $total=$mysql->echo_one($getEmailCntSql);
	  $result=mysql_query($getEmailSql);
}

$cnt=mysql_num_rows($result);
//echo  $getEmailCntSql;
echo mysql_error();//echo "select * from help where status=1 and id<$id $search order by id desc;";
//echo $row[0][0];

?>
<link href="style.css" rel="stylesheet" type="text/css">

<table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center"><a href="viewems.php<?php if(isset($_REQUEST['cid'])) echo "?cid=$_REQUEST[cid]";?>">All Emails</a>  | <a href="viewems.php?action=active<?php echo $cc;?>">Active Emails </a> | <a href="viewems.php?action=unsub<?php echo $cc;?>">Unsubscribed Emails</a> | <a href="category_viewall.php">Emails in Mailing Lists </a> | <a href="searchem.php">Search Emails</a> </td>
  </tr>
</table><br>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  
  <tr>
    <td height="25" colspan="4"><?php 
	$msg="";
	$msg.="<span class=\"inserted\">Showing all</span>";
	if(isset($_REQUEST['action']))
	{
		if($_REQUEST['action']=="active")
		{
		$msg.="<span class=\"inserted\"> active emails in</span>";
		
		}
		if($_REQUEST['action']=="unsub")
		{
		$msg.="<span class=\"inserted\"> unsubscribed emails in</span>";
		
		}
	}
	else
	{
	$msg.="<span class=\"inserted\"> emails in</span>";
	}
	if(isset($_REQUEST['cid']))
	{
	$cid=$_REQUEST['cid'];
	$cat = $mysql->echo_one("select name from ".$table_prefix."email_advt_category where id='$cid'");
	if($cid==0)
		$msg.="<span class=\"inserted\"> the system</span>";
	else
		$msg.="<span class=\"inserted\"> the list :</span><strong>".$cat."</strong>"; 
	
	}
	else
	{
		$msg.="<span class=\"inserted\"> the system</span>";
	}
	if(isset($_REQUEST['search']))
	{
	if($cnt!=0)
	echo "<span class=\"inserted\">Showing search results for </span><strong>".$_REQUEST['search']."</strong>"; 
	
	}
	else
		echo $msg; 
	?></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="50" colspan="4">The email addresses are listed below. 
	Emails displayed in <span class="error pagetable_activecell">RED</span> color are unsubscribed emails from system. If a list corresponding to an email is displayed in <span class="error pagetable_activecell">RED</span> it means that the email is unsubscribed from that particular list.<br><?php
	
	 if(!isset($_COOKIE['inout_sub_admin']))
	 {
	 ?>
		<span class="info">[It is highly recommended not to delete an unsubscribed email, because you may add again that email later by accident. Delete the same only if you unsubscribed an email by accident. You cannot activate an unsubscribed email.]<br>
		<br>
		</span>
	<?php
	}
	?>	</td>
    <td width="3%">&nbsp;</td>
  </tr>
  <form name="form2" method="get" action="viewems.php">
  <tr>
    <td height="35" colspan="4" ><?php 
	
$listcat='
<select name="cid">        
	  <option value="" selected> - Select an Email List - </option>';
	   $getListSql="select * from ".$table_prefix."email_advt_category order by name";
	  if(isset($_COOKIE['inout_sub_admin']))
	  {
		$subAdminId=getAdminId($mysql);
		$getListSql="SELECT a.*	FROM ".$table_prefix."email_advt_category a inner join 
		( SELECT eid FROM ".$table_prefix."admin_access_control where aid=$subAdminId ) b
		on a.id=b.eid order by a.name";
	  }
	  $result1=mysql_query($getListSql);
	  $c=$_REQUEST['cid'];
	  $a=trim($_REQUEST['action']);
	  
	 
	  while($row1=mysql_fetch_row($result1)){
	  if($row1[0]==$c)
	  $listcat.= '<option value="'.$row1[0].'"  selected >'.$row1[1].'</option>'; 
	  else
	  
	  $listcat.= '<option value="'.$row1[0].'"   >'.$row1[1].'</option>'; 
	  }
	  if($c==0)
	   $listcat.= '<option value="0" style="color:#000099;background-color:#00FFFF; " selected>System</option>'; 
	   else
	    $listcat.= '<option value="0" style="color:#000099;background-color:#00FFFF; ">System</option>'; 
	  $listcat.=' </select>'; echo "<strong>Show Emails in".$listcat; ?>
            <input name="Submit" type="submit" id="Submit" value="Search !">
			<?php if($a!="") {?>
      <input type="hidden" name="action" value="<?php echo $a;?>">
	  <?php } ?></td>
    <td>&nbsp;</td>
  </tr>
  </form>
  	<?php 
	if($total>=1)
	{
	?>
  <tr>
  <td width="38%" height="50" >

		Showing Emails <span class="inserted"><?php echo ($pageno-1)*$pagesize+1; ?></span> -
        <span class="inserted"> <?php if(($pageno*$pagesize)>$total) echo $total; else echo $pageno*$pagesize; ?>
        </span>&nbsp; of &nbsp;<span class="inserted"><?php echo $total; ?></span>&nbsp;  &nbsp;
	<?php 
		if($ccid=="")
		{
			//echo "database.";
		}	
		else
		{
			//echo $mysql->echo_one("select name  from ".$table_prefix."email_advt_category where id= '$ccid'")." list.";
		}
	
	?>    </td>
	    <td width="2%"></td>
    <td align="right" ><?php echo $paging->page($total,$pagesize,"","viewems.php?action=$_REQUEST[action]".$cc.$searchappnd); ?></td>
    <td align="right" >&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  
  <?php
  }
  
  if($cnt==0)
{
 	echo "<tr><td width=\"2%\"></td><td colspan=\"3\">- No Emails Found -<br><br><a href=\"javascript:history.back(-1);\">Go Back</a><br><br></td></tr></table>";
 	include("admin.footer.inc.php");
	exit(0);
}
?>
  
  <tr>
  <td colspan="4">
  

	<form name="form1" method="post" action="massedit.php">
     <table width="100%" cellpadding="3" cellspacing="3">
	  <input type="hidden" name="param" id="param" value="<?php echo $_SERVER['REQUEST_URI'];?>">
	 <tr>
      <td height="25" colspan="3"   valign="top"><a href="javascript:checkAll('document.form1.C',<?php echo $cnt;?>);">Check all</a> | <a href="javascript:uncheckAll('document.form1.C',<?php echo $cnt;?>);">Uncheck all</a> | <a href="javascript:switchAll('document.form1.C',<?php echo $cnt;?>);">Switch Selection</a></td>
	 </tr>
	
    <?php
	$i=0;
	$fst=1000;
	$lst=0;
	//echo $fst;
	while($row=mysql_fetch_row($result))
	{
		if($i==0)
			$fst=$row[0];
		if($i==$pagesize)
			 break;
	?>
    <tr>
       <td width="22" height="25" valign="top"><input name="C<?php echo $i; ?>" type="checkbox"  id="C<?php echo $i; ?>" value="<?php echo $row[0]; ?>"></td>
       <td width="859" height="35" valign="top">
		  	<?php
			 if($row[2]==1)//unsubscribed
			 {
			?>
              <span class="red_unsub">
			<?php 
			 }
			 if($mysql->total("".$table_prefix."ea_extraparam","eid=$row[0] and name='name'")!=0) //displaying name
			 	echo  "\"".$mysql->echo_one("select value from ".$table_prefix."ea_extraparam where eid=$row[0] and name='name'")."\" &lt;"; 
			 echo $row[1];
			 if($mysql->total("".$table_prefix."ea_extraparam","eid=$row[0] and name='name'")!=0) 
			 	echo "&gt;";   
			 if($row[2]==1)//unsubscribed
			 {
			?>
				</span>
			<?php 
			 }
			?> 
			<span class="note">
				<br>Email Lists :  
				<?php 
				$getListSql="select ".$table_prefix."ea_em_n_cat.cid,".$table_prefix."ea_em_n_cat.unsubstatus from ".$table_prefix."email_advt,".$table_prefix."ea_em_n_cat 
				where ".$table_prefix."email_advt.id=".$table_prefix."ea_em_n_cat.eid  and ".$table_prefix."email_advt.id=$row[0]";
	  			if(isset($_COOKIE['inout_sub_admin']))
				{
					$subAdminId=getAdminId($mysql);
					$getListSql="SELECT a.cid,a.unsubstatus FROM ".$table_prefix."ea_em_n_cat a inner join 
					( SELECT eid FROM ".$table_prefix."admin_access_control where aid=$subAdminId ) b on a.cid=b.eid 
					where a.eid= $row[0]";
					
				}
		//echo $getListSql;

				$result2=mysql_query($getListSql); 
	  		    while($r2=mysql_fetch_row($result2)) 
				{
				  $result3=mysql_query("select name,id from ".$table_prefix."email_advt_category where id=$r2[0]");
				  $r3=mysql_fetch_row($result3);
				  if($r2[1]==1)
  echo " <span class=\"red_unsub1\">'$r3[0]'</span>  <a href=\"delemcat.php?cid=$r2[0]&eid=$row[0]&param=".urlencode($_SERVER['REQUEST_URI'])."\">(Delete)</a>, ";
  else
  echo "'$r3[0]'  <a href=\"delemcat.php?cid=$r2[0]&eid=$row[0]&param=".urlencode($_SERVER['REQUEST_URI'])."\">(Delete)</a>, ";
				}
			    if(mysql_num_rows($result2)==0)
			    {
			      echo "Not in any list"; 
			    }
  		      	?>
			</span>		 </td>
         <td width="427" nowrap> 
		   <a href="editem.php?action=edit&id=<?php echo $row[0].$cc; ?>&param=<?php echo urlencode($_SERVER['REQUEST_URI']);?>">Edit</a>&nbsp;|&nbsp;
		   <?php 
		   if(!isset($_COOKIE['inout_sub_admin']))
		  	{
			?>
		   <a href="editem.php?action=delete&id=<?php echo $row[0].$cc; ?>&param=<?php echo urlencode($_SERVER['REQUEST_URI']);?>">Delete</a>
		   &nbsp;|&nbsp;
		   <?php } ?><a href="viewdetails.php?id=<?php echo $row[0].$cc; ?>&param=<?php echo urlencode($_SERVER['REQUEST_URI']);?>">View details</a>
            <?php
			if(!isset($_COOKIE['inout_sub_admin']))
		  	{
		   	 if($row[2]==0)//not unsubscribed
				{
				?>
              	&nbsp;|&nbsp;<a href="editem.php?action=unsub&id=<?php echo $row[0].$cc; ?>&param=<?php echo urlencode($_SERVER['REQUEST_URI']);?>">Unsubscribe</a>
            	<?php
				}
			}
			?>
			 <br><br>		 </td>
       </tr>
       <?php
	    $i+=1; 
		$lst=$row[0];
	}
	?>
        
    <tr>
      <td height="24" colspan="3"   valign="top"><a href="javascript:checkAll('document.form1.C',<?php echo $cnt;?>);">Check all</a> | <a href="javascript:uncheckAll('document.form1.C',<?php echo $cnt;?>);">Uncheck all</a> | <a href="javascript:switchAll('document.form1.C',<?php echo $cnt;?>);">Switch Selection</a></td>
	</tr>
	
	<tr>
	 <td colspan="3"><?php
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
	  	<input name="Submit" type="submit" id="Submit" value="Unsubscribe !">&nbsp;| &nbsp;
	  <?php
	  }
	  
	  $listcat='
	  <select name="category">        
	  <option value="" selected> - Select an Email List - </option>';
	  $getListSql="select * from ".$table_prefix."email_advt_category order by name";
	  if(isset($_COOKIE['inout_sub_admin']))
	  {
		$subAdminId=getAdminId($mysql);
		$getListSql="SELECT a.*	FROM ".$table_prefix."email_advt_category a inner join 
		( SELECT eid FROM ".$table_prefix."admin_access_control where aid=$subAdminId ) b
		on a.id=b.eid order by a.name";
	  }
	  $result=mysql_query($getListSql);
	  while($row=mysql_fetch_row($result))
	  {
	  	$listcat.= '<option value="'.$row[0].'">'.$row[1].'</option>'; 
	  }
	  $listcat.=' </select>'; echo $listcat;
	  ?>
      <input name="Submit" type="submit" id="Submit" value="Add to List !">	  </td>
     </tr> 
	</table>
    </form>  </td>
  <td>&nbsp;</td>
  </tr>
  <?php 
	if($total>=1)
	{
	?>
  <tr>
  
  <td width="38%" height="50" >
	
		Showing Emails <span class="inserted"><?php echo ($pageno-1)*$pagesize+1; ?></span> -
        <span class="inserted"> <?php if(($pageno*$pagesize)>$total) echo $total; else echo $pageno*$pagesize; ?>
        </span>&nbsp; of &nbsp;<span class="inserted"><?php echo $total; ?></span>&nbsp; </td>
	    <td width="2%"></td>
    <td align="right" ><?php echo $paging->page($total,$pagesize,"","viewems.php?action=$_REQUEST[action]".$cc.$searchappnd); ?></td>
    <td align="right" >&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <?php
  }
  ?>
</table>



<br>
<br>

<?php include("admin.footer.inc.php"); ?>