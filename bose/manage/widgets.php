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
   exit;
}
if ($_SESSION['LevelAkses']!="Administrator") {
	exit;
}

$admin  ='<h3 class="page-header">Modul Manager</h3>';

$admin .='<ol class="breadcrumb">
<li><a href="?opsi=widgets">Home</a></li>
<li><a href="?opsi=widgets&amp;aksi=tambah_modul">Tambah Modul</a></li>
<li><a href="?opsi=widgets&amp;aksi=tambah_blok">Tambah Blok</a></li>
</ol>';

#############################################
# Data Modul
#############################################
if ($_GET['aksi'] == ""){

if (isset($_POST['submit'])) {

if (is_array($_POST['order'])) {
foreach($_POST['order'] as $key=>$val) {
$update = mysql_query("UPDATE modul SET ordering='$val' WHERE id='$key'");
}
$admin .= '<div class="alert alert-success">Ordering modul berhasil disimpan</div>';
$style_include[] ='<meta http-equiv="refresh" content="1; url=?opsi=widgets">';
}
}

$admin .= '<h4 class="page-header">Sidebar Kiri</h4>';
$admin .= '<form class="form-inline" method="post" action="">
<div class="table-responsive"><table class="table table-striped table-hover">
<thead><tr>
<th>Nama</th>
<th>Publikasi</th>
<th>Posisi</th>
<th>Order</th>
<th>Tipe</th>
<th>Aksi</th>
</tr></thead><tbody>';
$query = mysql_query("SELECT * FROM modul WHERE posisi=0 ORDER BY ordering");
while($data = mysql_fetch_assoc($query)) {
$publikasi = ($data['published'] == 1) ? '<a class="text-success" href="?opsi=widgets&amp;aksi=pub&amp;pub=tidak&amp;id='.$data['id'].'"><span class="glyphicon glyphicon-ok"></span></a>' : '<a class="text-danger" href="?opsi=widgets&amp;aksi=pub&amp;pub=ya&amp;id='.$data['id'].'"><span class="glyphicon glyphicon-remove"></span></a>';
$posisi = ($data['posisi'] == 1) ? '<a class="text-info" href="?opsi=widgets&amp;aksi=posisi&amp;posisi=kiri&amp;id='.$data['id'].'">Kanan</a>' : '<a class="text-info" href="?opsi=widgets&amp;aksi=posisi&amp;posisi=kanan&amp;id='.$data['id'].'">Kiri</a>';

$admin .= '<tr>
<td>'.$data['modul'].'</td>
<td>'.$publikasi.'</td>
<td>'.$posisi.'</td>
<td><input type="text" name="order['.$data['id'].']" value="'.$data['ordering'].'" size="1" class="text-center"></td>
<td>'.$data['type'].'</td>
<td><a class="text-info" href="?opsi=widgets&amp;aksi=edit_modul&amp;id='.$data['id'].'">Edit</a> - 
<a class="text-danger" href="?opsi=widgets&amp;aksi=hapus_modul&amp;id='.$data['id'].'" onclick="return confirm(\'Apakah anda yakin ?\')">Hapus</a></td></tr>';
}
$admin .= '<tr><td colspan="6"><input type="submit" name="submit" value="Simpan" class="btn btn-success"></td></tr></tbody></table></div>';

$admin .= '<h4 class="page-header">Sidebar Kanan</h4>';

$admin .= '<div class="table-responsive"><table class="table table-striped table-hover">
<thead><tr>
<th>Nama</th>
<th>Publikasi</th>
<th>Posisi</th>
<th>Order</th>
<th>Tipe</th>
<th>Aksi</th>
</tr><thead><tbody>';
$query = mysql_query("SELECT * FROM modul WHERE posisi=1 ORDER BY ordering");
while($data = mysql_fetch_assoc($query)) {
$publikasi = ($data['published'] == 1) ? '<a class="text-success" href="?opsi=widgets&amp;aksi=pub&amp;pub=tidak&amp;id='.$data['id'].'"><span class="glyphicon glyphicon-ok"></span></a>' : '<a class="text-danger" href="?opsi=widgets&amp;aksi=pub&amp;pub=ya&amp;id='.$data['id'].'"><span class="glyphicon glyphicon-remove"></span></a>';
$posisi = ($data['posisi'] == 1) ? '<a class="text-info" href="?opsi=widgets&amp;aksi=posisi&amp;posisi=kiri&amp;id='.$data['id'].'">Kanan</a>' : '<a class="text-info" href="?opsi=widgets&amp;aksi=posisi&amp;posisi=kanan&amp;id='.$data['id'].'">Kiri</a>';
	
$admin .= '<tr>
<td>'.$data['modul'].'</td>
<td>'.$publikasi.'</td>
<td>'.$posisi.'</td>
<td><input type="text" name="order['.$data['id'].']" value="'.$data['ordering'].'" size="1" class="text-center"></td>
<td>'.$data['type'].'</td>
<td><a class="text-info" href="?opsi=widgets&amp;aksi=edit_modul&amp;id='.$data['id'].'">Edit</a> -
<a class="text-danger" href="?opsi=widgets&amp;aksi=hapus_modul&amp;id='.$data['id'].'" onclick="return confirm(\'Apakah anda yakin ?\')">Hapus</a></td></tr>';
}
$admin .= '<tr><td colspan="6"><input type="submit" name="submit" value="Simpan" class="btn btn-success"></td></tr></tbody></table></div></form>';
}

#############################################
# Tambah Blok
#############################################
if ($_GET['aksi'] == 'tambah_blok'){

$admin .= '<h4 class="page-header">Tambah Blok</h4>';

if (isset($_POST['submit'])) {
	$error = null;
	if (empty($_POST['title'])) {
		$error .= '- Error Judul blok tidak boleh kosong<br/>';
	}
	if (empty($_POST['modul'])) {
		$error .= '- Error isi blok tidak boleh kosong<br/>';
	}
	
	if ($error != '') {
		$admin .= '<div class="alert alert-danger">'.$error.'</div>';
	}else {
		$title = trim(strip_tags($_POST['title']));
		$modul = trim(strip_tags($_POST['modul']));
		$posisi = trim(strip_tags($_POST['posisi']));
		$cek = mysql_query("SELECT MAX(`ordering`) + 1 AS `ordering` FROM `modul` WHERE `posisi` = '$posisi'");
		$data = mysql_fetch_assoc($cek);
		$ordering = $data['ordering'];
		$insert = mysql_query("INSERT INTO `modul` (`modul`,`isi`,`posisi`,`ordering`,`type`) VALUES ('$title','$modul','$posisi','$ordering','block')");
		if ($insert) {
header("location: ?opsi=widgets");
		exit;	
}else {
$admin .= '<div class="alert alert-danger">'.mysql_error().'</div>';	
}
}
}
$admin .= '<form class="form-horizontal" method="post" action="">
<div class="form-group">
	<label class="col-sm-2 control-label">Judul Blok</label>
	<div class="col-sm-10"><input type="text" name="title" value="'.htmlentities(stripslashes(@$_POST['title'])).'" class="form-control"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Konten / Kode HTML</label>
	<div class="col-sm-10"><textarea name="modul" rows="5" class="form-control">'.htmlentities(stripslashes(@$data['modul'])).'</textarea></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Posisi</label>
	<div class="col-sm-10 form-inline"><select name="posisi" class="form-control">
		<option value="1">Kanan</option>
		<option value="0">Kiri</option>
</select></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label"></label>
	<div class="col-sm-10"><input type="submit" name="submit" value="Tambah" class="btn btn-success"></div>
</div>
</form>';
}

#############################################
# Tambah Modul
#############################################
if ($_GET['aksi'] == 'tambah_modul'){

$admin .= '<h4 class="page-header">Tambah Modul</h4>';

if (isset($_POST['submit'])) {
$error = null;
if (empty($_POST['title'])) $error .= '- Error Judul modul tidak boleh kosong<br/>';
if (empty($_POST['modul'])) $error .= '- Error File Modul tidak boleh kosong<br/>';
	
if ($error != '') {
$admin .= '<div class="alert alert-danger">'.$error.'</div>';
}else {
$title = trim(strip_tags($_POST['title']));
$modul = trim(strip_tags($_POST['modul']));
$posisi = trim(strip_tags($_POST['posisi']));
$cek = mysql_query("SELECT MAX(`ordering`) + 1 AS ordering FROM modul WHERE posisi='$posisi'");
$data = mysql_fetch_assoc($cek);
$ordering = $data['ordering'];
$insert = mysql_query("INSERT INTO modul (modul,isi,posisi,ordering,type) VALUES ('$title','$modul','$posisi','$ordering','module')");
if ($insert) {
$style_include[] ='<meta http-equiv="refresh" content="1; url=?opsi=widgets">';	
}else{
$admin .= '<div class="alert alert-danger">'.mysql_error().'</div>';	
}
}
}

$admin .= '<form class="form-horizontal" method="post" action="">
<div class="form-group">
	<label class="col-sm-2 control-label">Judul Modul</label>
	<div class="col-sm-10"><input type="text" name="title" value="'.htmlentities(stripslashes(@$_POST['title'])).'" class="form-control"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">File Modul (*.php)</label>
	<div class="col-sm-10"><input type="text" name="modul" value="'.htmlentities(stripslashes(@$_POST['modul'])).'" class="form-control"  placeholder="contoh : id-content/modul/berita/kategori.php"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Posisi</label>
	<div class="col-sm-10 form-inline"><select name="posisi" class="form-control">
  <option value="1">Kanan</option>
  <option value="0">Kiri</option>
</select></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label"></label>
	<div class="col-sm-10"><input type="submit" name="submit" value="Tambah" class="btn btn-success"></div>
</div>
</form>';
}

#############################################
# Tambah Blok
#############################################
if ($_GET['aksi'] == "edit_modul"){

$id = intval($_GET['id']);

if (isset($_POST['submit'])) {
	$title = trim(strip_tags($_POST['title']));
	
	$cek = mysql_num_rows(mysql_query("SELECT `type` FROM `modul` WHERE `id` = '$id' AND `type` = 'module'"));
	if ($cek) {
	$modul = trim(strip_tags($_POST['modul']));
	}else {
	$modul = $_POST['modul'];	
	}
	$update = mysql_query("UPDATE `modul` SET `modul` = '$title',`isi` = '$modul' WHERE `id` = '$id'");
	if ($update) {
		header("location: ?opsi=widgets");
		exit;
	}else {
		$admin .= '<div class="alert alert-danger">'.mysql_error().'</div>';
	}
}

$query = mysql_query("SELECT * FROM `modul` WHERE `id` = '$id'");
$data = mysql_fetch_assoc($query);
if ($data['type'] == 'module') {
$admin .= '<fieldset style="padding:20px;width:80%;border: 1px solid #d1d1d1;">
<legend>Edit Module</legend>
<form method="post" action="">
<table>
<tr>
	<td>Judul modul</td>
	<td>:</td>
	<td><input type="text" name="title" value="'.htmlentities(stripslashes(@$data['modul'])).'" size="40" /></td>
</tr>
<tr>
	<td>File Modul (*.php)</td>
	<td>:</td>
	<td><input type="text" name="modul" value="'.htmlentities(stripslashes(@$data['isi'])).'" size="40" /></td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><input type="submit" name="submit" value="Simpan" class="btn btn-success"></td>
</tr>
</table>
</form>
</fieldset>';
}else {
$admin .= '<fieldset style="padding:20px;width:80%;border: 1px solid #d1d1d1;">
<legend>Edit block</legend>
<form method="post" action="">
<table>
<tr><td>Judul blok</td><td>:</td><td><input type="text" name="title" value="'.htmlentities(stripslashes(@$data['modul'])).'" size="40" /></td></tr>
<tr><td>isi blok</td><td>:</td><td><textarea name="modul" cols="40" rows="5">'.htmlentities(stripslashes(@$data['isi'])).'</textarea></td></tr>
<tr><td>&nbsp;</td><td>&nbsp;</td><td><input type="submit" name="submit" value="Simpan" class="btn btn-success"></td></tr>
</table>
</form>
</fieldset>';
}
}

#############################################
# Hapus Modul
#############################################
if ($_GET['aksi'] == 'hapus_modul'){

$id     = int_filter($_GET['id']);

$hasil = $koneksi_db->sql_query("DELETE FROM modul WHERE id=$id");
if ($hasil) {
$admin .= '<div class="alert alert-success"><strong>Berhasil!</strong> Widget berhasil dihapus</div>';
$style_include[] ='<meta http-equiv="refresh" content="1; url=?opsi=widgets">';
}else {
$admin .= '<div class="alert alert-danger">'.mysql_error().'</div>';	
}
}

#############################################
# Publikasi Modul
#############################################
if ($_GET['aksi'] == 'pub'){	
if ($_GET['pub'] == 'tidak'){	
$id = int_filter ($_GET['id']);	
$koneksi_db->sql_query ("UPDATE modul SET published=0 WHERE id='$id'");		
}	
	
if ($_GET['pub'] == 'ya'){	
$id = int_filter ($_GET['id']);	
$koneksi_db->sql_query ("UPDATE modul SET published=1 WHERE id='$id'");		
}	
header ("location:?opsi=widgets");
exit;
}

#############################################
# Posisi Modul
#############################################
if ($_GET['aksi'] == 'posisi'){	
if ($_GET['posisi'] == 'kiri'){	
$id = int_filter ($_GET['id']);	
$koneksi_db->sql_query ("UPDATE modul SET posisi=0 WHERE id='$id'");		
}	
	
if ($_GET['posisi'] == 'kanan'){	
$id = int_filter ($_GET['id']);	
$koneksi_db->sql_query ("UPDATE modul SET posisi=1 WHERE id='$id'");		
}	
header ("location:?opsi=widgets");
exit;
}

echo $admin;

?>