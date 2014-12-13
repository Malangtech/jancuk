<?php

#############################################
# Teamworks Content Management System
# http://www.teamworks.co.id
# 23 Februari 2014
# Author: webmaster@teamworks.co.id
#############################################

if (!defined('CMS_admin')) {
	Header("Location: ../../../index.php");
	exit;
}

if (!cek_login ()){
   $admin .='<p class="judul">Access Denied !!!!!!</p>';
   exit;
}

$username = $_SESSION["UserName"];

#############################################
# Edit Profil
#############################################
if($_GET['aksi']==""){

$admin .= '<h3 class="page-header">Edit Profil</h3>';

if (isset($_POST["submit"])) {

define("GIS_GIF", 1);
define("GIS_JPG", 2);
define("GIS_PNG", 3);
define("GIS_SWF", 4);

include "id-includes/hft_image.php";

$nama		= addslashes($_POST['nama']);
$alamat		= $_POST['alamat'];
$kota		= $_POST['kota'];
$handphone	= $_POST['handphone'];
$web		= $_POST['web'];
$ym			= $_POST['ym'];
$namafile_name 	= $_FILES['gambar']['name'];

$error = '';
//if (!$hintjawab)  $error .= "Error: Formulir Hint Jawab belum diisi , silahkan ulangi.<br />";
//if (!$email)  $error .= "Error: email tidak boleh kosong!<br />";

if ($error) {
$admin .='<div class="error">'.$error.'</div>';
} else {
if (!empty ($namafile_name)){
$files 		= $_FILES['gambar']['name'];
$tmp_files 	= $_FILES['gambar']['tmp_name'];
$namagambar = $username.'.jpg';
$tempnews 	= 'id-content/modul/profile/images/temp/';
$uploaddir 	= $tempnews . $namagambar; 
$uploads 	= move_uploaded_file($tmp_files, $uploaddir);
if (file_exists($uploaddir)){
@chmod($uploaddir,0644);
}
	
$tnews 		= 'id-content/modul/profile/images/';
$small 	= $tnews . $namagambar;
create_thumbnail ($uploaddir, $small, $new_width = 100, $new_height = 'auto', $quality = 85);
unlink($uploaddir);
$hasil = $koneksi_db->sql_query("UPDATE users SET nama='$nama',alamat='$alamat',kota='$kota',handphone='$handphone',web='$web',ym='$ym',avatar='$namagambar' WHERE user='$username'");
$admin.='<div class="alert alert-success"><strong>Berhasil!</strong> Profil telah disimpan</div>';
$style_include[] ='<meta http-equiv="refresh" content="1; url=?opsi=profile&modul=yes">';
}else{
$hasil = $koneksi_db->sql_query("UPDATE users SET nama='$nama',alamat='$alamat',kota='$kota',handphone='$handphone',web='$web',ym='$ym' WHERE user='$username'");
$admin.= '<div class="alert alert-success"><strong>Berhasil!</strong> Profil telah disimpan</div>';
$style_include[] ='<meta http-equiv="refresh" content="1; url=?opsi=profile&modul=yes">';
}
}
}

$hasil =  $koneksi_db->sql_query("SELECT * FROM users WHERE user='$username'");
while ($data = $koneksi_db->sql_fetchrow($hasil)) {
$nama		= $data['nama'];
$alamat		= $data['alamat'];
$kota		= $data['kota'];
$handphone	= $data['handphone'];
$email		= $data['email'];
$web		= $data['web'];
$ym			= $data['ym'];
if($data['avatar']==''){
$gambarlama	= 'profile-default.jpg';
}else{
$gambarlama	= $data['avatar'];
}
}

$admin .='<form class="form-horizontal" method="post" action=""enctype ="multipart/form-data">
<div class="form-group">
	<label class="col-sm-2 control-label">Nama Lengkap</label>
	<div class="col-sm-10"><input type="text" name="nama" value="'.$nama.'" class="form-control" placeholder="Nama lengkap"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Alamat</label>
	<div class="col-sm-10"><input type="text" name="alamat" value="'.$alamat.'" class="form-control" placeholder="Alamat lengkap"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Kota</label>
	<div class="col-sm-10"><input type="text" name="kota" value="'.$kota.'" class="form-control" placeholder="Kota"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Handphone</label>
	<div class="col-sm-10"><input type="text" name="handphone" value="'.$handphone.'" class="form-control" placeholder="Nomor ponsel"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Email</label>
	<div class="col-sm-10">'.$email.'</div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Website</label>
	<div class="col-sm-10"><input type="text" name="web" value="'.$web.'" class="form-control" placeholder="www.namawebsite.com"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Yahoo Messenger</label>
	<div class="col-sm-10"><input type="text" name="ym" value="'.$ym.'" class="form-control" placeholder="Yahoo Messenger"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Avatar</label>
	<div class="col-sm-10"><input type="file" name="gambar"><input type="hidden" name="gambarlama" value="'.$gambarlama.'"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label"></label>
	<div class="col-sm-10"><img src="id-content/modul/profile/images/'.$gambarlama.'"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label"></label>
	<div class="col-sm-10">	<input type="submit" name="submit" value="Simpan" class="btn btn-success"></div>
</div>
</form>';
}

echo $admin;

?>