<?php
//include 'tambahan/mysql.php';
 
$hasil					= $koneksi_crot->sql_query("SELECT * FROM setting");
$data 					= $koneksi_crot->sql_fetchrow($hasil);
$email_master			= $data['email_master'];
$judul_cuk 				= $data['judul_situs'];
$url_cuk 				= $data['url_situs'];
$slogan					= $data['slogan'];
$description			= $data['description'];
$keywords				= $data['keywords'];
$foldersitus			= $data['foldersitus'];
$_META['description'] 	= $description;
$_META['keywords'] 		= $keywords;
$theme					= $data['theme'];
$author					= $data['author'];
$_META['author']		= $author;
$alamatkantor			= $data['alamatkantor'];
$publishwebsite			= $data['publishwebsite'];
$publishnews			= $data['publishnews'];
$tags					= $_META['keywords'];
$maxgalleridata 		= $data['maxgalleridata'];
$lightbox				= $data['lightbox'];

?>