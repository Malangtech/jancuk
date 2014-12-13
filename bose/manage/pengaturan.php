<?php

#############################################
# Teamworks Content Management System
# http://www.teamworks.co.id
# 23 Februari 2014
# Author: webmaster@teamworks.co.id
#############################################

if (!defined('CMS_admin')) {
	Header("Location: ../index.php");
	exit;
}

if (!cek_login ()){
$admin .='<h4 class="bg">Access Denied !!!!!!</h4>';
}else{

global $koneksi_db,$maxadmindata,$theme,$style_include,$script_include,$url_situs;

$script_include[] = '
<script type="text/javascript" src="id-includes/js/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
tinymce.init({
    selector: "textarea#mce",
    theme: "modern",
 });
</script>';

$script_include[] = '
<script type="text/javascript" src="includes/js/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
tinymce.init({
    selector: "textarea#elm1",
    theme: "modern",
 });
</script>';

$admin  ='<h3 class="page-header">Pengaturan</h3>';

$admin .='<ol class="breadcrumb">
<li><a href="?opsi=pengaturan">Akun Setting</a></li>
<li><a href="?opsi=pengaturan&amp;aksi=profil">Profil Setting</a></li>
<li><a href="?opsi=pengaturan&amp;aksi=website">Web Setting</a></li>
<li><a href="?opsi=pengaturan&amp;aksi=favicon">Favicon</a></li>
</ol>';

#############################################
# Pengaturan Password
#############################################
if($_GET['aksi']==""){

if (isset($_POST["submit"])) {

$user		= text_filter($_POST['user']);
$email		= text_filter($_POST['email']);
$password0 	= md5($_POST["password0"]);
$password1 	= $_POST["password1"];
$password2 	= $_POST["password2"];

$hasil = $koneksi_db->sql_query( "SELECT password,email FROM users WHERE user='$user'" );
while ($data = $koneksi_db->sql_fetchrow($hasil)) {
$password	= $data['password'];
$email0		= $data['email'];
}
$error = '';
if (!$password0)  $error .= "<strong>Gagal!</strong> Masukkan password baru Anda<br />";
if (!$password1)  $error .= "<strong>Gagal!</strong> Masukkan password baru Anda<br />";
if (!$password2)  $error .= "<strong>Gagal!</strong> Ulangi password baru Anda<br />";
checkemail($email);
if ($password0 != $password)  $error .= "<strong>Gagal!</strong> Password lama Anda tidak cocok, silahkan ulangi lagi<br />";
if ($password1 != $password2)   $error .= "<strong>Gagal!</strong> Password baru dan ulangan password berbeda, silahkan ulangi<br />";
if ($koneksi_db->sql_numrows($koneksi_db->sql_query("SELECT email FROM users WHERE email='$email' and user!='$user'")) > 0) $error .= "<strong>Gagal!</strong> Email <strong>".$email."</strong> sudah terdaftar, silahkan ulangi.<br />";

if ($error) {
$admin .='<div class="alert alert-danger">'.$error.'</div>';
}else{
$password3=md5($password1);
$hasil = $koneksi_db->sql_query( "UPDATE users SET user='$user', email='$email', password='$password3' WHERE user='$user'" );
$admin.='<div class="alert alert-success"><strong>Berhasil!</strong> Infromasi akun berhasil diupdated</div>';
$style_include[] ='<meta http-equiv="refresh" content="1; url=?opsi=pengaturan">';
}
}

$username =  $_SESSION['UserName'];
$hasil =  $koneksi_db->sql_query( "SELECT * FROM users WHERE user='$username'" );
while ($data = $koneksi_db->sql_fetchrow($hasil)) {
$UserId	= $data['UserId'];
$user	= $data['user'];
$email	= $data['email'];
}

$admin .='<div class="border">';
$admin .='<form class="form-horizontal" method="post" action="">
<div class="form-group">
	<label class="col-sm-2 control-label">Username</label>
	<div class="col-sm-5"><input type="text" size="30" name="email" value="'.$user.'" class="form-control" disabled></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Email</label>
	<div class="col-sm-5"><input type="email" size="30" name="email" value="'.$email.'" class="form-control" required></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Password Lama</label>
	<div class="col-sm-5"><input type="password" size="10" name="password0" class="form-control" placeholder="Masukkan password lama Anda"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Password Baru</label>
	<div class="col-sm-5"><input type="password" size="10" name="password1" class="form-control" placeholder="Masukkan password baru Anda"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Ulangi Password Baru</label>
	<div class="col-sm-5"><input type="password" size="10" name="password2" class="form-control" placeholder="Ulangi password baru Anda"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label"></label>
	<div class="col-sm-5"><input type="hidden" name="user" value="'.$user.'"><input type="submit" name="submit" value="Simpan" class="btn btn-success"></div>
</div></form>';
$admin .='</div>';
}

#############################################
# Pengaturan Profil
#############################################
if($_GET['aksi']=="profil"){

if (isset($_POST["submit"])) {

define("GIS_GIF", 1);
define("GIS_JPG", 2);
define("GIS_PNG", 3);
define("GIS_SWF", 4);

include "includes/hft_image.php";

$id		= $_POST['id'];
$nama	= $_POST['nama'];
$alamat	= $_POST['alamat'];
$handphone= $_POST['handphone'];
$web	= $_POST['web'];
$ym		= $_POST['ym'];
$namafile_name 	= $_FILES['gambar']['name'];
$error = '';
if (!$nama)  $error .= "<strong>Gagal!</strong> Nama lengkap harus diisi<br />";

if ($error) {
$admin .='<div class="alert alert-danger">'.$error.'</div>';
} else {
if (!empty ($namafile_name)){
$files = $_FILES['gambar']['name'];
$tmp_files = $_FILES['gambar']['tmp_name'];
$namagambar = md5 (rand(1,100).$files) .'.jpg';
$tempnews 	= 'modul/profile/temp/';
$uploaddir = $tempnews . $namagambar; 
$uploads = move_uploaded_file($tmp_files, $uploaddir);
if (file_exists($uploaddir)){
@chmod($uploaddir,0644);
}
	
$tnews 		= 'modul/profile/images/';
$small 	= $tnews . $namagambar;
create_thumbnail ($uploaddir, $small, $new_width = 100, $new_height = 'auto', $quality = 100);
unlink($uploaddir);

$hasil = $koneksi_db->sql_query( "UPDATE users SET nama='$nama', alamat='$alamat', handphone='$handphone', web='$web', ym='$ym', avatar='$namagambar' WHERE UserId='$id'" );
$admin.='<div class="alert alert-success"><strong>Berhasil!</strong> Pengaturan profil akun berhasil diupdate</div>';
}else{
$hasil = $koneksi_db->sql_query( "UPDATE users SET nama='$nama', alamat='$alamat', handphone='$handphone', web='$web', ym='$ym' WHERE UserId='$id'" );
$admin.='<div class="alert alert-success"><strong>Berhasil!</strong> Pengaturan profil akun berhasil diupdate</div>';
$style_include[] ='<meta http-equiv="refresh" content="1; url=?opsi=pengaturan&amp;aksi=profil">';
}
}
}
$username =  $_SESSION['UserName'];
$hasil =  $koneksi_db->sql_query( "SELECT * FROM users WHERE user='$username'" );
while ($data = $koneksi_db->sql_fetchrow($hasil)) {
$id			= $data['UserId'];
$user		= $data['user'];
$nama		= $data['nama'];
$alamat		= $data['alamat'];
$handphone	= $data['handphone'];
$email		= $data['email'];
$web		= $data['web'];
$ym			= $data['ym'];
if(!$data['avatar']){
$gambarlama = 'profile-default.jpg';
}else{
$gambarlama	= $data['avatar'];
}
}

$admin .='<div class="border">';
$admin .='<form class="form-horizontal" method="post" action=""enctype ="multipart/form-data">
<div class="form-group">
	<label class="col-sm-2 control-label">Nama Lengkap</label>
	<div class="col-sm-10"><input type="text" size="40" name="nama" value="'.$nama.'" class="form-control" placeholder="Nama Lengkap"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Alamat</label>
	<div class="col-sm-10"><input type="text" size="40" name="alamat" value="'.$alamat.'" class="form-control" placeholder="Alamat"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Handphone</label>
	<div class="col-sm-10"><input type="text" size="40" name="handphone" value="'.$handphone.'" class="form-control" placeholder="Contoh : 6282166000063"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Website</label>
	<div class="col-sm-10"><input type="text" size="35" name="web" value="'.$web.'" class="form-control" placeholder="Contoh : http://www.teamworks.co.id"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Yahoo Messenger</label>
	<div class="col-sm-10"><input type="text" size="40" name="ym" value="'.$ym.'" class="form-control" placeholder="Yahoo Messenger"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Avatar</label>
	<div class="col-sm-10"><input type="file" name="gambar"><input type="hidden" name="gambarlama" size="40" value="'.$gambarlama.'"><p class="help-block">Extensi file *.JPG, *.JPEG</p></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label"></label>
	<div class="col-sm-10"><img src="id-content/modul/profile/images/'.$gambarlama.'"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label"></label>
	<div class="col-sm-10"><input type="hidden" name="id" value="'.$id.'"><input type="submit" name="submit" value="Simpan" class="btn btn-success"></div>
</div>
</form>';
$admin .='</div>';
}
}

#############################################
# Setting Website
#############################################
if($_GET['aksi']=="website"){

if (isset($_POST["submit"])) {
$judul_situs 	= $_POST['judul_situs'];
$url_situs 		= $_POST['url_situs'];
$publishwebsite = $_POST["publishwebsite"];
$slogan 		= $_POST['slogan'];
$email_master 	= $_POST['email_master'];
$description 	= $_POST['description'];
$keywords 		= $_POST['keywords'];
$alamatkantor 	= $_POST['alamatkantor'];

$error = '';
if (!$judul_situs)  $error .= "<strong>Gagal!</strong> Judul Situs tidak boleh kosong<br />";
if (!$url_situs)  	$error .= "<strong>Gagal!</strong> URL Situs tidak boleh kosong<br />";
if (!$slogan)  		$error .= "<strong>Gagal!</strong> Slogan Situs tidak boleh kosong<br />";
if (!$email_master)	$error .= "<strong>Gagal!</strong> Email Master Situs tidak boleh kosong<br />";
if (!$description)  $error .= "<strong>Gagal!</strong> Deskripsi Situs tidak boleh kosong<br />";
if (!$keywords)  	$error .= "<strong>Gagal!</strong> Keyword Situs tidak boleh kosong<br />";

if ($error) {
$admin .='<div class="alert alert-danger">'.$error.'</div>';
}else{

$password3 = md5($password1);
$hasil = $koneksi_db->sql_query( "UPDATE setting SET judul_situs='$judul_situs', url_situs='$url_situs', publishwebsite='$publishwebsite', slogan='$slogan', email_master='$email_master', description='$description', keywords='$keywords', alamatkantor='$alamatkantor' WHERE id='1'" );
$admin.='<div class="alert alert-success"><strong>Berhasil!</strong> Pengaturan website berhasil disimpan</div>';
$style_include[] ='<meta http-equiv="refresh" content="1; url=?opsi=pengaturan&amp;aksi=website">';
}
}

$hasil =  $koneksi_db->sql_query( "SELECT * FROM setting WHERE id='1'" );
while ($data = $koneksi_db->sql_fetchrow($hasil)) {
$id				= $data['id'];
$judul_situs	= $data['judul_situs'];
$url_situs		= $data['url_situs'];
$publishwebsite = $data['publishwebsite'];
$slogan			= $data['slogan'];
$email_master	= $data['email_master'];
$description	= $data['description'];
$keywords		= $data['keywords'];
$alamatkantor	= $data['alamatkantor'];
}

$admin .='<div class="border">';
$admin .='
<form class="form-horizontal" method="post" action="">
<div class="form-group">
	<label class="col-sm-2 control-label">Judul Website</label>
	<div class="col-sm-10"><input type="text" size="80" name="judul_situs" value="'.$judul_situs.'" class="form-control" placeholder="Judul Website"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">URL Website</label>
	<div class="col-sm-10"><input type="text" size="80" name="url_situs" value="'.$url_situs.'" class="form-control" placeholder="URL Website"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Publikasi Website</label>
	<div class="col-sm-10">';
if($publishwebsite==1){
$admin .= '
<label><input name="publishwebsite" type="radio" value="1" checked="checked"> Ya</label>&nbsp;
<label><input name="publishwebsite" type="radio" value="0"> Tidak</label>';
}else{
$admin .= '
<label><input name="publishwebsite" type="radio" value="1"> Ya</label>&nbsp;
<label><input name="publishwebsite" type="radio" value="0" checked="checked"> Tidak</label>';
}
$admin .='</div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Slogan</label>
	<div class="col-sm-10"><input type="text" size="80" name="slogan" value="'.$slogan.'" class="form-control" placeholder="Slogan Website"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Email Master</label>
	<div class="col-sm-10"><input type="text" size="80" name="email_master" value="'.$email_master.'" class="form-control" placeholder="Email Master"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Deskripsi [META]</label>
	<div class="col-sm-10"><textarea name="description" rows="3" class="form-control">'.$description.'</textarea></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Keywords [META]</label>
	<div class="col-sm-10"><textarea name="keywords" rows="3" class="form-control">'.$keywords.'</textarea></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Alamat Kontak</label>
	<div class="col-sm-10"><textarea name="alamatkantor" rows="3" id="mce" class="form-control">'.$alamatkantor.'</textarea></div>
</div>		
<div class="form-group">
	<label class="col-sm-2 control-label"></label>
	<div class="col-sm-10"><input type="hidden" name="id" value="'.$id.'"><input type="submit" name="submit" value="Simpan" class="btn btn-success"></div>
</div></form>';
$admin .='</div>';
}

#############################################
# Favicon
#############################################
if($_GET['aksi']=="favicon"){

if (isset($_POST['submit'])){

define("GIS_GIF", 1);
define("GIS_JPG", 2);
define("GIS_PNG", 3);
define("GIS_SWF", 4);

include "id-includes/hft_image.php";

$namafile_name 	= $_FILES['gambar']['name'];
if (!empty ($namafile_name)){
$files 		= $_FILES['gambar']['name'];
$tmp_files 	= $_FILES['gambar']['tmp_name'];
$tempnews 	= ''.$_SERVER['DOCUMENT_ROOT'].'/';
$namagambar = 'favicon.png';
$uploaddir 	= $tempnews . $namagambar; 
$uploads 	= move_uploaded_file($tmp_files, $uploaddir);
if (file_exists($uploaddir)){
@chmod($uploaddir,0644);
}
$gnews 		= '';
$nsmall = $gnews . $namagambar;
//create_thumbnail ($uploaddir, $nsmall, $new_width = 64, $new_height = 64, $quality = 100);
	
$admin.='<div class="alert alert-success"><strong>Berhasil!</strong> Favicon berhasil diupload</div>';
$style_include[] ='<meta http-equiv="refresh" content="1; url=?opsi=pengaturan&amp;aksi=favicon">';
}
}
$admin .= '<div class="border">
<form class="form-horizontal" method="post" action="" enctype ="multipart/form-data" id="posts">
<div class="form-group">
	<label class="col-sm-2 control-label"></label>
	<div class="col-sm-10"><img src="'.$url_situs.'/favicon.png" width="64" alt="Favicon" class="img-thumbnail"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Favicon</label>
	<div class="col-sm-10"><input type="file" name="gambar" size="53"><p class="help-block">Extensi file *.PNG Ukuran Max. 64x64 px</p></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label"></label>
	<div class="col-sm-10"><input type="submit" value="Upload" name="submit" class="btn btn-success"></div>
</div>
</form></div>';
}

echo $admin;

?>