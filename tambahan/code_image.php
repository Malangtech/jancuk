<?php

#############################################
# Teamworks Content Management System
# http://www.teamworks.co.id
# 23 Februari 2014
# Author: webmaster@teamworks.co.id
#############################################

include 'session.php';

include 'kcaptcha/kcaptcha.php';
$captcha = new KCAPTCHA();
$_SESSION['Var_session'] = $captcha->getKeyString();

?>