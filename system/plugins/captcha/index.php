<?php

require_once 'captcha_plugin.php';

SJB_Event::handle('editCaptcha', array('CaptchaPlugin', 'editCaptcha'));
SJB_Event::handle('getCaptchaProperties', array('CaptchaPlugin', 'getCaptchaProperties'));
SJB_Event::handle('captchaValidation', array('CaptchaPlugin', 'captchaValidation'));
SJB_Event::handle('getPropertyInfo', array('CaptchaPlugin', 'getPropertyInfo'));
SJB_Event::handle('captchaIsEmpty', array('CaptchaPlugin', 'captchaIsEmpty'));

