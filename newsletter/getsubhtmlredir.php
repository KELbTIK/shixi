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
$cid=$_REQUEST['cid'];?>


<table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center"><a href="category_addnew.php">Create new List</a>&nbsp;| <a href="category_viewall.php">Manage all Lists</a>&nbsp;| <a href="configurehtml.php">Subscribe HTML Code </a> </td>
  </tr>
</table><table width="90%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td> <span class="inserted">The following HTML codes will enable users to redirect to the specified URL after subscription.</span><br>    &nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td rowspan="3" class="box"><span class="inserted">Subscribe Only</span><br>
      Please copy the HTML code displayed in the text area below and paste it into an html page to subscribe to this list <br>
      <br>      <textarea name="textfield" cols="60" rows="5"><form action="<?php echo $sub_path."?cid=$cid"; ?>" method="post" name="category_sub" id="category_sub">
Your Email Address :
<input name="email" type="text" id="email" maxlength="150">
<input name="category" type="hidden" id="category" value="<?php echo $cid; ?>">
<input type="submit" name="Submit" value="Subscribe!"><input name="redir" type="hidden" id="redir" value="<?php echo $_POST['redir']; ?>">
          </form>
      </textarea>
      <br>
      <br>
      <br>
      <table width="75%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><strong>The above code will create an interface like below (Fully Customizable). </strong></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td><form action="<?php echo $sub_path."?cid=$cid"; ?>" method="post" name="category_sub" id="category_sub">
Your Email Address :
<input name="email" type="text" id="email" maxlength="150">
<input name="category" type="hidden" id="category" value="<?php echo $cid; ?>">
<input type="submit" name="Submit" value="Subscribe!">
<input name="redir" type="hidden" id="redir" value="<?php echo $_POST['redir']; ?>">
          </form>
          </td>
        </tr>
    </table>      <blockquote>&nbsp;</blockquote></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
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
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td rowspan="5" class="box"><span class="inserted">
Subscribe OR Unsubscribe </span><br>
Please copy the HTML code displayed in the text area below and paste it into an html page for people to subscribe to this list or unsubscribe their email. <br>
      <br>
      <textarea name="textarea" cols="60" rows="5"><form action="<?php echo $sub_path."?cid=$cid"; ?>" method="post" name="category_subunsub" id="category_subunsub">
        Your Email Address :
            <input name="email" type="text" id="email" maxlength="150">
            <input name="category" type="hidden" id="category" value="<?php echo $cid; ?>">
            <br>
            <input name="subst" type="radio" value="sub" checked><input name="redir" type="hidden" id="redir" value="<?php echo $_POST['redir']; ?>">
            Subscribe 
            <input name="subst" type="radio" value="un">
            Unsubscribe
            <input name="Submit" type="submit" id="Submit" value="Submit !">
          </form>
      </textarea>
      <br>
      <br>
    <br>    <table width="85%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><strong>The above code will create an interface like below (Fully Customizable). </strong></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td><form action="<?php echo $sub_path."?cid=$cid"; ?>" method="post" name="category_subunsub" id="category_subunsub">
          Your Email Address :
              <input name="email" type="text" id="email" maxlength="150">
              <input name="category" type="hidden" id="category" value="<?php echo $cid; ?>">
              <input name="redir" type="hidden" id="redir" value="<?php echo $_POST['redir']; ?>">
              <br>
              <input name="subst" type="radio" value="sub" checked>
              Subscribe 
              <input name="subst" type="radio" value="un">
              Unsubscribe
              <input name="Submit" type="submit" id="Submit" value="Submit !">
            </form>
      </td>
        </tr>
    </table>    <br></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr><tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
    <td>&nbsp;</td>
    <td class="box"> <span class="inserted">Subscribe only with Name</span><br>
      <table width="89%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>Please copy the HTML code displayed in the text area below and paste it into an html page for people to subscribe to this list or unsubscribe their email. <br>
            <br>
            <textarea name="textarea3" cols="60" rows="5"><form action="<?php echo $sub_path."?cid=$cid"; ?>" method="post" name="category_subunsub" id="category_subunsub">
        Your Name :
            <input name="name" type="text" id="name">
        Email Address :
        <input name="email" type="text" id="email" maxlength="150"><input name="redir" type="hidden" id="redir" value="<?php echo $_POST['redir']; ?>">
        <input name="category" type="hidden" id="category" value="<?php echo $cid; ?>">
        <input name="Submit32" type="submit" id="Submit32" value="Subscribe !">
          </form>
          </textarea>
            <br></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td><strong>The above code will create an interface like below (Fully Customizable). </strong></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td><form action="<?php echo $sub_path."?cid=$cid"; ?>" method="post" name="category_subunsub" id="category_subunsub">
        Your Name :
            <input name="name" type="text" id="name">
        Email 
        <input name="email" type="text" id="email" maxlength="150">
        <input name="redir" type="hidden" id="redir" value="<?php echo $_POST['redir']; ?>">
        <input name="category" type="hidden" id="category" value="<?php echo $cid; ?>">
        <input name="Submit32" type="submit" id="Submit32" value="Subscribe !">
          </form>
    </td>
      </tr>
    </table></td>
    <td>&nbsp;</td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr><tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td rowspan="3" class="box"><span class="inserted">Subscribe/Unsubscribe with  Name</span><br>
      <table width="76%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td>Please copy the HTML code displayed in the text area below and paste it into an html page for people to subscribe to this list or unsubscribe their email. <br>
            <br>
            <textarea name="textarea2" cols="60" rows="5"><form action="<?php echo $sub_path."?cid=$cid"; ?>" method="post" name="category_subunsub" id="category_subunsub">
        Your Name :
            <input name="name" type="text" id="name">
            Email Address :
            <input name="email" type="text" id="email" maxlength="150"><input name="redir" type="hidden" id="redir" value="<?php echo $_POST['redir']; ?>">
            <input name="category" type="hidden" id="category" value="<?php echo $cid; ?>">
            <br>
            <input name="subst" type="radio" value="sub" checked>
        Subscribe
        <input name="subst" type="radio" value="un">
        Unsubscribe
        <input name="Submit3" type="submit" id="Submit2" value="Submit !">
          </form>
          </textarea>
            <br></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td><strong>The above code will create an interface like below (Fully Customizable). </strong></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td><form action="<?php echo $sub_path."?cid=$cid"; ?>" method="post" name="category_subunsub" id="category_subunsub">
          Your Name :
              <input name="name" type="text" id="name">
              Email Address :
              <input name="email" type="text" id="email" maxlength="150">
              <input name="category" type="hidden" id="category" value="<?php echo $cid; ?>">
              <input name="redir" type="hidden" id="redir" value="<?php echo $_POST['redir']; ?>">
              <br>
              <input name="subst" type="radio" value="sub" checked>
          Subscribe
          <input name="subst" type="radio" value="un">
          Unsubscribe
          <input name="Submit3" type="submit" id="Submit2" value="Submit !">
            </form>
      </td>
        </tr>
          </table></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
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
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
<?php
include_once("admin.footer.inc.php");
?>
