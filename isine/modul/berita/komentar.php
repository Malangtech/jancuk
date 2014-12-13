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

$hasil 	= $koneksi_db->sql_query("SELECT * FROM berita_komentar ORDER BY tgl DESC LIMIT 10");
while ($data = $koneksi_db->sql_fetchrow($hasil)) {

$hasilb = $koneksi_db->sql_query( "SELECT * FROM berita WHERE id=".$data['berita']."");
$datab = $koneksi_db->sql_fetchrow($hasilb);

echo '<div class="komentar_terbaru"><a href="'.$url_situs.'/'.get_link($datab['id'],$datab['judul'],lihat).'#respon-'.$data['id'].'" title="'.$datab['judul'].'">'.$data['nama'].'</a> on 
<a href="'.$url_situs.'/'.get_link($datab['id'],$datab['judul'],lihat).'#respon-'.$data['id'].'" title="'.$datab['judul'].'">'.$datab['judul'].'</a></div>';
}

$out = ob_get_contents();
ob_end_clean();

?>