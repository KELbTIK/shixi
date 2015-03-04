<?php 
 
  $Sender = "Name <you@yoursite.com>"; //Sender Name
 
  $Subject = "Confirm your subscription"; // Subject of Confirmation Email
 
 
 // Email Body Below. 
  
  // Variables you can use are,
  // {NAME} will be replaced with sender name.
  // {CONFIRM-LINK} will be replaced with the confirmation link
  // You can use the extra parameters  in this mail in the format {EXTRAPARAM}  eg: {AGE}, {GENDER}
  $text = "
  
  Hello {NAME},
  
  You have just subscribed your email address in our website. 
  Please follow the link below to confim your subscription
  {CONFIRM-LINK}

  Regards
  
  
  ";

  
  ?>