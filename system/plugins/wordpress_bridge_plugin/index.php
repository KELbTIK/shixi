<?php

require_once 'wordpress_bridge_plugin.php';

SJB_Event::handle('Login', array('WordPressBridgePlugin', 'login'));
SJB_Event::handle('Logout', array('WordPressBridgePlugin', 'logout'));
SJB_Event::handle('DisplayBlogContent', array('WordPressBridgePlugin', 'displayBlogContent'));
