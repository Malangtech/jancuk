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

if (!cek_login ()){
   $admin .='<p class="judul">Access Denied !!!!!!</p>';
   exit;
}

if (isset ($_GET['pg'])) $pg = int_filter ($_GET['pg']); else $pg = 0;
if (isset ($_GET['stg'])) $stg = int_filter ($_GET['stg']); else $stg = 0;
if (isset ($_GET['offset'])) $offset = int_filter ($_GET['offset']); else $offset = 0;

$admin  ='<h3 class="page-header">Links Manager</h3>';

$total =  $koneksi_db->sql_query("SELECT * FROM links");
$jumlah = $koneksi_db->sql_numrows($total);

$totalk =  $koneksi_db->sql_query("SELECT * FROM links_kat");
$jumlahk = $koneksi_db->sql_numrows($totalk);

$admin .='<ol class="breadcrumb">
<li><a href="?opsi=links&amp;modul=yes">Data Links</a> <span class="badge">'.$jumlah.'</span></li>
<li><a href="?opsi=links&amp;modul=yes&amp;aksi=tambah_links">Tambah Links</a></li>
<li><a href="?opsi=links&amp;modul=yes&amp;aksi=list_kategori">Kategori</a> <span class="badge">'.$jumlahk.'</span></li>
</ol>';

#############################################
# List Links
#############################################
if($_GET['aksi']==""){

$admin .= '<form method="get" action="main.php"><div class="input-group">
<input type="text" name="query" class="form-control" placeholder="Cari Data Links">
<span class="input-group-btn">
	<input type="hidden" name="opsi" value="links">
	<input type="hidden" name="modul" value="yes">
	<button class="btn btn-default" type="submit" name="submit" value="Search">Go!</button>
</span>
</div></form>';

$admin .= '<h4 class="page-header">Data Links</h4>';

$query 		= $_GET['query'];
$kategori 	= $_GET['kategori'];
$limit 		= 20;

if ($query) {
$total = $koneksi_db->sql_query("SELECT * FROM links WHERE judul like '%$query%' ORDER BY id DESC");
}elseif ($kategori) {
$total = $koneksi_db->sql_query("SELECT * FROM links WHERE kid=$kategori ORDER BY id DESC");
}else{
$total = $koneksi_db->sql_query("SELECT * FROM links ORDER BY id DESC");
}
$jumlah = $koneksi_db->sql_numrows($total);
$a = new paging ($limit);

if ($jumlah<1){
$admin.='<div class="alert alert-danger">Data links <strong>kosong</strong></div>';
}else{
if ($query) {
$hasil = $koneksi_db->sql_query("SELECT * FROM links WHERE judul like '%$query%' ORDER BY id DESC LIMIT $offset,$limit");
}elseif ($kategori) {
$hasil = $koneksi_db->sql_query("SELECT * FROM links WHERE kid=$kategori ORDER BY id DESC LIMIT $offset,$limit");
}else{
$hasil = $koneksi_db->sql_query("SELECT * FROM links ORDER BY id DESC LIMIT $offset,$limit" );
}

$admin .='<div class="table-responsive"><table class="table table-striped table-hover">
<thead><tr>
<th>No</th>
<th>Nama</th>
<th>Links</th>
<th>Kategori</th>
<th>Hits</th>
<th>Publikasi</th>
<th>Aksi</th>
</tr></thead><tbody>';
$no = 1;
while ($data = $koneksi_db->sql_fetchrow($hasil)) {
$kid		= $data['kid'];
$tanggal	= datetimes($data['tanggal']);
$published = ($data['publikasi'] == 1) ? '<a href="?opsi=links&amp;modul=yes&amp;aksi=pub&amp;pub=tidak&amp;id='.$data['id'].'"><span class="text-success"><strong>Ya</strong></span></a>' : '<a href="?opsi=links&amp;modul=yes&amp;aksi=pub&amp;pub=ya&amp;id='.$data['id'].'"><span class="text-danger"><strong>Tidak</strong></span></a>';

$hasil2 = $koneksi_db->sql_query( "SELECT * FROM links_kat WHERE kid=$kid");
$data2 = $koneksi_db->sql_fetchrow($hasil2);
$kategori = $data2['kategori'];

$admin .='<tr>
<td>'.$no.'</td>
<td><a class="text-info" href="?opsi=links&amp;modul=yes&amp;aksi=edit_links&amp;id='.$data['id'].'">'.$data['judul'].'</a><br>
<small class="text-muted">'.$tanggal.'</small></td>
<td><a class="text-info" href="'.$data['url'].'" target="_blank">'.$data['url'].'</a></td>
<td><a class="text-info" href="?opsi=links&amp;modul=yes&amp;kategori='.$kid.'">'.$kategori.'</a></td>
<td>'.$data['hit'].'</td>
<td>'.$published.'</td>
<td><a class="text-info" href="?opsi=links&amp;modul=yes&amp;aksi=edit_links&amp;id='.$data['id'].'">Edit</a> - 
<a class="text-danger" href="?opsi=links&amp;modul=yes&amp;aksi=hapus_links&amp;id='.$data['id'].'">Hapus</a></td>
</tr>';
$no++;
}
$admin .='</tbody></table></div>';
}

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
# Tambah Links
#############################################
if($_GET['aksi']=="tambah_links"){

$admin .='<h4 class="page-header">Tambah Links</h4>';

if (isset($_POST['submit'])){
	
$judul		= text_filter($_POST['judul']);
$url		= $_POST['url'];
$kid		= $_POST['kid'];
$keterangan	= text_filter($_POST['keterangan']);
$slug		= SEO($_POST['judul']);

$error = '';
if (!$judul)  		$error .= "<strong>Gagal!</strong> Nama link belum diisi<br />";
if (!$kid)  		$error .= "<strong>Gagal!</strong> Kategori link belum dipilih<br />";
if (!$keterangan)	$error .= "<strong>Gagal!</strong> Keterangan link belum diisi<br />";

if ($error){
$admin.='<div class="alert alert-danger">'.$error.'</div>';
}else{
//masukkan data
$tanggal = date('Y-m-d H:i:s');
$hasil = $koneksi_db->sql_query( "INSERT INTO links (kid,tanggal,judul,keterangan,url,slug) VALUES ('$kid','$tanggal','$judul','$keterangan','$url','$slug')" );
if($hasil){
$admin.='<div class="alert alert-success"><strong>Berhasil!</strong> Link bernama <strong>'.stripslashes ($_POST['judul']).'</strong> berhasil ditambah</div>';
$admin.='<meta http-equiv="refresh" content="1; url=?opsi=links&amp;modul=yes">';
}else{
$admin .= '<div class="alert alert-danger">'.mysql_error().'</div>';
}
}
}

$judul 		= !isset($judul) ? '' : $judul;
$url 		= !isset($url) ? '' : $url;
$keterangan	= !isset($keterangan) ? '' : $keterangan;

$admin .= '<form class="form-horizontal" method="post" action="" enctype ="multipart/form-data">
<div class="form-group">
	<label class="col-sm-2 control-label">Nama</label>
	<div class="col-sm-10"><input type="text" name="judul" value="'.$judul.'" class="form-control" placeholder="Masukkan nama website"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">URL Website</label>
	<div class="col-sm-10"><input type="text" name="url" value="'.$url.'" class="form-control" placeholder="Contoh : http://www.namadomain.com"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Kategori</label>
	<div class="col-sm-10"><select name="kid" class="form-control">';
$hasil = $koneksi_db->sql_query("SELECT * FROM links_kat ORDER BY kategori");
$admin .= '<option value="">None</option>';
while ($datas =  $koneksi_db->sql_fetchrow ($hasil)){
$admin .= '<option value="'.$datas['kid'].'">'.$datas['kategori'].'</option>';
}	
$admin .='</select></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Keterangan</label>
	<div class="col-sm-10"><textarea name="keterangan" rows="3" class="form-control" placeholder="Masukkan keterangan website">'.$keterangan.'</textarea></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label"></label>
	<div class="col-sm-10"><input type="submit" name="submit" value="Tambah" class="btn btn-success"></div>
</div>
</form></div>';
}

#############################################
# Edit Links
#############################################
if($_GET['aksi']=="edit_links"){

$id = int_filter($_GET['id']);

$admin .='<h4 class="page-header">Edit Links <span class="text-success">ID '.$id.'</span></h4>';

if (isset($_POST['submit'])){
	
$judul		= text_filter($_POST['judul']);
$url		= $_POST['url'];
$kid		= $_POST['kid'];
$keterangan	= text_filter($_POST['keterangan']);
$slug		= SEO($_POST['judul']);

$error = '';
if (!$judul)  		$error .= "<strong>Gagal!</strong> Nama link belum diisi<br />";
if (!$kid)  		$error .= "<strong>Gagal!</strong> Kategori link belum dipilih<br />";
if (!$keterangan)	$error .= "<strong>Gagal!</strong> Keterangan link belum diisi<br />";

if ($error){
$admin.='<div class="alert alert-danger">'.$error.'</div>';
}else{
//masukkan data
$hasil = $koneksi_db->sql_query( "UPDATE links SET judul='$judul',url='$url',keterangan='$keterangan',kid='$kid',slug='$slug' WHERE id='$id'" );
if($hasil){
$admin.='<div class="alert alert-success"><strong>Berhasil!</strong> Link bernama <strong>'.stripslashes ($_POST['judul']).'</strong> telah disimpan</div>';
$admin.='<meta http-equiv="refresh" content="1; url=?opsi=links&amp;modul=yes">';
}else{
$admin .= '<div class="alert alert-danger">'.mysql_error().'</div>';
}
}
}

$hasil = $koneksi_db->sql_query( "SELECT * FROM links WHERE id=$id" );
$data = $koneksi_db->sql_fetchrow($hasil);
$judul     	= $data['judul'];
$keterangan	= $data['keterangan'];
$kid     	= $data['kid'];
$url 		= $data['url'];

$admin .='<form class="form-horizontal" method="post" action="" enctype ="multipart/form-data">
<div class="form-group">
	<label class="col-sm-2 control-label">Nama</label>
	<div class="col-sm-10"><input type="text" name="judul" value="'.$judul.'" class="form-control" placeholder="Masukkan nama link"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">URL Website</label>
	<div class="col-sm-10"><input type="text" name="url" value="'.$url.'" class="form-control" placeholder="Contoh : http://www.namadomain.com"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Kategori</label>
	<div class="col-sm-10"><select name="kid" class="form-control">';
$hasil = $koneksi_db->sql_query("SELECT * FROM links_kat ORDER BY kategori ASC");
$admin .='<option value="">None</option>';
while ($datas =  $koneksi_db->sql_fetchrow ($hasil)){
$pilihan = ($datas['kid']==$kid)?"selected":'';
$admin .='<option value="'.$datas['kid'].'" '.$pilihan.'>'.$datas['kategori'].'</option>';
}
$admin .='</select></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Keterangan</label>
	<div class="col-sm-10"><textarea name="keterangan" rows="3" class="form-control" placeholder="Masukkan keterangan link">'.$keterangan.'</textarea></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label"></label>
	<div class="col-sm-10"><input type="submit" value="Simpan" name="submit" class="btn btn-success"></div>
</div></form>';
}

#############################################
# Hapus Links
#############################################
if($_GET['aksi']=="hapus_links"){

$id     = int_filter($_GET['id']);

$koneksi_db->sql_query("DELETE FROM links WHERE id='$id'");
$admin.='<div class="alert alert-success"><strong>Berhasil!</strong> Link telah dihapus</div>';
$admin.='<meta http-equiv="refresh" content="1; url=?opsi=links&amp;modul=yes">';
}

#############################################
# List Kategori
#############################################
if($_GET['aksi']=="list_kategori"){

$admin .='<div class="row"><div class="col-md-4">';

if (isset($_POST['submit'])) {

$kategori	= text_filter($_POST['kategori']);
$keterangan	= text_filter($_POST['keterangan']);
$slug    	= SEO($_POST['kategori']);

$total = $koneksi_db->sql_query( "SELECT * FROM links_kat WHERE kategori = '".$_POST['kategori']."'");
$jumlah = $koneksi_db->sql_numrows($total);

$error = '';
if ($jumlah)  		$error .= "<strong>Gagal!</strong> Nama kategori sudah ada dalam database<br />";
if (!$kategori)  	$error .= "<strong>Gagal!</strong> Nama kategori belum diisi<br />";
if (!$keterangan) 	$error .= "<strong>Gagal!</strong> Keterangan kategori belum diisi<br />";

if ($error){
$admin.='<div class="alert alert-danger">'.$error.'</div>';
}else{
$hasil = $koneksi_db->sql_query("INSERT INTO links_kat (kategori,keterangan,slug) VALUES ('$kategori','$keterangan','$slug')");
$admin.='<div class="alert alert-success"><strong>Berhasil!</strong> Kategori links telah ditambah</div>';
$admin.='<meta http-equiv="refresh" content="1; url=?opsi=links&amp;modul=yes&amp;aksi=list_kategori">';
}
}
$kategori	= !isset($kategori) ? '' : $kategori;
$keterangan	= !isset($keterangan) ? '' : $keterangan;

$admin .='<form method="post" action="">
<div class="form-group">
    <label>Kategori</label>
	<input type="text" size="30" name="kategori" value="'.$kategori.'" class="form-control" placeholder="Masukkan nama kategori">
</div>
<div class="form-group">
    <label>Keterangan</label>
	<textarea name="keterangan" rows="3" class="form-control" placeholder="Masukkan keterangan kategori">'.$keterangan.'</textarea>
</div>
<div class="form-group">
    <input type="submit" name="submit" value="Tambah" class="btn btn-success">
</div>
</form>';
$admin .='</div>';

$admin .='<div class="col-md-8">';
$query = $koneksi_db->sql_query( "SELECT * FROM links_kat ORDER BY kategori" );
$admin .='<div class="table-responsive"><table class="table table-striped table-hover">
<thead><tr>
<th>No</th>
<th>Kategori</th>
<th>Keterangan</th>
<th>Aksi</th>
</tr></thead><tbody>';

$no = 1;
while ($data = $koneksi_db->sql_fetchrow($query)) {
$kid = $data['kid'];

$total = $koneksi_db->sql_query( "SELECT * FROM links WHERE kid=$kid");
$jumlah = $koneksi_db->sql_numrows( $total );

$admin .='<tr>
<td>'.$no.'</td>
<td><a href="?opsi=links&amp;modul=yes&amp;kategori='.$kid.'">'.$data['kategori'].'</a> <span class="badge">'.$jumlah.'</span></td>
<td>'.$data['keterangan'].'</td>
<td><a class="text-info" href="?opsi=links&amp;modul=yes&amp;aksi=edit_kategori&amp;kid='.$kid.'">Edit</a> - 
<a class="text-danger" href="?opsi=links&amp;modul=yes&amp;aksi=hapus_kategori&amp;kid='.$kid.'">Hapus</a></td>
</tr>';
$no++;
}
$admin .= '</tbody></table></div>';
$admin .='</div></div>';
}

#############################################
# Edit Kategori
#############################################
if($_GET['aksi']=="edit_kategori"){

$kid = int_filter($_GET['kid']);

$admin .='<h4 class="page-header">Edit Kategori ID '.$kid.'</h4>';

if (isset($_POST['submit'])) {
$kategori	= $_POST['kategori'];
$keterangan	= $_POST['keterangan'];
$slug		= SEO($_POST['kategori']);

$total = $koneksi_db->sql_query( "SELECT * FROM links_kat WHERE kategori = '".$_POST['kategori']."' and kid != '".$kid."'");
$jumlah = $koneksi_db->sql_numrows( $total );

$error = '';
if ($jumlah)  		$error .= "<strong>Gagal!</strong> Nama kategori sudah ada dalam database<br />";
if (!$kategori)  	$error .= "<strong>Gagal!</strong> Nama kategori belum diisi<br />";
if (!$keterangan)	$error .= "<strong>Gagal!</strong> Keterangan kategori belum diisi<br />";

if ($error){
$admin.='<div class="alert alert-danger">'.$error.'</div>';
}else{
$hasil = $koneksi_db->sql_query( "UPDATE links_kat SET kategori='$kategori',keterangan='$keterangan',slug='$slug' WHERE kid='$kid'" );
$admin.='<div class="alert alert-success"><strong>Berhasil!</strong> Kategori links berhasil disimpan</div>';
$admin.='<meta http-equiv="refresh" content="1; url=?opsi=links&amp;modul=yes&amp;aksi=list_kategori">';
}
}

$hasil = $koneksi_db->sql_query( "SELECT * FROM links_kat WHERE kid=$kid" );
$data = $koneksi_db->sql_fetchrow($hasil);
$kategori  	= $data['kategori'];
$keterangan	= $data['keterangan'];

$admin .='<form class="form-horizontal" method="post" action="">
<div class="form-group">
    <label class="col-sm-2 control-label">Kategori</label>
	<div class="col-sm-10"><input type="text" name="kategori" value="'.$kategori.'" class="form-control" placeholder="Masukkan nama kategori"></div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">Keterangan</label>
	<div class="col-sm-10"><textarea name="keterangan" rows="3" class="form-control" placeholder="Masukkan keterangan kategori">'.$keterangan.'</textarea></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label"></label>
    <div class="col-sm-10"><input type="submit" name="submit" value="Simpan" class="btn btn-success"></div>
</div>
</form>';
}

#############################################
# Hapus Kategori
#############################################
if($_GET['aksi']=="hapus_kategori"){

$kid = int_filter($_GET['kid']);

$admin .='<h4 class="page-header">Hapus kategori links dengan ID '.$kid.'</h4>';

$hasil = $koneksi_db->sql_query( "SELECT * FROM links_kat WHERE kid=$kid" );
$data = $koneksi_db->sql_fetchrow($hasil);
$kategori 	= $data['kategori'];
$ket   		= $data['ket'];

if (empty ($kategori)){
$admin.='<div class="alert alert-danger">Kategori dengan ID <strong>'.$kid.' Kosong</strong></div>';
$admin.='<meta http-equiv="refresh" content="3; url=?opsi=links&amp;modul=yes">';
}else {
$admin .= '<div class="alert alert-danger">Seluruh links dalam kategori ini akan dihapus! Anda yakin?<br />
<a class="text-info" href="?opsi=links&amp;modul=yes&amp;aksi=hapus_kategori&amp;kid='.$kid.'&amp;konfirm=yes">Ya</a> | <a class="text-info" href="?opsi=links&amp;mod=yes&amp;aksi=kategori">Tidak</a></div>';
}
}

if (isset($_GET['konfirm'])=="yes") {
$koneksi_db->sql_query("DELETE FROM links_kat WHERE kid='$kid'");
$koneksi_db->sql_query("DELETE FROM links WHERE kid='$kid'");
$admin.='<div class="alert alert-success"><strong>Berhasil!</strong> Kategori dan links dalam kategori ini telah dihapus!</div>';
$admin.='<meta http-equiv="refresh" content="3; url=?opsi=links&amp;modul=yes&amp;aksi=kategori">';
}

##########################
# Publikasi Links
##########################
if ($_GET['aksi'] == 'pub'){
if ($_GET['pub'] == 'tidak'){
	$id = int_filter ($_GET['id']);
	$koneksi_db->sql_query ("UPDATE links SET publikasi=0 WHERE id='$id'");
	header ("location:?opsi=links&modul=yes");
}	
	
if ($_GET['pub'] == 'ya'){
	$id = int_filter ($_GET['id']);
	$koneksi_db->sql_query ("UPDATE links SET publikasi=1 WHERE id='$id'");
	header ("location:?opsi=links&modul=yes");
}	
}

echo $admin;

?>