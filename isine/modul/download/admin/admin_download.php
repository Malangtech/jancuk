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

if (!cek_login ()){
   $admin .='<p class="judul">Access Denied !!!!!!</p>';
   exit;
}

if (isset ($_GET['pg'])) $pg = int_filter ($_GET['pg']); else $pg = 0;
if (isset ($_GET['stg'])) $stg = int_filter ($_GET['stg']); else $stg = 0;
if (isset ($_GET['offset'])) $offset = int_filter ($_GET['offset']); else $offset = 0;

$admin  ='<h3 class="page-header">Download Manager</h3>';

$total =  $koneksi_db->sql_query("SELECT * FROM download");
$jumlah = $koneksi_db->sql_numrows($total);

$admin .='<ol class="breadcrumb">
<li><a href="?opsi=download&amp;modul=yes">Data Download</a> <span class="badge">'.$jumlah.'</span></li>
<li><a href="?opsi=download&amp;modul=yes&amp;aksi=tambah_download">Tambah Download</a></li>
<li><a href="?opsi=download&amp;modul=yes&amp;aksi=list_kategori">Kategori</a></li>
</ol>';

#############################################
# List Download
#############################################
if($_GET['aksi']==""){

$admin .= '<form method="get" action="main.php"><div class="input-group">
<input type="text" name="query" class="form-control" placeholder="Cari Data Download">
<span class="input-group-btn">
	<input type="hidden" name="opsi" value="download">
	<input type="hidden" name="modul" value="yes">
	<button class="btn btn-default" type="submit" name="submit" value="Search">Go!</button>
</span>
</div></form>';

$admin .= '<h4 class="page-header">Data Download</h4>';

$query 		= $_GET['query'];
$kategori 	= $_GET['kategori'];
$limit 		= 20;

if ($query) {
$total = $koneksi_db->sql_query("SELECT * FROM download WHERE judul like '%$query%' ORDER BY id DESC");
}elseif ($kategori) {
$total = $koneksi_db->sql_query("SELECT * FROM download WHERE kid=$kategori ORDER BY id DESC");
}else{
$total = $koneksi_db->sql_query("SELECT * FROM download ORDER BY id DESC");
}
$jumlah = $koneksi_db->sql_numrows($total);

if (!isset ($_GET['offset'])) {
	$offset = 0;
}

$a = new paging ($limit);

if ($jumlah<1){
$admin.='<div class="alert alert-danger">Data download <strong>kosong</strong></div>';
}else{
if ($query) {
$hasil = $koneksi_db->sql_query("SELECT * FROM download Where judul like '%$query%' ORDER BY id DESC LIMIT $offset,$limit");
}elseif ($kategori) {
$hasil = $koneksi_db->sql_query("SELECT * FROM download WHERE kid=$kategori ORDER BY id DESC");
}else{
$hasil = $koneksi_db->sql_query("SELECT * FROM download ORDER BY id DESC LIMIT $offset,$limit");
}

if($offset){
$no = $offset+1;
}else{
$no = 1;
}

$admin .='<div class="table-responsive"><table class="table table-striped table-hover">
<thead><tr>
<th>No</th>
<th>Judul</th>
<th>Kategori</th>
<th>Files</th>
<th class="text-right">Ukuran</th>
<th class="text-center"><span class="glyphicon glyphicon-download"></span></th>
<th>Aksi</th>
</tr></thead><tbody>';
while ($data = $koneksi_db->sql_fetchrow($hasil)) {
$kid		= $data['kid'];
$tanggal	= datetimes($data['tanggal']);
$size 		= ''.number_format($data['size']/1024,1).' KB';

$hasil2 = $koneksi_db->sql_query( "SELECT * FROM download_kat WHERE kid=$kid");
$data2 = $koneksi_db->sql_fetchrow($hasil2);
$kategori = $data2['kategori'];

$admin .='<tr>
<td>'.$no.'</td>
<td><a class="text-info" href="?opsi=download&amp;modul=yes&amp;aksi=edit_download&amp;id='.$data['id'].'">'.$data['judul'].'</a></td>
<td><a class="text-info" href="?opsi=download&amp;modul=yes&amp;kategori='.$kid.'">'.$kategori.'</a></td>
<td><a class="text-info" href="id-content/modul/download/files/'.$data['file'].'" target="_blank">'.$data['file'].'</a></td>
<td class="text-right">'.$size.'</td>
<td class="text-center">'.$data['hit'].'</td>
<td><a class="text-info" href="?opsi=download&amp;modul=yes&amp;aksi=edit_download&amp;id='.$data['id'].'">Edit</a> - 
<a class="text-danger" href="?opsi=download&amp;modul=yes&amp;aksi=hapus_download&amp;id='.$data['id'].'">Hapus</a></td>
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
# Tambah Download
#############################################
if($_GET['aksi']=="tambah_download"){

$admin .='<h4 class="page-header">Tambah File Download</h4>';

if (isset($_POST['submit'])){
	
$judul     		= text_filter($_POST['judul']);
$kid			= $_POST['kid'];
$keterangan		= text_filter($_POST['keterangan']);
$dokumen_name 	= $_FILES['dokumen']['name'];

$error = '';
if (!$judul)  		$error .= "Error: Judul download belum diisi!<br />";
if (!$keterangan)	$error .= "Error: Keterangan download belum diisi!<br />";
if (!$dokumen_name) $error .= "Error: File belum diisi!<br />";

if ($error){
$admin.='<div class="alert alert-danger">'.$error.'</div>';
}else{
if (!empty ($dokumen_name)){
$dokumen = $_FILES['dokumen']['name'];
$type_dokumen 		= $_FILES['dokumen']['type'];
$tmp_dokumen 		= $_FILES['dokumen']['tmp_name'];
$size 				= $_FILES['dokumen']['size'];
$tempdokumen 		= 'id-content/modul/download/files/';
$namadokumen 		= $dokumen;
$uploaddirdokumen 	= $tempdokumen . $namadokumen; 
//$namadokumen 		= $tempdokumen . $namadokumen;
$uploadsdokumen = move_uploaded_file($tmp_dokumen, $uploaddirdokumen);
if (file_exists($uploaddirdokumen)){
@chmod($uploaddirdokumen,0644);
}
	
//masukkan data
$tanggal = date('Y-m-d H:i:s');
$hasil = $koneksi_db->sql_query( "INSERT INTO download (tanggal,judul,kid,keterangan,file,size) VALUES ('$tanggal','$judul','$kid','$keterangan','$namadokumen','$size')" );
if($hasil){
$admin.='<div class="alert alert-success">Berhasil memasukkan Download dg judul <strong>'.stripslashes ($_POST['judul']).' nama file : '.$namadokumen.'</strong></div>';
$admin.='<meta http-equiv="refresh" content="1; url=?opsi=download&amp;modul=yes">';
//unlink($uploaddirdokumen);
}
}
}
}

$judul 		= !isset($judul) ? '' : $judul;
$textarea	= !isset($textarea) ? '' : $textarea;
$konten		= !isset($konten) ? '' : $konten;

$admin .= '<form class="form-horizontal" method="post" action="" enctype ="multipart/form-data">
<div class="form-group">
	<label class="col-sm-2 control-label">Judul</label>
	<div class="col-sm-10"><input type="text" name="judul" value="'.$judul.'" class="form-control" placeholder="Masukkan judul download"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Kategori</label>
	<div class="col-sm-10"><select name="kid" class="form-control">';
$hasil = $koneksi_db->sql_query("SELECT * FROM download_kat ORDER BY kategori");
$admin .= '<option value="">None</option>';
while ($datas =  $koneksi_db->sql_fetchrow ($hasil)){
$admin .= '<option value="'.$datas['kid'].'">'.$datas['kategori'].'</option>';
}	
$admin .='</select></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Keterangan</label>
	<div class="col-sm-10"><textarea name="keterangan" rows="3" class="form-control" placeholder="Masukkan keterangan download">'.$keterangan.'</textarea></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">File</label>
	<div class="col-sm-10"><input type="file" name="dokumen"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label"></label>
	<div class="col-sm-10"><input type="submit" name="submit" value="Tambah" class="btn btn-success"></div>
</div>
</form>';
}

#############################################
# Edit Download
#############################################
if($_GET['aksi']=="edit_download"){

$id = int_filter($_GET['id']);

$admin .='<h4 class="page-header">Edit Download ID '.$id.'</h4>';

if (isset($_POST['submit'])){
	
$judul     		= text_filter($_POST['judul']);
$kid  			= $_POST['kid'];
$keterangan  	= text_filter($_POST['keterangan']);
$dokumen_name 	= $_FILES['dokumen']['name'];
$dokumenlama	= $_POST['dokumenlama'];

$error = '';
if (!$judul)  		$error .= "Error: Judul download belum diisi!<br />";
if (!$keterangan)	$error .= "Error: Keterangan download belum diisi!<br />";

if ($error){
$admin.='<div class="alert alert-danger">'.$error.'</div>';
}else{
//if (!empty ($dokumen_name)){
$dokumen 		= $_FILES['dokumen']['name'];
$type_dokumen 	= $_FILES['dokumen']['type'];
$tmp_dokumen 	= $_FILES['dokumen']['tmp_name'];
$tempdokumen 	= 'id-content/modul/download/files/';
$namadokumen 	= $dokumen;
$uploaddirdokumen = $tempdokumen . $namadokumen; 
//$namadokumen = $uploaddirdokumen;
$uploadsdokumen = move_uploaded_file($tmp_dokumen, $uploaddirdokumen);
if (file_exists($uploaddirdokumen)){
@chmod($uploaddirdokumen,0644);
}

//masukkan data
$hasil = $koneksi_db->sql_query( "UPDATE download SET judul='$judul',keterangan='$keterangan',kid='$kid',file='$namadokumen' WHERE id='$id'" );
if($hasil){
$admin.='<div class="alert alert-success"><strong>Berhasil!</strong> Download dengan judul <strong>'.stripslashes ($_POST['judul']).'</strong> telah disimpan</div>';
$admin.='<meta http-equiv="refresh" content="1; url=?opsi=download&amp;modul=yes">';
unlink($tempdokumen.$dokumenlama);
}else{
$admin .= '<div class="alert alert-danger">'.mysql_error().'</div>';
}
}
}

$hasil = $koneksi_db->sql_query( "SELECT * FROM download WHERE id=$id" );
$data = $koneksi_db->sql_fetchrow($hasil);
$judul     	= $data['judul'];
$keterangan	= $data['keterangan'];
$kid     	= $data['kid'];
$dokumenlama = $data['file'];

$admin .='<form class="form-horizontal" method="post" action="" enctype ="multipart/form-data">
<div class="form-group">
	<label class="col-sm-2 control-label">Judul</label>
	<div class="col-sm-10"><input type="text" name="judul" value="'.$judul.'" class="form-control"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Kategori</label>
	<div class="col-sm-10"><select name="kid" class="form-control">';
$hasil = $koneksi_db->sql_query("SELECT * FROM download_kat ORDER BY kategori");
while ($datas =  $koneksi_db->sql_fetchrow ($hasil)){
$pilihan = ($datas['kid']==$kid)?"selected":'';
$admin .='<option value="'.$datas['kid'].'" '.$pilihan.'>'.$datas['kategori'].'</option>';
}
$admin .='</select></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Keterangan</label>
	<div class="col-sm-10"><textarea name="keterangan" rows="3" id="mce" class="form-control">'.$keterangan.'</textarea></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">File</label>
	<div class="col-sm-10">
		<input type="file" name="dokumen">
		<input type="hidden" name="dokumenlama" value="'.$dokumenlama.'"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label"></label>
	<div class="col-sm-10"><input type="submit" value="Simpan" name="submit" class="btn btn-success"></div>
</div></form>';
}

#############################################
# Hapus Download
#############################################
if($_GET['aksi']=="hapus_download"){

$id     = int_filter($_GET['id']);

$hasil = $koneksi_db->sql_query( "SELECT * FROM download WHERE id=$id" );
while($data = mysql_fetch_array($hasil)){
$folder	= 'id-content/modul/download/files/';
$file 	=  $data['file'];
$uploaddir = $folder . $file; 
unlink($uploaddir);
}
$koneksi_db->sql_query("DELETE FROM download WHERE id='$id'");
$admin.='<div class="alert alert-success"><strong>Berhasil!</strong> Data download telah dihapus</div>';
$admin.='<meta http-equiv="refresh" content="1; url=?opsi=download&amp;modul=yes&amp;aksi=list_download">';
}

#############################################
# List Kategori
#############################################
if($_GET['aksi']=="list_kategori"){

$admin .='<div class="row">';
$admin .='<div class="col-md-4">';

if (isset($_POST['submit'])) {

$kategori	= $_POST['kategori'];
$keterangan	= $_POST['keterangan'];
$slug    	= SEO($_POST['kategori']);

$total = $koneksi_db->sql_query( "SELECT * FROM download_kat WHERE kategori = '".$_POST['kategori']."'");
$jumlah = $koneksi_db->sql_numrows($total);

$error = '';
if ($jumlah)  		$error .= "Error: Nama kategori sudah ada dalam database!<br />";
if (!$kategori)  	$error .= "Error: Nama kategori belum diisi!<br />";
if (!$keterangan) 	$error .= "Error: Keterangan kategori belum diisi!<br />";

if ($error){
$admin.='<div class="alert alert-danger">'.$error.'</div>';
}else{
$hasil = $koneksi_db->sql_query("INSERT INTO download_kat (kategori,keterangan,slug) VALUES ('$kategori','$keterangan','$slug')");
$admin.='<div class="alert alert-success"><strong>Berhasil!</strong> Kategori download telah ditambah</div>';
$admin.='<meta http-equiv="refresh" content="1; url=?opsi=download&amp;modul=yes&amp;aksi=list_kategori">';
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
	<label></label>
    <input type="submit" name="submit" value="Tambah" class="btn btn-success">
</div>
</form>';
$admin .='</div>';

$admin .='<div class="col-md-8">';
$query = $koneksi_db->sql_query( "SELECT * FROM download_kat ORDER BY kategori" );
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

$total = $koneksi_db->sql_query( "SELECT * FROM download WHERE kid=$kid");
$jumlah = $koneksi_db->sql_numrows( $total );

$admin .='<tr>
<td>'.$no.'</td>
<td><a href="?opsi=download&amp;modul=yes&amp;kategori='.$kid.'">'.$data['kategori'].'</a> <span class="badge">'.$jumlah.'</span></td>
<td>'.$data['keterangan'].'</td>
<td><a class="text-info" href="?opsi=download&amp;modul=yes&amp;aksi=edit_kategori&amp;kid='.$kid.'">Edit</a> - 
<a class="text-danger" href="?opsi=download&amp;modul=yes&amp;aksi=hapus_kategori&amp;kid='.$kid.'">Hapus</a></td>
</tr>';
$no++;
}
$admin .= '</tbody></table></div>';
$admin .='</div>';
$admin .='</div>';
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

$total = $koneksi_db->sql_query( "SELECT * FROM download_kat WHERE kategori = '".$_POST['kategori']."' and kid != '".$kid."'");
$jumlah = $koneksi_db->sql_numrows( $total );

$error = '';
if ($jumlah)  		$error .= "Error: Nama kategori sudah ada dalam database!<br />";
if (!$kategori)  	$error .= "Error: Nama kategori belum diisi!<br />";
if (!$keterangan)	$error .= "Error: Keterangan kategori belum diisi!<br />";

if ($error){
$admin.='<div class="alert alert-danger">'.$error.'</div>';
}else{
$hasil = $koneksi_db->sql_query( "UPDATE download_kat SET kategori='$kategori',keterangan='$keterangan',slug='$slug' WHERE kid='$kid'" );
$admin.='<div class="alert alert-success"><strong>Berhasil!</strong> Kategori download berhasil disimpan</div>';
$admin.='<meta http-equiv="refresh" content="1; url=?opsi=download&amp;modul=yes">';
}
}

$hasil = $koneksi_db->sql_query( "SELECT * FROM download_kat WHERE kid=$kid" );
$data = $koneksi_db->sql_fetchrow($hasil);
$kategori  	= $data['kategori'];
$keterangan	= $data['keterangan'];

$admin .='<form class="form-horizontal" method="post" action="">
<div class="form-group">
    <label class="col-sm-2 control-label">Kategori</label>
	<div class="col-sm-10"><input type="text" name="kategori" value="'.$kategori.'" class="form-control"></div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">Keterangan</label>
	<div class="col-sm-10"><textarea name="keterangan" rows="3" class="form-control">'.$keterangan.'</textarea></div>
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

$admin .='<h4 class="page-header">Hapus kategori download <span class="text-success">ID '.$kid.'</span></h4>';
$koneksi_db->sql_query("DELETE FROM download_kat WHERE kid='$kid'");
$admin.='<div class="alert alert-success"><strong>Berhasil!</strong> Kategori download telah dihapus</div>';
$admin.='<meta http-equiv="refresh" content="1; url=?opsi=download&amp;modul=yes">';
}

echo $admin;

?>