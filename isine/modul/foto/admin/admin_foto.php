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

$script_include[] = '
<script type="text/javascript" src="id-includes/js/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
tinymce.init({
    selector: "textarea#mce",
    theme: "modern",
 });
</script>';

$username = $_SESSION['UserName'];
$temp 	= 'id-content/modul/foto/images/temp/';
$thumb 	= 'id-content/modul/foto/images/thumb/';
$normal = 'id-content/modul/foto/images/normal/';

$seting = $koneksi_db->sql_query( "SELECT * FROM foto_setting WHERE id=1");
$datag = $koneksi_db->sql_fetchrow($seting);
$thumb_width 	= int_filter($datag['thumb_width']);
$thumb_height 	= int_filter($datag['thumb_height']);
$normal_width	= int_filter($datag['normal_width']);
$normal_height	= int_filter($datag['normal_height']);
$kualitas 		= int_filter($datag['kualitas']);

if (isset ($_GET['pg'])) $pg = int_filter ($_GET['pg']); else $pg = 0;
if (isset ($_GET['stg'])) $stg = int_filter ($_GET['stg']); else $stg = 0;
if (isset ($_GET['offset'])) $offset = int_filter ($_GET['offset']); else $offset = 0;

$total = $koneksi_db->sql_query("SELECT * FROM foto");	
$jumlah = $koneksi_db->sql_numrows($total);

$admin  = '<h3 class="page-header">Foto Manager</h3>';

$admin .= '<ol class="breadcrumb">
<li><a href="?opsi=foto&amp;modul=yes">Data Foto</a> <span class="badge">'.$jumlah.'</span></li>
<li><a href="?opsi=foto&amp;modul=yes&amp;aksi=tambah_foto">Tambah Foto</a></li>
<li><a href="?opsi=foto&amp;modul=yes&amp;aksi=list_kategori">Kategori</a></li>
<li><a href="?opsi=foto&amp;modul=yes&aksi=editthumb">Gambar Default</a></li>
<li><a href="?opsi=foto&amp;modul=yes&amp;aksi=pengaturan">Pengaturan</a></li>
</ol>';

#############################################
# List Foto
#############################################
if($_GET['aksi']==""){

$admin .= '<form method="get" action="main.php"><div class="input-group">
<input type="text" name="cari" class="form-control" placeholder="Cari Data Foto">
<span class="input-group-btn">
	<input type="hidden" name="opsi" value="foto">
	<input type="hidden" name="modul" value="yes">
	<button class="btn btn-default" type="submit" name="submit" value="Search">Go!</button>
</span>
</div></form><br>';

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
$hasil = $koneksi_db->sql_query( "SELECT * FROM foto WHERE id in($pcheck)" );
while($data = mysql_fetch_array($hasil)){
    $tempnews 	= 'id-content/modul/foto/images/normal/';
    $namagambar =  $data['gambar'];
    $uploaddir = $tempnews . $namagambar; 
	unlink($uploaddir);
}
$koneksi_db->sql_query("DELETE FROM foto WHERE id in($pcheck)");
if ($sukses){
$admin.='<div class="alert alert-success">'.$sukses.'</div>';
}
}

$cari 		= $_GET['cari'];
$kategori 	= $_GET['kategori'];

$limit = 20;

if($cari){
$total = $koneksi_db->sql_query( "SELECT * FROM foto WHERE publikasi=1 and nama like '%$cari%' or keterangan like '%$cari%' ORDER BY tanggal DESC");
}elseif ($kategori) {
$total = $koneksi_db->sql_query( "SELECT * FROM foto WHERE publikasi=1 and kid=".$kategori." ORDER BY tanggal DESC");
}else{
$total = $koneksi_db->sql_query( "SELECT * FROM foto WHERE publikasi=1 ORDER BY tanggal DESC");
}
$jumlah = $koneksi_db->sql_numrows( $total );

if (!isset ($_GET['offset'])) {
	$offset = 0;
}

$a = new paging ($limit);
if ($jumlah<1){
$admin.='<div class="alert alert-danger">Data foto <strong>kosong</strong></div>';
}else{
if($cari){
$hasil = $koneksi_db->sql_query( "SELECT * FROM foto WHERE publikasi=1 and nama like '%$cari%' or keterangan like '%$cari%' ORDER BY tanggal DESC LIMIT $offset,$limit ");
}elseif ($kategori) {
$hasil = $koneksi_db->sql_query( "SELECT * FROM foto WHERE publikasi=1 and kid=".$kategori." ORDER BY tanggal DESC LIMIT $offset,$limit ");
}else{
$hasil = $koneksi_db->sql_query( "SELECT * FROM foto WHERE publikasi=1 ORDER BY tanggal DESC LIMIT $offset,$limit");
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
<th>Nama</th>
<th>Author</th>
<th>Kategori</th>
<th>Tanggal</th>
<th>Aksi</th>
</tr></thead><tbody>';
while ($data = $koneksi_db->sql_fetchrow($hasil)) {
$tanggal	= datetimes($data['tanggal']);
$kid		= $data['kid'];
$gambar 	='<img class="img-thumbnail" src="id-content/modul/foto/images/thumb/'.$data['gambar'].'" width="50" height="50">';

$hasil2 = $koneksi_db->sql_query( "SELECT * FROM foto_kat WHERE kid=$kid");
$data2 = $koneksi_db->sql_fetchrow($hasil2);
$kategori = $data2['kategori'];

$admin .='<tr>
<td>'.$no.'</td>
<td><input type=checkbox name=check'.$no.' value="'.$data['id'].'"></td>
<td>'.$gambar.'</td>
<td><a class="text-info" href="?opsi=foto&amp;modul=yes&aksi=edit_foto&id='.$data['id'].'">'.$data['nama'].'</a></td>
<td>'.$data['user'].'</td>
<td><a class="text-info" href="?opsi=foto&amp;modul=yes&amp;kategori='.$kid.'">'.$kategori.'</a></td>
<td><span class="text-muted"><small>'.$tanggal.'</small></span></td>
<td><a class="text-info" href="?opsi=foto&amp;modul=yes&amp;aksi=edit_foto&amp;id='.$data['id'].'">Edit</a> - 
<a class="text-danger" href="?opsi=foto&amp;modul=yes&amp;aksi=hapus_foto&amp;id='.$data['id'].'" onclick="return confirm(\'Apakah Anda Ingin Menghapus Data Ini ?\')">Hapus</a></td>
</tr>';		
$no++;
}
$admin .='<tr><td colspan="8"><input type="hidden" name="tot" value="'.$jumlah.'">
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

$admin .= '<div class="border"><center>';
$admin .= $a-> getPaging($jumlah, $pg, $stg);
$admin .= '</center></div>';
}
}

#############################################
# Tambah Foto
#############################################
if($_GET['aksi'] =='tambah_foto'){

if(isset($_POST['submit'])){

define("GIS_GIF", 1);
define("GIS_JPG", 2);
define("GIS_PNG", 3);
define("GIS_SWF", 4);

include "id-includes/hft_image.php";

$kid	 		= text_filter($_POST['kid']);
$image_name1	= $_FILES['image1']['name'];
$image_name2	= $_FILES['image2']['name'];
$image_name3	= $_FILES['image3']['name'];
$image_name4	= $_FILES['image4']['name'];
$image_name5	= $_FILES['image5']['name'];

if (!empty ($image_name1)){
$files     = $_FILES['image1']['name'];
$tmp_files = $_FILES['image1']['tmp_name'];
// cari id transaksi terakhir yang berawalan tanggal hari ini
$query = $koneksi_db->sql_query("SELECT max(id) AS last FROM foto");
$data  = $koneksi_db->sql_fetchrow($$query);
$newID = $data['last']+1;
$nm_tgl = date('Ymd-His');

$simpan    = ''.$newID.'-'.$nm_tgl.'.jpg';
$uploaddir = $temp . $simpan;
$uploads   = move_uploaded_file($tmp_files, $uploaddir);
if (file_exists($uploaddir)){
@chmod($uploaddir,0644);
}
$small = $thumb . $simpan;
$large = $normal . $simpan;
create_thumbnail ($uploaddir, $small, $thumb_width, $thumb_height, $kualitas);
create_thumbnail ($uploaddir, $large, $normal_width, $normal_height, $kualitas);
$tanggal = date('Y-m-d H:i:s');
$hasil = $koneksi_db->sql_query( "INSERT INTO foto (tanggal,user,publikasi,gambar,kid) VALUES ('$tanggal','$username','1','$simpan','$kid')" );
$admin .= '<div class="alert alert-success"><strong>Berhasil!</strong> Foto 1 telah diupload</div>';
$style_include[] ='<meta http-equiv="refresh" content="1; url=?opsi=foto&modul=yes">';
unlink($uploaddir);
}

if (!empty ($image_name2)){
$files     = $_FILES['image2']['name'];
$tmp_files = $_FILES['image2']['tmp_name'];
// cari id transaksi terakhir yang berawalan tanggal hari ini
$query = $koneksi_db->sql_query("SELECT max(id) AS last FROM foto");
$data  = $koneksi_db->sql_fetchrow($$query);
$newID = $data['last']+1;
$nm_tgl = date('Ymd-His');

$simpan    = ''.$newID.'-'.$nm_tgl.'.jpg';
$uploaddir = $temp . $simpan; 
$uploads   = move_uploaded_file($tmp_files, $uploaddir);
if (file_exists($uploaddir)){
@chmod($uploaddir,0644);
}
$small = $thumb . $simpan;
$large = $normal . $simpan;
create_thumbnail ($uploaddir, $small, $thumb_width, $thumb_height, $kualitas);
create_thumbnail ($uploaddir, $large, $normal_width, $normal_height, $kualitas);
$tanggal = date('Y-m-d H:i:s');
$hasil = $koneksi_db->sql_query( "INSERT INTO foto (tanggal,user,publikasi,gambar,kid) VALUES ('$tanggal','$username','1','$simpan','$kid')" );
$admin .= '<div class="alert alert-success"><strong>Berhasil!</strong> Foto 2 telah diupload</div>';
$style_include[] ='<meta http-equiv="refresh" content="1; url=?opsi=foto&modul=yes">';
unlink($uploaddir);
}

if (!empty ($image_name3)){
$files     = $_FILES['image3']['name'];
$tmp_files = $_FILES['image3']['tmp_name'];
// cari id transaksi terakhir yang berawalan tanggal hari ini
$query = $koneksi_db->sql_query("SELECT max(id) AS last FROM foto");
$data  = $koneksi_db->sql_fetchrow($$query);
$newID = $data['last']+1;
$nm_tgl = date('Ymd-His');

$simpan    = ''.$newID.'-'.$nm_tgl.'.jpg';
$uploaddir = $temp . $simpan; 
$uploads   = move_uploaded_file($tmp_files, $uploaddir);
if (file_exists($uploaddir)){
@chmod($uploaddir,0644);
}
$small = $thumb . $simpan;
$large = $normal . $simpan;
create_thumbnail ($uploaddir, $small, $thumb_width, $thumb_height, $kualitas);
create_thumbnail ($uploaddir, $large, $normal_width, $normal_height, $kualitas);
$tanggal = date('Y-m-d H:i:s');
$hasil = $koneksi_db->sql_query( "INSERT INTO foto (tanggal,user,publikasi,gambar,kid) VALUES ('$tanggal','$username','1','$simpan','$kid')" );
$admin .= '<div class="alert alert-success"><strong>Berhasil!</strong> Foto 3 telah diupload</div>';
$style_include[] ='<meta http-equiv="refresh" content="1; url=?opsi=foto&modul=yes">';
unlink($uploaddir);
}

if (!empty ($image_name4)){
$files     = $_FILES['image4']['name'];
$tmp_files = $_FILES['image4']['tmp_name'];
// cari id transaksi terakhir yang berawalan tanggal hari ini
$query = $koneksi_db->sql_query("SELECT max(id) AS last FROM foto");
$data  = $koneksi_db->sql_fetchrow($$query);
$newID = $data['last']+1;
$nm_tgl = date('Ymd-His');

$simpan    = ''.$newID.'-'.$nm_tgl.'.jpg';
$uploaddir = $temp . $simpan; 
$uploads   = move_uploaded_file($tmp_files, $uploaddir);
if (file_exists($uploaddir)){
@chmod($uploaddir,0644);
}
$small = $thumb . $simpan;
$large = $normal . $simpan;
create_thumbnail ($uploaddir, $small, $thumb_width, $thumb_height, $kualitas);
create_thumbnail ($uploaddir, $large, $normal_width, $normal_height, $kualitas);
$tanggal = date('Y-m-d H:i:s');
$hasil = $koneksi_db->sql_query( "INSERT INTO foto (tanggal,user,publikasi,gambar,kid) VALUES ('$tanggal','$username','1','$simpan','$kid')" );
$admin .= '<div class="alert alert-success"><strong>Berhasil!</strong> Foto 4 telah diupload</div>';
$style_include[] ='<meta http-equiv="refresh" content="1; url=?opsi=foto&modul=yes">';
unlink($uploaddir);
}

if (!empty ($image_name5)){
$files     = $_FILES['image5']['name'];
$tmp_files = $_FILES['image5']['tmp_name'];
// cari id transaksi terakhir yang berawalan tanggal hari ini
$query = $koneksi_db->sql_query("SELECT max(id) AS last FROM foto");
$data  = $koneksi_db->sql_fetchrow($$query);
$newID = $data['last']+1;
$nm_tgl = date('Ymd-His');

$simpan    = ''.$newID.'-'.$nm_tgl.'.jpg';
$uploaddir = $temp . $simpan; 
$uploads   = move_uploaded_file($tmp_files, $uploaddir);
if (file_exists($uploaddir)){
@chmod($uploaddir,0644);
}
$small = $thumb . $simpan;
$large = $normal . $simpan;
create_thumbnail ($uploaddir, $small, $thumb_width, $thumb_height, $kualitas);
create_thumbnail ($uploaddir, $large, $normal_width, $normal_height, $kualitas);
$tanggal = date('Y-m-d H:i:s');
$hasil = $koneksi_db->sql_query( "INSERT INTO foto (tanggal,user,publikasi,gambar,kid) VALUES ('$tanggal','$username','1','$simpan','$kid')" );
$admin .= '<div class="alert alert-success"><strong>Berhasil!</strong> Foto 5 telah diupload</div>';
$style_include[] ='<meta http-equiv="refresh" content="1; url=?opsi=foto&modul=yes">';
unlink($uploaddir);
}
}

$admin .= '<form class="form-horizontal" action="" method="post" enctype ="multipart/form-data">
<div class="form-group">
	<label class="col-sm-2 control-label">Kategori</label>
	<div class="col-sm-10"><select name="kid" class="form-control">';
$s = $koneksi_db->sql_query("SELECT * FROM foto_kat ORDER BY kategori ASC");	
while($data = $koneksi_db->sql_fetchrow($s)){
$admin .= '<option value="'.$data['kid'].'">'.$data['kategori'].'</option>';
}
$admin .= '</select></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Foto 1</label>
	<div class="col-sm-10"><input type="file" name="image1"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Foto 2</label>
	<div class="col-sm-10"><input type="file" name="image2"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Foto 3</label>
	<div class="col-sm-10"><input type="file" name="image3"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Foto 4</label>
	<div class="col-sm-10"><input type="file" name="image4"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Foto 5</label>
	<div class="col-sm-10"><input type="file" name="image5"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label"></label>
	<div class="col-sm-10"><input type="submit" name="submit" value="Upload" class="btn btn-success"></div>
</div>
</form>';
}

#############################################
# Edit Foto
#############################################
if($_GET['aksi'] =='edit_foto'){

$id	= int_filter($_GET['id']);

$admin .= '<h4 class="page-header">Edit Foto</h4>';

if(isset($_POST['submit'])){

define("GIS_GIF", 1);
define("GIS_JPG", 2);
define("GIS_PNG", 3);
define("GIS_SWF", 4);

include "id-includes/hft_image.php";

$nama   		= text_filter($_POST['nama']);
$kid			= int_filter($_POST['kid']);
$keterangan		= addslashes($_POST['keterangan']);
$label 			= tags_filter($_POST['label']);
$slug			= SEO($_POST['nama']);
$gambar 		= $_FILES['gambar']['name'];
$gambar_lama	= text_filter($_POST['gambar_lama']);
$namagambar		= date('Ymd-His');

$error = '';
if (!$nama)	$error .= "Error: Nama foto belum diisi!<br />";
if (!$kid)	$error .= "Error: Kategori foto belum dipilih!<br />";

if ($error){
$admin .= '<div class="alert alert-danger">'.$error.'</div>';
}else {
if (!empty ($gambar)){
$files     = $_FILES['gambar']['name'];
$tmp_files = $_FILES['gambar']['tmp_name'];
$simpan    = ''.$id.'-'.$namagambar.'.jpg';
$uploaddir = $temp . $simpan; 
$uploads   = move_uploaded_file($tmp_files, $uploaddir);

if (file_exists($uploaddir)){
@chmod($uploaddir,0644);
}
$small = $thumb . $simpan;
$large = $normal . $simpan;

create_thumbnail ($uploaddir, $small, $thumb_width, $thumb_height, $kualitas);
create_thumbnail ($uploaddir, $large, $normal_width, $normal_height, $kualitas);

$hasil = $koneksi_db->sql_query("UPDATE foto SET nama='$nama',kid='$kid',keterangan='$keterangan',slug='$slug',gambar='$simpan' WHERE id='$id'");
if($hasil){
$admin .= '<div class="alert alert-success"><strong>Berhasil!</strong> Foto dengan nama <strong>"'.$nama.'"</strong> berhasil disimpan</div>';
$style_include[] ='<meta http-equiv="refresh" content="1; url=?opsi=foto&modul=yes">';
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
$admin .= '<div class="alert alert-danger"><strong>Gagal!</strong> Foto gagal disimpan</div>';
$admin .= '<div class="alert alert-danger">'.mysql_error().'</div>';
unlink($small);
unlink($large);
}
}else{
$hasil = $koneksi_db->sql_query("UPDATE foto SET nama='$nama',kid='$kid',keterangan='$keterangan',slug='$slug' WHERE id='$id'");
if($hasil){
$admin .= '<div class="alert alert-success"><strong>Berhasil!</strong> Foto dengan nama <strong>"'.$nama.'"</strong> berhasil disimpan</div>';
$style_include[] ='<meta http-equiv="refresh" content="1; url=?opsi=foto&modul=yes">';
}else{
$admin .= '<div class="alert alert-danger"><strong>Gagal!</strong> Foto gagal disimpan</div>';
$admin .= '<div class="alert alert-danger">'.mysql_error().'</div>';
}
}
}
}

$a = $koneksi_db->sql_query( "SELECT * FROM foto WHERE id=$id" );
$data = $koneksi_db->sql_fetchrow($a);	
$nama 			= $data['nama'];
$kid			= $data['kid']; 
$keterangan		= $data['keterangan'];
$gambar_lama	= $data['gambar'];

$admin .='<form class="form-horizontal" method="post" action="" enctype ="multipart/form-data">
<div class="form-group">
	<label class="col-sm-2 control-label">Nama</label>
	<div class="col-sm-10"><input type="text" name="nama" value="'.$nama.'" class="form-control"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Kategori</label>
	<div class="col-sm-10"><select name="kid" class="form-control">';
$hasil = $koneksi_db->sql_query("SELECT * FROM foto_kat ORDER BY kategori");
$admin .= '<option value="">None</option>';
while ($datas =  $koneksi_db->sql_fetchrow ($hasil)){
$pilihan = ($datas['kid']==$kid)? "selected":'';
$admin .='<option value="'.$datas['kid'].'" '.$pilihan.'>'.$datas['kategori'].'</option>';
}
$admin .='</select></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Keterangan</label>
	<div class="col-sm-10"><textarea name="keterangan" id="mce" class="form-control" rows="5">'.$keterangan.'</textarea></div>
</div>';
if(!$gambar_lama){
$gambar = '<img class="img-thumbnail" src="id-content/modul/foto/images/foto-default.jpg" width="120">';
}else{
$gambar = '<img class="img-thumbnail" src="id-content/modul/foto/images/thumb/'.$gambar_lama.'" width="120">';
}
$admin .='<div class="form-group">
	<label class="col-sm-2 control-label"></label>
	<div class="col-sm-10">'.$gambar.'</div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Gambar</label>
	<div class="col-sm-10"><input type="file" name="gambar"><input type="hidden" name="gambar_lama" size="53" value="'.$gambar_lama.'"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label"></label>
	<div class="col-sm-10"><input type="submit" value="Simpan" name="submit" class="btn btn-success"></form></div>
</div>';
}

#############################################
# List Kategori
#############################################
if($_GET['aksi'] =='list_kategori'){

$admin .= '<div class="row">';
$admin .= '<div class="col-md-4">';
if(isset($_POST['submit'])){
$kategori   = text_filter($_POST['kategori']);
$keterangan = text_filter($_POST['keterangan']);
$slug		= SEO($_POST['kategori']);

$total 	= $koneksi_db->sql_query( "SELECT * FROM foto_kat WHERE kategori='".$_POST['kategori']."'");
$jumlah = $koneksi_db->sql_numrows($total);
$error = '';
if ($jumlah) 		$error .= "<strong>Gagal!</strong> Nama kategori <strong>$judul</strong> sudah ada di database!<br />";
if (!$kategori)   	$error .= "Error: Kategori tidak boleh kosong!<br />";
if (!$keterangan)	$error .= "Error: Keterangan tidak boleh kosong!<br />";

if ($error){
$admin .= '<div class="alert alert-danger">'.$error.'</div>';
}else {
$hasil = $koneksi_db->sql_query( "INSERT INTO foto_kat (kategori,keterangan,slug) VALUES ('$kategori','$keterangan','$slug')" );
if($hasil){
$admin .= '<div class="alert alert-success">Kategori : '.$kategori.' Berhasil dimasukkan ke database</div>';
$style_include[] ='<meta http-equiv="refresh" content="1; url=?opsi=foto&modul=yes&aksi=list_kategori">';
}else{
$admin .= '<div class="alert alert-danger">Kategori Gagal dimasukkan ke database</div>';
}
}
}

$kategori 	= !isset($kategori) ? '' : $kategori;
$keterangan = !isset($keterangan) ? '' : $keterangan;

$admin .= '<form action="" method="post">
<div class="form-group">
	<label control-label">Kategori</label>
	<input type="text" name="kategori" value="'.$kategori.'" class="form-control" placeholder="Masukkan nama kategori">
</div>
<div class="form-group">
	<label control-label">Keterangan</label>
	<textarea name="keterangan" rows="3" class="form-control" placeholder="Masukkan keterangan kategori">'.htmlentities($keterangan).'</textarea>
</div>
<div class="form-group">
	<input type="submit" name="submit" value="Tambah" class="btn btn-success">
</div>
</form>';
$admin .= '</div>';

$admin .= '<div class="col-md-8">';
$admin .= '<table class="table table-striped">
<thead><tr>
<th>No</th>
<th>Kategori</th>
<th>Aksi</th>
</tr></thead><tbody>';
$no = 1;
$s = $koneksi_db->sql_query("SELECT * FROM foto_kat ORDER BY kategori ASC");	
while($data = $koneksi_db->sql_fetchrow($s)){
$kid 		= $data['kid'];
$kategori 	= $data['kategori'];

$s2 = $koneksi_db->sql_query("SELECT * FROM foto WHERE kid='$kid'");	
$jumlah = $koneksi_db->sql_numrows($s2);

$admin .= '<tr>
<td>'.$no.'</td>
<td><a class="text-info" href="?opsi=foto&amp;modul=yes&amp;kategori='.$kid.'">'.$kategori.'</a> <span class="badge">'.$jumlah.'</span></td>
<td><a class="text-info" href="?opsi=foto&amp;modul=yes&amp;aksi=edit_kategori&amp;kid='.$kid.'">Edit</a> - 
<a class="text-danger" href="?opsi=foto&amp;modul=yes&amp;aksi=hapus_kategori&amp;kid='.$kid.'" onclick="return confirm(\'Apakah Anda Ingin Menghapus Data Ini ?\')">Delete</a></td>
</tr>';
$no++;
}
$admin .='</tbody></table>';
$admin .= '</div>';
$admin .= '</div>';
}

#############################################
# Edit Kategori
#############################################
if($_GET['aksi'] =='edit_kategori'){

$kid     = int_filter($_GET['kid']);

if(isset($_POST['submit'])){
$kategori   = text_filter($_POST['kategori']);
$keterangan = text_filter($_POST['keterangan']);
$slug 		= SEO($_POST['kategori']);

$total = $koneksi_db->sql_query( "SELECT * FROM foto_kat WHERE kategori='".$_POST['kategori']."' and kid!=$kid");
$jumlah = $koneksi_db->sql_numrows( $total );

$error = '';
if ($jumlah) 		$error .= "<strong>Gagal!</strong> Nama kategori <strong>$kategori</strong> sudah ada di dalam database!<br />";
if (!$kategori)   	$error .= "<strong>Gagal!</strong> Kategori tidak boleh kosong!<br />";
if (!$keterangan) 	$error .= "<strong>Gagal!</strong> Keterangan tidak boleh kosong!<br />";
if ($error){
$admin .= '<div class="alert alert-danger">'.$error.'</div>';
}else {
$hasil = $koneksi_db->sql_query("UPDATE foto_kat SET kategori='$kategori', keterangan='$keterangan', slug='$slug' WHERE kid='$kid'");
if($hasil){
$admin .= '<div class="alert alert-success"><strong>Berhasil!</strong> Kategori berhasil disimpan</div>';
$style_include[] ='<meta http-equiv="refresh" content="1; url=?opsi=foto&modul=yes&aksi=list_kategori">';
}else{
$admin .= '<div class="alert alert-danger"><strong>Berhasil!</strong> Kategori gagal disimpan</div>';
}
}
}

$a = $koneksi_db->sql_query( "SELECT * FROM foto_kat WHERE kid=$kid" );
$data = $koneksi_db->sql_fetchrow($a);	
$kategori	= $data['kategori'];    
$keterangan	= $data['keterangan'];    

$admin .= '<form class="form-horizontal" action="" method="post">
<div class="form-group">
	<label class="col-sm-2 control-label">Kategori</label>
	<div class="col-sm-10"><input type="text" name="kategori" value="'.$kategori.'" class="form-control"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Keterangan</label>
	<div class="col-sm-10"><textarea name="keterangan" rows="3" class="form-control">'.htmlentities($keterangan).'</textarea></div>
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
if($_GET['aksi'] =='hapus_kategori'){
$id = int_filter ($_GET['kid']);
$hapusphoto = mysql_query ("select * FROM `photo` WHERE `kategori`='$id'");
while($data = mysql_fetch_array($hapusphoto)){
$gambar=$data['gambar'];
$uploaddir = $normal . $gambar;
unlink($uploaddir);
}	
$hapusphoto2 = mysql_query ("delete FROM `photo` WHERE `kategori`='$id'");
$hapus = mysql_query ("DELETE FROM `photo_kat` WHERE `kid`='$id'");	
if ($hapus){
$admin .= '<div class="alert alert-success">Kategori Dengan ID = '.$id.' Berhasil Dihapus</div>';	
$style_include[] ='<meta http-equiv="refresh" content="3; url=?opsi=photo&mod=yes&aksi=kategori" />';
}else {
$admin .= '<div class="alert alert-danger">Kategori Dengan ID = '.$id.' Gagal dihapus</div>';	
}	
}

#############################################
# Hapus Foto
#############################################
if($_GET['aksi'] =='hapus_foto'){
$id = int_filter ($_GET['id']);
$cek  = $koneksi_db->sql_query( "SELECT gambar FROM foto WHERE id=$id" );
while($data = mysql_fetch_array($cek)){ 
$gambar = $data['gambar'];
$uploaddir = $normal . $gambar;
unlink($uploaddir);
$cek  = $koneksi_db->sql_query( "update foto_kat set gambar = '' WHERE gambar = '$gambar'" );
}
$hapus = $koneksi_db->sql_query("DELETE FROM foto WHERE id='$id'");	
if ($hapus){
$admin .= '<div class="alert alert-success">Photo Dengan ID = '.$id.' Berhasil Dihapus</div>';	
$style_include[] ='<meta http-equiv="refresh" content="3; url=main.php?opsi=foto&modul=yes">';
}else {
$admin .= '<div class="alert alert-danger">Photo Dengan ID = '.$id.' Gagal dihapus</div>';	
}
}

#############################################
# xxxxxxxxxxxxx
#############################################
if($_GET['aksi'] =='photo'){
$kid     = int_filter($_GET['kid']);
$hasil =  $koneksi_db->sql_query( "SELECT * FROM photo_kat where kid='$kid' " );
$data = $koneksi_db->sql_fetchrow($hasil);
$kategori=$data['kategori'];
$admin .= '<div class="border"><b>'.$kategori.'</b></div>';
$admin .= '<div class="border">';
$admin .= '<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr>';
$no =0;
$s = mysql_query ("SELECT * FROM `photo` where kategori='$kid'");	
while($data = mysql_fetch_array($s)){
$urutan = $no + 1;
$kategori = $data['kategori'];
$id = $data['id'];
$judul = $data['judul'];
$ket = $data['ket'];
$gambar = $data['gambar'];
if(!$gambar){
$gambar = 'photo-default.jpg';
}

$deleted = '<a href="admin.php?opsi=photo&mod=yes&aksi=hapus_foto&id='.$id.'" onclick="return confirm(\'Apakah Anda Ingin Menghapus Data Ini ?\')" style="color:red">Delete</a>';

$admin .= '<td align="center" style="padding:5px 5px 20px 5px;">
<a href="admin.php?opsi=photo&mod=yes&aksi=detail&id='.$id.'&kid='.$kid.'">
<img src="mod/photo/images/normal/'.$gambar.'" alt="'.$gambar.'" style="padding:3px; border:1px solid #dddddd; border="0"  width="150" height="100"></a><br>
<a href="admin.php?opsi=photo&mod=yes&aksi=cover_photo&id='.$id.'&kid='.$kid.'">Cover</a> - 
<a href="admin.php?opsi=photo&mod=yes&aksi=edit_photo&id='.$id.'">Edit</a> - 
<span class="del">'.$deleted.'</span>
</td>';
if ($urutan  % $maxgalleri == 0) {
$admin .= '</tr></tr>';
}
$no++;
}
$admin .= '</table>';
$admin .= '</div>';
}

if($_GET['aksi'] =='detail'){
$id     = int_filter($_GET['id']);
$kid     = int_filter($_GET['kid']);
$admin .= '<div class=border>';
$admin .= '<table width="100%" border="0" cellspacing="0" cellpadding="0">';
$no =0;
$s = mysql_query ("SELECT * FROM `photo` where id='$id' ");	
while($data = mysql_fetch_array($s)){
$urutan = $no + 1;
$kategori = $data['kategori'];
$id = $data['id'];
$judul = $data['judul'];
$ket = $data['ket'];
$gambar = $data['gambar'];
$s2 = mysql_query ("SELECT * FROM `photo_kat` where kid ='$kategori'");	
$data2 = mysql_fetch_array($s2);
$kat2 = $data2['kategori'];
if(!$gambar){
$gambar = 'photo-default.jpg';
}
$admin .= '<tr><td>
<img src="mod/photo/images/normal/'.$gambar.'" alt="'.$gambar.'" style="padding:3px; border:1px solid #dddddd; background:#fff;">
</td></tr>
<tr>
<td>
<b>'.$judul.'</b> (<a href="admin.php?opsi=photo&mod=yes&aksi=photo&kid='.$kategori.'"><span style="font-size:11px;">'.$kat2.'</a>)<br>
'.$ket.'<br><br>
<div align="left">
<a href="admin.php?opsi=photo&mod=yes&aksi=cover_photo&id='.$id.'&kid='.$kategori.'"><img src="images/cover.gif">
<a href="admin.php?opsi=photo&mod=yes&aksi=edit_photo&id='.$id.'"><img src="images/edit.gif"></a>
<a href="admin.php?opsi=photo&mod=yes&aksi=hapus_foto&id='.$id.'"><img src="images/delete.gif"></a></div>
</td></tr>';
}
$admin .= '</table>';
$admin .= '</div>';
}

if($_GET['aksi'] =='cover_photo'){
$id = int_filter ($_GET['id']);
$kid = int_filter ($_GET['kid']);
$s  = $koneksi_db->sql_query( "SELECT * FROM photo WHERE id=$id" );
$data = mysql_fetch_array($s);
$gambar = $data['gambar'];
$cek  = $koneksi_db->sql_query( "update photo_kat set gambar = '$gambar' WHERE kid = '$kid'" );
if ($cek){
$admin .= '<div class="alert alert-success">Photo Dengan ID = '.$id.' Berhasil Dibuat Cover = '.$kid.'</div>';	
$style_include[] ='<meta http-equiv="refresh" content="3; url=?opsi=foto&mod=yes">';
}
}

#############################################
# Thumbnail
#############################################
if($_GET['aksi']=="editthumb"){

$id     = int_filter($_GET['id']);
$topik 	= int_filter($_GET['topik']);

$admin .='<div class="border">';
$admin .='<b>Edit Gambar Default</b>';
$admin .='</div>';

if (isset($_POST['submit'])){	
define("GIS_GIF", 1);
define("GIS_JPG", 2);
define("GIS_PNG", 3);
define("GIS_SWF", 4);

include "includes/hft_image.php";
$namafile_name = $_FILES['gambar']['name'];
if (!empty ($namafile_name)){

    $files = $_FILES['gambar']['name'];
    $tmp_files = $_FILES['gambar']['tmp_name'];
    $namagambar = 'photo-default.jpg';
    $tempnews 	= 'mod/photo/images/temp/';
    $uploaddir = $tempnews . $namagambar; 
    $uploads = move_uploaded_file($tmp_files, $uploaddir);
	if (file_exists($uploaddir)){
		@chmod($uploaddir,0644);
	}
	//$tnews 		= 'mod/news/images/thumb/';
	$gnews 		= 'mod/photo/images/normal/';
    $small 	= $tnews . $namagambar;
	$nsmall = $gnews . $namagambar;
	create_thumbnail ($uploaddir, $nsmall, $new_width = 250, $new_height = 'auto', $quality = 100);
    $gambar = $_POST['gambarlama'];

    //masukkan data
	unlink($uploaddir);
	unlink($tnews.$gambar);
	unlink($gnews.$gambar);
    $admin.='<div class="alert alert-success">Berhasil update gambar </div>';
	 $style_include[] ='<meta http-equiv="refresh" content="1; url=?opsi=photo&mod=yes&aksi=editthumb" />';
}
}
$gambarlama = $data['gambar'];
$admin .='<div class="border">';
$admin .="
<form method='post' action='' enctype ='multipart/form-data' id='posts'>";
$admin .="
<label>Gambar Default</label><br /><input type='file' name='gambar' size='53'><input type='hidden' name='gambarlama' size='53' value=\"$gambarlama\"><br />
<br />
<input type='submit' value='Update' name='submit' class='button'></form>";
$admin .='</div>';
$gambarlama='photo-default.jpg';
$admin .='<div class="border">Preview Thumb Picture:<br>
<img src="mod/photo/images/normal/'.$gambarlama.'" height="120"></div>';
}

#############################################
# Pengaturan Foto
#############################################
if($_GET['aksi']=="pengaturan"){

$admin .='<h4 class="page-header">Pengaturan Foto</h4>';

if (isset($_POST['submit'])) {

$thumb_width  	= int_filter($_POST['thumb_width']);
$thumb_height	= int_filter($_POST['thumb_height']);
$normal_width	= int_filter($_POST['normal_width']);
$normal_height	= int_filter($_POST['normal_height']);
$kualitas		= int_filter($_POST['kualitas']);

$error = '';
if (!$thumb_width)	$error .= "<strong>Gagal!</strong> Lebar gambar thumbnail tidak boleh kosong!<br />";
if (!$thumb_height)	$error .= "<strong>Gagal!</strong> Tinggi gambar thumbnail tidak boleh kosong!<br />";
if (!$normal_width)	$error .= "<strong>Gagal!</strong> Lebar gambar normal tidak boleh kosong!<br />";
if (!$normal_height)	$error .= "<strong>Gagal!</strong> Tinggi gambar normal tidak boleh kosong!<br />";
if (!$kualitas)		$error .= "<strong>Gagal!</strong> Kualitas gambar tidak boleh kosong!<br />";

if ($error){
$admin.='<div class="alert alert-danger">'.$error.'</div>';
}else{
$hasil = $koneksi_db->sql_query( "UPDATE foto_setting SET thumb_width='$thumb_width',thumb_height='$thumb_height',normal_width='$normal_width',normal_height='$normal_height',kualitas='$kualitas' WHERE id=1" );
$admin.='<div class="alert alert-success"><strong>Berhasil!</strong> Pengaturan foto berhasil disimpan</div>';
$admin.='<meta http-equiv="refresh" content="1; url=?opsi=foto&amp;modul=yes&amp;aksi=pengaturan">';
}
}

$hasil = $koneksi_db->sql_query( "SELECT * FROM foto_setting WHERE id=1" );
$data = $koneksi_db->sql_fetchrow($hasil);
$thumb_width  	= $data['thumb_width'];
$thumb_height	= $data['thumb_height'];
$normal_width  	= $data['normal_width'];
$normal_height	= $data['normal_height'];
$kualitas		= $data['kualitas'];

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
	<label class="col-sm-3 control-label"></label>
    <div class="col-sm-9"><input type="submit" name="submit" value="Simpan" class="btn btn-success"></div>
</div></form>';
}

echo $admin;

?>