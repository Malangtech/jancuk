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

global $koneksi_db,$error;
$username = $_SESSION['UserName'];

#############################################
# Ubah Password
#############################################
if($_GET['aksi']=="ubah_password"){

if (!cek_login ()){
   $tengah .='<p class="judul">Access Denied !!!!!!</p>';
   
}else{

$tengah .='<h3 class="page-header">Edit User</h3>';

if (isset($_POST["submit"])) {

$email		= text_filter($_POST['email']);
$password0 	= md5($_POST["password0"]);
$password1 	= $_POST['password1'];
$password2 	= $_POST['password2'];

$hasil = $koneksi_db->sql_query("SELECT password,email FROM users WHERE user='$username'");
while ($data = $koneksi_db->sql_fetchrow($hasil)){
$password	= $data['password'];
$email0		= $data['email'];
}

if (!$password0)  $error .= "Error: Please enter your Old Password!<br />";
if (!$password1)  $error .= "Error: Please enter new password!<br />";
if (!$password2)  $error .= "Error: Please retype your your new password!<br />";
if (!is_valid_email($email)) $error .= "Error, E-Mail address invalid!<br />";
if ($password0 != $password)  $error .= "Invalid old pasword, silahkan ulangi lagi.<br />";
if ($password1 != $password2)   $error .= "New password dan retype berbeda, silahkan ulangi.<br />";
if ($koneksi_db->sql_numrows($koneksi_db->sql_query("SELECT email FROM users WHERE email='$email' and user!='$username'")) > 0) $error .= "Error: Email ".$email." sudah terdaftar , silahkan ulangi.<br />";

if ($error) {
$tengah.='<div class="alert alert-danger">'.$error.'</div>';
} else {

$password3 = md5($password1);
$hasil = $koneksi_db->sql_query("UPDATE users SET email='$email', password='$password3' WHERE user='$username'");
$tengah .= '<div class="alert alert-success"><strong>Berhasil!</strong> Akun telah disimpan, Silahkan <a href="index.php?aksi=logout" target="_top">Logout</a>!</div>';
}
}

$hasil =  $koneksi_db->sql_query("SELECT * FROM users WHERE user='$username'");
while ($data = $koneksi_db->sql_fetchrow($hasil)) {
$user	= $data['user'];
$email	= $data['email'];
}

$tengah .='<form class="form-horizontal" method="post" action="">
<div class="form-group">
	<label class="col-sm-2 control-label">Username</label>
	<div class="col-sm-10"><input type="text" value="'.$user.'" class="form-control" disabled></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Email</label>
	<div class="col-sm-10"><input type="email" name="email" value="'.$email.'" class="form-control" placeholder="Masukkan email" required></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Password Lama</label>
	<div class="col-sm-10"><input type="password" name="password0" class="form-control" placeholder="Masukkan password lama" required></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Password Baru</label>
	<div class="col-sm-10"><input type="password" name="password1" class="form-control" placeholder="Masukkan password baru" required></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Ulangi Password Baru</label>
	<div class="col-sm-10"><input type="password" name="password2" class="form-control" placeholder="Ulangi password baru" required></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label"></label>
	<div class="col-sm-10"><input type="submit" name="submit" value="Simpan" class="btn btn-success"></div>
</div>
</form> ';
}
}

echo $tengah;


?>