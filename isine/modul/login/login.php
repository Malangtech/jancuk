<?php

#############################################
# Teamworks Content Management System
# http://www.teamworks.co.id
# 23 Februari 2014
# Author: webmaster@teamworks.co.id
#############################################

if (!defined('CMS_MODULE')) {
    Header("Location: ../index.php");
    exit;
}

ob_start();
global $koneksi_db;

if (isset ($_POST['submit_login']) && @$_POST['loguser'] == 1){
$login .= cms_login ();
}

if (!cek_login ()){

$login .= '<form method="post" action="">
<div class="form-group">
    <label>Username</label>
	<input type="text" name="username" size="20" class="form-control">
</div>
<div class="form-group">
    <label>Password</label>
	<input type="password" name="password" size="20" class="form-control">
</div>
<div class="form-group">
    <input type="hidden" value="1" name="loguser">
	<input type="submit" value="Masuk" name="submit_login" class="btn btn-success">
</div>
</form>';

$login .= '<span>
<a href="'.$url_situs.'/daftar.html">Daftar</a> | 
<a href="'.$url_situs.'/lupa-password.html">Lupa Password</a></span>';
}else{
$login .= 'Hello   : <a href="main.php"><b>'.$_SESSION['UserName'].'</b></a>, <a href="index.php?aksi=logout"><b>Keluar</b></a><br>';
if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){ 

$login .= 'Anda mengakses dengan proxy server<br>';
$login .= 'IP Anda : '. $_SERVER['HTTP_X_FORWARDED_FOR'].'<br>';
$login .= 'Terkoneksi lewat engine : '. $_SERVER['HTTP_VIA'].'<br>';
$login .= 'IP Proxy : '.$_SERVER['REMOTE_ADDR'].'<br>';
}else{
$login .= 'Anda terkoneksi tanpa proxy <br>'; 
$login .= 'IP Anda : '. $_SERVER['REMOTE_ADDR'].'<br>';
}

} //akhir cek login

echo $login;

$out = ob_get_contents();
ob_end_clean();

?>