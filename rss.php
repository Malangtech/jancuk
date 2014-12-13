<?php
ob_start();
header("content-type: text/xml; charset=utf-8");

include 'tambahan/itile.php';
include 'tambahan/mysql.php';
include 'tambahan/feedcreator.class.php'; 

global $koneksi_crot,$url_cuk,$judul_cuk,$slogan;

$hasil =  $koneksi_crot->sql_query("SELECT * FROM setting");
$data = $koneksi_crot->sql_fetchrow($hasil);
$email_master	= $data['email_master'];
$judul_cuk 	    = $data['judul_situs'];
$url_cuk 		= $data['url_situs'];
$slogan			= $data['slogan'];
$description	= $data['description'];
$keywords		= $data['keywords'];

$_GET['aksi'] = isset($_GET['aksi']) ? $_GET['aksi'] : 'rss20';
$rss = new UniversalFeedCreator(); 
$rss->useCached(); 
$rss->title 			= $judul_cuk; 
$rss->description 		= $slogan; 
$rss->link 				= $url_cuk; 
$rss->feedURL 			= $url_cuk."/".$_SERVER['PHP_SELF'];
$rss->syndicationURL	= $url_cuk; 
$rss->cssStyleSheet		= NULL; 

$image = new FeedImage(); 
$image->title 		= $slogan; 
$image->url 		= $url_cuk."/images/browser-48x48.png"; 
$image->link 		= $url_cuk; 
$image->description 	= "Feed provided by Jancuk CMS. Click to visit."; 

$rss->image = $image; 

// Ngambil dari database 
$hasil = $koneksi_crot->sql_query( "SELECT * FROM berita WHERE publikasi=1 ORDER BY id DESC LIMIT 10" );
while ($data = $koneksi_crot->sql_fetchrow($hasil)) {
$id	  		= $data['id'];
$tanggal	= $data['tanggal'];		
$judul 		= $data['judul'];
$konten		= $data['konten'];
$author		= $data['user'];

$item = new FeedItem(); 
$item->title 		= $judul;
$item->link 		= $url_cuk."/lihat/".$id."/".SEO($judul).".html";
$item->description 	= limitTXT(strip_tags($konten),250); 	
$item->date   		= strtotime($tanggal); 
$item->source 		= $url_cuk;
$item->author 		= $author;
$rss->addItem($item); 
} 

$rss->outputFeed("RSS2.0");

?>