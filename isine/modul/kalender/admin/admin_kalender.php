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

$admin  ='<h3 class="page-header">Kalender Manager</h3>';

$total =  $koneksi_db->sql_query("SELECT * FROM kalender");
$jumlah = $koneksi_db->sql_numrows($total);

$admin .='<ol class="breadcrumb">
<li><a href="?opsi=kalender&amp;modul=yes">Data Kegiatan</a> <span class="badge">'.$jumlah.'</span></li>
<li><a href="?opsi=kalender&amp;modul=yes&aksi=tambah_kegiatan">Tambah Kegiatan</a></li>
</ol>';

#############################################
# List Kegiatan
#############################################
if($_GET['aksi']==""){

$admin .= '<form method="get" action="main.php"><div class="input-group">
<input type="text" name="query" class="form-control" placeholder="Cari Data Kegiatan">
<span class="input-group-btn">
	<input type="hidden" name="opsi" value="kalender">
	<input type="hidden" name="modul" value="yes">
	<button class="btn btn-default" type="submit" name="submit" value="Search">Go!</button>
</span>
</div></form>';

$admin .= '<h4 class="page-header">Data Kegiatan</h4>';

$query = $_GET['query'];
$limit = 20;

if ($query) {
$total = $koneksi_db->sql_query("SELECT * FROM kalender WHERE judul like '%$query%' ORDER BY id DESC");
}else{
$total = $koneksi_db->sql_query("SELECT * FROM kalender ORDER BY id DESC");
}
$jumlah = $koneksi_db->sql_numrows( $total );

if (!isset ($_GET['offset'])) {
	$offset = 0;
}

$a = new paging ($limit);

if ($jumlah<1){
$admin.='<div class="alert alert-danger">Data kegiatan <strong>kosong</strong></div>';
}else{
if ($query) {
$hasil = $koneksi_db->sql_query("SELECT * FROM kalender WHERE judul like '%$query%' ORDER BY id DESC LIMIT $offset,$limit");
}else{
$hasil = $koneksi_db->sql_query("SELECT * FROM kalender ORDER BY id DESC LIMIT $offset,$limit");
}

if($offset){
$no = $offset+1;
}else{
$no = 1;
}

$admin .='<div class="table-responsive"><table class="table table-striped table-hover">
<thead><tr>
<th>No</th>
<th>Nama</th>
<th>Tanggal Mulai</th>
<th>Tanggal Akhir</th>
<th>Keterangan</th>
<th>Aksi</th>
</tr></thead><tbody>';
while ($data = $koneksi_db->sql_fetchrow($hasil)) {
$kid	= $data['kid'];

$hasil2 = $koneksi_db->sql_query( "SELECT * FROM links_kat WHERE kid=$kid");
$data2 = $koneksi_db->sql_fetchrow($hasil2);
$kategori = $data2['kategori'];

$admin .='<tr>
<td>'.$no.'</td>
<td><a class="text-info" href="?opsi=kalender&amp;modul=yes&aksi=edit_kegiatan&id='.$data['id'].'">'.$data['judul'].'</a></td>
<td>'.$data['waktu_mulai'].'</td>
<td>'.$data['waktu_akhir'].'</td>
<td>'.$data['ket'].'</td>
<td><a class="text-info" href="?opsi=kalender&amp;modul=yes&aksi=edit_kegiatan&id='.$data['id'].'">Edit</a> - 
<a class="text-danger" href="?opsi=kalender&amp;modul=yes&aksi=hapus_kegiatan&id='.$data['id'].'">Hapus</a></td>
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
# Tambah Kegiatan
#############################################
if($_GET['aksi']=="tambah_kegiatan"){

$admin .='<h4 class="page-header">Tambah Kegiatan</h4>';

if (isset($_POST['submit'])){
	
$judul			= text_filter($_POST['judul']);
$waktu_mulai 	= ''.$_POST['thnm'].'-'.$_POST['blnm'].'-'.$_POST['tglm'].' '.$_POST['jmm'].':'.$_POST['mntm'].':'.$_POST['dtkm'].'';
$waktu_akhir	= ''.$_POST['thna'].'-'.$_POST['blna'].'-'.$_POST['tgla'].' '.$_POST['jma'].':'.$_POST['mnta'].':'.$_POST['dtka'].'';
$ket			= text_filter($_POST['ket']);
$slug			= SEO($_POST['judul']);

$error = '';
if (!$judul)	$error .= "<strong>Gagal!</strong> Nama kegiatan belum diisi<br />";
if (!$ket)		$error .= "<strong>Gagal!</strong> Keterangan kegiatan belum diisi<br />";

if ($error){
$admin.='<div class="alert alert-danger">'.$error.'</div>';
}else{
//masukkan data
$hasil = $koneksi_db->sql_query("INSERT INTO kalender (judul,waktu_mulai,waktu_akhir,ket,slug) VALUES ('$judul','$waktu_mulai','$waktu_akhir','$ket','$slug')");
if($hasil){
$admin.='<div class="alert alert-success"><strong>Berhasil!</strong> Kegiatan bernama <strong>'.stripslashes ($_POST['judul']).'</strong> berhasil ditambah</div>';
$admin.='<meta http-equiv="refresh" content="1; url=?opsi=kalender&amp;modul=yes">';
}else{
$admin .= '<div class="alert alert-danger">'.mysql_error().'</div>';
}
}
}

$judul	= !isset($judul) ? '' : $judul;
$ket	= !isset($ket) ? '' : $ket;

$tgl_explode = str_to_time(date('Y-m-d H:i:s'), '%Y-%m-%d %H:%i:%s');

$admin .= '<form class="form-horizontal" method="post" action="" enctype ="multipart/form-data">
<div class="form-group">
	<label class="col-sm-2 control-label">Nama Kegiatan</label>
	<div class="col-sm-10"><input type="text" name="judul" value="'.$judul.'" class="form-control" placeholder="Masukkan nama kegiatan"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Waktu Mulai</label>
	<div class="col-sm-10 form-inline">';

// menampilkan pilihan combobox untuk tanggal
$admin .= '<select name="tglm" class="form-control">';
for ($tglm=1; $tglm<=31; $tglm++){
if ($tglm == $tgl_explode['d']) 
$admin .= '<option value="'.$tglm.'" selected>'.$tglm.'</option>';
else 
$admin .= '<option value="'.$tglm.'">'.$tglm.'</option>';
}
$admin .= '</select> ';

// menampilkan pilihan combobox untuk bulan
$admin .= '<select name="blnm" class="form-control">';
for ($blnm=1; $blnm<=12; $blnm++){
if ($blnm == $tgl_explode['m']) 
$admin .= '<option value="'.$blnm.'" selected>'.$blnm.'</option>';
else 
$admin .= '<option value="'.$blnm.'">'.$blnm.'</option>';
}
$admin .= '</select> ';

// menampilkan pilihan combobox untuk tahun
$getdate = getdate();
$stahun = $getdate['year'];
$atahun = $getdate['year']+5;
$admin .= '<select name="thnm" class="form-control">';
for ($thnm=$stahun; $thnm<=$atahun; $thnm++){
if ($thnm == $tgl_explode['Y']) 
$admin .= '<option value="'.$thnm.'" selected>'.$thnm.'</option>';
else 
$admin .= '<option value="'.$thnm.'">'.$thnm.'</option>';
}
$admin .= '</select> ';

// menampilkan pilihan combobox untuk jam
$admin .= '<select name="jmm" class="form-control">';
for ($jmm=0; $jmm<=23; $jmm++){
if ($jmm == $tgl_explode['H'])
$admin .= '<option value="'.$jmm.'" selected>'.$jmm.'</option>';
else 
$admin .= '<option value="'.$jmm.'">'.$jmm.'</option>';
}
$admin .= '</select> ';

// menampilkan pilihan combobox untuk menit
$admin .= ' <select name="mntm" class="form-control">';
for ($mntm=0; $mntm<=59; $mntm++){
if ($mntm == $tgl_explode['i'])
$admin .= '<option value="'.$mntm.'" selected>'.$mntm.'</option>';
else 
$admin .= '<option value="'.$mntm.'">'.$mntm.'</option>';
}
$admin .= '</select> ';

// menampilkan pilihan combobox untuk detik
$admin .= '<select name="dtkm" class="form-control">';
for ($dtkm=0; $dtkm<=59; $dtkm++){
if ($dtkm == $tgl_explode['s']) 
$admin .= '<option value="'.$dtkm.'" selected>'.$dtkm.'</option>';
else 
$admin .= '<option value="'.$dtkm.'">'.$dtkm.'</option>';
}
$admin .= '</select>';

$admin .= '<span class="help-block">Format : 30-12-2014 12:00:00</span></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Waktu Akhir</label>
	<div class="col-sm-10 form-inline">';

// menampilkan pilihan combobox untuk tanggal
$admin .= '<select name="tgla" class="form-control">';
for ($tgla=1; $tgla<=31; $tgla++){
if ($tgla == $tgl_explode['d']) 
$admin .= '<option value="'.$tgla.'" selected>'.$tgla.'</option>';
else 
$admin .= '<option value="'.$tgla.'">'.$tgla.'</option>';
}
$admin .= '</select> ';

// menampilkan pilihan combobox untuk bulan
$admin .= '<select name="blna" class="form-control">';
for ($blna=1; $blna<=12; $blna++){
if ($blna == $tgl_explode['m']) 
$admin .= '<option value="'.$blna.'" selected>'.$blna.'</option>';
else 
$admin .= '<option value="'.$blna.'">'.$blna.'</option>';
}
$admin .= '</select> ';

// menampilkan pilihan combobox untuk tahun
$getdate = getdate();
$stahun = $getdate['year'];
$atahun = $getdate['year']+5;
$admin .= '<select name="thna" class="form-control">';
for ($thna=$stahun; $thna<=$atahun; $thna++){
if ($thna == $tgl_explode['Y']) 
$admin .= '<option value="'.$thna.'" selected>'.$thna.'</option>';
else 
$admin .= '<option value="'.$thna.'">'.$thna.'</option>';
}
$admin .= '</select> ';

// menampilkan pilihan combobox untuk jam
$admin .= '<select name="jma" class="form-control">';
for ($jma=0; $jma<=23; $jma++){
if ($jma == $tgl_explode['H'])
$admin .= '<option value="'.$jma.'" selected>'.$jma.'</option>';
else 
$admin .= '<option value="'.$jma.'">'.$jma.'</option>';
}
$admin .= '</select> ';

// menampilkan pilihan combobox untuk menit
$admin .= ' <select name="mnta" class="form-control">';
for ($mnta=0; $mnta<=59; $mnta++){
if ($mnta == $tgl_explode['i'])
$admin .= '<option value="'.$mnta.'" selected>'.$mnta.'</option>';
else 
$admin .= '<option value="'.$mnta.'">'.$mnta.'</option>';
}
$admin .= '</select> ';

// menampilkan pilihan combobox untuk detik
$admin .= '<select name="dtka" class="form-control">';
for ($dtka=0; $dtka<=59; $dtka++){
if ($dtka == $tgl_explode['s']) 
$admin .= '<option value="'.$dtka.'" selected>'.$dtka.'</option>';
else 
$admin .= '<option value="'.$dtka.'">'.$dtka.'</option>';
}
$admin .= '</select>';

$admin .= '<span class="help-block">Format : 30-12-2014 12:00:00</span></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Keterangan</label>
	<div class="col-sm-10"><textarea name="ket" rows="3" id="mce" class="form-control" placeholder="Masukkan keterangan kegiatan">'.$ket.'</textarea></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label"></label>
	<div class="col-sm-10"><input type="submit" name="submit" value="Tambah" class="btn btn-success"></div>
</div>
</form></div>';
}

#############################################
# Edit Kegiatan
#############################################
if($_GET['aksi']=="edit_kegiatan"){

$id = int_filter($_GET['id']);

$admin .='<h4 class="page-header">Edit Kegiatan ID '.$id.'</h4>';

if (isset($_POST['submit'])){
	
$judul			= text_filter($_POST['judul']);
$waktu_mulai 	= ''.$_POST['thnm'].'-'.$_POST['blnm'].'-'.$_POST['tglm'].' '.$_POST['jmm'].':'.$_POST['mntm'].':'.$_POST['dtkm'].'';
$waktu_akhir 	= ''.$_POST['thna'].'-'.$_POST['blna'].'-'.$_POST['tgla'].' '.$_POST['jma'].':'.$_POST['mnta'].':'.$_POST['dtka'].'';
$ket			= text_filter($_POST['ket']);
$slug			= SEO($_POST['judul']);

$error = '';
if (!$judul)	$error .= "<strong>Gagal!</strong> Nama kegiatan belum diisi<br />";
if (!$ket)		$error .= "<strong>Gagal!</strong> Keterangan kegiatan belum diisi<br />";

if ($error){
$admin.='<div class="alert alert-danger">'.$error.'</div>';
}else{
//masukkan data
$hasil = $koneksi_db->sql_query( "UPDATE kalender SET judul='$judul',waktu_mulai='$waktu_mulai',waktu_akhir='$waktu_akhir',ket='$ket',slug='$slug' WHERE id='$id'" );
if($hasil){
$admin.='<div class="alert alert-success"><strong>Berhasil!</strong> Kegiatan bernama <strong>'.stripslashes ($_POST['judul']).'</strong> telah disimpan</div>';
$admin.='<meta http-equiv="refresh" content="1; url=?opsi=kalender&amp;modul=yes">';
}else{
$admin .= '<div class="alert alert-danger">'.mysql_error().'</div>';
}
}
}

$hasil = $koneksi_db->sql_query( "SELECT * FROM kalender WHERE id=$id" );
$data = $koneksi_db->sql_fetchrow($hasil);
$judul			= $data['judul'];
$waktu_mulai 	= str_to_time(date($data['waktu_mulai']), '%Y-%m-%d %H:%i:%s');
$waktu_akhir 	= str_to_time(date($data['waktu_akhir']), '%Y-%m-%d %H:%i:%s');
$ket			= $data['ket'];

$admin .='<form class="form-horizontal" method="post" action="" enctype ="multipart/form-data">
<div class="form-group">
	<label class="col-sm-2 control-label">Nama Kegiatan</label>
	<div class="col-sm-10"><input type="text" name="judul" value="'.$judul.'" class="form-control" placeholder="Masukkan nama link"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Waktu Mulai</label>
	<div class="col-sm-10 form-inline">';

// menampilkan pilihan combobox untuk tanggal
$admin .= '<select name="tglm" class="form-control">';
for ($tglm=1; $tglm<=31; $tglm++){
if ($tglm == $waktu_mulai['d']) 
$admin .= '<option value="'.$tglm.'" selected>'.$tglm.'</option>';
else 
$admin .= '<option value="'.$tglm.'">'.$tglm.'</option>';
}
$admin .= '</select> ';

// menampilkan pilihan combobox untuk bulan
$admin .= '<select name="blnm" class="form-control">';
for ($blnm=1; $blnm<=12; $blnm++){
if ($blnm == $waktu_mulai['m']) 
$admin .= '<option value="'.$blnm.'" selected>'.$blnm.'</option>';
else 
$admin .= '<option value="'.$blnm.'">'.$blnm.'</option>';
}
$admin .= '</select> ';

// menampilkan pilihan combobox untuk tahun
$getdate = getdate();
$stahun = $getdate['year'];
$atahun = $getdate['year']+5;
$admin .= '<select name="thnm" class="form-control">';
for ($thnm=$stahun; $thnm<=$atahun; $thnm++){
if ($thnm == $waktu_mulai['Y']) 
$admin .= '<option value="'.$thnm.'" selected>'.$thnm.'</option>';
else 
$admin .= '<option value="'.$thnm.'">'.$thnm.'</option>';
}
$admin .= '</select> ';

// menampilkan pilihan combobox untuk jam
$admin .= '<select name="jmm" class="form-control">';
for ($jmm=0; $jmm<=23; $jmm++){
if ($jmm == $waktu_mulai['H'])
$admin .= '<option value="'.$jmm.'" selected>'.$jmm.'</option>';
else 
$admin .= '<option value="'.$jmm.'">'.$jmm.'</option>';
}
$admin .= '</select> ';

// menampilkan pilihan combobox untuk menit
$admin .= ' <select name="mntm" class="form-control">';
for ($mntm=0; $mntm<=59; $mntm++){
if ($mntm == $waktu_mulai['i'])
$admin .= '<option value="'.$mntm.'" selected>'.$mntm.'</option>';
else 
$admin .= '<option value="'.$mntm.'">'.$mntm.'</option>';
}
$admin .= '</select> ';

// menampilkan pilihan combobox untuk detik
$admin .= '<select name="dtkm" class="form-control">';
for ($dtkm=0; $dtkm<=59; $dtkm++){
if ($dtkm == $waktu_mulai['s']) 
$admin .= '<option value="'.$dtkm.'" selected>'.$dtkm.'</option>';
else 
$admin .= '<option value="'.$dtkm.'">'.$dtkm.'</option>';
}
$admin .= '</select>';

$admin .= '<span class="help-block">Format : 30-12-2014 12:00:00</span></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Waktu Akhir</label>
	<div class="col-sm-10 form-inline">';

// menampilkan pilihan combobox untuk tanggal
$admin .= '<select name="tgla" class="form-control">';
for ($tgla=1; $tgla<=31; $tgla++){
if ($tgla == $waktu_akhir['d']) 
$admin .= '<option value="'.$tgla.'" selected>'.$tgla.'</option>';
else 
$admin .= '<option value="'.$tgla.'">'.$tgla.'</option>';
}
$admin .= '</select> ';

// menampilkan pilihan combobox untuk bulan
$admin .= '<select name="blna" class="form-control">';
for ($blna=1; $blna<=12; $blna++){
if ($blna == $waktu_akhir['m']) 
$admin .= '<option value="'.$blna.'" selected>'.$blna.'</option>';
else 
$admin .= '<option value="'.$blna.'">'.$blna.'</option>';
}
$admin .= '</select> ';

// menampilkan pilihan combobox untuk tahun
$getdate = getdate();
$stahun = $getdate['year'];
$atahun = $getdate['year']+5;
$admin .= '<select name="thna" class="form-control">';
for ($thna=$stahun; $thna<=$atahun; $thna++){
if ($thna == $waktu_akhir['Y']) 
$admin .= '<option value="'.$thna.'" selected>'.$thna.'</option>';
else 
$admin .= '<option value="'.$thna.'">'.$thna.'</option>';
}
$admin .= '</select> ';

// menampilkan pilihan combobox untuk jam
$admin .= '<select name="jma" class="form-control">';
for ($jma=0; $jma<=23; $jma++){
if ($jma == $waktu_akhir['H'])
$admin .= '<option value="'.$jma.'" selected>'.$jma.'</option>';
else 
$admin .= '<option value="'.$jma.'">'.$jma.'</option>';
}
$admin .= '</select> ';

// menampilkan pilihan combobox untuk menit
$admin .= ' <select name="mnta" class="form-control">';
for ($mnta=0; $mnta<=59; $mnta++){
if ($mnta == $waktu_akhir['i'])
$admin .= '<option value="'.$mnta.'" selected>'.$mnta.'</option>';
else 
$admin .= '<option value="'.$mnta.'">'.$mnta.'</option>';
}
$admin .= '</select> ';

// menampilkan pilihan combobox untuk detik
$admin .= '<select name="dtka" class="form-control">';
for ($dtka=0; $dtka<=59; $dtka++){
if ($dtka == $waktu_akhir['s']) 
$admin .= '<option value="'.$dtka.'" selected>'.$dtka.'</option>';
else 
$admin .= '<option value="'.$dtka.'">'.$dtka.'</option>';
}
$admin .= '</select>';

$admin .= '<span class="help-block">Format : 30-12-2014 12:00:00</span></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Keterangan</label>
	<div class="col-sm-10"><textarea name="ket" rows="5" id="mce" class="form-control" placeholder="Masukkan keterangan link">'.$ket.'</textarea></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label"></label>
	<div class="col-sm-10"><input type="submit" value="Simpan" name="submit" class="btn btn-success"></div>
</div></form>';
}

#############################################
# Hapus Kegiatan
#############################################
if($_GET['aksi']=="hapus_kegiatan"){

$id     = int_filter($_GET['id']);

$koneksi_db->sql_query("DELETE FROM kalender WHERE id='$id'");
$admin.='<div class="alert alert-success"><strong>Berhasil!</strong> Kegiatan telah dihapus</div>';
$admin.='<meta http-equiv="refresh" content="1; url=?opsi=kalender&amp;modul=yes">';
}

echo $admin;

?>