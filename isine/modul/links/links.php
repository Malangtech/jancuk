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

$tengah ='<h4 class="bg">Links Website</h4>';

#############################################
# List Berita
#############################################
if($_GET['aksi']==""){

//title 
$judul_situs = 'Links Website | '.$judul_situs.'';

$tengah .= '<div class="row">';
$hasil = $koneksi_db->sql_query("SELECT * FROM links_kat ORDER BY kid DESC");
while ($data = $koneksi_db->sql_fetchrow($hasil)) {
$kid		= $data['kid'];
$kategori	= $data['kategori'];
$keterangan	= $data['keterangan'];

$total =  $koneksi_db->sql_query("SELECT * FROM links WHERE kid=$kid");
$jumlah = $koneksi_db->sql_numrows($total);

$tengah .= '<div class="col-md-6">
<a href="'.$url_situs.'/'.get_link($kid,$kategori,"k-links").'"><strong>'.$kategori.'</strong></a> <span class="badge">'.$jumlah.'</span><br>
<span class="text-muted">'.$keterangan.'</span>
</div>';
}
$tengah .= '</div>';
}

echo $tengah;

?>