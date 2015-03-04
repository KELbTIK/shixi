<?php
include_once("config.inc.php");

//** EMAIL CLASS 2.0
//** ===============

//** Script is completely rewritten. MIME block generation made OO to reduce
//** errors. Allows for nested MIME blocks, meaning multipart/alternative
//** emails that also contain attachments.
//** -------------------------
//** September 27, 2004 (v2.0)
//**
//** Added support for CC and BCC fields. Added support for 
//** multipart/alternative messages. Added ability to create attachments 
//** manually using literal content.
//** -------------------
//** May 13, 2004 (v1.2)
//**
//** The Email class wrappers PHP's mail function into a class capable of 
//** sending attachments and HTML email messages. Custom headers can also be 
//** included as if using the mail function.
//** -----------------------
//** December 2, 2003 (v1.1)
//** (String) the newline character(s) to be used when generating an email 
//** message. If using QMail change this value to be just "\n". QMail 
//** incorrectly replaces all "\n" with "\r\n", even when already proceded by
//** a "\r". This leads to the headers showing up as text in a mail client.
	if($mailserver_type==1)
  		define('EmailNewLine', "\n"); 
  	else
  		define('EmailNewLine', "\r\n"); 

//** (String) the unique X-Mailer identifier for emails sent with this tool. 
//** Please do not alter - everyone knows you didn't write this code :)

  define('EmailXMailer', 'PHP-EMAIL,v2.0 (wmfwlr AT cogeco DOT ca)'); 

//** (String) the default charset values for both text and HTML blocks. 

  define('EmailDefaultCharset',"$charset_encoding"); 

//** (Boolean) whether or not the debugging data should be displayed.

  define('EmailIsDebugging', false); 

//**EMAIL_MESSAGE_CLASS_DEFINITION******************************************** 
//** The Email class wrappers PHP's mail function into a class capable of 
//** sending attachments and HTML email messages. Custom headers can also be 
//** included as if using the mail function. 

class Email 
{ 
//** (String) the recipiant email address, or comma separated addresses. 

  var $To = null; 

//** (String) the recipiant addresses to receive a copy. Can be comma
//** separated addresses. 

  var $Cc = null; 

//** (String) the recipiant addresses to receive a hidden copy. Can be 
//** comma separated addresses. 

  var $Bcc = null; 

//** (String) the email address of the message sender. 

  var $From = null; 

//** (String) the subject of the email message. 

  var $Subject = null; 

//** (Array of MimeBlock Objects) array of MimeBlock instances representing
//** different versions of the message content.

  var $Versions = array(); 

//** (Array of MimeBlock Objects) array of MimeBlock instances to be sent 
//** with this email message. 

  var $Attachments = array(); 

//** (String) any custom header information that must be used when 
//** sending email. 

  var $Headers = null; 

//** (ContainerMimeBlock Object) root container for the various MIME
//** blocks associated with this email.

  var $RootContainer = null;

//** Returns: None
//** Create a new email message with the parameters provided. 

  function Email($to=null, $from=null, $subject=null, $headers=null)
  { 
    $this->To = strval($to); 

	if(strcasecmp(EmailDefaultCharset, "UTF-8") == 0)
			{
		
				$this->Subject = "=?UTF-8?B?".base64_encode(strval($subject))."?=";
				if (ereg("(.*)<(.*)>", $from, $regs)) 
					{
					   // There is a name for the expeditor !
					   $this->From = '=?UTF-8?B?'.base64_encode($regs[1]).'?= <'.$regs[2].'>';
					 
					} 
				else 
					{
					
					   // Nothing to do, the from is directly the email !
					   $this->From = strval($from);
					}

			}
    else
			{	
		
			$this->Subject = strval($subject); 
			$this->From = strval($from); 
			}
		
    $this->Headers = strval($headers); 

//** assume that initially the email message will be mixed.

    $this->RootContainer = new ContainerMimeBlock('multipart/mixed');
  }
//** Returns: Boolean
//** Set the plain text version of this email message.

  function SetTextContent($content)
  {
    return $this->SetContent($content, 'text/plain', '8bit');
  }
//** Returns: Boolean
//** Set the HTML-based version of this email message.

  function SetHtmlContent($content)
  {
//** all HTML content should really be encoded here using quoted-printable
//** ecoding. Since there is no built-in PHP function for this sending the
//** plain HTML code is used for now.

    return $this->SetContent($content, 'text/html', '8bit');
  }
//** Returns: Boolean
//** Attempt to add the content (of the MIME type and optional encoding given)
//** to this email message. If successful TRUE is returned.

  function SetContent($content, $mimeType='text/plain', $encoding='8bit')
  {
    $contentBlock = new LiteralMimeBlock($mimeType, $content, $encoding);
    return $this->AddContentBlock($contentBlock);
  }
//** Returns: Boolean 
//** Attempt to add the file content as a version of the email content. If the
//** file cannot be located no version is created and FALSE is returned. 

  function SetFileContent($pathToFile, $mimeType=null) 
  { 
//** create the appropriate MIME block from the file given. If the file does 
//** not exist no content is added and FALSE is returned. 

    $fileVersion = new AttachmentMimeBlock($mimeType, $pathToFile);
    $fileVersion->IsAttachment = false;

    if(!$fileVersion->IsValid()) 
      return false; 
    else 
    { 
      $this->Versions[] = $fileVersion;  //** add the file content to list. 
      return true;                       //** version successfully added.
    } 
  } 
//** Returns: Boolean
//** Attempt to add the MIME block content to this email message. If 
//** successful TRUE is returned.

  function AddContentBlock($mimeBlock=null)
  {
    if(!$mimeBlock || !$mimeBlock->IsValid()) 
      return false; 
    else
    { 
      $this->Versions[] = $mimeBlock;  //** add content to version list.
      return true;                     //** content successfully added.
    } 
  }
//** Returns: Boolean 
//** Create a new file attachment for the file (and optionally MIME type) 
//** given. If the file cannot be located no attachment is created and 
//** FALSE is returned. 

  function Attach($pathToFile, $mimeType=null) 
  { 
//** create the appropriate email attachment block. If the attachment does not
//** exist the MIME attachment is not created and FALSE is returned. 

    $attachment = new AttachmentMimeBlock($mimeType, $pathToFile);
    if(!$attachment->IsValid()) 
      return false; 
    else 
    { 
      $this->Attachments[] = $attachment;  //** add the attachment to list. 
      return true;                         //** attachment successfully added.
    } 
  } 
//** Returns: Boolean 
//** Determine whether or not the email message is ready to be sent. A TO and 
//** FROM address are required. 

  function IsComplete() 
  { 
    return (strlen(trim($this->To)) > 0 && strlen(trim($this->From)) > 0); 
  } 
//** Returns: None
//** Clear any content versions and attachments added to this email.

  function Clear()
  {
    $this->RootContainer->Clear();
    $this->Versions = array();
    $this->Attachments = array();
  }
//** Returns: Boolean 
//** Attempt to send the email message. Attach all files that are currently 
//** valid. Send the appropriate text/html message. If not complete FALSE is 
//** returned and no message is sent. 

  function Send() 
  { 
    if(!$this->IsComplete())  //** message is not ready to send. 
      return false;           //** no message will be sent. 

//** start generating headers for the message. Add the from email address and
//** the current date of sending. 

    $headers = 'Date: ' . date('r', time()) . EmailNewLine .
               'From: ' . strval($this->From) . EmailNewLine;
	 $headers .='Return-Path: ' . strval($this->From) . EmailNewLine;

//** if a non-empty CC field is provided add it to the headers here.

    if(strlen(trim(strval($this->Cc))) > 0)
      $headers .= 'CC: ' . strval($this->Cc) . EmailNewLine;
    
//** if a non-empty BCC field is provided add it to the headers here.

    if(strlen(trim(strval($this->Bcc))) > 0)
      $headers .= 'BCC: ' . strval($this->Bcc) . EmailNewLine;

//** add the custom headers here, before important headers so that none are 
//** overwritten by custom values. 
	 $headers .='Reply-to: ' . strval($this->From) . EmailNewLine;

    if($this->Headers != null && strlen(trim($this->Headers)) > 0) 
      $headers .= $this->Headers . EmailNewLine; 

//** determine whether or not this email contains more than one version or any
//** file attachments.

    $hasMultipleVersions = (count($this->Versions) > 1);
    $hasOneVersion = (count($this->Versions) == 1);
    $hasAttachments = (count($this->Attachments) > 0);

//** there are multiple versions of this email as well as attachments.

    if($hasMultipleVersions && $hasAttachments)
    {
      $this->RootContainer->ContentType = 'multipart/mixed';

//** create the container that will contain the multiple message versions.

      $contentContainer = new ContainerMimeBlock('multipart/alternative');

//** loop over the content versions and add them to the new container.

      foreach($this->Versions as $mimeVersion)
        $contentContainer->Add(&$mimeVersion);

//** add the content container to the root container first.

      $this->RootContainer->Add(&$contentContainer);

//** loop over the attachments and add them to the root container.

      foreach($this->Attachments as $mimeFile)
        $this->RootContainer->Add(&$mimeFile);
    }
//** many versions of this email exist but no attachments need to be sent.

    else if($hasMultipleVersions)
    {
      $this->RootContainer->ContentType = 'multipart/alternative';

//** loop over the content versions and add them to the root container.

      foreach($this->Versions as $mimeVersion)
        $this->RootContainer->Add(&$mimeVersion);
    }
//** there is a single version of this email and attachments.

    else if($hasAttachments)
    {
      $this->RootContainer->ContentType = 'multipart/mixed';

//** if a content version is available add it as the first message item.

      if($hasOneVersion)
        $this->RootContainer->Add(&$this->Versions[0]);

//** loop over the attachments and add them to the root container.

      foreach($this->Attachments as $mimeFile)
        $this->RootContainer->Add(&$mimeFile);
    }
//** there is a single version of this email and no attachments.

    else if($hasOneVersion)
    {
      $this->RootContainer->ContentType = 'multipart/mixed';
	  //$this->RootContainer->ContentType = $this->Versions[0]->getContentType();
      $this->RootContainer->Add(&$this->Versions[0]);
    }
//** add the MIME encoding version and MIME type for the email message and
//** the standard message boundary.

    $headers .= 'X-Mailer: ' . EmailXMailer . EmailNewLine . 
                'MIME-Version: 1.0' . EmailNewLine . 
                'Content-Type: ' . $this->RootContainer->ContentType . '; ' .
                'boundary="' . $this->RootContainer->Boundary . '"' . 
                 EmailNewLine . EmailNewLine; 

//** get the raw data from the root container. Clear for future calls.

    $thebody = $this->RootContainer->GetEncodedData();
    $this->RootContainer->Clear();

//** if debugging render the entire headers and body for this message
//** to the browser. 

    if(EmailIsDebugging)
    {
      print('<b>&lt;email&gt;</b><br>');
      print(str_replace(EmailNewLine, '&lt;newline&gt;<br>', $headers));
      print(str_replace(EmailNewLine, '&lt;newline&gt;<br>', $thebody));
      print('<br><b>&lt;/email&gt;</b><br>');
    }
//** attempt to send the email message. Return the operation success. 


	if(strpos($this->From, "<") !== FALSE)
	 {
		preg_match("/.* <(.*)>/iU", $this->From, $m);
		$env_sender = trim($m[1]);
	 }
	 else
	  {
		 $env_sender = $this->From;
	  }

     if(!(mail($this->To, $this->Subject, $thebody, $headers, "-f{$env_sender}")))
	  {	
		return mail($this->To, $this->Subject, $thebody, $headers); 
	  }
	  else
	  {
		  return true;
	  }
	  
  } 
} 
//**MIME_BLOCK_CLASS**********************************************************
//** The MimeBlock class acts as a base class to be used to handle any part of
//** and MIME message that is self contained.

class MimeBlock
{
//** (String) the standard MIME type for this data block.

  var $ContentType = null;

//** (String) encoding method used when encoding block data.

  var $Encoding = null;

//** Returns: None
//** Create a new MIME block having the type and optional encoding provided.

  function MimeBlock($type, $encMethod=null)
  {
    $this->ContentType = strval($type);
    $this->Encoding = strval($encMethod);
  }
//** Returns: Boolean
//** Determine whether or not this MIME block should be rendered.

  function IsValid()
  {
    return false;
  }
//** Returns: Boolean
//** Determine whether or not a special encoding is used with this MIME block.

  function HasEncoding()
  {
    return (strlen(trim($this->Encoding)) > 0);
  }
  
   function getContentType()
  {
    return trim($this->ContentType);
  }
//** Returns: String
//** Get the encoded content for this MIME block. To be overridden.

  function GetEncodedData()
  {
    return '';
  }
//** Returns: String
//** Get any additional header information to be included after the MIME
//** content type. Must start with a colon if non-empty.

  function GetAdditionalContentTypeHeader()
  {
    return '';
  }
//** Returns: String
//** Get any additional header(s) to be included before the data for this 
//** block. Each header must end with the standard email newline character.

  function GetCustomHeaders()
  {
    return '';
  }
//** Returns: String
//** Get this MIME block as a header/data encoded string. The standard email
//** newline will be used to separate values.

  function ToString()
  {
//** add the MIME type for this data block. Add any custom type header text.

    $text = 'Content-Type: ' . $this->ContentType . 
             $this->GetAdditionalContentTypeHeader() . EmailNewLine;

//** if available add the encoding used on the data.

    if($this->HasEncoding())
      $text .= 'Content-Transfer-Encoding: ' . $this->Encoding . EmailNewLine;

//** add each of the additional headers (if any).

    $text .= $this->GetCustomHeaders();

//** always add an extra newline to separate the headers and data.
    $text .= EmailNewLine;

//** add the (possibly encoded) block content.

    $text .= $this->GetEncodedData();
    return $text;
  }
}
//**LITERAL_MIME_BLOCK_CLASS**************************************************
//** The LiteralMimeBlock class acts as a simple binary data passthrough
//** block. Any content provided is output exactly as is provided. This
//** class should only be used for text data (not safe for binary data).

class LiteralMimeBlock extends MimeBlock
{
//** (String) literal text data for this MIME block.

  var $LiteralContent = null;

//** (String) official character set used to transmit the literal content.

  var $Charset = EmailDefaultCharset;

//** Returns: None
//** Create a new literal MIME block having the MIME type and optional
//** encoding provided. The data for the block is that provided.

  function LiteralMimeBlock($type, $content, $encMethod=null)
  {
    MimeBlock::MimeBlock($type, $encMethod);
    $this->LiteralContent = strval($content);
  }
//** Returns: Boolean
//** Determine whether or not this MIME block should be rendered. Only TRUE
//** if literal content has been provided.

  function IsValid()
  {
    return (strlen($this->LiteralContent) > 0);
  }
//** Returns: String
//** Get the encoded content provided for this MIME block. No encoding is
//** performed.

  function GetEncodedData()
  {
    return $this->LiteralContent;
  }
//** Returns: String
//** Add the character set data after the content type header.

  function GetAdditionalContentTypeHeader()
  {
    return '; charset="' . $this->Charset . '"';
  }
}
//**FILE_ATTACHMENT_MIME_BLOCK_CLASS******************************************
//** The AttachmentMimeBlock class allows for any file accessible from the
//** server to be MIME encoded as an attachment.

class AttachmentMimeBlock extends MimeBlock
{
//** (String) the full path to the file to be attached on the server. 

  var $FilePath = null; 

//** (Boolean) whether or not this block should be treated as a file 
//** attachment or be treated as a normal MIME block.

  var $IsAttachment = true;

//** Returns: None
//** Create a new literal MIME block having the MIME type and optional
//** encoding provided. The data for the block is that provided.

  function AttachmentMimeBlock($type, $filePath)
  {
    MimeBlock::MimeBlock($type, 'base64');
    $this->FilePath = strval($filePath);
  }
//** Returns: Boolean
//** Determine whether or not this MIME block should be rendered. If no file
//** attachment can be located FALSE is returned.

  function IsValid()
  {
    return $this->Exists();
  }
//** Returns: Boolean
//** Determine whether or not the server attachment file actually exists.

  function Exists() 
  { 
    if($this->FilePath == null || strlen(trim($this->FilePath)) == 0) 
      return false; 
    else 
      return @file_exists($this->FilePath); 
  } 
//** Returns: String
//** Get the encoded content provided for this MIME block. This will be the
//** binary file data encoded using base64.

  function GetEncodedData()
  {
    $fileData = '';

//** if the file exists open the file attachment in binary mode and read 
//** the entire contents. 

    if($this->Exists())
    {
      $thefile = @fopen($this->FilePath, 'rb'); 
      $fileData = @fread($thefile, filesize($this->FilePath));
      @fclose($thefile);
    }
//** base64 encode the file data and split it into lines of 76 bytes each.

    $encData = chunk_split(base64_encode($fileData), 76, EmailNewLine);

//** remove the last email newline from the encoded data.

    return substr($encData, 0, strlen($encData) - strlen(EmailNewLine));
  }
//** Returns: String
//** Add the additional header that indicates this MIME block contains a
//** file attachment. If not an attachment nothing is returned.

  function GetCustomHeaders()
  {
//** block represents a file attachment. Add the disposition header.

    if($this->IsAttachment)
    {
      return 'Content-Disposition: attachment; filename="' .
              @basename($this->FilePath) . '"' . EmailNewLine;
    }
    else          //** file is not an attachment.
      return '';  //** no additional headers.
  }
//** Returns: String
//** Add the name of the attachment file after the content type header.

  function GetAdditionalContentTypeHeader()
  {
//** if this block is an attachment add the file name after the type
//** header, otherwise no additional header information.

    if($this->IsAttachment)
      return '; name="' . @basename($this->FilePath) . '"';
    else         
      return ''; 
  }
//** Returns: String
//** Get this MIME block as a header/data encoded string. The standard email
//** newline will be used to separate values.

  function ToString()
  {
//** use the default content type if none is provided.

    if(strlen(trim($this->ContentType)) == 0)
      $this->ContentType = 'application/octet-stream';

//** return the base implementation.

    return MimeBlock::ToString();
  }
}
//**CONTAINER_MIME_BLOCK_CLASS************************************************
//** The ContainerMimeBlock class acts as a container that holds other MIME
//** blocks. Each contained MIME boock is separated by a boundry marker.
//** This acts as a 'multipart/mixed' or 'multipart/alternative' email 
//** message container. 

class ContainerMimeBlock extends MimeBlock
{
//** (String) text boundary marker for this MIME container.

  var $Boundary = null;

//** (Array) array of contained MimeBlock instances.

  var $Blocks = array();

//** Returns: None
//** Create a new container MIME block having the type provided. The type
//** should be with 'multipart/XXX'.

  function ContainerMimeBlock($type)
  {
    MimeBlock::MimeBlock($type, '');

//** generate a random boundry for this container. Should not be duplicated.

    $this->Boundary = '--' . md5(uniqid('mime_container'));
  }
//** Returns: None
//** Clear any contained MIME blocks.

  function Clear()
  {
    $this->Blocks = array();
  }
//** Returns: Boolean
//** Attempt to add the MIME block provided to this container. If no block is
//** provided or the block is not valid FALSE is returned.

  function Add($mimeBlock=null)
  {
    if(!$mimeBlock || !$mimeBlock->IsValid())  //** no valid block given.
      return false;                            //** nothing added.
    else
    {
      $this->Blocks[] = $mimeBlock;
      return true;
    }
  }
//** Returns: Boolean
//** Determine whether or not this MIME block should be rendered. TRUE if more
//** than one MIME block is contained within.

  function IsValid()
  {
    return ($this->Blocks != null && count($this->Blocks) > 0);
  }
//** Returns: Boolean
//** Determine whether or not a special encoding is used with this MIME block.
//** Nevr use an encoding value.

  function HasEncoding()
  {
    return false;
  }
//** Returns: String
//** Get the encoded content for this MIME block. Returns the content for
//** each contained block separated by the appropriate boundary markers.

  function GetEncodedData()
  {
    $text = '';            //** default empty content.
    if(!$this->IsValid())  //** no contained blocks available.
      return $text;        //** no data to return.

//** foreach contained MIME block add the opening boundary followed by the
//** MIME content followed by two newline characters.

    foreach($this->Blocks as $mimeBlock)
    {
      $text .= '--' . $this->Boundary . EmailNewLine . 
               $mimeBlock->ToString() . EmailNewLine . EmailNewLine;
    }
//** add the end boundary separator to indicate the container end.

    $text .= '--' . $this->Boundary . '--';
    return $text;
  }
//** Returns: String
//** Get any additional header information to be included after the MIME
//** content type. Must start with a colon if non-empty.

  function GetAdditionalContentTypeHeader()
  {
    return '; boundary="' . $this->Boundary . '"';
  }
}
?>
