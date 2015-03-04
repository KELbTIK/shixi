<?php
echo "Smtp status	-  ";
			$flag_var=1;
					 if (!class_exists('phpmailer')) {
							require("PHPMailer/class.phpmailer.php");
							}
			
		
		  if($smtpmailer == 1)
		  	{
				echo "enabled";
			}
		else
				echo "disabled";
			
		  ?></li>
        <li>SMTP username - <?php echo $smtp_user; ?></li>
        <li><?php 
		
				$mail = new PHPMailer(true);
				//$mail->SMTPDebug  = 2;                     // enables SMTP debug information (for testing)
				$mail->IsSMTP();                                        // set mailer to use SMTP
				$mail->Host = $smtp_host; // specify SMTP mail server
				$mail->Port = $smtp_port; // specify SMTP Port
				$mail->SMTPAuth = $smtp_auth; // turn on SMTP authentication
				$mail->Username = $smtp_user; //Full SMTP username
				$mail->Password =$smtp_pass; //SMTP password
				//if($smtp_secure )
				 $mail->SMTPSecure =$smtp_secure; 
				 
				 try{
							$mail->SmtpConnect();
							echo "SMTP connected successfully.";
						}
				catch(Exception $e)
						{
						echo "SMTP not connected. Please verify the details.";
						}
					$mail->SmtpClose();
				/* if($mail->SmtpConnect())
				 	echo "SMTP connected successfully";
				else
					echo "SMTP not connected please verify your configuration";*/
		?></li>
        <li><?php echo "BMH mail username - $bmh_mailbox_username"; ?></li>
        <li><?php
			$dirTmp = getcwd();
	// define the "base" directory of the application
	if (!defined('_PATH_BMH')) {
	  $dirTmp = $_SERVER['DOCUMENT_ROOT'] . '/' . $dirTmp;
	  if ( strlen( substr($dirTmp,strlen($_SERVER['DOCUMENT_ROOT']) + 1) ) > 0 ) {
		define('_PATH_BMH', substr($dirTmp,strlen($_SERVER['DOCUMENT_ROOT']) + 1) . "/bmh/");
	  } else {
		define('_PATH_BMH', '');
	  }
	}
		 include(_PATH_BMH .'class.phpmailer-bmh.php');
		$bmh = new BounceMailHandler();
		$bmh->mailhost           = $bmh_mailhost; 
		$bmh->mailbox_username   = $bmh_mailbox_username; 
		$bmh->mailbox_password   = $bmh_mailbox_password; 
		$bmh->port               = $bmh_port;
		$bmh->service            = $bmh_service;
		$bmh->service_option     = $bmh_service_option; 
		$bmh->boxname            = $bmh_boxname; 
		$bmh->verbose			 = VERBOSE_QUIET;
		if($bmh->openMailbox()) {
							echo "BMH mailbox succesfully opened.";
							imap_close($bmh->_mailbox_link);
							}
						else 
							echo "BMH mailbox could not be opened. Please verify the settings.";
?>
