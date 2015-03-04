<?php

require_once 'google_analytics_plugin.php';
SJB_Event::handle('moduleManagerCreated', array('GoogleAnalyticsPlugin', 'init'));
