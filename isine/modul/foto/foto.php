<?php

#############################################
# Teamworks Content Management System
# http://www.teamworks.co.id
# 23 Februari 2014
# Author: webmaster@teamworks.co.id
#############################################

if (!defined('CMS_MODULE')) {
    Header("Location: ../../index.php");
    exit;
}

//$index_hal=1;

$tengah .='<h4 class="bg">Galeri Foto</h4>';

global $maxdata,$maxgalleri,$maxgalleridata;

$style_include[] = '<style type="text/css">
@import url("'.$url_situs.'/mod/photo/lightbox/css/lightbox.css");
@import url("'.$url_situs.'/mod/photo/css/photo.css");
</style>';
$script_include[] = '
<script src="/mod/photo/lightbox/js/prototype.js" type="text/javascript"></script>
<script src="/mod/photo/lightbox/js/scriptaculous.js?load=effects,builder" type="text/javascript"></script>
<script src="/mod/photo/lightbox/js/lightbox.js" type="text/javascript"></script>';

if (isset ($_GET['pg'])) $pg = int_filter ($_GET['pg']); else $pg = 0;
if (isset ($_GET['stg'])) $stg = int_filter ($_GET['stg']); else $stg = 0;
if (isset ($_GET['offset'])) $offset = int_filter ($_GET['offset']); else $offset = 0;

#############################################
# List Foto
#############################################
if($_GET['aksi'] ==''){

$tengah .= '<div class="border"><table width="100%" border="0" cellspacing="0" cellpadding="0"><tr>';
$no =0;
$s = mysql_query ("SELECT * FROM foto_kat ORDER BY kategori ASC");	
while($data = mysql_fetch_array($s)){
$urutan = $no + 1;
$kategori = $data['kategori'];
$kid = $data['kid'];
$gambar = $data['gambar'];
$s2 = mysql_query ("SELECT * FROM foto where kategori='$data[kid]'");	
$jumlah = $koneksi_db->sql_numrows( $s2 );
if(!$gambar){
$gambar = 'photo-default.jpg';
}
$tengah .= '<td align="center">
<div id="album-category">
<div class="category-foto">
<a href="/'.get_link($kid,$kategori,"album").'">
<div class="foto-bg">
<div class="foto-thumb">
<img src="mod/photo/images/normal/'.$gambar.'" border="0" width="130" height="90">
</div>
</div>
</div>
<div class="category-title">
<span class="category-title-h">'.$kategori.'</span></a><br>
<span class="category-title-j">'.$jumlah.') foto</span>
</div>
</div>
</td>';
if ($urutan  % 4 == 0) {
$tengah .= '</tr></tr>';
}
$no++;
}
$tengah .= '</table>';
$tengah .= '</div>';
}

#############################################
# Kategori Foto
#############################################
if($_GET['aksi'] =='photo'){

$kid     = int_filter($_GET['kid']);

$hasil =  $koneksi_db->sql_query( "SELECT * FROM photo_kat where kid='$kid' " );
$data = $koneksi_db->sql_fetchrow($hasil);
$kategori=$data['kategori'];
$tengah .='<div class="border"><a href="'.$url_situs.'/photo.html">Home</a> &raquo; '.$kategori.'</div>';
$tengah .= '<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr>';
$no =0;

$limit = 50;
$total = mysql_query ("SELECT * FROM `photo` where kategori='$kid'");	
$jumlah = $koneksi_db->sql_numrows( $total );
if (!isset ($_GET['offset'])) {
	$offset = 0;
}
$a = new paging ($limit);

//$hasil =  $koneksi_db->sql_query( "SELECT * FROM photo_kat where kid='$kid' " );
//$data = $koneksi_db->sql_fetchrow($hasil);

$s = $koneksi_db->sql_query("SELECT * FROM `photo` where kategori='$kid' ORDER BY `id` DESC LIMIT $offset,$limit");	
if($offset){
$no = $offset;
}else{
$no = 1;
}
$urutan = 1;
while($data = $koneksi_db->sql_fetchrow($s)){

$kategori = $data['kategori'];
$id = $data['id'];
$judul = $data['judul'];
$ket = $data['ket'];
$gambar = $data['gambar'];
if(!$gambar){
$gambar = 'photo-default.jpg';
}
if($lightbox=="on"){
$tengah .= '<td align="center" valign="top"><a href="'.$url_situs.'/mod/photo/images/normal/'.$gambar.'" rel="lightbox">
<img src="'.$url_situs.'/mod/photo/images/normal/'.$gambar.'" alt="'.$gambar.'" style="margin-right:10px; margin-top:5px; padding: 3px; border:1px solid #dddddd; background:#fff; float:left;" border="0" width="120" height="80"></a>
</td>';
}else{
$tengah .= '<td align="center" valign="top"><a href="'.$url_situs.'/'.get_link2($id,$kid,$judul,"photo").'">
<img src="'.$url_situs.'/mod/photo/images/normal/'.$gambar.'" alt="'.$gambar.'" style="margin-right:10px; margin-top: 5px; padding: 3px; border:1px solid #dddddd; background:#fff; float:left;" border="0" width="120" height="80"></a>
</td>';
}
if ($urutan  % 4 == 0) {
$tengah .= '</tr></tr>';
}
$no++;
$urutan++;
}
$tengah .= '</table>';

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
$tengah .= '<center>';
$tengah .= $a-> getPaging($jumlah, $pg, $stg);
$tengah .= '</center>';
}
}

#############################################
# Detail Foto
#############################################
if($_GET['aksi'] =='detail'){

$id = int_filter($_GET['id']);

$s = $koneksi_db->sql_query("SELECT * FROM foto WHERE id=$id");	
$datas = $koneksi_db->sql_fetchrow($s);
$kid = $datas['kid'];

$ss = $koneksi_db->sql_query("SELECT * FROM foto_kat WHERE kid=$kid");	
$datass = $koneksi_db->sql_fetchrow($ss);
$kids = $datass['kid'];
$kategori = $datass['kategori'];

$tengah .= '<ol class="breadcrumb">
<li><a href="'.$url_situs.'/foto.html">Home</a></li>
<li><a href="'.$url_situs.'/'.get_link($kids,$kategori,"album").'">'.$kategori.'</a></li>
<li class="active">'.$kategori.'</li>
</ol>';

$no =0;
$hasil = $koneksi_db->sql_query("SELECT * FROM foto where id='$id'");	
$data = $koneksi_db->sql_fetchrow($hasil);
$urutan = $no;
$kategori = $data['kategori'];
$id = $data['id'];
$judul = $data['judul'];
$ket = $data['ket'];
$gambar = $data['gambar'];
if(!$gambar){
$gambar = 'photo-default.jpg';
}
$sp = $koneksi_db->sql_query("SELECT * FROM foto WHERE id<'$id' ORDER by id DESC");	
$datasp = $koneksi_db->sql_fetchrow($sp);
$idsp = $datasp['id'];
$judulsp = $datasp['judul'];

$sn = $koneksi_db->sql_query("SELECT * FROM foto WHERE id>'$id' ORDER by id ASC");	
$datasn = $koneksi_db->sql_fetchrow($sn);
$idsn = $datasn['id'];
$judulsn = $datasn['judul'];

if(!$idsp){
$prev ="";
}else{
$prev ='<span class="text-info"><a href="/'.get_link($idsp,$judulsp,"foto").'">Sebelumnya</a></span>';
}
if(!$idsn){
$next ="";
}else{
$next ='<span class="text-info"><a href="/'.get_link($idsn,$judulsn,"foto").'">Selanjutnya</a></span>';
}
$tengah .= '<div class="alert alert-info text-center">'.$prev.' - '.$next.'</div>';
$tengah .= '<img class="img-responsive" src="'.$url_situs.'/id-content/modul/foto/images/normal/'.$gambar.'" alt="'.$judul.'">
<h1>'.$judul.'</h1>'.$ket.'';
}

echo $tengah;

?>