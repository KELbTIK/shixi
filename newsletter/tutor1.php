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
error_reporting(0);
include("admin.header.inc.php");
?>

<table width="90%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="12%">&nbsp;</td>
    <td width="85%">&nbsp;</td>
    <td width="3%">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><span class="inserted"><strong>Step by step guide to implement a  basic solution for a first time user. </strong></span></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><strong>I have a few email addresses with me. How can I use the script to send them emails?</strong></td>
    <td>&nbsp;</td>
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
  <tr>
    <td>&nbsp;</td>
    <td><strong>Step 1 - Create a new Email List </strong></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>First of all you need to create a new email list to where you want to add the emails. So currently if you don't have any email list yet, please create a new list clicking on the corresponding link in the admin area homepage.</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><strong>Step 2 - Add Emails </strong></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>In this step, you can add emails to the email list. You can  add emails to the above list from various resources. You can either add a single email,  add many emails together,import mails from a database, ask the script to automatically extract emails from the given HTML code or URLs where you have  some emails,ask the script to automatically extract emails from the given text based file or add emails from any form using automatic subscription PHP code. While adding emails you can configure as much extra fields you need.</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><strong>Step 3 - Create a new Email Campaign </strong></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>Prepare an email  subject and body and other related setting in this step. Also see the various parameters you can set. Also add the list to which you want to send the emails.</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><strong>Step 4 - Send Emails </strong></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>Click on the 'send emails now' link in the admin area homepage. The emails created by the email campaign, will be sent to all the emails in the selected email list. </td>
    <td>&nbsp;</td>
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
  <?php
if(!isset($_COOKIE['inout_sub_admin']))
{
?>
  <tr>
    <td>&nbsp;</td>
    <td><strong>I want to create multiple user accounts and assign different email lists to each of them. How can  the script help me with this?</strong></td>
    <td>&nbsp;</td>
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
  <tr>
    <td>&nbsp;</td>
    <td><strong>Step 1 - Create a new Administrator </strong></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>Go to your admin area homepage. From the 

 
&nbsp; <strong>Administrator Accounts</strong> menu chose '

 
<a href="<?php echo $dirpath;?>create_new_sub_admin.php">Create New Administrator </a> '.Fill in all relevant details and submit. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><strong>Step 2 - Define Access Privilege </strong></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>Once you have created an administrator account you are ready to define the privileges for him. Click on <a href="<?php echo $dirpath;?>manage_sub_admins.php">Manage Administrators </a>from <strong>Administrator Accounts</strong> menu. Select <br>
 'Access Control'
from the action .Here you can check or uncheck the lists which you want the administrator to control. <br></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><strong>Step 3 - Manage Administrator Accounts </strong></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>You can redefine the privileges for an administrator any time. If you remove administrator's access to a list, he will automatically lose his access on the campaigns which he has fired on those lists.These access rights are automatically restored if you give the list access back to him.You can block/activate administrators anytime. </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><strong>Step 4 - View Activity Logs </strong></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>From your admin area you can view the logs of all the activities happening in your system. You can see the logs for each administrator separately. Also you can see logs related to each category.Inout Mailing List Manager also keeps track of any unauthorized access attempts by any of your administrator. </td>
    <td>&nbsp;</td>
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
  <?php
  }
  ?>
  <tr>
    <td>&nbsp;</td>
    <td>The above steps, helps you to implement a basic solution with the script. The script is designed in such a way that it meets all your mailing list requirements even in a very complex environment.<br>      <br>
      <a href="tutor2.php" class="mainmenu"></a></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><a href="tutor2.php" class="mainmenu">A Practical Example</a></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><a href="tutor2.php" class="mainmenu"><br>
    </a></td>
    <td>&nbsp;</td>
  </tr>
</table>
<?php include("admin.footer.inc.php"); ?>