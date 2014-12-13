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

$perintah = "SELECT * FROM mod_download ORDER BY hit DESC LIMIT 0,5";
$hasil    = mysql_query( $perintah );

echo '<ul>';
while ($data = mysql_fetch_array($hasil)) {
$id = $data['id'];
$judul = $data['judul'];

echo '<li><a href="'.$url_situs.'/'.get_link($id,$judul,"download").'" target="_blank">'.$data['judul'].' ('.$data['hit'].')</a></li>';
	
}
echo '</ul>';

$out = ob_get_contents();
ob_end_clean();

?>