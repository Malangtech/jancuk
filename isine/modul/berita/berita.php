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

global $koneksi_db,$maxdata,$maxkonten,$maxgalleri,$maxadmindata,$widgetnews;

//$index_hal=1;

if (isset ($_GET['pg'])) $pg = int_filter ($_GET['pg']); else $pg = 0;
if (isset ($_GET['stg'])) $stg = int_filter ($_GET['stg']); else $stg = 0;
if (isset ($_GET['offset'])) $offset = int_filter ($_GET['offset']); else $offset = 0;

#############################################
# List Berita
#############################################
if($_GET['aksi']==""){

$tengah .= '<h4 class="bg">Berita Terbaru</h4>';

//title 
$judul_situs = 'Berita Terbaru | '.$judul_situs.'';

$tengah .= '<div class="border" style="text-align:center;">
<form method="get" class="searchform" action="index.php">
<p><input type="text" name="query" class="textbox">
<input type="submit" name="submit" class="button" value="Search">
<input type="hidden" name="pilih" value="search"></p>
</form></div>';

$hitungjumlah = $koneksi_db->sql_query( "SELECT * FROM berita WHERE publikasi=1");
$jumlah = $koneksi_db->sql_numrows($hitungjumlah);
$limit	= 10;
//$ada = new paging ($limit);
$ada = new paging_s ($limit,'berita-hal');
$tengah .= '<table cellspacing="0" style="width:100%">';
$hasil = $koneksi_db->sql_query( "SELECT * FROM berita WHERE publikasi=1 ORDER BY tanggal DESC LIMIT $offset, $limit" );
while ($data = $koneksi_db->sql_fetchrow($hasil)) {
$id			= $data['id'];
$tanggal	= datetimes($data['tanggal']);
$judul		= $data['judul'];
$konten		= $data['konten'];
$user		= $data['user'];
$hits		= $data['hits'];
$gambar = ($data['gambar'] == '') ? 
'<img class="img-thumbnail" style="float:left; margin:0 10px 0 0;" src="'.$url_situs.'/id-content/modul/berita/images/thumb_default.jpg" alt="'.$data['judul'].'">' : 
'<img class="img-thumbnail" style="float:left; margin:0 10px 0 0;" src="'.$url_situs.'/id-content/modul/berita/images/thumb/'.$data['gambar'].'" alt="'.$data['judul'].'">';

$tot_komen =  $koneksi_db->sql_query("SELECT * FROM berita_komentar WHERE berita=$id");
$jum_komen = $koneksi_db->sql_numrows($tot_komen);

$tengah .= '<tr><td style="border-bottom:1px dashed #dddddd; padding:5px 0px 10px 0px;">
<a href="'.$url_situs.'/'.get_link($id,$judul,"lihat").'"><h4>'.$judul.'</h4></a>
<span class="text-muted">'.$tanggal.' - Dibaca '.$data['hits'].' kali - <a href="'.$url_situs.'/'.get_link($id,$judul,"lihat").'#respon">'.$jum_komen.' Komentar</a></span>
'.$gambar.'<p>'.limitTXT(strip_tags($konten),400).'</p></td>
</tr>';
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
	
$tengah .='<center>';
$tengah .= $ada-> getPaging($jumlah, $pg, $stg);
$tengah .='</center>';
}
}

#############################################
# Detail Berita
#############################################
if($_GET['aksi']=="lihat"){

$id 	= $_GET['id'];
$judul 	= $_GET['judul'];

$hasil  = $koneksi_db->sql_query("SELECT * FROM berita WHERE publikasi=1 and id='$id'");
$data = $koneksi_db->sql_fetchrow($hasil);
$tanggal	= datetimes($data['tanggal']);
$judul		= $data['judul'];
$kid		= $data['kid'];
$konten		= $data['konten'];
$hits		= $data['hits'];
$gambar = ($data['gambar'] == '') ? '' : '<div style="text-align:center; background:#f2f2f2;"><img style="max-height:400px;" src="'.$url_situs.'/id-content/modul/berita/images/normal/'.$data['gambar'].'" class="img-responsive" alt="'.$data['judul'].'"></div>
<div style="font-size:11px; padding:10px 0 10px 0; color:#666666;">'.$data['caption'].'</div>';

// Title Website
$judul_situs = ''.$judul.' | '.$judul_situs.'';
$_META['description'] = limittxt(htmlentities(strip_tags($data['konten'])),500);
$_META['keywords'] = empty($data['tags']) ? implode(',',explode(' ',htmlentities(strip_tags($data['judul'])))) : $data['tags'];

if (empty ($judul)){
$tengah.='<div class="alert alert-danger">Halaman yang Anda cari tidak ditemukan</div>';
$tengah .='<meta http-equiv="refresh" content="5; url=index.php">';
}else {
$hits = $hits +1;
$updatehits = $koneksi_db->sql_query("UPDATE berita SET hits='$hits' WHERE id='$id'");

# Kategori
$k = $koneksi_db->sql_query( "SELECT * FROM berita_kat WHERE kid='$kid'" );
$datak = $koneksi_db->sql_fetchrow($k);
$kidk	= $datak['kid'];
$kat	= $datak['kategori'];
# Kategori

# Author
$u = $koneksi_db->sql_query( "SELECT * FROM users WHERE user='".$data['user']."'" );
$datau = $koneksi_db->sql_fetchrow($u);
$UserId	= $datau['UserId'];
$user	= $datau['user'];
# Author

$tengah .='<ol class="breadcrumb">
<li><a href="'.$url_situs.'">Home</a></li>
<li><a href="'.$url_situs.'/'.get_link($kidk,$kat,"kategori").'">'.$kat.'</a></li>
<li class="active">'.$data['judul'].'</li></ol>';
$tengah .= '<h4>'.$judul.'</h4>';
$tengah .= '<div class="text-muted">'.$tanggal.' - Oleh <a href="'.$url_situs.'/'.get_link($UserId,$user,"pesan").'" title="'.$user.'">'.$user.'</a> - Dibaca '.$data['hits'].' kali</div>';
$tengah .= ''.$gambar.''.$konten.'';

# Tags
$hasit = $koneksi_db->sql_query("SELECT tags FROM berita WHERE id='$id' AND publikasi = 1");
$TampungData = array();
while ($datat = $koneksi_db->sql_fetchrow($hasit)) {
$tags = explode(',',strtolower(trim($datat['tags'])));
foreach($tags as $val) {
$TampungData[] = $val;
}
}

$totalTags = count($TampungData);
$jumlah_tag = array_count_values($TampungData);
ksort($jumlah_tag);
if ($totalTags > 0) {
$output = array();

foreach($jumlah_tag as $key=>$val) {
$output[] = '<a href="'.$url_situs.'/'.get_tags(urlencode($key),tags).'" title="'.$key.'"><span class="label label-success">#'.$key.'</span></a>';
}
$tags = implode(' ',$output);
}
$tengah .= '<p class="text-info">Tags '.$tags.'</p>';
if($data['sumber']==''){
$tengah .= '';
}else{
$tengah .= '<p class="text-warning">Sumber : <a href="'.singkatURL($data['sumber']).'" target="_blank">'.singkatURL($data['sumber']).'</a></p>';
}
# Tags

////////////ADD THIS////////////////////////////////////
$hasilw =  $koneksi_db->sql_query( "SELECT * FROM widget where id=$widgetnews " );
$dataw = $koneksi_db->sql_fetchrow($hasilw);
$widget=$dataw[2];
$tengah .='<div class="border">'.$widget.'</div>';
///////////////////////////////////////////////////////

# Berita Terkait
$query = $koneksi_db->sql_query( "SELECT * FROM berita_kat WHERE kid='$kid'" );
while ($data1 = $koneksi_db->sql_fetchrow($query)) {
$rubrik=$data1[1];
}
$hitungjumlah = $koneksi_db->sql_query( "SELECT id FROM berita WHERE id!='$id' AND publikasi=1 and topik='$topik'");
$jumlah = $koneksi_db->sql_numrows($hitungjumlah);

$tengah .='<h4 class="page-header">Baca "'.$kat.'" Lainnya</h4>';
$hasill = $koneksi_db->sql_query( "SELECT * FROM berita WHERE id!='$id' and publikasi=1 and kid='$kid' ORDER BY tanggal DESC LIMIT 5" );
$tengah .= '<table width="100%" cellspacing="0" cellpadding="0">';
while ($datal = $koneksi_db->sql_fetchrow($hasill)) {
$id2	= $datal['id'];
$judul2	= $datal['judul'];

$tengah .= '<tr><td style="padding:4px 0px 4px 0px; border-bottom:1px dashed #dddddd;"><a href="'.$url_situs.'/'.get_link($id2,$judul2,"lihat").'">'.$judul2.'</a></td></tr>';
}
$tengah .= '</table>';

#############################################
# Komentar Berita
#############################################
$total =  $koneksi_db->sql_query("SELECT * FROM berita_komentar WHERE berita=$id");
$jumlah = $koneksi_db->sql_numrows( $total );

$tengah .= '<h4 class="page-header" id="respon">'.$jumlah.' Komentar</h4>';
$hitungjumlah = $koneksi_db->sql_query( "SELECT * FROM berita_komentar WHERE berita=$id");
$jumlah = $koneksi_db->sql_numrows($hitungjumlah);
$limit	= 5;
$ada = new paging ($limit);
$hasil = $koneksi_db->sql_query( "SELECT * FROM berita_komentar WHERE berita=$id and balas=0 ORDER BY tgl ASC LIMIT $offset, $limit" );
while ($datak = $koneksi_db->sql_fetchrow($hasil)) {
$nama_pengirim = ($datak['website'] == '') ? '<strong id="respon-'.$datak['id'].'">'.$datak['nama'].'</strong>' : '<a href="http://'.$datak['website'].'" title="'.$datak['ip'].'" target="_blank"><strong id="respon-'.$datak['id'].'">'.$datak['nama'].'</strong></a>';

$tengah .= '<img style="margin:0 10px 0 0;" class="img-circle pull-left" src="'.get_gravatar($datak['email'], $s = 50).'">
'.$nama_pengirim.'<br>
<font style="font-size:11px; color:#888888;">'.datetimes($datak['tgl']).'</font><br>
'.$datak['komentar'].'<hr>';
# Blas Komentar
$hasilb = $koneksi_db->sql_query( "SELECT * FROM berita_komentar WHERE berita=$id and balas=".$datak['id']." ORDER BY tgl ASC LIMIT $offset, $limit" );
while ($datab = $koneksi_db->sql_fetchrow($hasilb)) {
$nama_pengirimm = ($datab['website'] == '') ? '<strong id="respon-'.$datab['id'].'">'.$datab['nama'].'</strong>' : '<a href="http://'.$datab['website'].'" title="'.$datab['ip'].'" target="_blank"><strong id="respon-'.$datab['id'].'">'.$datab['nama'].'</strong></a>';

$tengah .= '<img style="margin:0 10px 0 60px;" class="img-circle pull-left" src="'.get_gravatar($datab['email'], $s = 50).'">
'.$nama_pengirimm.'<br>
<font style="font-size:11px; color:#888888;">'.datetimes($datab['tgl']).'</font><br>
'.$datab['komentar'].'<hr>';
}
}

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
$tengah .='<div class="border"><center>';
$tengah .= $ada-> getPaging($jumlah, $pg, $stg);
$tengah .='</center></div>';
}

if(isset($_POST['submit'])){

$nama 		= $_POST['nama'];
$email 		= $_POST['email'];
$website 	= $_POST['website'];
$komentar 	= $_POST['komentar'];
$tgl 		= date('Y-m-d H:i:s');
$ip      	= getenv("REMOTE_ADDR");
$gfx_check	= $_POST['gfx_check'];

if (!$nama) 	$error .= "Error: Nama belum diisi!<br />";
if (!$email) 	$error .= "Error: Email belum diisi!<br />";
if (!$komentar) $error .= "Error: Komentar belum diisi!<br />";
if ($gfx_check != $_SESSION['Var_session'] or !isset($_SESSION['Var_session'])) {
$error .= "Error: Kode keamanan salah!<br />";
}

if ($error){
$tengah.='<div class="alert alert-danger">'.$error.'</div>';
}else{
$hasil = $koneksi_db->sql_query( "INSERT INTO berita_komentar (berita,ip,tgl,nama,email,website,komentar) VALUES ('$id','$ip','$tgl','$nama','$email','$website','$komentar')" );
if($hasil){
$tengah.='<div class="alert alert-success">Komentar berhasil dikirim!</div>';
$tengah .='<meta http-equiv="refresh" content="1; url='.$url_situs.'/'.get_link($id,$judul,"lihat").'">';
}
}
}

$tengah .= '<h4 class="page-header" id="respon">Tinggalkan Komentar</h4>';
$tengah .= '<form method="post" action="#respon" enctype ="multipart/form-data">
<div class="form-group">
	<label>Nama</label>
	<input type="text" name="nama" value="'.$nama.'" class="form-control" placeholder="Nama">
</div>
<div class="form-group">
	<label>Email</label>
	<input type="text" name="email" size="53" value="'.$email.'" class="form-control" placeholder="Email">
</div>
<div class="form-group">
	<label>Website</label>
	<input type="text" name="website" size="53" value="'.$website.'" class="form-control" placeholder="www.namawebsite.com">
</div>
<div class="form-group">
	<label>Komentar</label>
	<textarea name="komentar" rows="3" class="form-control" placeholder="Komentar"></textarea>
</div>';
if (extension_loaded("gd")) {
$tengah .= '
<div class="form-group">
    <label>Kode Keamanan</label><br>
    <img style="border:1px dashed #cccccc;" src="'.$url_situs.'/id-includes/code_image.php" alt="Kode Keamanan">
</div>
<div class="form-group">
    <input type="text" name="gfx_check" maxlength="6" class="form-control" placeholder="Tulis kode di atas">
</div>';
}
$tengah .= '<div class="form-group">
	<input type="submit" value="Kirim Komentar" name="submit" class="btn btn-success">
</div>
</form>';
}
} 

#############################################
# Kirim Pesan
#############################################
if($_GET['aksi']=="pesan"){

$UserId = int_filter($_GET['UserId']);

$hasil = $koneksi_db->sql_query("SELECT * FROM users WHERE UserId = $UserId");
$data = $koneksi_db->sql_fetchrow($hasil);
$email_author = $data['email'];
$nama_author = $data['user'];

$tengah .='<h4 class="bg">Kirim Pesan Ke : '.$nama_author.'</h4>';

if (isset($_POST['submit'])) {

$nama = text_filter($_POST['nama']);
$email = text_filter($_POST['email']);
$subyek = text_filter($_POST['subyek']);
$pesan = nl2br(text_filter($_POST['pesan'], 2));
checkemail($email);
$gfx_check = intval($_POST['gfx_check']);
if (!$nama)  $error .= "Error: Please enter your name!<br />";
if (!$pesan) $error .= "Error: Please enter a message!<br />";
if (!$subyek) $error .= "Error: Please enter a Subject!<br />";
if ($gfx_check != $_SESSION['Var_session'] or !isset($_SESSION['Var_session'])) {
$error .= "Error: Kode keamanan salah!<br />";
}

if ($error) {
$tengah.='<div class="alert alert-danger">'.$error.'</div>';
} else {
$subject = "$sitename - Contact Form";
$msg = "$sitename - Contact Form<br /><br />Nama Pengirim: $nama<br />Email Pengirim: $email<br /><br />Pesan: $pesan";
mail_send($kontributor, "From: $nama - $email", $subyek, $pesan, 1, 1);
$tengah.='<div class="sukses"><p>Pesan Anda telah dikirim ke teman Anda.<br>Terima kasih mau mendistribusikan artikel di situs ini.</p></div>';
$tengah .='<meta http-equiv="refresh" content="3; url=?pilih=news&amp;mod=yes&aksi=lihat&amp;id='.$id.'">';
}
}
$tengah .='<div class="bg-info">Anda ingin mengirim pesan kepada '.$nama_author.'<br />
Silahkan isi formulir dibawah ini:</div>';

$nama 	= !isset($nama) ? '' : $nama;
$email 	= !isset($email) ? '' : $email;
$subyek = !isset($subyek) ? '' : $subyek;
$pesan 	= !isset($pesan) ? '' : $pesan;
$op 	= !isset($_GET['op']) ? '' : $_GET['op'];

$tengah .= '<form class="form-horizontal" method="POST" action="">
<div class="form-group">
    <label class="col-sm-2 control-label">Nama</label>
    <div class="col-sm-10"><input type="text" name="nama" value="'.$nama.'" class="form-control" placeholder="Masukkan nama lengkap Anda"></div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">Email</label>
    <div class="col-sm-10"><input type="text" name="email" value="'.$email.'" class="form-control" placeholder="Masukkan email Anda"></div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">Subyek</label>
    <div class="col-sm-10"><input type="text" name="subyek" value="'.$subyek.'" class="form-control" placeholder="Masukkan judul pesan Anda"></div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">Pesan</label>
    <div class="col-sm-10"><textarea name="pesan" rows="3" class="form-control">'.$pesan.'</textarea></div>
</div>';
if (extension_loaded("gd")) {
$tengah .= '
<div class="form-group">
    <label class="col-sm-2 control-label">Kode Keamanan</label>
    <div class="col-sm-10"><img style="border:1px dashed #cccccc;" src="'.$url_situs.'/id-includes/code_image.php" alt="Kode Keamanan"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Tulis Kode</label>
    <div class="col-sm-10"><input type="text" name="gfx_check" maxlength="6" class="form-control" placeholder="Tulis kode di atas"></div>
</div>';
}
$tengah .= '
<div class="form-group">
    <label class="col-sm-2 control-label"></label>
    <div class="col-sm-10"><input type="submit" name="submit" value="Kirim Pesan" class="btn btn-success"></div>
</div></form>';
}

#############################################
# Rekomendasi Berita
#############################################
if(@$_GET['aksi']=="rekomendasi"){

$seftitle = text_filter(cleanText($_GET['seftitle']));
$id = text_filter(cleanText($_GET['id']));
$tengah .='<div class="right_post">Kirim Artikel Ke Teman</div>';

$data = mysql_fetch_array(mysql_query( "SELECT judul FROM artikel WHERE id='$id' AND publikasi=1" ));
$judul_artikel = $data['judul'];

$tengah .='<div class="left_message"><p>Anda ing memberitahu teman Anda tentang artikel ini yang berjudul : <b>'.$judul_artikel.'</b></p></div>';

if (isset($_POST['submit'])) {


    $yemail = text_filter($_POST['yemail']);
    $femail = text_filter($_POST['femail']);
    $pesan = text_filter($_POST['pesan']);
    $error = '';
    if (!is_valid_email($yemail)) {$error .= "your email invalid, Please use the standard format (admin@domain.com)<br />";}
 	if (!is_valid_email($femail)) {$error .= "Friend email invalid, Please use the standard format (admin@domain.com)<br />";}
    $yname = text_filter($_POST['yname']);
    $fname = text_filter($_POST['fname']);
    
        if (!$fname)  $error .= "Error: Please enter your Frind's Name!<br />";
        if (!$yname)  $error .= "Error: Please enter your Name!<br />";

    $gfx_check = intval($_POST['gfx_check']);
    if ($_POST['gfx_check'] != $_SESSION['Var_session'] or !isset($_SESSION['Var_session'])) {$error .= "Error: Security Code Invalid <br />";}


if ($error){
        $tengah.='<div class="alert alert-danger">'.$error.'</div>';
}else{


$subject = "Ada artikel bagus di $url_situs";
$full_pesan = "Hallo,\n\nBerikut ini ada artikel yang bagus untuk dibaca,
<br />Artikel dengan judul : $judul_artikel, silahkan klik aja <a href='$url_situs/?pilih=news&amp;mod=yes&aksi=lihat&id=$id'>$url_situs/?pilih=news&amp;mod=yes&aksi=lihat&id=$id</a>.
<br />
<br />
$pesan
<br />
<br />Terima kasih.";
mail_send($femail, $yemail, $subject, $full_pesan, 0, 3);
$tengah.='<div class="sukses"><p>Pesan Anda telah dikirim ke teman Anda.<br />Terima kasih mau mendistribusikan artikel di situs ini.</p></div>';
$tengah .='<meta http-equiv="refresh" content="3; url=?pilih=news&amp;mod=yes&aksi=lihat&id='.$id.'">';
}
}

$tengah .='<div class="border">';
$tengah .="
<form method=\"post\" action=\"\">
<table border=\"0\"  cellpadding=\"3\" cellspacing=\"0\" align=\"center\">
  <tr>
    <td valign=\"top\">Your Name</td>
    <td valign=\"top\">:</td>
    <td valign=\"top\"><input type=\"text\" name=\"yname\" style=\"width:150px\" size=\"50\" /></td>
  </tr>
  <tr>
    <td valign=\"top\">Your E-mail</td>
    <td valign=\"top\">:</td>
    <td valign=\"top\"><input type=\"text\" name=\"yemail\" style=\"width:150px\" size=\"50\" /></td>
  </tr>
  <tr>
    <td valign=\"top\">Your Friend's Name</td>
    <td valign=\"top\">:</td>
    <td valign=\"top\"><input type=\"text\" name=\"fname\" style=\"width:150px\" size=\"50\" /></td>
  </tr>
  <tr>
    <td valign=\"top\">Your Friend's E-Mail</td>
    <td valign=\"top\">:</td>
    <td valign=\"top\"><input type=\"text\" name=\"femail\" style=\"width:150px\" size=\"50\" /></td>
  </tr>
  <tr>
    <td valign=\"top\">Message (option)</td>
    <td valign=\"top\">:</td>
    <td valign=\"top\"><textarea name=\"pesan\"  cols=\"50\" rows=\"10\" style=\"width:250px\"></textarea></td>
  </tr>";


if (extension_loaded("gd")) {
$random_num = gen_pass(10);
$tengah .= "
  <tr>
    <td valign=\"top\">Security Code</td>
    <td valign=\"top\">:</td>
    <td valign=\"top\"><img src=\"{web}/includes/code_image.php\" border=\"1\" alt=\"Security Code\" /></td>
  </tr>
  <tr>
    <td valign=\"top\">Type Code</td>
    <td valign=\"top\">:</td>
    <td valign=\"top\"><input type=\"text\" name=\"gfx_check\" size=\"10\" maxlength=\"6\" /></td>
  </tr>";

}
$tengah .= "
  <tr>
    <td valign=\"top\"></td>
    <td valign=\"top\"></td>
    <td valign=\"top\"></td>
  </tr>
  <tr>
    <td valign=\"top\"></td>
    <td valign=\"top\"></td>
    <td valign=\"top\"><input type=\"submit\" name=\"submit\" value=\"Submit\" /></td>
  </tr>
</table>
</form>";
$tengah .='</div>';
}

#############################################
# Kategori
#############################################
if($_GET['aksi']=="kategori"){

$kid = int_filter($_GET['kid']);

$hasil = $koneksi_db->sql_query( "SELECT * FROM berita_kat WHERE kid='$kid'" );
$data = $koneksi_db->sql_fetchrow($hasil);
$id_parent 	= $data['id_parent'];
$kategori 	= $data['kategori'];

$tengah .='<ol class="breadcrumb">
<li><a href="'.$url_situs.'">Home</a></li>
<li class="active">'.$kategori.'</li>
</ol>';

$tengah .='<h4 class="page-header">Rubrik "'.$kategori.'"</h4>';

// Title Website
$judul_situs = ''.$kategori.' | '.$judul_situs.'';
$_META['description'] = htmlentities(strip_tags($data['ket']));
$_META['keywords'] = implode(',',explode(' ',htmlentities(strip_tags($data['kategori']))));

if (empty ($kategori)){
$tengah.='<div class="alert alert-danger">Akses Ditolak<br /><br />Salam<br /><br />Teamworks Indonesia<br />webmaster@teamworks.co.id</div>';
}else {

$limit = 10;
$offset = int_filter(@$_GET['offset']);
$pg		= int_filter(@$_GET['pg']);
$stg    = int_filter(@$_GET['stg']);
////////////////////

$hasilp = $koneksi_db->sql_query( "SELECT * FROM berita_kat WHERE id_parent='$kid'" );
while ($datap = $koneksi_db->sql_fetchrow($hasilp)) {
$topikp = $datap['kid'];
$topikp2 .=$topikp.',';
}
$topikp2 .=$kid.','.$topikp2;
$topikp2 = substr_replace($topikp2, "", -1, 1);
//$tengah.=$topikp2.'<br>';
///////////////////////////////////////
$totals = $koneksi_db->sql_query( "SELECT * FROM berita WHERE publikasi=1 and kid in($topikp2)" );
$jumlah = $koneksi_db->sql_numrows( $totals );
//$a = new paging ($limit);
$a = new paging_s ($limit,''.$url_situs.'/kategori-hal-'.$kid.'-'.SEO($kategori).'');
if ($jumlah>0 ){

$hasil = $koneksi_db->sql_query( "SELECT * FROM berita WHERE publikasi=1 and kid in($topikp2) ORDER BY id DESC LIMIT $offset, $limit" );
$tengah .= '<table width="100%" border="0" cellspacing="0" cellpadding="0">';
while ($data = $koneksi_db->sql_fetchrow($hasil)) {
$id			= $data['id'];
$tanggal	= datetimes($data['tanggal']);
$judul		= $data['judul'];
$konten		= $data['konten'];
$gambar 	= ($data['gambar'] == '') ? 
'<img class="img-thumbnail" style="float:left; margin:0 10px 0 0;" src="'.$url_situs.'/id-content/modul/berita/images/thumb_default.jpg" alt="'.$data['judul'].'">' : 
'<img class="img-thumbnail" style="float:left; margin:0 10px 0 0;" src="'.$url_situs.'/id-content/modul/berita/images/thumb/'.$data['gambar'].'" alt="'.$data['judul'].'">';

$tot_komen =  $koneksi_db->sql_query("SELECT * FROM berita_komentar WHERE berita=$id");
$jum_komen = $koneksi_db->sql_numrows($tot_komen);

$tengah .= '<tr><td style="border-bottom:1px dashed #dddddd; padding:5px 0px 10px 0px;">
<a href="'.$url_situs.'/'.get_link($id,$judul,"lihat").'"><h4>'.$judul.'</h4></a>
<span class="text-muted">'.$tanggal.' - Dibaca '.$data['hits'].' kali - <a href="'.$url_situs.'/'.get_link($id,$judul,"lihat").'#respon">'.$jum_komen.' Komentar</a></span>
'.$gambar.'<p>'.limitTXT(strip_tags($konten),400).'</p></td></tr>';
}
$tengah .= '</table>';

if($jumlah>$limit){
$tengah .='<div class="border">';
$tengah.="<center>";
if (empty($_GET['offset']) and !isset ($_GET['offset'])) {
$offset = 0;
}

if (empty($_GET['pg']) and !isset ($_GET['pg'])) {
$pg = 1;
}

if (empty($_GET['stg']) and !isset ($_GET['stg'])) {
$stg = 1;
}

$tengah.= $a-> getPaging($jumlah, $pg, $stg);
$tengah.="</center>";
$tengah .='</div>';
}
}else{
//////////////////////////////////////////////
$hasilp = $koneksi_db->sql_query( "SELECT * FROM berita_kat WHERE id_parent='$kid'" );
while ($datap = $koneksi_db->sql_fetchrow($hasilp)) {
$topikp = $datap['kid'];
$topikp2 .=$topikp.',';
}
$topikp2 = substr_replace($topikp2, "", -1, 1);
//$tengah.=$topikp2.'<br>';
///////////////////////////////////////
$totals = $koneksi_db->sql_query( "SELECT id FROM berita WHERE publikasi=1 and kid in($topikp2)" );
$jumlah = $koneksi_db->sql_numrows( $totals );
$a = new paging ($limit);
$hasil = $koneksi_db->sql_query( "SELECT * FROM berita WHERE publikasi=1 and kid in($topikp2) ORDER BY id DESC LIMIT $offset, $limit" );
$tengah .= '<table width="100%" border="0" cellspacing="0" cellpadding="0">';
while ($data = $koneksi_db->sql_fetchrow($hasil)) {
$id			= $data['id'];
$tanggal	= datetimes($data['tanggal']);
$judul		= $data['judul'];
$konten		= $data['konten'];
$gambar = ($data['gambar'] == '') ? 
'<img class="img-thumbnail" style="float:left; margin:0 10px 0 0;" src="'.$url_situs.'/timthumb.php?src='.$url_situs.'/id-content/modul/berita/images/news-default.jpg&amp;w=120&amp;h=90&amp;zc=1">' : 
'<img class="img-thumbnail" style="float:left; margin:0 10px 0 0;" src="'.$url_situs.'/id-content/modul/berita/images/thumb/'.$data['gambar'].'" alt="'.$data['judul'].'">';

$tot_komen =  $koneksi_db->sql_query("SELECT * FROM berita_komentar WHERE berita=$id");
$jum_komen = $koneksi_db->sql_numrows($tot_komen);

$tengah .= '<tr><td style="border-bottom:1px dashed #dddddd; padding:5px 0px 10px 0px;">
<a href="'.$url_situs.'/'.get_link($id,$judul,"lihat").'"><h4>'.$judul.'</h4></a>
<span class="text-muted">'.$tanggal.' - Dibaca '.$data['hits'].' kali - <a href="'.$url_situs.'/'.get_link($id,$judul,"lihat").'#respon">'.$jum_komen.' Komentar</a></span>
'.$gambar.'<p>'.limitTXT(strip_tags($konten),400).'</p></td></tr>';
}
$tengah .= '</table>';

if($jumlah>$limit){
$tengah .='<div class="border">';
$tengah.="<center>";
if (empty($_GET['offset']) and !isset ($_GET['offset'])) {
$offset = 0;
}

if (empty($_GET['pg']) and !isset ($_GET['pg'])) {
$pg = 1;
}

if (empty($_GET['stg']) and !isset ($_GET['stg'])) {
$stg = 1;
}
$tengah.= $a-> getPaging($jumlah, $pg, $stg);
$tengah.="</center>";
$tengah .='</div>';
}
if($jumlah<1){
$tengah .='<div class="alert alert-danger">Tidak ada berita dalam kategori ini</div>';
}
}
}
}

#############################################
# Tags
#############################################
if($_GET['aksi']=="tags"){

$tag = strip_tags($_GET['tag']);

$tengah .= '<h4 class="bg">Tags : '.stripslashes(strip_tags($_GET['tag'])).'</h4>';

//title 
$judul_situs = ''.stripslashes(strip_tags($_GET['tag'])).' | '.$judul_situs.'';

$limit = 5;
$offset = int_filter(@$_GET['offset']);
$pg        = int_filter(@$_GET['pg']);
$stg    = int_filter(@$_GET['stg']);
			
if (strlen($tag) == 3) {
$finder = "`tags` LIKE '%$tag%'";
}else {
$finder = "MATCH (tags) AGAINST ('$tag' IN BOOLEAN MODE)";
}
			
$totals = mysql_query( "SELECT count(`id`) AS `total` FROM `berita` WHERE $finder AND publikasi = 1" );
$tot = mysql_fetch_assoc ( $totals );
$jumlah = $tot['total'];
$a = new paging ($limit);
if (empty($_GET['offset']) and !isset ($_GET['offset'])) {
$offset = 0;
}
					
if (empty($_GET['pg']) and !isset ($_GET['pg'])) {
$pg = 1;
}
					
if (empty($_GET['stg']) and !isset ($_GET['stg'])) {
$stg = 1;
}
					
if ($jumlah > 0) {		
$query = $koneksi_db->sql_query("SELECT * FROM berita WHERE $finder AND publikasi = 1 ORDER BY tanggal DESC LIMIT $offset,$limit");
$tengah .= '<table width="100%" border="0" cellspacing="0" cellpadding="0">';
while ($data = $koneksi_db->sql_fetchrow($query)) {
$id	= $data['id'];
$tanggal = datetimes($data['tanggal']);
$gambar = ($data['gambar'] == '') ? 
'<img style="float:left; margin:0 10px 0 0;" src="'.$url_situs.'/id-content/modul/berita/images/thumb_default.jpg" alt="'.$data['judul'].'">' : 
'<img style="float:left; margin:0 10px 0 0;" src="'.$url_situs.'/id-content/modul/berita/images/thumb/'.$data['gambar'].'" alt="'.$data['judul'].'">';

$tot_komen =  $koneksi_db->sql_query("SELECT * FROM berita_komentar WHERE berita=$id");
$jum_komen = $koneksi_db->sql_numrows($tot_komen);

$tengah .= '<tr><td style="border-bottom:1px dashed #dddddd; padding:5px 0px 10px 0px;">
<a href="'.$url_situs.'/'.get_link($data['id'],$data['judul'],"lihat").'"><h4>'.$data['judul'].'</h4></a>
<span class="text-muted">'.$tanggal.' - Dibaca '.$data['hits'].' kali - <a href="'.$url_situs.'/'.get_link($id,$judul,"lihat").'#respon">'.$jum_komen.' Komentar</a></span>
'.$gambar.'<p>'.limitTXT(strip_tags($data['konten']),250).'</p></td></tr>';
}
$tengah .= '</table>';
}else {
$tengah .= '<div class="alert alert-danger">Berita Kosong</div>';
}
				
if($jumlah>$limit){
$tengah .= '<center>';
$tengah .=  $a-> getPaging($jumlah, $pg, $stg);
$tengah .= '</center>';
}
}

#############################################
# Arsip
#############################################
if($_GET['aksi']=="arsip"){

$bulan = text_filter(cleanText($_GET['bulan']));

//if (!empty($_GET['date'])){
if (preg_match('/\d{4}\.\d{2}/',$_GET['date'])) {
list($tahun,$bulan) = explode('.',$_GET['date']);
if (checkdate($bulan,1,$tahun)) {
//include 'mod/news/include/function.php';
$tengah .= '<h4 class="bg"><span class="judul">Arsip pada bulan : '.kebulan($bulan).' '.$tahun.'</span></h4>';

$limit 	= 10;
$offset = int_filter(@$_GET['offset']);
$pg		= int_filter(@$_GET['pg']);
$stg    = int_filter(@$_GET['stg']);
			
$totals = mysql_query( "SELECT * FROM berita WHERE month(tanggal) = '$bulan' AND year(tanggal) = '$tahun' AND publikasi = 1" );
$jumlah = mysql_num_rows ( $totals );
//$a = new paging ($limit);
$a = new paging_s ($limit,''.$url_situs.'/arsip-hal/'.$tahun.'.'.$bulan.'');

if (empty($_GET['offset']) and !isset ($_GET['offset'])) {
$offset = 0;
}
					
if (empty($_GET['pg']) and !isset ($_GET['pg'])) {
$pg = 1;
}
					
if (empty($_GET['stg']) and !isset ($_GET['stg'])) {
$stg = 1;
}
					
if ($jumlah > 0) {		
$query = $koneksi_db->sql_query("SELECT * FROM berita WHERE month(`tanggal`) = '$bulan' AND year(`tanggal`) = '$tahun' AND publikasi = 1 ORDER BY tanggal DESC LIMIT $offset,$limit");
$tengah .= '<table width="100%" border="0" cellspacing="0" cellpadding="0">';
while($data = $koneksi_db->sql_fetchrow($query)) {
$id			= $data['id'];
$judul		= $data['judul'];
$tanggal	= datetimes($data['tanggal']);
$gambar 	= ($data['gambar'] == '') ? 
'<img class="img-thumbnail" style="float:left; margin:0 10px 0 0;" src="'.$url_situs.'/id-content/modul/berita/images/thumb_default.jpg" alt="'.$data['judul'].'">' : 
'<img class="img-thumbnail" style="float:left; margin:0 10px 0 0;" src="'.$url_situs.'/id-content/modul/berita/images/thumb/'.$data['gambar'].'" alt="'.$data['judul'].'">';

$tot_komen =  $koneksi_db->sql_query("SELECT * FROM berita_komentar WHERE berita=$id");
$jum_komen = $koneksi_db->sql_numrows($tot_komen);

$tengah .= '<tr><td style="border-bottom:1px dashed #dddddd; padding:5px 0px 10px 0px;">
<a href="'.$url_situs.'/'.get_link($id,$judul,"lihat").'"><h4>'.$data['judul'].'</h4></a>
<span class="text-muted">'.$tanggal.' - Dibaca '.$data['hits'].' kali - <a href="'.$url_situs.'/'.get_link($id,$judul,"lihat").'#respon">'.$jum_komen.' Komentar</a></span>
'.$gambar.'<p>'.limitTXT(strip_tags($data['konten']),250).'</p></td></tr>';
}
$tengah .= '</table>';
}else {
$tengah .= '<div class="alert alert-danger">Arsip berita kosong</div>';
}
				
if($jumlah>$limit){
$tengah .= '<div class="border">';
$tengah .= "<center>";
					
$tengah .=  $a-> getPaging($jumlah, $pg, $stg);
$tengah .= "</center>";
$tengah .= '</div>';
}
}else {
$tengah .= '<h4 class="bg"><span class="judul">Error ...</h4>';
$tengah .= '<div class="alert alert-danger">format date salah</div>';
}
}else {
$tengah .= '<h4 class="bg"><span class="judul">Error ...</h4>';
$tengah .= '<div class="alert alert-danger">Paramater date salah,<br/> contoh : 2008.01</div>';
}
}

echo $tengah;

?>