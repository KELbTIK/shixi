<?php

include('kcaptcha.php');
$captcha = new KCAPTCHA();
$_SESSION['captcha_keystring'] = $captcha->getKeyString();
$captcha->getImage();

