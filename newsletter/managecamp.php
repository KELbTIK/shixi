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
<?php

$pageno=1;
$pagesize=10;
if(isset($_REQUEST['page']))
	$pageno=$_REQUEST['page'];
	
$sql="select * from ".$table_prefix."email_advt_curr_run a ";
$sqltot="select count(*) from ".$table_prefix."email_advt_curr_run a ";

$str="";
if($_REQUEST['action']=="all")
	$str.="where 1";
if($_REQUEST['action']=="active")
	$str.=" where a.status=1";
	
if($_REQUEST['action']=="pending")
	$str.=" where a.status=-1";
if($_REQUEST['action']=="inactive")
	$str.=" where a.status=0";
$sql.=$str;
$sqltot.=$str;

$sql.=" order by a.id desc";
$limitstr=" LIMIT ".(($pageno-1)*$pagesize).", ".$pagesize;
$sql.=$limitstr;

if(isset($_COOKIE['inout_sub_admin']))
{
	$subAdminId=getAdminId($mysql);
	
	
	$sqltot="SELECT count( distinct a.id) FROM ".$table_prefix."email_advt_curr_run a inner join 
	( SELECT cid FROM ".$table_prefix."campaign_access_control where aid=$subAdminId and access_status=1) b
	on a.id=b.cid $str ";
	
	$sql="SELECT a.* FROM ".$table_prefix."email_advt_curr_run a inner join 
	( SELECT cid FROM ".$table_prefix."campaign_access_control where aid=$subAdminId and access_status=1) b
	on a.id=b.cid $str group by a.id  order by a.id desc ";

	$sql=$sql.$limitstr;
	//echo $sql;
	
}


 $total=$mysql->echo_one($sqltot);
 $result=mysql_query($sql);
 $no=mysql_num_rows($result);

?>
<link href="style.css" rel="stylesheet" type="text/css">
<table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center"><br /><a href="createcamp.php"> New Campaign</a> | <a href="managecamp.php?action=all">All Campaigns</a> | <a href="managecamp.php?action=active">Active Campaigns</a> | <a href="managecamp.php?action=pending">Pending Campaigns</a> | <a href="managecamp.php?action=inactive">Inactive Campaigns</a></td>
  </tr>
</table>
<br>

 <?php
 if($total>0)
 {
?>
<table width="100%"  border="0" cellpadding="0" cellspacing="0">
    <tr>
	<td width="5%"></td>
    <td width="95%" ><?php if($total>=1) {?>   Showing campaigns <span class="inserted"><?php echo ($pageno-1)*$pagesize+1; ?></span> -
        <span class="inserted">
        <?php if((($pageno)*$pagesize)>$total) echo $total; else echo ($pageno)*$pagesize; ?>
        </span>&nbsp;of <span class="inserted"><?php echo $total; ?></span>&nbsp;&nbsp;<?php 
		} 
	 echo $paging->page($total,$pagesize,"","managecamp.php?action=$_REQUEST[action]"); ?></td>
  </tr>
  <tr>
  <td colspan="2"><br></td></tr>
  </table>
 <?php 
 }
 while($row=mysql_fetch_row($result))
 {
 ?>


<?php 
		if($mysql->total("".$table_prefix."ea_cnc","campid=$row[0]")!=0)
		{
		
		  $catid=$mysql->echo_one("select ".$table_prefix."email_advt_category.id from ".$table_prefix."email_advt_category,".$table_prefix."ea_cnc where ".$table_prefix."email_advt_category.id=".$table_prefix."ea_cnc.catid and ".$table_prefix."ea_cnc.campid=$row[0]");
		
		  if($row[13]=="")
			{
				$tot=$mysql->echo_one("select count(*) from ".$table_prefix."email_advt a, ".$table_prefix."ea_em_n_cat b where a.id=b.eid AND a.unsubstatus=0 AND b.unsubstatus=0 AND b.cid=$catid ");
			}
		else
			{
				$rule ="b.name='".$row[13]."' and b.value " .$row[14]." '".$row[15]."'";
				$tot=$mysql->echo_one("select count(*) from ".$table_prefix."ea_em_n_cat a, ".$table_prefix."ea_extraparam b where a.eid=b.eid  AND a.cid=$catid  AND  a.unsubstatus=0  AND $rule");
			//	echo "select count(*) from ".$table_prefix."ea_em_n_cat a, ".$table_prefix."ea_extraparam b where a.eid=b.eid  AND a.cid=$catid  AND  a.unsubstatus=0  AND $rule";
			}
		  
		  $sent=$row[12];
		  if($sent>$tot)
			  $sent=$tot;
		  //$rem=  $mysql->echo_one("select count(*) from ".$table_prefix."email_advt a, ".$table_prefix."ea_em_n_cat b where a.id=b.eid AND a.unsubstatus=0 AND b.unsubstatus=0 AND b.cid=$catid and b.id>$row[3]");
		  $rem= $tot-$sent;
		  $queue=$row[2];
		  if($queue>$rem)
		  $queue=$rem;
		}
		else 
		{
		
			if(isset($_COOKIE['inout_sub_admin']))
			{
		
			
				$subAdminId=getAdminId($mysql);
				$sent=$mysql->echo_one("SELECT count(distinct a.id ) FROM ".$table_prefix."email_advt a, ".$table_prefix."ea_em_n_cat b  inner join 
				( SELECT eid FROM ".$table_prefix."admin_access_control where aid=$subAdminId )c  on b.cid=c.eid 
				 where a.unsubstatus=0  AND b.unsubstatus=0 and  a.id= b.eid and b.id<=$row[3] order by a.id ");
				
				$rem=$mysql->echo_one("SELECT count(distinct a.id ) FROM ".$table_prefix."email_advt a, ".$table_prefix."ea_em_n_cat b  inner join 
				( SELECT eid FROM ".$table_prefix."admin_access_control where aid=$subAdminId )c  on b.cid=c.eid 
				 where a.unsubstatus=0  AND b.unsubstatus=0 and  a.id= b.eid and b.id>$row[3] order by a.id ");
			}
			else
			{
			if($row[13]=="")
				{
				$tot=$mysql->total("".$table_prefix."email_advt","unsubstatus=0 ");
				}
			else
				{
					$rule ="b.name='".$row[13]."' and b.value " .$row[14]."' ".$row[15]."'";
					$tot=$mysql->echo_one("select count(*) from ".$table_prefix."email_advt a, ".$table_prefix."ea_extraparam b where a.id=b.eid   AND  a.unsubstatus=0  AND $rule");
				}
				
				//$rem=$mysql->total("".$table_prefix."email_advt","unsubstatus=0 and id>$row[3]");
		$sent=$row[12];
		  if($sent>$tot)
			  $sent=$tot;
		$rem=$tot-$sent;
			}
		}
		$perc=round(($sent/($sent+$rem))*100,1);
		  $queue=$row[2];
		  if($queue>$rem)
		  $queue=$rem;
  
  ?>






 <table width="90%"  border="0" align="center" cellpadding="2" cellspacing="0" style="border:1px solid #CCCCCC " >

  <tr bgcolor="#EDEDED">
    <td width="61%" valign="top">Campaign &nbsp;:
      <span class="pagetable_activecell">
      <?php if( $row[11]=="") echo "Untitled"; else echo $row[11]; ?>
      </span>&nbsp;<br>
[<a href="previewcampaign.php?id=<?php echo $row[0]; ?>" class="mainmenu">Preview Campaign</a>]
	</td>
	<td width="39%" valign="top">
	<?php if($mysql->total($table_prefix."campaign_access_control","cid='$row[0]'")==0) 
	{?>
	Created By&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <strong>Admin</strong><br>

	Current Handler : <strong>Admin</strong>
	<?php
	}
	else
	{
		if($mysql->total($table_prefix."campaign_access_control","cid='$row[0]' and access_status='1'")==0)
		{

			$aid=$mysql->echo_one("select aid from ".$table_prefix."campaign_access_control where cid='$row[0]' and access_status='0' order by id limit 0,1 ");
		//	echo "select aid from ".$table_prefix."campaign_access_control where cid='$row[0]' and access_status='0' order by id limit 0,1 ";
			$name=$mysql->echo_one("select username from ".$table_prefix."subadmin_details where id='$aid'");
		
			?>
			Created By&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <strong><?php echo $name;?></strong><br>

			Current Handler : <strong>Admin</strong>
			
			<?php
			
		}
		else
		{
			$aid=$mysql->echo_one("select aid from ".$table_prefix."campaign_access_control where cid='$row[0]' and access_status='1'");
			$name=$mysql->echo_one("select username from ".$table_prefix."subadmin_details where id='$aid'");

			?>
			Created By&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <strong><?php echo $name;?></strong><br>

			<?php
			if($mysql->echo_one("select status from ".$table_prefix."subadmin_details where id='$aid'")==0)
			{
			?>
			Current Handler : <strong>Admin</strong>
			<?php
			}
			else
			{
			?>
			Current Handler : <strong><?php echo $name;?></strong>
			<?php
			}
		}
		?>
		<?php
	}
	?>
	
	</td>
  </tr>
  <tr>
<td colspan="2"><table cellpadding="0" cellspacing="0" width="100%" border="0">
<tr>
    <td width="18%" valign="top" >      Email Subject  </td>
	<td width="1%">: </td>
	<td width="56%"><?php echo $row[4]; ?> </td>
	  <td width="25%" rowspan="5" align="right">

<table width="100%">
<tr><td width="59%">Emails&nbsp;sent</td>
  <td width="6%">:</td>
  <td width="35%"><?php echo $sent; ?></td></tr>
<tr><td>Emails&nbsp;remaining</td>
  <td>:</td>
  <td><?php echo $rem; ?></td></tr>
<tr><td>Emails&nbsp;in&nbsp;queue</td>
  <td>:</td>
  <td><?php echo $queue; ?></td></tr>
<tr><td>% Completed</td>
  <td>:</td>
  <td><?php echo $perc; ?></td></tr>
</table>         </td>
</tr>
	  <tr>
	<td>
        Status </td>
	<td>: </td>
	<td>
        <?php if($row[9]=="1") echo "<span class=\"inserted\">Active</span>"; 
		elseif($row[9]=="-1") echo "<span class=\"already\">Pending</span>"; 
		else echo "<span class=\"already\">Inactive</span>";?>
</td>
	  </tr>
	  <tr>
	<td>        Started on </td>
	<td>: </td> <td><?php echo date("l, F d, Y",$row[10]); ?>
        </td>
	  </tr>
	  <tr>
	<td>
        Email List </td>
	<td>: </td><td> <?php 
		if($mysql->total("".$table_prefix."ea_cnc","campid=$row[0]")!=0){
		
		echo $mysql->echo_one("select ".$table_prefix."email_advt_category.name from ".$table_prefix."email_advt_category,".$table_prefix."ea_cnc where ".$table_prefix."email_advt_category.id=".$table_prefix."ea_cnc.catid and ".$table_prefix."ea_cnc.campid=$row[0]");
		}
		else {
		echo "All Emails";
		}
		
  
  ?>
        </td>
	  </tr>
	 <tr>
			  <td   style="vertical-align:middle "></td><td> </td>
			  <td ></td>
	</tr>

          </table>
</td>
</tr>


	 <tr height="25px">
<td style="border-top:1px solid #CCCCCC;background-color:#EFF0D9 " colspan="2" align="center">
         <a href="attach.php?id=<?php echo $row[0];?>">Attachments (<?php echo $mysql->total("".$table_prefix."ea_attachments","cid=$row[0]");?>)</a> ,   <a href="editad.php?id=<?php echo $row[0]; ?>&status=<?php echo $row[9]; ?>">Edit</a> <?php if($row[9]!=-1) {?>       , <a href="confirmedit.php?action=restart&id=<?php echo $row[0]; ?>">Restart</a>  
        <?php } if(($row[9]==0) || ($row[9]==-1)) {?>    ,  
            <a href="confirmedit.php?action=activate&id=<?php echo $row[0]; ?>">Activate</a>        <?php }else { ?>    , 
            <a href="confirmedit.php?action=inactivate&id=<?php echo $row[0]; ?>">Inactivate</a> <?php } ?>, <a href="confirmedit.php?action=delete&id=<?php echo $row[0]; ?>">Delete</a> , <a href="sendtestmail.php?id=<?php echo $row[0]; ?>" >Send test email</a>
</td> </tr>



</table>
 <br>
 <?php
 //echo $row[5];
 }

 if($total>0)
 {
?>
<table width="100%"  border="0" cellpadding="0" cellspacing="0">
    <tr>
	<td width="5%"></td>
    <td width="95%" ><?php if($total>=1) {?>   Showing campaigns <span class="inserted"><?php echo ($pageno-1)*$pagesize+1; ?></span> -
        <span class="inserted">
        <?php if((($pageno)*$pagesize)>$total) echo $total; else echo ($pageno)*$pagesize; ?>
        </span>&nbsp;of <span class="inserted"><?php echo $total; ?></span>&nbsp;&nbsp;<?php 
		} 
	 echo $paging->page($total,$pagesize,"","managecamp.php?action=$_REQUEST[action]"); ?></td>
  </tr>
  <tr>
  <td colspan="2"><br></td></tr>
  </table>
  <br>
 <?php 
 }
?><?php  if($no==0)
echo "-No Email Campaigns-<br><br>"; ?>
<?php include("admin.footer.inc.php"); ?>