<?php

require_once 'mailchimp_plugin.php';

// init handle
SJB_Event::handle('moduleManagerCreated', array('MailChimpPlugin', 'init'));