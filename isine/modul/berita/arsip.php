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

//include 'id-content/modul/berita/include/function.php';

$hasil = $koneksi_db->sql_query("SELECT date_format(tanggal, '%Y.%m' ) AS date FROM berita WHERE publikasi = 1 GROUP BY date DESC LIMIT 15");
echo '<ul>';
while ($data = $koneksi_db->sql_fetchrow($hasil)) {
list($tahun,$bulan) = explode('.',$data['date']);
$quer = $koneksi_db->sql_query("SELECT count(id) AS total FROM berita WHERE month(tanggal) = '$bulan' AND year(tanggal) = '$tahun' AND publikasi = 1");
$tot = $koneksi_db->sql_fetchrow($quer);
$total = $tot['total'];
echo '<li><a href="'.$url_situs.'/arsip/'.$data['date'].'.html">'.kebulan($bulan).' '.$tahun.' ('.$total.') </a></li>';
}
echo '</ul>';

$out = ob_get_contents();
ob_end_clean();

?>