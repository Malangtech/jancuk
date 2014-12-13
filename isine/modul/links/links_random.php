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

global $koneksi_db;

$query = $koneksi_db->sql_query( "SELECT * FROM links ORDER BY RAND() LIMIT 1");
$data = $koneksi_db->sql_fetchrow($query);
$id    		= $data['id'];
$judul 		= $data['judul'];	
$url   		= $data['url'];
$hit   		= $data['hit'];
$tanggal	= datetimes($data['tanggal']);

echo '<div align="center">
<a href="'.$url_situs.'/'.get_link($id,$judul,"links").'" target="_blank" title="'.htmlentities($judul).'">'.htmlentities($judul).'<br />
<img class="img-thumbnail" src="http://api.webthumbnail.org?width=150&height=100&screen=1024&url='.$url.'" alt="'.$judul.'"></a>
<br />Dilihat '.$hit.' kali<br />'.$tanggal.'<br /></div>';

$out = ob_get_contents();
ob_end_clean();

?>