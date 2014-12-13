<?php

#############################################
# Teamworks Content Management System
# http://www.teamworks.co.id
# 23 Februari 2014
# Author: webmaster@teamworks.co.id
#############################################

include "../../id-includes/session.php";
include "../../id-includes/config.php";
include "../../id-includes/mysql.php";
include "../../id-includes/configsitus.php";
include "../../id-includes/fungsi.php";
ob_start();
//cek_license ();
global $koneksi_db;
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Teamworks Indonesia - License Invalid</title>
<link rel="stylesheet" href="../../id-admin/themes/administrator/css/licenseerror.css">
</head>

<body>
<?php

if($_GET['licenseerror']==""){
header ("location:../../index.php");
}

####################################
# Expired
####################################
if($_GET['licenseerror']=="expired"){

$hasil	= $koneksi_db->sql_query("SELECT * FROM setting WHERE id=1");
$data	= $koneksi_db->sql_fetchrow($hasil);

if ($data['license'] == ''.sha1(md5($_SERVER['HTTP_HOST'])).''){
header ("location:../../id-login.php");
}

echo '<div id="login_container">
<div id="login_msg">License Invalid</div>

<p>Lisensi Anda :<br>
<span class="license">'.$data['license'].'</span><br>
tidak valid. Hal ini mungkin disebabkan :</p>
<ul>
	<li>Salah memasukkan kode lisensi</li>
	<li>CMS ini diinstal di domain lain yang tidak berlisensi</li>
</ul>
<p>Punya kode lisensi baru? <a href="licenseerror.php?licenseerror=change">Klik di sini untuk memasukkannya</a></p>
</div>';
}

####################################
# Change License
####################################
if($_GET['licenseerror']=="change"){

$hasil	= $koneksi_db->sql_query("SELECT * FROM setting WHERE id=1");
$data	= $koneksi_db->sql_fetchrow($hasil);

if ($data['license'] == ''.sha1(md5($_SERVER['HTTP_HOST'])).''){
header ("location:../../id-login.php");
}
/*
if (isset ($_POST['submit']) && @$_POST['loguser'] == 1){
echo cms_login ();
}
*/
if (isset($_POST['submit'])) {

$username  	= $_POST['username'];
$password  	= $_POST['password'];
$license	= $_POST['license'];

$error = '';
if ($koneksi_db->sql_numrows($koneksi_db->sql_query("SELECT user FROM users WHERE user!='$username'"))) $error .= "Error: Username <strong>".$username."</strong> salah, silahkan ulangi.<br />";
if ($koneksi_db->sql_numrows($koneksi_db->sql_query("SELECT password FROM users WHERE password!='md5($password)'"))) $error .= "Error: Password salah, silahkan ulangi.<br />";

if ($error){
echo '<div class="error">'.$error.'</div>';
}else{
$hasil = $koneksi_db->sql_query("UPDATE setting SET license='$license' WHERE id=1");
echo '<div class="sukses"><strong>Berhasil!</strong> Lisensi website telah disimpan</div>';
echo '<meta http-equiv="refresh" content="1; url=main.php">';
}
}

echo '<div id="login_container"><div id="login_msg">License Change</div>';
echo '<form id="form" method="post" action="" autocomplete="off">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
    <td style="padding:5px;"><label>Username</label></td>
	<td style="padding:5px;"><input type="text" name="username" placeholder="Username" class="textbox" required></td>
</tr>
<tr>
    <td style="padding:5px;"><label>Password</label></td>
	<td style="padding:5px;"><input type="password" name="password" placeholder="Password" class="textbox" required></td>
</tr>
<tr>
    <td style="padding:5px;"><label>New License Key</label></td>
	<td style="padding:5px;"><input type="text" name="license" placeholder="New License Key" class="textbox" required></td>
</div>
<tr>
	<td style="padding:5px;"></td>
    <td style="padding:5px;"><input type="hidden" value="1" name="loguser"><input type="submit" name="submit" value="Change License Key" class="button"></td>
</tr></table></form>';
echo '</div>';
}

?>
</body>
</html>