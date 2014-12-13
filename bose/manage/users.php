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
   $admin .='<p class="judul">Access Denied !!!!!!</p>';
   exit;
}
	
global $maxadmindata;
	
$admin ='<h3 class="page-header">Users Manager</h3>';

$admin .= '<ol class="breadcrumb">
<li><a href="?opsi=users">Data Users</a></li>
<li><a href="?opsi=users&amp;aksi=tambah_user">Tambah User</a></li>
</ol>';

$admin .= '<form method="get" action="main.php"><div class="input-group">
<input type="text" name="cari" class="form-control" placeholder="Cari Username / Email">
<span class="input-group-btn">
	<input type="hidden" name="opsi" value="users">
	<button class="btn btn-default" type="submit" name="submit" value="Search">Cari</button>
</span>
</div></form>';

#############################################
# List Users
#############################################
if ($_GET['aksi'] == ''){

if (isset($_POST['submit'])){

$tot     .= $_POST['tot'];
$pcheck ='';
for($i=1;$i<=$tot;$i++){
$check = $_POST['check'.$i] ;
if($check <> "") {
$pcheck .= $check . ",";
}
}
$pcheck = substr_replace($pcheck, "", -1, 1);
$error = '';
if ($error){
$admin.='<div class="alert alert-danger">'.$error.'</div>';
}
if ($pcheck)  $sukses .= "<strong>Berhasil!</strong> User dengan UserId <strong>$pcheck</strong> telah dihapus !<br />";
$koneksi_db->sql_query("DELETE FROM users WHERE UserId in($pcheck)");
if ($sukses){
$admin.='<div class="alert alert-success">'.$sukses.'</div>';
}
}

$cari = $_GET['cari'];
$admin .= '<h4 class="page-header">Data Users</h4>';

$admin.='<form method="post" action=""><div class="table-responsive"><table class="table table-striped table-hover">
<thead><tr>
<th>No</th>
<th>#</th>
<th>Username</th>
<th>Email</th>
<th>Nama</th>
<th>Level</th>
<th>Status</th>
<th>Aksi</th>
</tr></thead><tbody>';

$offset = int_filter($_GET['offset']);
$pg		= int_filter($_GET['pg']);
$stg	= int_filter($_GET['stg']);
if($cari){
$hasil = $koneksi_db->sql_query("SELECT * FROM users WHERE user like '%$cari%' or email like '%$cari%'");
}else{
$hasil = $koneksi_db->sql_query("SELECT * FROM users");
}
$jumlah = $koneksi_db->sql_numrows($hasil);

$limit = 25;
$a = new paging ($limit);
if ($jumlah > 0){
if($cari){
$query = $koneksi_db->sql_query("SELECT * FROM users WHERE user like '%$cari%' or email like '%$cari%' LIMIT $offset,$limit");
}else{
$query = $koneksi_db->sql_query("SELECT * FROM users LIMIT $offset,$limit");
}
if($offset){
$no = $offset+1;
}else{
$no = 1;
}
while ($data = $koneksi_db->sql_fetchrow($query)){
if($data['tipe']=='aktif'){
$status = '<a class="text-success" href="?opsi=users&amp;aksi=status&amp;pub=pasif&amp;id='.$data['UserId'].'">'.$data['tipe'].'</a>';
}else{
$status = '<a class="text-danger" href="?opsi=users&amp;aksi=status&amp;pub=aktif&amp;id='.$data['UserId'].'">'.$data['tipe'].'</a>';
}

$admin.='<tr>
<td>'.$no.'</td>
<td><input type=checkbox name=check'.$no.' value='.$data[0].'></td>
<td>'.$data['user'].'</td>
<td>'.$data['email'].'</td>
<td>'.$data['nama'].'</td>
<td>'.$data['level'].'</td>
<td>'.$status.'</td>
<td><a class="text-info" href="?opsi=users&amp;aksi=edit_user&amp;id='.$data['UserId'].'">Edit</a> - 
<a class="text-danger" href="?opsi=users&amp;aksi=hapus_user&amp;id='.$data['UserId'].'" onclick="return confirm(\'Apakah Anda ingin menghapus User '.$data['user'].' ?\')">Hapus</a> - 
<a class="text-warning" href="?opsi=users&amp;aksi=reset_password&amp;id='.$data['UserId'].'">Reset Password</a></td>
</tr>';  
$no++;
}
$admin .='<tr>
<td colspan="8"><input type="hidden" name="tot" value="'.$jumlah.'"><input type="submit" value="Hapus" name="submit" class="btn btn-danger"></td>
</tr>';
}
$admin .= '</tbody></table></div></form>';

if($jumlah>$limit){
if (empty($_GET['offset']) and !isset ($_GET['offset'])) {
$offset = 0;
}

if (empty($_GET['pg']) and !isset ($_GET['pg'])) {
$pg = 1;
}

if (empty($_GET['stg']) and !isset ($_GET['stg'])) {
$stg = 1;
}

$admin .= '<center>';
$admin .= $a-> getPaging($jumlah, $pg, $stg);
$admin .= '</center>';
}
}

#############################################
# Tambah Users
#############################################
if ($_GET['aksi'] == 'tambah_user'){
	
if (isset($_POST['submit'])){
	
$user 		= cleantext($_POST['user']);	
$level 		= cleantext($_POST['level']);	
$tipe 		= cleantext($_POST['tipe']);
$password 	= cleantext($_POST['password']);
$email 		= cleantext($_POST['email']);

if (!$user || preg_match("/[^a-zA-Z0-9_-]/", $user)) $error .= "Error: Karakter Username tidak diizinkan kecuali a-z,A-Z,0-9,-, dan _<br />";
if (strlen($user) > 10) $error .= "Username terlalu panjang maks. 10 karakter<br />";
if (strrpos($user, " ") > 0) 	$error .= "Username tidak boleh menggunakan spasi";
if ($koneksi_db->sql_numrows($koneksi_db->sql_query("SELECT user FROM users WHERE user='$user'")) > 0) $error .= "Error: Username ".$user." sudah terdaftar, silahkan ulangi.<br />";
if ($koneksi_db->sql_numrows($koneksi_db->sql_query("SELECT email FROM users WHERE email='$email'")) > 0) $error .= "Error: Email ".$email." sudah terdaftar, silahkan ulangi.<br />";
if (!is_valid_email($email)) 	$error .= "Error: E-Mail address invalid!<br />";

if ($error){
$admin.='<div class="alert alert-danger">'.$error.'</div>';
}else{
$hasil = $koneksi_db->sql_query ("INSERT INTO users (user,password,level,tipe,email) VALUES ('$user',md5('$password'),'$level','$tipe','$email')");	
$admin .= '<div class="alert alert-success"><strong>Berhasil!</strong> User berhasil ditambah</div>';
$style_include[] ='<meta http-equiv="refresh" content="1; url=?opsi=users">';
}
}	

if (isset ($_GET['offset']) && isset ($_GET['pg']) && isset ($_GET['stg'])) {
$qss = "&pg=$pg&stg=$stg&offset=$offset";
}	
$admin .= '<h4 class="page-header">Tambah User</h4>';

$admin .= '<form class="form-horizontal" method="post" action="">
<div class="form-group">
    <label class="col-sm-2 control-label">Username</label>
    <div class="col-sm-10"><input type="text" name="user" class="form-control" placeholder="Masukkan username" required></div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">Password</label>
    <div class="col-sm-10"><input type="text" name="password" class="form-control" placeholder="Masukkan password" required></div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">Email</label>
    <div class="col-sm-10"><input type="email" name="email" class="form-control" placeholder="Masukkan email" required></div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">Level</label>
    <div class="col-sm-10"><select name="level" class="form-control">';
$arrs = array ('Administrator','User');
foreach ($arrs as $kk=>$vv){
$admin .= '<option value="'.$vv.'">'.$vv.'</option>';	
}
$admin .= '</select></div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">Status</label>
    <div class="col-sm-10"><select name="tipe" class="form-control">';
$arrs = array ('aktif','pasif');
foreach ($arrs as $kk=>$vv){
$admin .= '<option value="'.$vv.'">'.$vv.'</option>';	
}
$admin .= '</select></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label"></label>
    <div class="col-sm-10"><input type="submit" name="submit" value="Tambah" class="btn btn-success"></div>
</div></form>';
}

#############################################
# Edit Users
#############################################
if ($_GET['aksi'] == 'edit_user'){

$id = int_filter($_GET['id']);

if(isset($_POST['submit'])){

$user 		= cleantext($_POST['user']);	
$level 		= cleantext($_POST['level']);	
$tipe 		= cleantext($_POST['tipe']);
$password 	= cleantext($_POST['password']);
$email 		= cleantext($_POST['email']);
$nama 		= addslashes($_POST['nama']);
$handphone	= $_POST['handphone'];
$alamat		= addslashes($_POST['alamat']);
$kota		= $_POST['kota'];

$error = '';
if (!$user || preg_match("/[^a-zA-Z0-9_-]/", $user)) $error .= "Error: Karakter Username tidak diizinkan kecuali a-z,A-Z,0-9,-, dan _<br />";
if (strlen($user) > 10) $error .= "Username terlalu panjang maks. 10 karakter<br />";
if (strrpos($user, " ") > 0) 	$error .= "Username tidak boleh menggunakan spasi";
if ($koneksi_db->sql_numrows($koneksi_db->sql_query("SELECT user FROM users WHERE user='$user' and UserId!='$id'")) > 0) $error .= "Error: Username <strong>".$user."</strong> sudah terdaftar, silahkan ulangi.<br />";
if ($koneksi_db->sql_numrows($koneksi_db->sql_query("SELECT email FROM users WHERE email='$email' and UserId!='$id'")) > 0) $error .= "Error: Email <strong>".$email."</strong> sudah terdaftar, silahkan ulangi.<br />";

if ($error){
$admin .= '<div class="alert alert-danger">'.$error.'</div>';
}else {
$hasil = $koneksi_db->sql_query( "UPDATE users SET user='$user',email='$email',level='$level',tipe='$tipe',nama='$nama',handphone='$handphone',alamat='$alamat',kota='$kota' WHERE UserId='$id'" );
if($hasil){
$admin .= '<div class="alert alert-success"><strong>Berhasil!</strong> User berhasil disimpan</div>';
$style_include[] ='<meta http-equiv="refresh" content="1; url=?opsi=users">';
}
}
}

$s = $koneksi_db->sql_query("SELECT * FROM users WHERE UserId='$id'");	
$data = $koneksi_db->sql_fetchrow($s);
$user 		= $data['user'];
$email 		= $data['email'];	
$level 		= $data['level'];	
$tipe 		= $data['tipe'];
$nama 		= $data['nama'];
$handphone 	= $data['handphone'];
$alamat 	= $data['alamat'];
$kota 		= $data['kota'];

$ss = mysql_query ("SHOW FIELDS FROM users");
while ($as = mysql_fetch_array ($ss)){
$arrs = $as['Type'];
if (substr($arrs,0,4) == 'enum' && $as['Field'] == 'level') break;
}

if (isset ($_GET['offset']) && isset ($_GET['pg']) && isset ($_GET['stg'])) {
$qss = "&amp;pg=$pg&amp;stg=$stg&amp;offset=$offset";
}	
$admin .= '<h4 class="page-header">Edit User</h4>';

$admin .= '<form class="form-horizontal" method="post" action="">
<div class="form-group">
    <label class="col-sm-2 control-label">Username</label>
    <div class="col-sm-10"><input type="text" name="user" value="'.$user.'" class="form-control" placeholder="Masukkan username" required></div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">Email</label>
    <div class="col-sm-10"><input type="email" name="email" value="'.$email.'" class="form-control" placeholder="Masukkan email" required></div>
</div>';  
  
$sel = '<select name="level" class="form-control">';
$arrs = ''.substr ($arrs,4);
$arr = eval( '$arr5 = array'.$arrs.';' );
foreach ($arr5 as $k=>$v){
	if ($level == $v){
	$sel .= '<option value="'.$v.'" selected="selected">'.$v.'</option>';
	}else {
	$sel .= '<option value="'.$v.'">'.$v.'</option>';	
	}
}

$sel .= '</select>';  
  
$sel2 = '<select name="tipe" class="form-control">';
$arr2 = array ('aktif','pasif');
foreach ($arr2 as $kk=>$vv){
	if ($tipe == $vv){
	$sel2 .= '<option value="'.$vv.'" selected="selected">'.$vv.'</option>';
	}else {
	$sel2 .= '<option value="'.$vv.'">'.$vv.'</option>';	
	}
}

$sel2 .= '</select>';    
$admin .= '<div class="form-group">
    <label class="col-sm-2 control-label">Level</label>
    <div class="col-sm-10">'.$sel.'</div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">Status</label>
    <div class="col-sm-10">'.$sel2.'</div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">Nama Lengkap</label>
    <div class="col-sm-10"><input type="text" name="nama" value="'.$nama.'" class="form-control" placeholder="Masukkan nama lengkap"></div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">No. Ponsel</label>
    <div class="col-sm-10"><input type="text" name="handphone" value="'.$handphone.'" class="form-control" placeholder="Masukkan nomor ponsel"></div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">Alamat</label>
    <div class="col-sm-10"><input type="text" name="alamat" value="'.$alamat.'" class="form-control" placeholder="Masukkan alamat lengkap"></div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">Kota</label>
    <div class="col-sm-10"><input type="text" name="kota" value="'.$kota.'" class="form-control" placeholder="Masukkan kota"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label"></label>
    <div class="col-sm-10"><input type="submit" value="Simpan" name="submit" class="btn btn-success"></div>
</div>
</table></form>';
}

#############################################
# Hapus User
#############################################
if($_GET['aksi']=="hapus_user"){

$id     = int_filter($_GET['id']);

$hasil = $koneksi_db->sql_query( "SELECT * FROM users WHERE UserId=$id" );
while($data = mysql_fetch_array($hasil)){
$folder_gambar = 'id-content/modul/profile/images/';
$avatar =  $data['avatar'];
$uploaddir = $folder_gambar . $avatar;
if($avatar !==''){
unlink($uploaddir);
}
}

$koneksi_db->sql_query("DELETE FROM users WHERE UserId='$id'");
$admin.='<div class="alert alert-success"><strong>Berhasil!</strong> User telah dihapus</div>';
$style_include[] ='<meta http-equiv="refresh" content="1; url=?opsi=users">';
}

#############################################
# Reset Password
#############################################
if ($_GET['aksi'] == 'reset_password'){

$id = int_filter($_GET['id']);
$newpass 		= genpass();
$newpassword	= md5($newpass);

$hasil = $koneksi_db->sql_query("UPDATE users SET password='$newpassword' WHERE UserId='$id'");
if($hasil){
$admin .= '<div class="alert alert-success"><strong>Berhasil!</strong> Password user berhasil direset dan dikirim email</div>';
# Kirim Email
$s = $koneksi_db->sql_query( "SELECT * FROM setting WHERE id=1");
$datas = $koneksi_db->sql_fetchrow($s);
$nama_email = $datas['nama_email'];

$u = $koneksi_db->sql_query("SELECT * FROM users WHERE UserId='$id'");	
$datau = $koneksi_db->sql_fetchrow($u);
$emailuser = $datau['email'];	

$subject = 'Reset Password - '.$judul_situs.'';
$msg  ='Anda telah melakukan permintaan reset password akun di <a href="'.$url_situs.'">'.$url_situs.'</a><br>
Berikut ini informasi akun Anda :<br><br>
<table border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td style="padding:3px 0px 3px 0px;">Username</td>
    <td style="padding:3px 10px 3px 10px;">:</td>
    <td style="padding:3px 0px 3px 0px;">'.$datau['user'].'</td>
  </tr>
  <tr>
    <td style="padding:3px 0px 3px 0px;">Password</td>
    <td style="padding:3px 10px 3px 10px;">:</td>
    <td style="padding:3px 0px 3px 0px;">'.$newpass.'</td>
  </tr>
</table><br>
Semoga informasi ini bermanfaat bagi Anda.<br>
Terima kasih.<br><br><br>
 
Hormat Kami,<br><br><br>
 
'.$nama_email.'<br>
---------------------------------------------------------------------------------------------------<br>
Mohon tidak membalas email ini, karena email ini dikirim otomatis.';
mail_send($emailuser,$nama_email,$email_master,$subject,$msg,1,3);
# Kirim Email
$style_include[] ='<meta http-equiv="refresh" content="1; url=?opsi=users">';
}
}

##########################
# Status User
##########################
if ($_GET['aksi'] == 'status'){
if ($_GET['pub'] == 'pasif'){
	$id = int_filter ($_GET['id']);
	$koneksi_db->sql_query ("UPDATE users SET tipe='pasif' WHERE UserId='$id'");
	header ("location:?opsi=users");
}	
	
if ($_GET['pub'] == 'aktif'){
	$id = int_filter ($_GET['id']);
	$koneksi_db->sql_query ("UPDATE users SET tipe='aktif' WHERE UserId='$id'");
	header ("location:?opsi=users");
}
}

echo $admin;

?>
