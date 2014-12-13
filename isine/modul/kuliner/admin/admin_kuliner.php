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

global $koneksi_db,$maxadmindata,$theme,$style_include,$script_include,$url_situs;

$script_include[] = '
<script type="text/javascript" src="id-includes/js/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
tinymce.init({
    selector: "textarea#mce",
    theme: "modern",
 });
</script>';

if (!cek_login ()){
   $admin .='<p class="judul">Access Denied !!!!!!</p>';
   exit;
}

$username = $_SESSION["UserName"];

$temp 	= 'id-content/modul/berita/images/temp/';
$thumb	= 'id-content/modul/berita/images/thumb/';
$normal	= 'id-content/modul/berita/images/normal/';

$ss = $koneksi_db->sql_query( "SELECT * FROM berita_setting WHERE id=1");
$datass = $koneksi_db->sql_fetchrow($ss);
$thumb_width 	= int_filter($datass['thumb_width']);
$thumb_height 	= int_filter($datass['thumb_height']);
$normal_width	= int_filter($datass['normal_width']);
$normal_height	= int_filter($datass['normal_height']);
$kualitas 		= int_filter($datass['kualitas']);

if (isset ($_GET['pg'])) $pg = int_filter ($_GET['pg']); else $pg = 0;
if (isset ($_GET['stg'])) $stg = int_filter ($_GET['stg']); else $stg = 0;
if (isset ($_GET['offset'])) $offset = int_filter ($_GET['offset']); else $offset = 0;

$tot_pub =  $koneksi_db->sql_query( "SELECT * FROM berita WHERE publikasi=1" );
$jum_pub = $koneksi_db->sql_numrows( $tot_pub );

$tot_pend =  $koneksi_db->sql_query( "SELECT * FROM berita WHERE publikasi=0" );
$jum_pend = $koneksi_db->sql_numrows( $tot_pend );

$tot_komentar =  $koneksi_db->sql_query( "SELECT * FROM berita_komentar" );
$jum_komentar = $koneksi_db->sql_numrows($tot_komentar);

$admin  ='<h3 class="page-header">Kuliner Manager</h3>';

$admin .='<ol class="breadcrumb">
<li><a href="?opsi=kuliner&amp;modul=yes">List Kuliner</a> <span class="badge">'.$jum_pub.'</span></li>
<li><a href="?opsi=kuliner&amp;modul=yes&amp;aksi=list_berita_pending">Kuliner Pending</a> <span class="badge">'.$jum_pend.'</span></li>
<li><a href="?opsi=kuliner&amp;modul=yes&amp;aksi=tambah_berita">Tambah Kuliner</a></li>
<li><a href="?opsi=kuliner&amp;modul=yes&amp;aksi=list_kategori">Kategori</a></li>
<li><a href="?opsi=kuliner&amp;modul=yes&amp;aksi=list_komentar">Komentar</a> <span class="badge">'.$jum_komentar.'</span></li>
<li><a href="?opsi=kuliner&amp;modul=yes&amp;aksi=thumbnail">Thumbnail Default</a></li>
<li><a href="?opsi=kuliner&amp;modul=yes&amp;aksi=pengaturan">Pengaturan</a></li>
</ol>';

#############################################
# List Kuliner
#############################################
if($_GET['aksi']==""){

$admin .= '<form method="get" action="main.php"><div class="input-group">
<input type="text" name="cari" class="form-control" placeholder="Cari Judul / Author Berita">
<span class="input-group-btn">
	<input type="hidden" name="opsi" value="berita">
	<input type="hidden" name="modul" value="yes">
	<button class="btn btn-default" type="submit" name="submit" value="Search">Go!</button>
</span>
</div></form>';

$admin .= '<h4 class="page-header">List Berita</h4>';

if (isset($_POST['submit'])){
$tot     .= $_POST['tot'];
$pcheck ='';
for($i=1;$i<=$tot;$i++){
$check = $_POST['check'.$i] ;
if($check <> ""){
$pcheck .= $check . ",";
}
}
$pcheck = substr_replace($pcheck, "", -1, 1);
$error = '';
if ($error){
$admin.='<div class="alert alert-danger">'.$error.'</div>';
}
if ($pcheck)  $sukses .= "Sukses: news dengan id $pcheck  Telah di hapus !<br />";
$hasil = $koneksi_db->sql_query( "SELECT * FROM berita WHERE id in($pcheck)" );
while($data = mysql_fetch_array($hasil)){
    $tempnews 	= 'id-content/modul/berita/images/normal/';
    $namagambar =  $data['gambar'];
    $uploaddir = $tempnews . $namagambar; 
	unlink($uploaddir);
}
$koneksi_db->sql_query("DELETE FROM berita WHERE id in($pcheck)");
if ($sukses){
$admin.='<div class="alert alert-success">'.$sukses.'</div>';
}
}

$cari	= $_GET['cari'];
$kid 	= $_GET['kid'];

$limit = 25;

if($cari){
$total = $koneksi_db->sql_query( "SELECT * FROM berita WHERE publikasi=1 and judul like '%$cari%' or user like '%$cari%' ORDER BY tanggal DESC");
}
elseif ($kid) {
$total = $koneksi_db->sql_query( "SELECT * FROM berita WHERE publikasi=1 and kid=".$kid." ORDER BY tanggal DESC");
}else{
$total = $koneksi_db->sql_query( "SELECT * FROM berita WHERE publikasi=1 ORDER BY tanggal DESC");
}
$jumlah = $koneksi_db->sql_numrows( $total );

if (!isset ($_GET['offset'])) {
	$offset = 0;
}

$a = new paging ($limit);
if ($jumlah<1){
$admin.='<div class="alert alert-danger">Berita kosong</div>';
}else{
if($cari){
$hasil = $koneksi_db->sql_query( "SELECT * FROM berita WHERE publikasi=1 and judul like '%$cari%' or user like '%$cari%' ORDER BY tanggal DESC LIMIT $offset,$limit ");
}
elseif ($kid) {
$hasil = $koneksi_db->sql_query( "SELECT * FROM berita WHERE publikasi=1 and kid=".$kid." ORDER BY tanggal DESC LIMIT $offset,$limit ");
}else{
$hasil = $koneksi_db->sql_query( "SELECT * FROM berita WHERE publikasi=1 ORDER BY tanggal DESC LIMIT $offset,$limit");
}
if($offset){
$no = $offset+1;
}else{
$no = 1;
}

$admin .="<form method='post' action=''>";
$admin .= '<div class="table-responsive"><table class="table table-striped table-hover">
<thead><tr>
<th>No</th>
<th>#</th>
<th></th>
<th>Judul</th>
<th>Kategori</th>
<th>Author</th>
<th>Tanggal</th>
</tr></thead><tbody>';
while ($data = $koneksi_db->sql_fetchrow($hasil)) {
$tanggal	= datetimes($data['tanggal'],false);
$kid		= $data['kid'];
if($data['gambar']==''){
$gambar ='<img class="img-thumbnail" src="id-content/modul/berita/images/thumb_default.jpg" width="50" height="50">';
}else{
$gambar ='<img class="img-thumbnail" src="id-content/modul/berita/images/thumb/'.$data['gambar'].'" width="50" height="50">';
}

$hasil2 = $koneksi_db->sql_query( "SELECT * FROM berita_kat WHERE kid=$kid");
$data2 = $koneksi_db->sql_fetchrow($hasil2);
$kategori = $data2['kategori'];

$hapus_gambar = ($data['gambar'] == '') ? '' : '<a class="text-danger" href="?opsi=berita&amp;modul=yes&amp;aksi=hapus_gambar&amp;id='.$data['id'].'" onclick="return confirm(\'Apakah Anda ingin menghapus gambar ini ?\')">Hapus Gambar</a> -';
$published = ($data['publikasi'] == 1) ? '<a href="?opsi=berita&amp;modul=yes&amp;aksi=pub&amp;pub=tidak&amp;id='.$data['id'].'"><span class="text-success"><strong>Ya</strong></span></a>' : '<a href="?opsi=berita&amp;modul=yes&amp;aksi=pub&amp;pub=ya&amp;id='.$data['id'].'"><span class="text-danger"><strong>Tidak</strong></span></a>';

$admin .='<tr>
<td>'.$no.'</td>
<td><input type=checkbox name=check'.$no.' value="'.$data['id'].'"></td>
<td>'.$gambar.'</td>
<td><a href="?opsi=berita&amp;modul=yes&aksi=edit_berita&id='.$data['id'].'">'.$data['judul'].'</a><br>
<small>'.$hapus_gambar.'
<a class="text-info" href="?opsi=berita&amp;modul=yes&amp;aksi=edit_berita&amp;id='.$data['id'].'">Edit</a> - 
<a class="text-danger" href="?opsi=berita&amp;modul=yes&amp;aksi=hapus_berita&amp;id='.$data['id'].'" onclick="return confirm(\'Apakah Anda ingin menghapus data ini ?\')">Hapus</a>
</small></td>
<td><a class="text-info" href="?opsi=berita&amp;modul=yes&amp;kid='.$kid.'">'.$kategori.'</a></td>
<td>'.$data['user'].'</td>
<td><small><abbr title="'.datetimes($data['tanggal']).'">'.$tanggal.'</abbr><br>Publikasi '.$published.'</small></td>
</tr>';		
$no++;
}
$admin .='<tr><td colspan="7"><input type="hidden" name="tot" value="'.$jumlah.'">
<input type="submit" value="Hapus" name="submit" class="btn btn-danger"></td></tr>';

$admin .='</tbody></table></div>';
$admin .='</form>';
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
# List Berita Pending
#############################################
if($_GET['aksi']=="list_berita_pending"){

$admin .= '<form method="get" action="main.php"><div class="input-group">
<input type="text" name="cari" class="form-control" placeholder="Cari Judul / Author Berita">
<span class="input-group-btn">
	<input type="hidden" name="opsi" value="berita">
	<input type="hidden" name="modul" value="yes">
	<input type="hidden" name="aksi" value="list_berita_pending">
	<button class="btn btn-default" type="submit" name="submit" value="Search">Go!</button>
</span>
</div></form>';

$admin .= '<h4 class="page-header">List Berita Pending</h4>';

if (isset($_POST['submit'])){
$tot     .= $_POST['tot'];
$pcheck ='';
for($i=1;$i<=$tot;$i++){
$check = $_POST['check'.$i] ;
if($check <> ""){
$pcheck .= $check . ",";
}
}
$pcheck = substr_replace($pcheck, "", -1, 1);
$error = '';
if ($error){
$admin.='<div class="alert alert-danger">'.$error.'</div>';
}
if ($pcheck)  $sukses .= "Sukses: news dengan id $pcheck  Telah di hapus !<br />";
$hasil = $koneksi_db->sql_query( "SELECT * FROM berita WHERE id in($pcheck)" );
while($data = mysql_fetch_array($hasil)){
    $tempnews 	= 'id-content/modul/berita/images/normal/';
    $namagambar =  $data['gambar'];
    $uploaddir = $tempnews . $namagambar; 
	unlink($uploaddir);
}
$koneksi_db->sql_query("DELETE FROM berita WHERE id in($pcheck)");
if ($sukses){
$admin.='<div class="alert alert-success">'.$sukses.'</div>';
}
}

$cari	= $_GET['cari'];
$kid 	= $_GET['kid'];

$limit = 25;

if($cari){
$total = $koneksi_db->sql_query( "SELECT * FROM berita WHERE publikasi=0 and judul like '%$cari%' or user like '%$cari%' ORDER BY tanggal DESC");
}elseif ($kid) {
$total = $koneksi_db->sql_query( "SELECT * FROM berita WHERE publikasi=0 and kid=".$kid." ORDER BY tanggal DESC");
}else{
$total = $koneksi_db->sql_query( "SELECT * FROM berita WHERE publikasi=0 ORDER BY tanggal DESC");
}
$jumlah = $koneksi_db->sql_numrows( $total );

if (!isset ($_GET['offset'])) {
	$offset = 0;
}

$a = new paging ($limit);
if ($jumlah<1){
$admin.='<div class="alert alert-danger">Berita kosong</div>';
}else{
if($cari){
$hasil = $koneksi_db->sql_query( "SELECT * FROM berita WHERE publikasi=0 and judul like '%$cari%' or user like '%$cari%' ORDER BY tanggal DESC LIMIT $offset,$limit ");
}elseif ($kid) {
$hasil = $koneksi_db->sql_query( "SELECT * FROM berita WHERE publikasi=0 and kid=".$kid." ORDER BY tanggal DESC LIMIT $offset,$limit ");
}else{
$hasil = $koneksi_db->sql_query( "SELECT * FROM berita WHERE publikasi=0 ORDER BY tanggal DESC LIMIT $offset,$limit");
}
if($offset){
$no = $offset+1;
}else{
$no = 1;
}

$admin .="<form method='post' action=''>";
$admin .= '<div class="table-responsive"><table class="table table-striped table-hover">
<thead><tr>
<th>No</th>
<th>#</th>
<th></th>
<th>Judul</th>
<th>Kategori</th>
<th>Author</th>
<th>Tanggal</th>
</tr></thead><tbody>';
while ($data = $koneksi_db->sql_fetchrow($hasil)) {
$tanggal	= datetimes($data['tanggal'],false);
$kid		= $data['kid'];
if($data['gambar']==''){
$gambar ='<img class="img-thumbnail" src="id-content/modul/berita/images/news-default.jpg" width="50" height="50">';
}else{
$gambar ='<img class="img-thumbnail" src="id-content/modul/berita/images/thumb/'.$data['gambar'].'" width="50" height="50">';
}

$hasil2 = $koneksi_db->sql_query( "SELECT * FROM berita_kat WHERE kid=$kid");
$data2 = $koneksi_db->sql_fetchrow($hasil2);
$kategori = $data2['kategori'];

$hapus_gambar = ($data['gambar'] == '') ? '' : '<a class="text-danger" href="?opsi=berita&amp;modul=yes&amp;aksi=hapus_gambar&amp;id='.$data['id'].'" onclick="return confirm(\'Apakah Anda ingin menghapus gambar ini ?\')">Hapus Gambar</a> -';
$published = ($data['publikasi'] == 1) ? '<a href="?opsi=berita&amp;modul=yes&amp;aksi=pub&amp;pub=tidak&amp;id='.$data['id'].'"><span class="text-success"><strong>Ya</strong></span></a>' : '<a href="?opsi=berita&amp;modul=yes&amp;aksi=pub&amp;pub=ya&amp;id='.$data['id'].'"><span class="text-danger"><strong>Tidak</strong></span></a>';

$admin .='<tr>
<td>'.$no.'</td>
<td><input type=checkbox name=check'.$no.' value="'.$data['id'].'"></td>
<td>'.$gambar.'</td>
<td><a href="?opsi=berita&amp;modul=yes&aksi=edit_berita&id='.$data['id'].'">'.$data['judul'].'</a><br>
<small>'.$hapus_gambar.'
<a class="text-info" href="?opsi=berita&amp;modul=yes&amp;aksi=edit_berita&amp;id='.$data['id'].'">Edit</a> - 
<a class="text-danger" href="?opsi=berita&amp;modul=yes&amp;aksi=hapus_berita&amp;id='.$data['id'].'" onclick="return confirm(\'Apakah Anda ingin menghapus data ini ?\')">Hapus</a>
</small></td>
<td><a class="text-info" href="?opsi=berita&amp;modul=yes&amp;kid='.$kid.'">'.$kategori.'</a></td>
<td>'.$data['user'].'</td>
<td><small><abbr title="'.datetimes($data['tanggal']).'">'.$tanggal.'</abbr><br>Publikasi '.$published.'</small></td>
</tr>';		
$no++;
}
$admin .='<tr><td colspan="7"><input type="hidden" name="tot" value="'.$jumlah.'">
<input type="submit" value="Hapus" name="submit" class="btn btn-danger"></td></tr>';

$admin .='</tbody></table></div>';
$admin .='</form>';
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
# List Kategori
#############################################
if($_GET['aksi']=="list_kategori"){

$admin .= '<div class="row">';
$admin .= '<div class="col-md-4">';
if (isset($_POST['submit'])) {

$kategori	= text_filter($_POST['kategori']);
$ket     	= text_filter($_POST['ket']);
$slug		= SEO($_POST['kategori']);
$id_parent	= $_POST['id_parent'];

$total = $koneksi_db->sql_query( "SELECT * FROM berita_kat WHERE kategori='".$_POST['kategori']."'");
$jumlah = $koneksi_db->sql_numrows( $total );
$error = '';
if ($jumlah) 	$error .= "<strong>Gagal!</strong> Nama kategori <strong>$judul</strong> sudah ada di dalam database!<br />";
if (!$kategori)	$error .= "<strong>Gagal!</strong> Nama kategori belum diisi!<br />";
if (!$ket) 		$error .= "<strong>Gagal!</strong> Keterangan kategori belum diisi!<br />";

if ($error){
$admin.='<div class="alert alert-danger">'.$error.'</div>';
}else{
$hasil = $koneksi_db->sql_query("INSERT INTO berita_kat (kategori,ket,id_parent,slug) VALUES ('$kategori','$ket','$id_parent','$slug')");
if ($hasil) {
$admin .='<div class="alert alert-success"><strong>Berhasil!</strong> Kategori berita berhasil ditambah</div>';
$admin .='<meta http-equiv="refresh" content="1; url=?opsi=berita&amp;modul=yes&amp;aksi=list_kategori">';
}else{
$admin .= '<div class="alert alert-danger">'.mysql_error().'</div>';
}
}
}

$kategori	= !isset($kategori) 	? '' : $kategori;
$ket		= !isset($ket) 			? '' : $ket;
$id_parent	= !isset($id_parent)	? '' : $id_parent;
$slug		= !isset($slug) 		? '' : $slug;

$admin .='<form method="post" action="" id="posts">
<div class="form-group">
	<label>Kategori</label>
	<input type="text" size="30" name="kategori" value="'.$kategori.'" class="form-control">
</div>
<div class="form-group">
	<label>Parent</label>
	<select name="id_parent" class="form-control">';
$hasil = $koneksi_db->sql_query("SELECT * FROM berita_kat WHERE id_parent=0 ORDER BY kategori ASC");
$admin .= '<option value="0">None</option>';
while ($data =  $koneksi_db->sql_fetchrow ($hasil)){
$admin .= '<option value="'.$data['kid'].'">'.$data['kategori'].'</option>';
}
$admin .='</select>
</div>
<div class="form-group">
	<label>Keterangan</label>
	<textarea name="ket" rows="3" class="form-control">'.$ket.'</textarea>
</div>
<div class="form-group">
	<label></label>
	<input type="submit" name="submit" value="Tambah" class="btn btn-success">
</div>
</form>';
$admin .= '</div>';

$admin .= '<div class="col-md-8">';
$total = $koneksi_db->sql_query( "SELECT * FROM berita_kat WHERE id_parent=0");
$jumlah = $koneksi_db->sql_numrows( $total );
$limit = 5;

if (!isset ($_GET['offset'])) {
	$offset = 0;
}

$ada = new paging ($limit);
if ($jumlah<1){
$admin.='<div class="alert alert-danger">Kategori berita kosong</div>';
}else{
$hasil = $koneksi_db->sql_query( "SELECT * FROM berita_kat WHERE id_parent=0 ORDER BY kategori ASC LIMIT $offset,$limit");

if($offset){
$no = $offset+1;
}else{
$no = 1;
}

$admin .= '<div class="table-responsive"><table class="table table-striped table-hover">
<thead><tr>
<th>No</th>
<th>Kategori</th>
<th>Keterangan</th>
<th>Aksi</th>
</tr></thead><tbody>';
while ($data = $koneksi_db->sql_fetchrow($hasil)) {
$kid = $data['kid'];

$tot_ber =  $koneksi_db->sql_query("SELECT * FROM berita WHERE kid=$kid");
$jum_ber = $koneksi_db->sql_numrows($tot_ber);

$admin .='<tr>
<td>'.$no.'</td>
<td><a class="text-info" href="?opsi=berita&amp;modul=yes&amp;kid='.$kid.'"><strong>'.$data['kategori'].'</strong></a> <span class="badge">'.$jum_ber.'</span></td>
<td>'.$data['ket'].'</td>
<td><a class="text-info" href="?opsi=berita&amp;modul=yes&amp;aksi=edit_kategori&amp;kid='.$kid.'">Edit</a> - 
<a class="text-danger" href="?opsi=berita&amp;modul=yes&amp;aksi=hapus_kategori&amp;kid='.$kid.'">Hapus</a></td>
</tr>';

$hasils = $koneksi_db->sql_query( "SELECT * FROM berita_kat WHERE id_parent=$kid ORDER BY kategori ASC");
while ($datas = $koneksi_db->sql_fetchrow($hasils)) {
$kids = $datas['kid'];

$tot_bers =  $koneksi_db->sql_query("SELECT * FROM berita WHERE kid=$kids");
$jum_bers = $koneksi_db->sql_numrows($tot_bers);

$admin .='<tr>
<td></td>
<td><a class="text-info" href="?opsi=berita&amp;modul=yes&amp;kid='.$kids.'">'.$datas['kategori'].'</a> <span class="badge">'.$jum_bers.'</span></td>
<td>'.$datas['ket'].'</td>
<td><a class="text-info" href="?opsi=berita&amp;modul=yes&amp;aksi=edit_kategori&amp;kid='.$kids.'">Edit</a> - 
<a class="text-danger" href="?opsi=berita&amp;modul=yes&amp;aksi=hapus_kategori&amp;kid='.$kids.'" onclick="return confirm(\'Apakah Anda Ingin Menghapus Data Ini ?\')">Hapus</a></td>
</tr>';
}
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
$admin .= $ada-> getPaging($jumlah, $pg, $stg);
$admin .= '</center>';
}
$admin.='</div>';
$admin.='</div>';
}

#############################################
# Edit Kategori
#############################################
if($_GET['aksi']=="edit_kategori"){

$kid = int_filter($_GET['kid']);

$admin .='<h4 class="page-header">Edit kategori berita dengan ID '.$kid.'</h4>';

if (isset($_POST['submit'])) {

$kategori	= text_filter($_POST['kategori']);
$ket		= text_filter($_POST['ket']);
$slug   	= SEO($_POST['kategori']);
$id_parent	= $_POST['id_parent'];

$total = $koneksi_db->sql_query( "SELECT * FROM berita_kat WHERE kategori = '".$_POST['kategori']."' and kid != '".$kid."'");
$jumlah = $koneksi_db->sql_numrows( $total );

$error = '';
if ($jumlah)	$error .= "<strong>Gagal!</strong> Duplicate Title of Topic $judul!<br />";	
if (!$kategori)	$error .= "<strong>Gagal!</strong> Please enter Title of Topic!<br />";
if (!$ket)		$error .= "<strong>Gagal!</strong> Please enter Description of Topic!<br />";

if ($error){
$admin.='<div class="alert alert-danger">'.$error.'</div>';
}else{
$hasil = $koneksi_db->sql_query( "UPDATE berita_kat SET kategori='$kategori',ket='$ket',id_parent='$id_parent',slug='$slug' WHERE kid='$kid'" );
if($hasil){
$admin .='<div class="alert alert-success"><strong>Berhasil!</strong> Kategori berita berhasil disimpan</div>';
$admin .='<meta http-equiv="refresh" content="1; url=?opsi=berita&amp;modul=yes&amp;aksi=list_kategori">';
}else{
$admin .= '<div class="alert alert-danger">'.mysql_error().'</div>';
}
}
}

$hasil = $koneksi_db->sql_query( "SELECT * FROM berita_kat WHERE kid=$kid" );
$data = $koneksi_db->sql_fetchrow($hasil);

$admin .='<form class="form-horizontal" method="post" action="">
<div class="form-group">
    <label class="col-sm-2 control-label">Kategori</label>
	<div class="col-sm-10"><input type="text" name="kategori" value="'.$data['kategori'].'" class="form-control">
    <input type="hidden" name="slug" value="'.$data['slug'].'"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Parent Kategori</label>
	<div class="col-sm-10"><select name="id_parent" class="form-control">';
$hasil = $koneksi_db->sql_query("SELECT * FROM berita_kat WHERE id_parent='0' ORDER BY kategori ASC");
$admin .= '<option value="0">None</option>';
while ($datas =  $koneksi_db->sql_fetchrow ($hasil)){
$pilihan = ($datas['kid']== $data['id_parent'])?'selected':'';
$admin .= '<option value="'.$datas['kid'].'"'.$pilihan.'>'.$datas['kategori'].'</option>';
}
$admin .= '</select></div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">Keterangan</label>
	<div class="col-sm-10"><textarea name="ket" rows="3" class="form-control">'.$data['ket'].'</textarea></div>
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

$admin .='<h4 class="page-header">Hapus kategori berita dengan ID '.$kid.'</h4>';

$hasil = $koneksi_db->sql_query( "SELECT * FROM berita_kat WHERE kid=$kid" );
$data = $koneksi_db->sql_fetchrow($hasil);
$kategori 	= $data['kategori'];
$ket   		= $data['ket'];

if (empty ($kategori)){
$admin.='<div class="alert alert-danger">Kategori dengan ID <strong>'.$kid.' Kosong</strong></div>';
$admin.='<meta http-equiv="refresh" content="3; url=?opsi=berita&amp;modul=yes&amp;aksi=list_kategori">';
}else {
$admin .= '<div class="alert alert-danger">Seluruh berita dan komentar di kategori ini akan dihapus! Anda yakin?<br />
<a class="text-info" href="?opsi=berita&amp;modul=yes&amp;aksi=hapus_kategori&amp;kid='.$kid.'&amp;konfirm=yes">Ya</a> | <a class="text-info" href="?opsi=berita&amp;mod=yes">Tidak</a></div>';
}
}

if (isset($_GET['konfirm'])=="yes") {

$hasil = $koneksi_db->sql_query("SELECT * FROM berita WHERE kid=$kid" );
while($data = mysql_fetch_array($hasil)){
	$thumb 		= 'id-content/modul/berita/images/thumb/';
    $normal 	= 'id-content/modul/berita/images/normal/';
    $namagambar =  $data['gambar'];
	$uploaddir 	= $thumb . $namagambar;
    $uploaddir 	= $normal . $namagambar;
	unlink($uploaddir);
}

$koneksi_db->sql_query("DELETE FROM berita_kat WHERE kid='$kid'");
$koneksi_db->sql_query("DELETE FROM berita WHERE kid='$kid'");
$koneksi_db->sql_query("DELETE FROM berita_komentar WHERE berita='".$data['id']."'");
$admin.='<div class="alert alert-success"><strong>Berhasil!</strong> Kategori, berita dan komentar yang ada di kategori ini telah di hapus!</div>';
$admin.='<meta http-equiv="refresh" content="3; url=?opsi=berita&amp;modul=yes&amp;aksi=list_kategori">';
}

#############################################
# Tambah Berita
#############################################
if($_GET['aksi'] =='tambah_berita'){

$admin .= '<h4 class="page-header">Tambah Berita</h4>';

if(isset($_POST['submit'])){

define("GIS_GIF", 1);
define("GIS_JPG", 2);
define("GIS_PNG", 3);
define("GIS_SWF", 4);

include "id-includes/hft_image.php";

$judul			= text_filter($_POST['judul']);
$fitur		 	= int_filter($_POST['fitur']);
$kid		 	= int_filter($_POST['kid']);
$konten 		= addslashes($_POST['konten']);
$label 			= tags_filter($_POST['label']);
$slug			= SEO($_POST['judul']);
$caption		= text_filter($_POST['caption']);
$sumber			= text_filter($_POST['sumber']);
$publikasi 		= '1';
$gambar 		= $_FILES['gambar']['name'];
$namagambar		= date('Ymd-His');

$error = '';
if (!$judul)	$error .= "Error: Judul berita belum diisi!<br />";
if (!$kid) 		$error .= "Error: Kategori berita belum dipilih!<br />";
if (!$konten)	$error .= "Error: Konten berita belum diisi!<br />";

if ($error){
$admin .= '<div class="alert alert-danger">'.$error.'</div>';
}else {
if (!empty ($gambar)){
$files     = $_FILES['gambar']['name'];
$tmp_files = $_FILES['gambar']['tmp_name'];
$simpan    = $namagambar.'.jpg';
$uploaddir = $temp . $simpan; 
$uploads   = move_uploaded_file($tmp_files, $uploaddir);
if (file_exists($uploaddir)){
@chmod($uploaddir,0644);
}
$small = $thumb . $simpan;
$large = $normal . $simpan;

create_thumbnail ($uploaddir, $small, $thumb_width, $thumb_height, $kualitas);
create_thumbnail ($uploaddir, $large, $normal_width, $normal_height, $kualitas);
}else{
$simpan = '';
}

$user		= $_SESSION['UserName'];
//$tanggal	= date('Y-m-d H:i:s');
$tanggal = ''.$_POST['thn'].'-'.$_POST['bln'].'-'.$_POST['tgl'].' '.$_POST['jm'].':'.$_POST['mnt'].':'.$_POST['dtk'].'';
$hasil = $koneksi_db->sql_query( "INSERT INTO berita (tanggal,user,judul,kid,fitur,konten,tags,publikasi,gambar,slug,caption,sumber) VALUES ('$tanggal','$user','$judul','$kid','$fitur','$konten','$label','$publikasi','$simpan','$slug','$caption','$sumber')" );
if($hasil){
$admin .= '<div class="alert alert-success"><strong>Berhasil!</strong> Berita <b>"'.$judul.'"</b> berhasil ditambah</div>';
$style_include[] ='<meta http-equiv="refresh" content="1; url=?opsi=berita&modul=yes">';
if (!empty ($gambar)){
unlink($uploaddir);
}
unset($judul);
unset($konten);
unset($label);
unset($caption);
unset($sumber);

}else{
$admin .= '<div class="alert alert-danger"><strong>Gagal!</strong> Berita gagal disimpan</div>';
if (!empty ($gambar)){
unlink($small);
unlink($large);
}
}
}
}

$judul 		= !isset($judul) 	? '' : $judul;
$konten 	= !isset($konten) 	? '' : $konten;
$label 		= !isset($label) 	? '' : $label;
$caption 	= !isset($caption) 	? '' : $caption;
$sumber 	= !isset($sumber) 	? '' : $sumber;

$tgl_explode = str_to_time(date('Y-m-d H:i:s'), '%Y-%m-%d %H:%i:%s');

$admin .= '<form class="form-horizontal" action="" method="post" enctype ="multipart/form-data">
<div class="form-group">
	<label class="col-sm-2 control-label">Judul</label>
	<div class="col-sm-10"><input type="text" name="judul" value="'.$judul.'" class="form-control" placeholder="Masukkan judul berita"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Tanggal</label>
	<div class="col-sm-10 form-inline">';

// menampilkan pilihan combobox untuk tanggal
$admin .= '<select name="tgl" class="form-control">';
for ($tgl=1; $tgl<=31; $tgl++){
if ($tgl == $tgl_explode['d']) 
$admin .= '<option value="'.$tgl.'" selected>'.$tgl.'</option>';
else 
$admin .= '<option value="'.$tgl.'">'.$tgl.'</option>';
}
$admin .= '</select> ';

// menampilkan pilihan combobox untuk bulan
$admin .= '<select name="bln" class="form-control">';
for ($bln=1; $bln<=12; $bln++){
if ($bln == $tgl_explode['m']) 
$admin .= '<option value="'.$bln.'" selected>'.$bln.'</option>';
else 
$admin .= '<option value="'.$bln.'">'.$bln.'</option>';
}
$admin .= '</select> ';

// menampilkan pilihan combobox untuk tahun
$getdate = getdate();
$stahun = $getdate['year']-3;
$atahun = $getdate['year']+3;
$admin .= '<select name="thn" class="form-control">';
for ($thn=$stahun; $thn<=$atahun; $thn++){
if ($thn == $tgl_explode['Y']) 
$admin .= '<option value="'.$thn.'" selected>'.$thn.'</option>';
else 
$admin .= '<option value="'.$thn.'">'.$thn.'</option>';
}
$admin .= '</select> ';

// menampilkan pilihan combobox untuk jam
$admin .= '<select name="jm" class="form-control">';
for ($jm=0; $jm<=23; $jm++){
if ($jm == $tgl_explode['H'])
$admin .= '<option value="'.$jm.'" selected>'.$jm.'</option>';
else 
$admin .= '<option value="'.$jm.'">'.$jm.'</option>';
}
$admin .= '</select> ';

// menampilkan pilihan combobox untuk menit
$admin .= ' <select name="mnt" class="form-control">';
for ($mnt=0; $mnt<=59; $mnt++){
if ($mnt == $tgl_explode['i'])
$admin .= '<option value="'.$mnt.'" selected>'.$mnt.'</option>';
else 
$admin .= '<option value="'.$mnt.'">'.$mnt.'</option>';
}
$admin .= '</select> ';

// menampilkan pilihan combobox untuk detik
$admin .= '<select name="dtk" class="form-control">';
for ($dtk=0; $dtk<=59; $dtk++){
if ($dtk == $tgl_explode['s']) 
$admin .= '<option value="'.$dtk.'" selected>'.$dtk.'</option>';
else 
$admin .= '<option value="'.$dtk.'">'.$dtk.'</option>';
}
$admin .= '</select>';

$admin .= '</div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Fitur</label>
	<div class="col-sm-10">
	<label><input type="radio" name="fitur" value="1"> Ya</label> 
	<label><input type="radio" name="fitur" value="0"> Tidak</label></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Kategori</label>
	<div class="col-sm-10"><select name="kid" class="form-control">';
$s = $koneksi_db->sql_query("SELECT * FROM berita_kat WHERE id_parent=0 ORDER BY kategori ASC");
$admin .= '<option value="">None</option>';
while ($data = $koneksi_db->sql_fetchrow($s)) {
$admin .= '<option value="'.$data['kid'].'">'.$data['kategori'].'</option>';

$ss = $koneksi_db->sql_query("SELECT * FROM berita_kat WHERE id_parent=".$data['kid']." ORDER BY kategori ASC");	
while ($datas = $koneksi_db->sql_fetchrow($ss)) {
$admin .= '<option value="'.$datas['kid'].'">- '.$datas['kategori'].'</option>';
}
}
$admin .= '</select></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Konten</label>
	<div class="col-sm-10"><textarea name="konten" rows="10" id="mce" class="form-control">'.$konten.'</textarea></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Tags</label>
	<div class="col-sm-10"><input type="text" name="label" value="'.$label.'" class="form-control" placeholder="Masukkan tags berita">
	<span class="help-block">pisahkan dengan tanda koma (,)</span></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Gambar</label>
	<div class="col-sm-10"><input type="file" name="gambar"><p class="help-block">Extensi file *.JPG, *.JPEG</p></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Caption</label>
	<div class="col-sm-10"><input type="text" name="caption" value="'.$caption.'" class="form-control" placeholder="Masukkan keterangan gambar"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Sumber</label>
	<div class="col-sm-10"><input type="text" name="sumber" value="'.$sumber.'" class="form-control" placeholder="Masukkan link sumber berita"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label"></label>
	<div class="col-sm-10"><input type="submit" name="submit" value="Tambah" class="btn btn-success"></div>
</div>
</form>';
}

#############################################
# Edit Berita
#############################################
if($_GET['aksi']=="edit_berita"){

$id	= int_filter($_GET['id']);

$admin .='<h4 class="page-header">Edit berita dengan ID '.$id.'</h4>';

if(isset($_POST['submit'])){

define("GIS_GIF", 1);
define("GIS_JPG", 2);
define("GIS_PNG", 3);
define("GIS_SWF", 4);

include "id-includes/hft_image.php";

$judul   		= text_filter($_POST['judul']);
$fitur		 	= int_filter($_POST['fitur']);
$kid			= int_filter($_POST['kid']);
$konten			= addslashes($_POST['konten']);
$label 			= tags_filter($_POST['label']);
$slug			= SEO($_POST['judul']);
$caption 		= text_filter($_POST['caption']);
$sumber 		= text_filter($_POST['sumber']);
$gambar 		= $_FILES['gambar']['name'];
$gambar_lama	= text_filter($_POST['gambar_lama']);
$namagambar		= date('Ymd-His');

$error = '';
if (!$judul)	$error .= "Error: Judul berita belum diisi!<br />";
if (!$kid) 		$error .= "Error: Kategori berita belum dipilih!<br />";
if (!$konten)	$error .= "Error: Konten berita belum diisi!<br />";

if ($error){
$admin .= '<div class="alert alert-danger">'.$error.'</div>';
}else {
if (!empty ($gambar)){
$files     = $_FILES['gambar']['name'];
$tmp_files = $_FILES['gambar']['tmp_name'];
$simpan    = $namagambar.'.jpg';
$uploaddir = $temp . $simpan; 
$uploads   = move_uploaded_file($tmp_files, $uploaddir);

if (file_exists($uploaddir)){
@chmod($uploaddir,0644);
}
$small = $thumb . $simpan;
$large = $normal . $simpan;

create_thumbnail ($uploaddir, $small, $thumb_width, $thumb_height, $kualitas);
create_thumbnail ($uploaddir, $large, $normal_width, $normal_height, $kualitas);

$tanggal = ''.$_POST['thn'].'-'.$_POST['bln'].'-'.$_POST['tgl'].' '.$_POST['jm'].':'.$_POST['mnt'].':'.$_POST['dtk'].'';
$hasil = $koneksi_db->sql_query( "UPDATE berita SET tanggal='$tanggal',judul='$judul',kid='$kid',fitur='$fitur',konten='$konten',tags='$label',gambar='$simpan',caption='$caption',slug='$slug',sumber='$sumber' WHERE id='$id'" );
if($hasil){
$admin .= '<div class="alert alert-success"><strong>Berhasil!</strong> Berita dengan judul <strong>"'.$judul.'"</strong> berhasil disimpan</div>';
$style_include[] ='<meta http-equiv="refresh" content="1; url=?opsi=berita&modul=yes">';
unlink($uploaddir);
$nimg = $normal . $gambar_lama;
$timg = $thumb . $gambar_lama;
if(!empty ($gambar_lama)){
unlink($nimg);
unlink($timg);
}
unset($judul);
unset($konten);
unset($label);
unset($caption);
unset($sumber);

}else{
$admin .= '<div class="alert alert-danger"><strong>Gagal!</strong> Berita gagal disimpan</div>';
$admin .= '<div class="alert alert-danger">'.mysql_error().'</div>';
unlink($small);
unlink($large);
}
}else{
$tanggal = ''.$_POST['thn'].'-'.$_POST['bln'].'-'.$_POST['tgl'].' '.$_POST['jm'].':'.$_POST['mnt'].':'.$_POST['dtk'].'';
$hasil = $koneksi_db->sql_query( "UPDATE berita SET tanggal='$tanggal',judul='$judul',kid='$kid',fitur='$fitur',konten='$konten',tags='$label',caption='$caption',slug='$slug',sumber='$sumber' WHERE id='$id'" );
if($hasil){
$admin .= '<div class="alert alert-success"><strong>Berhasil!</strong> Berita dengan judul <strong>"'.$judul.'"</strong> berhasil disimpan</div>';
$style_include[] ='<meta http-equiv="refresh" content="1; url=?opsi=berita&modul=yes">';
}else{
$admin .= '<div class="alert alert-danger"><strong>Gagal!</strong> Berita gagal disimpan</div>';
$admin .= '<div class="alert alert-danger">'.mysql_error().'</div>';
}
}
}
}

$a = $koneksi_db->sql_query( "SELECT * FROM berita WHERE id=$id" );
$data = $koneksi_db->sql_fetchrow($a);	
$judul 			= $data['judul'];
$fitur 			= $data['fitur'];
$kid			= $data['kid']; 
$konten			= $data['konten'];
$label			= $data['tags'];
$caption		= $data['caption'];
$sumber			= $data['sumber'];
$gambar_lama	= $data['gambar'];
$tgl_explode 	= str_to_time(date($data['tanggal']), '%Y-%m-%d %H:%i:%s');

$admin .='<form class="form-horizontal" method="post" action="" enctype ="multipart/form-data">
<div class="form-group">
	<label class="col-sm-2 control-label">Judul</label>
	<div class="col-sm-10"><input type="text" name="judul" value="'.$judul.'" class="form-control"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Tanggal</label>
	<div class="col-sm-10 form-inline">';

// menampilkan pilihan combobox untuk tanggal
$admin .= '<select name="tgl" class="form-control">';
for ($tgl=1; $tgl<=31; $tgl++){
if ($tgl == $tgl_explode['d']) 
$admin .= '<option value="'.$tgl.'" selected>'.$tgl.'</option>';
else 
$admin .= '<option value="'.$tgl.'">'.$tgl.'</option>';
}
$admin .= '</select> ';

// menampilkan pilihan combobox untuk bulan
$admin .= '<select name="bln" class="form-control">';
for ($bln=1; $bln<=12; $bln++){
if ($bln == $tgl_explode['m']) 
$admin .= '<option value="'.$bln.'" selected>'.$bln.'</option>';
else 
$admin .= '<option value="'.$bln.'">'.$bln.'</option>';
}
$admin .= '</select> ';

// menampilkan pilihan combobox untuk tahun
$getdate = getdate();
$stahun = $getdate['year']-3;
$atahun = $getdate['year']+3;
$admin .= '<select name="thn" class="form-control">';
for ($thn=$stahun; $thn<=$atahun; $thn++){
if ($thn == $tgl_explode['Y']) 
$admin .= '<option value="'.$thn.'" selected>'.$thn.'</option>';
else 
$admin .= '<option value="'.$thn.'">'.$thn.'</option>';
}
$admin .= '</select> ';

// menampilkan pilihan combobox untuk jam
$admin .= '<select name="jm" class="form-control">';
for ($jm=0; $jm<=23; $jm++){
if ($jm == $tgl_explode['H'])
$admin .= '<option value="'.$jm.'" selected>'.$jm.'</option>';
else 
$admin .= '<option value="'.$jm.'">'.$jm.'</option>';
}
$admin .= '</select> ';

// menampilkan pilihan combobox untuk menit
$admin .= ' <select name="mnt" class="form-control">';
for ($mnt=0; $mnt<=59; $mnt++){
if ($mnt == $tgl_explode['i'])
$admin .= '<option value="'.$mnt.'" selected>'.$mnt.'</option>';
else 
$admin .= '<option value="'.$mnt.'">'.$mnt.'</option>';
}
$admin .= '</select> ';

// menampilkan pilihan combobox untuk detik
$admin .= '<select name="dtk" class="form-control">';
for ($dtk=0; $dtk<=59; $dtk++){
if ($dtk == $tgl_explode['s']) 
$admin .= '<option value="'.$dtk.'" selected>'.$dtk.'</option>';
else 
$admin .= '<option value="'.$dtk.'">'.$dtk.'</option>';
}
$admin .= '</select>';

$admin .= '</div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Fitur</label>
	<div class="col-sm-10">';
if($fitur==1){
$admin .= '<label><input type="radio" name="fitur" value="1" checked> Ya</label> 
	<label><input type="radio" name="fitur" value="0"> Tidak</label></div>';
}else{
$admin .= '<label><input type="radio" name="fitur" value="1"> Ya</label> 
	<label><input type="radio" name="fitur" value="0" checked> Tidak</label></div>';
}
$admin .= '</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Kategori</label>
	<div class="col-sm-10"><select name="kid" class="form-control">';
$hasil = $koneksi_db->sql_query("SELECT * FROM berita_kat WHERE id_parent=0 ORDER BY kategori");
$admin .= '<option value="">None</option>';
while ($datas =  $koneksi_db->sql_fetchrow ($hasil)){
$pilihan = ($datas['kid']==$kid)? "selected":'';
$admin .='<option value="'.$datas['kid'].'" '.$pilihan.'>'.$datas['kategori'].'</option>';

$hasilss = $koneksi_db->sql_query("SELECT * FROM berita_kat WHERE id_parent=".$datas['kid']." ORDER BY kategori");
while ($datass =  $koneksi_db->sql_fetchrow ($hasilss)){
$pilihans = ($datass['kid']==$kid)?'selected':'';
$admin .= '<option value="'.$datass['kid'].'" '.$pilihans.'>- '.$datass['kategori'].'</option>';
}
}
$admin .='</select></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Konten</label>
	<div class="col-sm-10"><textarea name="konten" id="mce" class="form-control" rows="10">'.$konten.'</textarea></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Tags</label>
	<div class="col-sm-10"><input type="text" name="label" value="'.$label.'" class="form-control">
	<span class="help-block">pisahkan dengan tanda koma (,)</span></div>
</div>';
if(!$gambar_lama){
$gambar = '<img class="img-thumbnail" src="id-content/modul/berita/images/thumb_default.jpg" width="120">';
}else{
$gambar = '<img class="img-thumbnail" src="id-content/modul/berita/images/normal/'.$gambar_lama.'" width="120">';
}
$admin .='<div class="form-group">
	<label class="col-sm-2 control-label"></label>
	<div class="col-sm-10">'.$gambar.'</div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Gambar</label>
	<div class="col-sm-10"><input type="file" name="gambar"><input type="hidden" name="gambar_lama" size="53" value="'.$gambar_lama.'"><p class="help-block">Extensi file *.JPG, *.JPEG</p></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Caption</label>
	<div class="col-sm-10"><input type="text" name="caption" value="'.$caption.'" class="form-control"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Sumber</label>
	<div class="col-sm-10"><input type="text" name="sumber" value="'.$sumber.'" class="form-control"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label"></label>
	<div class="col-sm-10"><input type="submit" value="Simpan" name="submit" class="btn btn-success"></form></div>
</div>';
}

#############################################
# Hapus Berita
#############################################
if($_GET['aksi']=="hapus_berita"){

$id     = int_filter($_GET['id']);

$hasil = $koneksi_db->sql_query( "SELECT * FROM berita WHERE id=$id" );
while($data = mysql_fetch_array($hasil)){
$gambar_thumb 	= 'id-content/modul/berita/images/thumb/';
$gambar_normal 	= 'id-content/modul/berita/images/normal/';
$namagambar =  $data['gambar'];
$uploaddir = $gambar_thumb . $namagambar; 
$uploaddir = $gambar_normal . $namagambar; 
unlink($uploaddir);
}

$koneksi_db->sql_query("DELETE FROM berita WHERE id='$id'");
$koneksi_db->sql_query("DELETE FROM berita_komentar WHERE berita='$id'");
$admin.='<div class="alert alert-success"><strong>Berhasil!</strong> Berita berhasil dihapus</div>';
$style_include[] ='<meta http-equiv="refresh" content="1; url=?opsi=berita&modul=yes">';
}

#############################################
# List Komentar
#############################################
if($_GET['aksi']=="list_komentar"){

$admin .= '<form method="get" class="searchform" action="main.php">
<p><input type="text" name="query" class="textbox">
<input type="submit" name="submit" class="button" value="Search">
<input type="hidden" name="opsi" value="berita">
<input type="hidden" name="modul" value="yes">
<input type="hidden" name="aksi" value="list_komentar">
</form>';

$query = $_GET['query'];
$limit = 10;
if (empty($_GET['query']) and !isset ($_GET['query'])) {
$total = $koneksi_db->sql_query("SELECT * FROM berita_komentar WHERE balas=0 ORDER BY id DESC");
}else{
$total = $koneksi_db->sql_query("SELECT * FROM berita_komentar WHERE judul like '%$query%'or konten like '%$query%' ORDER BY id DESC");
}

$jumlah = $koneksi_db->sql_numrows( $total );
$a = new paging ($limit);

if ($jumlah<1){
$admin.='<div class="alert alert-danger">Komentar <strong>kosong</strong></div>';
}else{
if (empty($_GET['query']) and !isset ($_GET['query'])) {
$hasil = $koneksi_db->sql_query("SELECT * FROM berita_komentar WHERE balas=0 ORDER BY id DESC LIMIT $offset,$limit" );
}else{
$hasil = $koneksi_db->sql_query("SELECT * FROM berita_komentar WHERE judul like '%$query%' or konten like '%$query%' ORDER BY id DESC LIMIT $offset,$limit");
}

$admin .='<table class="table table-striped table-hover">
<thead><tr>
<th>No</th>
<th>Tanggal dan IP</th>
<th>Pengirim</th>
<th>Komentar</th>
<th>Aksi</th></thead><tbody>';
$no = 1;
while ($data = $koneksi_db->sql_fetchrow($hasil)) {
$id		= $data['id'];
$tgl	= datetimes($data['tgl']);

$admin .='<tr>
<td>'.$no.'</td>
<td>'.$tgl.'<br><span class="text-danger">IP '.$data['ip'].'</span></td>
<td>'.$data['nama'].'<br>'.$data['email'].'</td>
<td>'.$data['komentar'].'</td>
<td><a class="text-info" href="?opsi=berita&amp;modul=yes&amp;aksi=balas_komentar&amp;id='.$id.'&berita='.$data['berita'].'">Balas</a> - 
<a class="text-danger" href="?opsi=berita&amp;modul=yes&amp;aksi=hapus_komentar&amp;id='.$id.'" onclick="return confirm(\'Apakah anda ingin menghapus data ini ?\')">Hapus</a></td>
</tr>';

$hasils = $koneksi_db->sql_query( "SELECT * FROM berita_komentar WHERE balas=$id ORDER BY tgl ASC");
while ($datas = $koneksi_db->sql_fetchrow($hasils)) {
$ids = $datas['id'];

$admin .='<tr>
<td></td>
<td>'.datetimes($datas['tgl']).'<br><span class="text-danger">IP '.$datas['ip'].'</span></td>
<td>'.$datas['nama'].'<br>'.$datas['email'].'</td>
<td>'.$datas['komentar'].'</td>
<td><a class="text-info" href="?opsi=berita&amp;modul=yes&amp;aksi=edit_komentar&amp;id='.$ids.'">Edit</a> - 
<a class="text-danger" href="?opsi=berita&amp;modul=yes&amp;aksi=hapus_komentar&amp;id='.$ids.'" onclick="return confirm(\'Apakah anda ingin menghapus data ini ?\')">Hapus</a></td>
</tr>';
}

$no++;	
}
$admin .='</tbody></table>';
$admin .='</div>';
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
# Balas Komentar
#############################################
if($_GET['aksi']=="balas_komentar"){

$id = int_filter($_GET['id']);
$berita = int_filter($_GET['berita']);

$k = $koneksi_db->sql_query( "SELECT * FROM berita_komentar WHERE id=$id");
$datak = $koneksi_db->sql_fetchrow($k);
$nama = $datak['nama'];

$admin .='<h4 class="page-header">Balas komentar : '.$nama.'</h4>';

if (isset($_POST['submit'])) {

$u = $koneksi_db->sql_query( "SELECT * FROM users WHERE user='$username'");
$datau = $koneksi_db->sql_fetchrow($u);
$nama 		= $datau['nama'];
$email 		= $datau['email'];
$website 	= $datau['web'];

$komentar	= addslashes($_POST['komentar']);
$tgl 		= date('Y-m-d H:i:s');
$ip      	= getenv("REMOTE_ADDR");

$error = '';
if (!$komentar)	$error .= "<strong>Gagal!</strong> Komentar belum diisi!<br />";

if ($error){
$admin.='<div class="alert alert-danger">'.$error.'</div>';
}else{
$hasil = $koneksi_db->sql_query("INSERT INTO berita_komentar (berita,balas,ip,tgl,nama,email,website,komentar) VALUES ('$berita','$id','$ip','$tgl','$nama','$email','$website','$komentar')");
if($hasil){
$admin .='<div class="alert alert-success"><strong>Berhasil!</strong> Komentar telah dibalas</div>';
$admin .='<meta http-equiv="refresh" content="1; url=?opsi=berita&amp;modul=yes&amp;aksi=list_komentar">';
}else{
$admin .= '<div class="alert alert-danger">'.mysql_error().'</div>';
}
}
}

$hasil = $koneksi_db->sql_query( "SELECT * FROM berita_komentar WHERE id=$id" );
$data = $koneksi_db->sql_fetchrow($hasil);

$admin .='<form method="post" action="">
<table class="table table-striped table-hover">
<tr>
    <td class="text-right"><strong>Tanggal</strong></td>
	<td>'.datetimes($data['tgl']).'</td>
</tr>
<tr>
    <td class="text-right"><strong>Email</strong></td>
	<td>'.$data['email'].'</td>
</tr>
<tr>
    <td class="text-right"><strong>Komentar</strong></td>
	<td>'.$data['komentar'].'</td>
</tr>
<tr>
    <td class="text-right"><strong>Balas</strong></td>
	<td><textarea name="komentar" rows="3" class="form-control" placeholder="Balas komentar"></textarea></td>
</tr>
<tr>
	<td></td>
    <td><input type="submit" name="submit" value="Balas" class="btn btn-success"></td>
</tr>
</table>
</form>';
}

#############################################
# Hapus Komentar
#############################################
if($_GET['aksi']=="hapus_komentar"){

$id = int_filter($_GET['id']);

$koneksi_db->sql_query("DELETE FROM berita_komentar WHERE id='$id'");
$admin.='<div class="alert alert-success"><strong>Berhasil!</strong> Komentar berhasil dihapus</div>';
$style_include[] ='<meta http-equiv="refresh" content="1; url=?opsi=berita&modul=yes&aksi=list_komentar">';
}

#############################################
# Publikasi Berita
#############################################
if($_GET['aksi']=="pubnews"){
global $koneksi_db;
$id     = int_filter($_GET['id']);
$topik 	= int_filter($_GET['topik']);
$koneksi_db->sql_query("update artikel set publikasi='1' WHERE id='$id'");
$admin.='<div class="alert alert-danger">Artikel telah di Publish</div>';
$admin.='<meta http-equiv="refresh" content="3; url=?opsi=berita&amp;modul=yes">';
}

#############################################
# Thumbnail
#############################################
if($_GET['aksi']=="thumbnail"){

if (isset($_POST['submit'])){

define("GIS_GIF", 1);
define("GIS_JPG", 2);
define("GIS_PNG", 3);
define("GIS_SWF", 4);

include "id-includes/hft_image.php";

$namafile_name = $_FILES['gambar']['name'];
if (!empty ($namafile_name)){

$files = $_FILES['gambar']['name'];
$tmp_files = $_FILES['gambar']['tmp_name'];
$nama_thumb = 'thumb_default.jpg';
$nama_normal = 'normal_default.jpg';
$uploaddir = $temp . $nama_normal;
$uploads = move_uploaded_file($tmp_files, $uploaddir);
if (file_exists($uploaddir)){
@chmod($uploaddir,0644);
}

$folder = 'id-content/modul/berita/images/';
$small = $folder . $nama_thumb;
$large = $folder . $nama_normal;

create_thumbnail ($uploaddir, $small, $thumb_width, $thumb_height, $kualitas);
create_thumbnail ($uploaddir, $large, $normal_width, $normal_height, $kualitas);

unlink($uploaddir);
$admin.='<div class="alert alert-success"><strong>Berhasil!</strong> Gambar default berhasil disimpan</div>';
$style_include[] ='<meta http-equiv="refresh" content="1; url=?opsi=berita&modul=yes&aksi=thumbnail">';
}
}

$admin .='<form class="form-horizontal" method="post" action="" enctype ="multipart/form-data" id="posts">';
$gambarlama = 'thumb_default.jpg';
$admin .='<div class="form-group">
	<label class="col-sm-2 control-label"></label>
	<div class="col-sm-10"><img class="img-thumbnail" src="id-content/modul/berita/images/'.$gambarlama.'" width="150"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Gambar Default</label>
	<div class="col-sm-10"><input type="file" name="gambar"><p class="help-block">Extensi file *.JPG, *.JPEG</p></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label"></label>
	<div class="col-sm-10"><input type="submit" value="Upload" name="submit" class="btn btn-success"></div>
</div></form>';
}

#############################################
# Pengaturan Berita
#############################################
if($_GET['aksi']=="pengaturan"){

$admin .='<h4 class="page-header">Pengaturan Berita</h4>';

if (isset($_POST['submit'])) {

$thumb_width  	= int_filter($_POST['thumb_width']);
$thumb_height  	= int_filter($_POST['thumb_height']);
$normal_width	= int_filter($_POST['normal_width']);
$normal_height	= int_filter($_POST['normal_height']);
$kualitas		= int_filter($_POST['kualitas']);
$pberita   		= int_filter($_POST['pberita']);
$pkomentar		= int_filter($_POST['pkomentar']);

$error = '';
if (!$thumb_width)	$error .= "<strong>Gagal!</strong> Lebar gambar thumbnail tidak boleh kosong!<br />";
if (!$thumb_height)	$error .= "<strong>Gagal!</strong> Tinggi gambar thumbnail tidak boleh kosong!<br />";
if (!$normal_width)	$error .= "<strong>Gagal!</strong> Lebar gambar normal tidak boleh kosong!<br />";
if (!$normal_height)	$error .= "<strong>Gagal!</strong> Tinggi gambar normal tidak boleh kosong!<br />";
if (!$kualitas)		$error .= "<strong>Gagal!</strong> Kualitas gambar tidak boleh kosong!<br />";

if ($error){
$admin.='<div class="alert alert-danger">'.$error.'</div>';
}else{
$hasil = $koneksi_db->sql_query("UPDATE berita_setting SET thumb_width='$thumb_width',thumb_height='$thumb_height',normal_width='$normal_width',normal_height='$normal_height',kualitas='$kualitas',pberita='$pberita',pkomentar='$pkomentar' WHERE id=1");
$admin.='<div class="alert alert-success"><strong>Berhasil!</strong> Pengaturan berita telah disimpan</div>';
$admin.='<meta http-equiv="refresh" content="1; url=?opsi=berita&amp;modul=yes&amp;aksi=pengaturan">';
}
}

$hasil = $koneksi_db->sql_query( "SELECT * FROM berita_setting WHERE id=1" );
$data = $koneksi_db->sql_fetchrow($hasil);
$thumb_width  	= $data['thumb_width'];
$thumb_height	= $data['thumb_height'];
$normal_width  	= $data['normal_width'];
$normal_height	= $data['normal_height'];
$kualitas		= $data['kualitas'];
$pberita   		= $data['pberita'];
$pkomentar		= $data['pkomentar'];

$admin .='<form class="form-horizontal" method="post" action="">
<div class="form-group">
    <label class="col-sm-3 control-label" class="col-md-3">Thumbnail - Lebar Maksimal</label>
	<div class="col-sm-9 form-inline"><input type="text" name="thumb_width" value="'.$thumb_width.'" size="2" class="form-control"> px</div>
</div>
<div class="form-group">
    <label class="col-sm-3 control-label" class="col-md-3">Thumbnail - Tinggi Maksimal</label>
	<div class="col-sm-9 form-inline"><input type="text" name="thumb_height" value="'.$thumb_height.'" size="2" class="form-control"> px</div>
</div>
<div class="form-group">
    <label class="col-sm-3 control-label">Normal - Lebar Maksimal</label>
	<div class="col-sm-9 form-inline"><input type="text" name="normal_width" value="'.$normal_width.'" size="2" class="form-control"> px</div>
</div>
<div class="form-group">
    <label class="col-sm-3 control-label">Normal - Tinggi Maksimal</label>
	<div class="col-sm-9 form-inline"><input type="text" name="normal_height" value="'.$normal_height.'" size="2" class="form-control"> px</div>
</div>
<div class="form-group">
    <label class="col-sm-3 control-label">Kualitas gambar</label>
	<div class="col-sm-9 form-inline"><input type="range" name="kualitas" value="'.$kualitas.'" size="2" min="80" max="100" step="5" class="form-control"><p class="help-block">Range 80 - 100</p></div>
</div>
<div class="form-group">
    <label class="col-sm-3 control-label">Auto publish berita user</label>
	<div class="col-sm-9">';
if($pberita==1){
$admin .='
<label><input type="radio" name="pberita" value="1" checked> Ya</label> 
<label><input type="radio" name="pberita" value="0"> Tidak</label>';
}else{
$admin .='
<label><input type="radio" name="pberita" value="1"> Ya</label> 
<label><input type="radio" name="pberita" value="0" checked> Tidak</label>';
}
$admin .='</div>
</div>
<div class="form-group">
    <label class="col-sm-3 control-label">Auto publish komentar berita</label>
	<div class="col-sm-9">';
if($pkomentar==1){
$admin .='
	<label><input type="radio" name="pkomentar" value="1" checked> Ya</label> 
	<label><input type="radio" name="pkomentar" value="0"> Tidak</label>';
}else{
$admin .='
	<label><input type="radio" name="pkomentar" value="1"> Ya</label> 
	<label><input type="radio" name="pkomentar" value="0" checked> Tidak</label>';
}
$admin .='</div>
</div>
<div class="form-group">
	<label class="col-sm-3 control-label"></label>
    <div class="col-sm-9"><input type="submit" name="submit" value="Simpan" class="btn btn-success"></div>
</div></form>';
}

#############################################
# Hapus Gambar
#############################################
if($_GET['aksi']=="hapus_gambar"){

$id     = int_filter($_GET['id']);

$hasil = $koneksi_db->sql_query( "SELECT * FROM berita WHERE id=$id" );
while($data = $koneksi_db->sql_fetchrow($hasil)){
$tthumb 	= 'id-content/modul/berita/images/thumb/';
$tnormal 	= 'id-content/modul/berita/images/normal/';
$namagambar = $data['gambar'];
$uploaddirt = $tthumb . $namagambar; 
$uploaddirn = $tnormal . $namagambar; 
unlink($uploaddirt);
unlink($uploaddirn);
}
$koneksi_db->sql_query("update berita set gambar='' WHERE id='$id'");
$admin.='<div class="alert alert-success"><strong>Berhasil!</strong> Gambar "<strong>'.$data['judul'].'</strong>" telah dihapus</div>';
$admin.='<meta http-equiv="refresh" content="1; url=?opsi=berita&amp;modul=yes">';
}

##########################
# Publikasi Berita
##########################
if ($_GET['aksi'] == 'pub'){
if ($_GET['pub'] == 'tidak'){
	$id = int_filter ($_GET['id']);
	$koneksi_db->sql_query ("UPDATE berita SET publikasi=0 WHERE id='$id'");
	header ("location:?opsi=berita&modul=yes&aksi=list_berita_pending");
}	
	
if ($_GET['pub'] == 'ya'){
	$id = int_filter ($_GET['id']);
	$koneksi_db->sql_query ("UPDATE berita SET publikasi=1 WHERE id='$id'");
	header ("location:?opsi=berita&modul=yes");
}	
}

echo $admin;

?>