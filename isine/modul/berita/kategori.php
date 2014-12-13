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

ob_start();

global $koneksi_db,$maxdata,$url_situs;

$hasil = $koneksi_db->sql_query("SELECT * FROM berita_kat WHERE id_parent='0' ORDER BY kategori ASC");
echo '<ul>';
while ($data = $koneksi_db->sql_fetchrow($hasil)) {
$kid		= $data['kid'];
$kategori	= $data['kategori'];

$total  = $koneksi_db->sql_query("SELECT * FROM berita WHERE publikasi=1 and kid='$kid'");
$jumlah = $koneksi_db->sql_numrows($total);

echo '<li><a href="'.$url_situs.'/'.get_link($kid,$kategori,"kategori").'" title="'.$kategori.'">'.$kategori.' ('.$jumlah.')</a></li>';

$hasils = $koneksi_db->sql_query("SELECT * FROM berita_kat WHERE id_parent='$kid' ORDER BY kategori ASC");
echo '<ul class="submenu">';
while ($datas = $koneksi_db->sql_fetchrow($hasils)) {
$kids		= $datas['kid'];
$kategoris	= $datas['kategori'];

$totals  = $koneksi_db->sql_query("SELECT * FROM berita WHERE publikasi=1 and kid='$kids'");
$jumlahs = $koneksi_db->sql_numrows($totals);

echo '<li><a href="'.$url_situs.'/'.get_link($kids,$kategoris,"kategori").'" title="'.$kategoris.'">'.$kategoris.' ('.$jumlahs.')</a></li>';
}
echo '</ul>';
}
echo '</ul>';

$out = ob_get_contents();
ob_end_clean();

?>