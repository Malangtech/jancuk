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

if (isset ($_GET['pg'])) $pg = int_filter ($_GET['pg']); else $pg = 0;
if (isset ($_GET['stg'])) $stg = int_filter ($_GET['stg']); else $stg = 0;
if (isset ($_GET['offset'])) $offset = int_filter ($_GET['offset']); else $offset = 0;

$admin  ='<h3 class="page-header">Berita Manager</h3>';

$admin .= '<ol class="breadcrumb">
<li><a href="?opsi=berita&amp;modul=yes">Home</a></li>
<li><a href="?opsi=berita&amp;modul=yes&aksi=tambah_berita">Tambah Berita</a></li>
<li><a href="?opsi=berita&amp;modul=yes&aksi=list_komentar">Komentar</a></li>
</ol>';

$username = $_SESSION["UserName"];

#############################################
# List Berita
#############################################
if($_GET['aksi']==""){

$admin .= '<div class="right_post">Arsip Berita</div>';
$admin .= '<div class="border"><form method="get" class="searchform" action="admin.php">
<p><input type="text" name="query" class="textbox" />
<input type="submit" name="submit" class="button" value="Search" />
<input type="hidden" name="pilih" value="news" />
<input type="hidden" name="mod" value="yes" />
<input type="hidden" name="aksi" value="listnews" />
</form></div>';
$query = $_GET['query'];
$limit = 10;
if (empty($_GET['query']) and !isset ($_GET['query'])) {
$total = $koneksi_db->sql_query( "SELECT * FROM artikel WHERE publikasi=1 and user = '$username' order by id desc ");
}else{
$total = $koneksi_db->sql_query( "SELECT * FROM artikel WHERE publikasi=1 and judul like '%$query%'and user = '$username' order by id desc ");
}

$jumlah = $koneksi_db->sql_numrows( $total );
$a = new paging ($limit);

if ($jumlah<1){
$admin.='<div class="error">Tidak Ada Artikel </div>';
}else{
if (empty($_GET['query']) and !isset ($_GET['query'])) {
$hasil = $koneksi_db->sql_query( "SELECT * FROM artikel WHERE publikasi=1  and user = '$username' ORDER BY id DESC LIMIT $offset,$limit" );
}else{
$hasil = $koneksi_db->sql_query( "SELECT * FROM artikel WHERE publikasi=1  and judul like '%$query%'and user = '$username' ORDER BY id DESC LIMIT $offset,$limit" );
}

$admin .='<div class="border"><table width="100%" cellspacing="0" cellpadding="4">';
while ($data = $koneksi_db->sql_fetchrow($hasil)) {
$gambar = $data['gambar'];
$topik = $data['topik'];
if($gambar){
$gambar2 ="<a href=mod/news/images/normal/$gambar target='new'><img height='30' src='mod/news/images/normal/$gambar'></a>";
}else
{
$gambar2 ="";
}
		$admin .="<tr><td width=80%><P><b>$data[1]</b><br />";
		$tgl=explode(",",$data[5]);
		$tgl[0]=strtr($tgl[0],$translateKal);
		$tgl[2]=strtr($tgl[2],$translateKal);

		$data_tgl="$tgl[0], $tgl[1] $tgl[2] $tgl[3]";
		$admin .="<span>$data_tgl - oleh : <a href=\"mailto:$data[4]\">$data[3]</a></span></p></td>";
		$admin .="<td valign=top>$gambar2&nbsp;</td>";
		$admin .="<td valign=top width=6% align=center><a href=admin.php?pilih=news&amp;mod=yes&aksi=hapus_berita&id=$data[0]&topik=$topik>delete</a></td><td valign=top width=6% align=center><a href=admin.php?pilih=news&amp;mod=yes&aksi=edit_berita&id=$data[0]&topik=$topik>edit</a></td></tr>";

	}
$admin .="</table>";
$admin .='</div>';
}

if($jumlah>10){

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
# Tambah Berita
#############################################
if($_GET['aksi']=="tambah_berita"){

$admin .='<div class="border">';
$admin .="<b>Menulis Artikel</b>";
$admin .='</div>';

if (isset($_POST['submit'])){
	
define("GIS_GIF", 1);
define("GIS_JPG", 2);
define("GIS_PNG", 3);
define("GIS_SWF", 4);

include "includes/hft_image.php";

$judul     		= text_filter($_POST['judul']);
$konten  		= text_filter($_POST['message']);
$namafile_name 	= $_FILES['gambar']['name'];
$publikasi = $publishnews-1;
$error = '';
if (!$judul)  $error .= "Error: Please enter Title of News!<br />";
if (!$konten) $error .= "Error: Please enter Content of News!<br />";

if ($error){
$admin.='<div class="error">'.$error.'</div>';

}else{

if (!empty ($namafile_name)){

    $files = $_FILES['gambar']['name'];
    $tmp_files = $_FILES['gambar']['tmp_name'];

    $tempnews 	= 'mod/news/images/temp/';
    $namagambar = md5 (rand(1,100).$files) .'.jpg';
    $uploaddir = $tempnews . $namagambar; 
    $uploads = move_uploaded_file($tmp_files, $uploaddir);
	if (file_exists($uploaddir)){
		@chmod($uploaddir,0644);
	}
	
	$tnews 		= 'mod/news/images/thumb/';
	$gnews 		= 'mod/news/images/normal/';
    $small 	= $tnews . $namagambar;
	$nsmall = $gnews . $namagambar;

	create_thumbnail ($uploaddir, $nsmall, $new_width = 415, $new_height = 'auto', $quality = 85);

    $judul  	= $_POST['judul'];
    $konten 	= $_POST['message'];
	$seftitle   = $_POST['seftitle'];
    $topik  	= $_POST['topik'];
    $tags 		= cleantext($_POST['tags']);
    $user   	= $_SESSION['UserName'];
    $email  	= $_SESSION['UserEmail'];

    //masukkan data
    $tgl= date('Y-m-d H:i:s');
    $hasil = $koneksi_db->sql_query( "INSERT INTO artikel (tgl,user,email,judul,konten,publikasi,topik,gambar,tags,seftitle) VALUES ('$tgl','$user','$email','$judul','$konten','$publikasi','$topik','$namagambar','$tags','$seftitle')" );
    if($hasil){
    $admin.='<div class="sukses">Berhasil memasukkan berita dg judul <u>' . stripslashes ($_POST['judul']) .'
	<br>nama file : '.$namafile_name.'
	
	</u></div>';

unlink($uploaddir);

    }
}else{

    $judul       = $_POST['judul'];
    $konten      = $_POST['message'];
    $topik       = $_POST['topik'];
	$seftitle    = $_POST['seftitle'];
    $tags 		 = cleantext($_POST['tags']);
    $user   	 = $_SESSION['UserName'];
    $email  	 = $_SESSION['UserEmail'];

     //masukkan data
    $tgl= date('Y-m-d H:i:s');
    $hasil = $koneksi_db->sql_query( "INSERT INTO artikel (tgl,user,email,judul,konten,publikasi,topik,tags,seftitle) VALUES ('$tgl','$user','$email','$judul','$konten','$publikasi','$topik','$tags','$seftitle')" );
    if($hasil){
    $admin.='<div class="sukses">Berhasil memasukkan berita dg judul <u>' . stripslashes ($_POST['judul']) . ' </u></div>';
    }

}

}

}
$judul 		= !isset($judul) ? '' : $judul;
$seftitle	= !isset($seftitle) ? '' : $seftitle;
$textarea	= !isset($textarea) ? '' : $textarea;
$tags		= !isset($tags) ? '' : $tags;
$admin .= "
<div class='border'>
<form method='post' action='' enctype ='multipart/form-data' id='posts'>
<label>Judul</label><br /><input type='text' name='judul' value='$judul' size='53' onkeyup=\"genSEF(this,document.forms['posts'].seftitle)\" onchange=\"genSEF(this,document.forms['posts'].seftitle)\">
<input type='hidden' name='seftitle' value='$seftitle' size='53'><br />
<label>Topik</label><br /><select size='1' name='topik'>";

$hasil = $koneksi_db->sql_query("SELECT id,topik FROM `topik` ORDER BY topik");
while ($datas =  $koneksi_db->sql_fetchrow ($hasil)){
	$admin .= '<option value="'.$datas['id'].'">'.$datas['topik'].'</option>';
}

$admin .="
</select><br />
<label>Isi Artikel</label><br /><textarea name='message' cols='40' rows='20' id='textarea1'>$textarea</textarea><br />
<label>Tags</label><br /><input type='text' name='tags' size='53' value='$tags'><br />
<label>Gambar</label><br /><input type='file' name='gambar' size='53'><br /><br />
<input type='submit' value='Kirim' name='submit'>
</form></div>";
}

#############################################
# Edit Berita
#############################################
if($_GET['aksi']=="edit_berita"){

$id     = int_filter($_GET['id']);
$topik 	= int_filter($_GET['topik']);

$admin .='<div class="border">';
$admin .='<b>Edit Artikel Dengan Id ='.$id.'</b>';
$admin .='</div>';

if (isset($_POST['submit'])){
	
define("GIS_GIF", 1);
define("GIS_JPG", 2);
define("GIS_PNG", 3);
define("GIS_SWF", 4);

include "includes/hft_image.php";

$judul     = text_filter($_POST['judul']);
$konten  = text_filter($_POST['message']);
$namafile_name = $_FILES['gambar']['name'];
$error = '';
if (!$judul)  $error .= "Error: Please enter Title of News!<br />";
if (!$konten) $error .= "Error: Please enter Content of News!<br />";

if ($error){
$admin.='<div class="error">'.$error.'</div>';
}else{

if (!empty ($namafile_name)){

    $files = $_FILES['gambar']['name'];
    $tmp_files = $_FILES['gambar']['tmp_name'];
    $namagambar = md5 (rand(1,100).$files) .'.jpg';
    $tempnews 	= 'mod/news/images/temp/';
    $uploaddir = $tempnews . $namagambar; 
    $uploads = move_uploaded_file($tmp_files, $uploaddir);
	if (file_exists($uploaddir)){
		@chmod($uploaddir,0644);
	}
	
	$tnews 		= 'mod/news/images/thumb/';
	$gnews 		= 'mod/news/images/normal/';
    $small 	= $tnews . $namagambar;
	$nsmall = $gnews . $namagambar;
    

	create_thumbnail ($uploaddir, $nsmall, $new_width = 415, $new_height = 'auto', $quality = 85);

    $judul  = $_POST['judul'];
    $konten = $_POST['message'];
    $seftitle      = $_POST['seftitle'];
    $topik  = $_POST['topik'];
    $gambar = $_POST['gambarlama'];
    $tags = cleantext($_POST['tags']);

    //masukkan data

    $hasil = $koneksi_db->sql_query( "UPDATE artikel SET judul='$judul', konten='$konten', seftitle='$seftitle', topik='$topik', tags='$tags', gambar='$namagambar' WHERE id='$id'" );
    if($hasil){
    $admin.='<div class="sukses">Berhasil memasukkan berita dg judul <u>' . stripslashes ($_POST['judul']) . ' </u></div>';
	unlink($uploaddir);
	unlink($tnews.$gambar);
	unlink($gnews.$gambar);
    }
}else{

    $judul       = $_POST['judul'];
    $konten      = $_POST['message'];
    $seftitle      = $_POST['seftitle'];
    $topik       = $_POST['topik'];
    $tags 		 = cleantext($_POST['tags']);

     //masukkan data

    $hasil = $koneksi_db->sql_query( "UPDATE artikel SET judul='$judul', konten='$konten', seftitle='$seftitle', topik='$topik', tags='$tags' WHERE id='$id'" );
    if($hasil){
    $admin.='<div class="sukses">Berhasil memasukkan berita dg judul <u>' . stripslashes ($_POST['judul']) . ' </u></div>';
    }

}

}

}

$hasil = $koneksi_db->sql_query( "SELECT * FROM artikel WHERE id=$id" );
$data = $koneksi_db->sql_fetchrow($hasil);

	$judul     	= $data['judul'];
	$konten  	= $data['konten'];
	$topik     	= $data['topik'];
	$tags     	= $data['tags'];
	$gambarlama = $data['gambar'];
	$seftitle   = $data['seftitle'];

$admin .='<div class="border">';
$admin .="
<form method='post' action='' enctype ='multipart/form-data' id='posts'>
<label>Judul</label><br /><input type='text' name='judul' value='".$judul."' size='53' onkeyup=\"genSEF(this,document.forms['posts'].seftitle)\" onchange=\"genSEF(this,document.forms['posts'].seftitle)\"><br />
<input type='hidden' name='seftitle' value=\"$seftitle\" size='53'>
<label>Topik</label><br /><select size='1' name='topik'>";

$hasil = $koneksi_db->sql_query("SELECT id,topik FROM `topik` ORDER BY topik");
while ($datas =  $koneksi_db->sql_fetchrow ($hasil)){

$pilihan = ($datas[0]==$topik)?"selected":'';
$admin .='<option value="'.$datas['id'].'" '.$pilihan.'>'.$datas['topik'].'</option>';

}

$admin .="
</select><br />
<label>Isi Artikel</label><br /><textarea name='message' cols='40' rows='20' id='textarea1'>".$konten."</textarea><br />
<label>Tags</label><br /><input type='text' name='tags' size='53' value=\"$tags\"><br />
<label>Gambar</label><br /><input type='file' name='gambar' size='53'><input type='hidden' name='gambarlama' size='53' value=\"$gambarlama\"><br />
<br />
<input type='submit' value='Submit' name='submit'></form>";
$admin .='</div>';
}

#############################################
# Hapus Berita
#############################################
if($_GET['aksi']=="hapus_berita"){

global $koneksi_db;
$hasil = $koneksi_db->sql_query( "SELECT * FROM artikel WHERE id=$id" );
$data = $koneksi_db->sql_fetchrow($hasil);
$gambar  	= $data['gambar'];
unlink($gambar);

$id     = int_filter($_GET['id']);
$topik 	= int_filter($_GET['topik']);

$koneksi_db->sql_query("DELETE FROM artikel WHERE id='$id'");
$koneksi_db->sql_query("DELETE FROM komentar WHERE artikel='$id'");
}

#############################################
# List Komentar
#############################################
if($_GET['aksi']=="list_komentar"){
$admin .= '<div class="right_post">Arsip Komentar</div>';
$admin .= '<div class="border"><form method="get" class="searchform" action="admin.php">
<p><input type="text" name="query" class="textbox">
<input type="submit" name="submit" class="button" value="Search">
<input type="hidden" name="pilih" value="news">
<input type="hidden" name="mod" value="yes">
<input type="hidden" name="aksi" value="listkomentar">
</form></div>';
$query = $_GET['query'];
$limit = 10;
if (empty($_GET['query']) and !isset ($_GET['query'])) {
$total = $koneksi_db->sql_query( "SELECT * FROM komentar order by id desc ");
}else{
$total = $koneksi_db->sql_query( "SELECT * FROM komentar WHERE judul like '%$query%'or konten like '%$query%' order by id desc ");
}

$jumlah = $koneksi_db->sql_numrows( $total );
$a = new paging ($limit);

if ($jumlah<1){
$admin.='<div class="error">Tidak Ada komentar </div>';
}else{
if (empty($_GET['query']) and !isset ($_GET['query'])) {
$hasil = $koneksi_db->sql_query( "SELECT * FROM komentar ORDER BY id DESC LIMIT $offset,$limit" );
}else{
$hasil = $koneksi_db->sql_query( "SELECT * FROM komentar WHERE judul like '%$query%' or konten like '%$query%' ORDER BY id DESC LIMIT $offset,$limit" );
}

$admin .='<div class="border"><table width="100%" cellspacing="0" cellpadding="4">';
while ($data = $koneksi_db->sql_fetchrow($hasil)) {

		$admin .="<tr><td valign=top width=20%><P><b>$data[1]</b><br />";
		$tgl=explode(",",$data[5]);
		$tgl[0]=strtr($tgl[0],$translateKal);
		$tgl[2]=strtr($tgl[2],$translateKal);
		$data_tgl="$tgl[0], $tgl[1] $tgl[2] ";
		$admin .="<span>$data[5] - oleh : <a href=\"mailto:$data[4]\">$data[3]</a></span></p></td>";
		$admin .="<td valign=top>$data[2]&nbsp;</td>";
		$admin .="<td valign=top width=6% align=center><a href=admin.php?pilih=news&amp;mod=yes&aksi=hapus_komentar&id=$data[0]>delete</a></td><td valign=top width=6% align=center></td></tr>";

	}
$admin .="</table>";
$admin .='</div>';
}

if($jumlah>10){

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
# Hapus Komentar
#############################################
if($_GET['aksi']=="hapus_komentar"){
global $koneksi_db;
$id = int_filter($_GET['id']);
$koneksi_db->sql_query("DELETE FROM komentar WHERE id='$id'");
}

echo $admin;

?>