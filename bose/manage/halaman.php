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

global $koneksi_db,$style_include,$script_include,$url_situs;

$script_include[] = '
<script type="text/javascript" src="id-includes/js/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
tinymce.init({
    selector: "textarea#mce",
    theme: "modern",
 });
</script>';

$admin = '<h3 class="page-header">Halaman Manager</h3>';

$admin .='<ol class="breadcrumb">
<li><a href="?opsi=halaman">Halaman</a></li>
<li><a href="?opsi=halaman&amp;aksi=tambah_halaman">Buat Halaman</a></li>
</ol>';

#############################################
# List Halaman
#############################################
if($_GET['aksi']==""){

$hasil = $koneksi_db->sql_query("SELECT `id`,`judul` FROM halaman ORDER BY id");
$admin .= '<table class="table table-striped table-hover">
<thead><tr>
<th class="text-center">No</th>
<th>Judul Halaman</th>
<th>Aksi</th>
</tr></thead><tbody>';
$no =1;
while ($data = $koneksi_db->sql_fetchrow($query)) {
if ($data['id'] == 1){
$deleted = '';	
}else {
$deleted = ' - <a class="text-danger" href="?opsi=halaman&amp;aksi=hapus_halaman&amp;id='.$data['id'].'" onclick="return confirm(\'Apakah Anda Ingin Menghapus Data Ini ?\')">Hapus</a>';		
}	
$admin .= '<tr>
<td class="text-center">'.$no.'</td>
<td><a href="'.$url_situs.'/'.get_pages($data['id'],$data['judul'],"halaman").'" title="'.$data['judul'].'" target="_blank">'.$data['judul'].'</a></td>
<td>
<a class="text-info" href="?opsi=halaman&amp;aksi=edit_halaman&amp;id='.$data['id'].'">Edit</a> - 
<a class="text-info" href="?opsi=menu&amp;aksi=tambah_menu&amp;url='.$url_situs.'/halaman-'.$data['id'].'-'.SEO($data['judul']).'.html">Buat Menu</a> - 
<a class="text-info" href="?opsi=menu&amp;aksi=tambah_menu&amp;menu='.$data['judul'].'&amp;url='.$url_situs.'/halaman-'.$data['id'].'-'.SEO($data['judul']).'.html">Buat Sub Menu</a><span class="del">'.$deleted.'</span></td>
</tr>';
$no++;		
}
$admin .= '</tbody></table>';
}

#############################################
# Tambah Halaman
#############################################
if($_GET['aksi']=="tambah_halaman"){

if (isset($_POST['submit'])) {

$judul	= text_filter($_POST['judul']);
$konten	= text_filter($_POST['konten']);
$slug 	= SEO($_POST['judul']);

$error = '';
if (!$judul)	$error .= "<strong>Gagal!</strong> Judul halaman belum diisi!<br />";
if (!$konten)	$error .= "<strong>Gagal!</strong> Konten halaman belum diisi!<br />";

if ($error != '') {
$admin .= '<div class="alert alert-danger">'.$error.'</div>';
}else{

$query = mysql_query("INSERT INTO halaman (judul,konten,slug) VALUES ('$judul','$konten','$slug')");	
if ($query) {
$admin .= '<div class="alert alert-success"><strong>Berhasil!</strong> Halaman berhasil ditambah</div>';
$style_include[] ='<meta http-equiv="refresh" content="1; url=?opsi=halaman">';
}else{
$admin .= '<div class="alert alert-danger">'.mysql_error().'</div>';
}
}
}

$admin .= '<form method="post" action="#">
<div class="form-group">
<label>Judul</label>
<input type="text" name="judul" value="'.$judul.'" class="form-control">
</div>
<div class="form-group">
<label>Konten</label>
<textarea name="konten" rows="5" id="mce" class="form-control">'.$konten.'</textarea>
</div>
<div class="form-group">
<button type="submit" name="submit" class="btn btn-success">Simpan</button>
</div>
</form>';
}

#############################################
# Edit Halaman
#############################################
if($_GET['aksi']=="edit_halaman"){

$id = int_filter($_GET['id']);	

if (isset($_POST['submit'])) {

$judul	= text_filter($_POST['judul']);
$konten	= text_filter($_POST['konten']);
$slug	= SEO($_POST['judul']);

$hasil = $koneksi_db->sql_query("UPDATE halaman SET judul='$judul', konten='$konten', slug='$slug' WHERE id='$id'");	
if ($hasil) {
$admin .= '<div class="alert alert-success"><strong>Berhasil!</strong> Halaman berhasil disimpan</div>';
$style_include[] ='<meta http-equiv="refresh" content="1; url=?opsi=halaman">';
}else{
$admin .= '<div class="alert alert-danger">'.mysql_error().'</div>';
}
}

$hasil = $koneksi_db->sql_query( "SELECT * FROM halaman WHERE id=$id" );
$data = $koneksi_db->sql_fetchrow($hasil);

$admin .= '<div class="border">';
$admin .= '<form method="post" action="#">
<div class="form-group">
<label>Judul</label>
<input type="text" name="judul" value="'.$data['judul'].'" class="form-control">
</div>
<div class="form-group">
<label>Konten</label>
<textarea name="konten" rows="5" id="mce" class="form-control">'.$data['konten'].'</textarea>
</div>
<div class="form-group">
<button type="submit" name="submit" class="btn btn-success">Simpan</button>
</div>
</form></div>';
}

#############################################
# Hapus Halaman
#############################################
if($_GET['aksi']=="hapus_halaman"){
$id = int_filter($_GET['id']);	
$query = mysql_query("DELETE FROM halaman WHERE id='$id'");
$admin .= '<div class="alert alert-success"><strong>Berhasil!</strong> Halaman dengan <strong>ID '.$id.'</strong> berhasil dihapus</div>';
$style_include[] ='<meta http-equiv="refresh" content="1; url=?opsi=halaman">';
}

echo $admin;

?>