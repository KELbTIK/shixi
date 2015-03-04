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

?>
<?php include("admin.header.inc.php");?>

<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center"><a href="batchinfo.php"> Manage Email Queue</a> | <a href="sendmails.php"> Select Campaign and Send</a> | <a href="cron.php">Execute all Campaigns</a></td>
  </tr>
</table>

<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td width="3%">&nbsp;</td>
    <td width="66%">&nbsp;</td>
    <td width="31%">&nbsp;</td>
  </tr>
	 <tr align="left">
	   <td height="20" colspan="3"><span class="inserted">Manage  Email Queue</span></td>
  </tr>
	 <tr>
	   <td colspan="3"><span class="info">You can manage number of emails you want to send in each batch. You can manage queue of corresponding campaign here. </span></td>
  </tr>
	 <tr>
	   <td colspan="3">&nbsp;</td>
  </tr>
	 <tr>
	   <td colspan="3">
	   <table width="100%"  border="0" cellspacing="2" cellpadding="0" bgcolor="#EEEEEE">
         <tr>
           <td>
		   <form name="form1" method="post" action="updatequeue.php">
             <table width="100%" border="0" cellspacing="0" cellpadding="0"  >
               <tr bgcolor="#CCCCCC">
                 <td height="30" colspan="7"><strong><span class="inserted">&nbsp;Active Campaigns </span></strong> [<?php 
				 
					if(isset($_COOKIE['inout_sub_admin']))
					{
						$subAdminId=getAdminId($mysql);
						$sqltot="SELECT count( distinct a.id) FROM ".$table_prefix."email_advt_curr_run a inner join ( SELECT cid FROM ".$table_prefix."campaign_access_control where aid=$subAdminId and access_status=1) b on a.id=b.cid where a.status=1 ";
						$n=$mysql->echo_one($sqltot);
						echo $n;
					}
					else
						 echo $mysql->total("".$table_prefix."email_advt_curr_run","status=1"); ?> Campaigns] 
				 </td>
               </tr>
			   <?php
			   $sql="select * from ".$table_prefix."email_advt_curr_run where status=1";
			 if(isset($_COOKIE['inout_sub_admin']))
                {
					$subAdminId=getAdminId($mysql);
					$sql="SELECT a.* FROM ".$table_prefix."email_advt_curr_run a inner join ( SELECT cid FROM ".$table_prefix."campaign_access_control where aid=$subAdminId and access_status=1) b on a.id=b.cid where status=1 group by a.id  order by a.id desc ";
				}
			  
			   $result=mysql_query($sql); 
			   $rlt=mysql_num_rows($result);
			   if($rlt==0)
			   {
				   echo "<tr><td colspan=7> <br> - No record to display -<br><br></td></tr>";
			  	//  include("admin.footer.inc.php");
			  	  //exit(0);
			   }
			   else
			   {
			   ?>

               
               <tr>
                 <td height="30"><strong>&nbsp;Campaign</strong></td>
                 <td>&nbsp;</td>
                 <td><strong>%&nbsp;Completed&nbsp;</strong>&nbsp;&nbsp;&nbsp;</td>
                 <td><strong>Emails&nbsp;Sent</strong>&nbsp;&nbsp;&nbsp;</td>
                 <td><strong>Remaining&nbsp;&nbsp;&nbsp;</strong></td>
                 <td><strong>Next&nbsp;in&nbsp;Queue&nbsp;&nbsp;&nbsp;</strong></td>
                 <td><strong>Defined&nbsp;Queue&nbsp;Size* </strong></td>
               </tr>
              
               <?php 
			   $i=0; $qtot=0;
	           while($row=mysql_fetch_row($result))
			   { ?>
               <tr  <?php if($i%2==0){?> bgcolor="#FFFFFF"<?php } ?>>
                 <td height="25" style="border-bottom:1px solid #CCCCCC; ">&nbsp;
                     <?php  if( $row[11]=="") echo "&lt;  Subject : $row[4] &gt;"; else echo $row[11]; ?></td>
                 <td style="border-bottom:1px solid #CCCCCC; "><?php  
		if($mysql->total("".$table_prefix."ea_cnc","campid=$row[0]")!=0){
		
		//echo $mysql->echo_one("select ".$table_prefix."email_advt_category.name from ".$table_prefix."email_advt_category,".$table_prefix."ea_cnc where ".$table_prefix."email_advt_category.id=".$table_prefix."ea_cnc.catid and ".$table_prefix."ea_cnc.campid=$row[0]");
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
		  
		 // $tot= $mysql->echo_one("select count(*) from ".$table_prefix."email_advt a, ".$table_prefix."ea_em_n_cat b where a.id=b.eid AND a.unsubstatus=0 AND b.unsubstatus=0 AND b.cid=$catid and b.id>$row[3]");
		  $rem= $tot-$sent;
		  
		
		//$sent=$mysql->total("".$table_prefix."ea_em_n_cat","cid=$catid and eid<=$row[3]");
		//$rem=$mysql->total("".$table_prefix."ea_em_n_cat","cid=$catid and eid>$row[3]");
		//echo $sent." ".$rem;
		//$perc
		}
		else
		 {
		//echo "All Emails";
		
		if($row[13]=="")
		{
		$tot=$mysql->total("".$table_prefix."email_advt","unsubstatus=0 ");
		}
		else
		{
			$rule ="b.name='".$row[13]."' and b.value " .$row[14]." '".$row[15]."'";
			$tot=$mysql->echo_one("select count(*) from ".$table_prefix."email_advt a, ".$table_prefix."ea_extraparam b where a.id=b.eid   AND  a.unsubstatus=0  AND $rule");
		}
		
		$sent=$row[12];
		  if($sent>$tot)
			  $sent=$tot;
		  
		$rem=$tot-$sent;
			$subAdminId=$mysql->echo_one("select aid from ".$table_prefix."campaign_access_control where cid=$row[0] and access_status=1");
				if($subAdminId!="")
				//	if(isset($_COOKIE['inout_sub_admin']))
			{
		
			
				//$subAdminId=getAdminId($mysql);
				$sent=$mysql->echo_one("SELECT count(distinct a.id ) FROM ".$table_prefix."email_advt a, ".$table_prefix."ea_em_n_cat b  inner join 
				( SELECT eid FROM ".$table_prefix."admin_access_control where aid=$subAdminId )c  on b.cid=c.eid 
				 where a.unsubstatus=0 and b.unsubstatus=0 and  a.id= b.eid and b.id<=$row[3] order by a.id ");
				
				$rem=$mysql->echo_one("SELECT count(distinct a.id ) FROM ".$table_prefix."email_advt a, ".$table_prefix."ea_em_n_cat b  inner join 
				( SELECT eid FROM ".$table_prefix."admin_access_control where aid=$subAdminId )c  on b.cid=c.eid 
				 where a.unsubstatus=0 and b.unsubstatus=0 and  a.id= b.eid and b.id>$row[3] order by a.id ");
			}

		}
		$perc=round(($sent/($sent+$rem))*100,1);
		  $queue=$row[2];
		  if($queue>$rem)
		  $queue=$rem;
  $qtot+=$queue;
  
  ?>
             &nbsp;</td>
                 <td align="center" style="border-bottom:1px solid #CCCCCC; "><?php echo $perc;?>&nbsp;</td>
                 <td align="center" style="border-bottom:1px solid #CCCCCC; "><?php echo $sent;?>&nbsp;</td>
                 <td align="center" valign="middle" style="border-bottom:1px solid #CCCCCC; "><?php echo $rem;?></td>
                 <td align="center" valign="middle" style="border-bottom:1px solid #CCCCCC; "><?php echo $queue; ?></td>
                 <td align="center" valign="middle" style="border-bottom:1px solid #CCCCCC; "><input name="H<?php echo $i; ?>" type="hidden" id="H<?php echo $i; ?>" value="<?php echo $row[0]; ?>">                   <input name="C<?php echo $i;?>" type="text" id="C" value="<?php echo $row[2]; ?>" size="5"></td>
               </tr>
               <?php $i+=1; 
			   }
			   ?>   
               <tr>
                 <td>&nbsp;</td>
                 <td>&nbsp;</td>
                 <td>&nbsp;</td>
                 <td>&nbsp;</td>
                 <td>&nbsp;</td>
                 <td><hr width="50%" size="1"></td>
                 <td align="center">&nbsp;</td>
               </tr>
               <tr>
                 <td>&nbsp;</td>
                 <td>&nbsp;</td>
                 <td>&nbsp;</td>
                 <td>&nbsp;</td>
                 <td>&nbsp;</td>
                 <td align="center"><?php echo $qtot; ?>&nbsp;</td>
                 <td align="center"><input name="total" type="hidden" id="total" value="<?php echo $i; ?>">                   <input type="submit" name="Submit" value="Update !"></td>
               </tr>
              
            <?php 
			}
			?>
             </table>
			 
           </form>
		   </td>
         </tr>
       </table>
	 </td>
  </tr>
	 <tr>
	   <td colspan="3"><span class="info">*Defined Queue Size- The default number of emails you have set, that need to be sent in each batch for the corresponding <br>
&nbsp;       campaign. </span></td>
  </tr>
	 <tr>
	   <td>&nbsp;</td>
	   <td>&nbsp;</td>
	   <td>&nbsp;</td>
  </tr>
	 <tr>
	   <td colspan="2"><a href="main.php">Back to Home </a></td>
	   <td>&nbsp;</td>
  </tr>
	 <tr>
	   <td>&nbsp;</td>
	   <td>&nbsp;</td>
	   <td>&nbsp;</td>
  </tr>
</table>


<?php include("admin.footer.inc.php");?>