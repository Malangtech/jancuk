<?php


if (!defined('CMS_CONTENT')) {
	Header("Location: ../index.php");
	exit;
}

global $koneksi_db;

$tengah .='<h4 class="page-header">Dashboard</h4>';

if ($_SESSION['LevelAkses']){
$username = $_SESSION['UserName'];
$query =  $koneksi_db->sql_query("SELECT * FROM users WHERE user='$username'");
$data = $koneksi_db->sql_fetchrow( $query );
$last_ping = datetimes($data['last_ping'],true);

#############################################
# Administrator
#############################################
if ($_SESSION['LevelAkses']=="Administrator"){

$tengah .='<div class="alert alert-info">Last Login : '.$last_ping.'</div>';

$tengah .='<div class="row">';
$tengah .='<div class="col-md-6">
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border:1px solid #DFDFDF; border-radius:5px;">
<tr>
<td style="text-align:left; padding:9px 10px 9px 10px; border-bottom:1px solid #DFDFDF;">Informasi Website</td>
</tr>';
  
$total_hal =  $koneksi_db->sql_query("SELECT * FROM halaman");
$jumlah_hal = $koneksi_db->sql_numrows($total_hal);
$tengah .='<tr>
<td style="padding:4px 10px 4px 10px; border-bottom:1px solid #DFDFDF;">
<b>'.$jumlah_hal.'</b> <a href="?opsi=halaman">Halaman</a></td>';

$total_ber_p =  $koneksi_db->sql_query("SELECT * FROM berita WHERE publikasi=1");
$jumlah_ber_p = $koneksi_db->sql_numrows($total_ber_p);
$tengah .='<tr>
<td style="padding:4px 10px 4px 10px; border-bottom:1px solid #DFDFDF;">
<b>'.$jumlah_ber_p.'</b> <a href="?opsi=berita&modul=yes"><font style="color:#008000;">Berita</font></a></td>';
$tengah .='</tr>';

$total_ber_u =  $koneksi_db->sql_query("SELECT * FROM berita WHERE publikasi=0");
$jumlah_ber_u = $koneksi_db->sql_numrows($total_ber_u);
$tengah .='<tr>
<td style="padding:4px 10px 4px 10px; border-bottom:1px solid #DFDFDF;">
<b>'.$jumlah_ber_u.'</b> <a href="?opsi=berita&modul=yes&aksi=listnewsmasuk"><font style="color:#E66F00;">Berita Pending</font></a></td>';
$tengah .='</tr>';

$totalber_kat =  $koneksi_db->sql_query("SELECT * FROM berita_kat");
$jumlah_ber_kat = $koneksi_db->sql_numrows($total_ber_kat);
$tengah .='<tr>
<td style="padding:4px 10px 4px 10px; border-bottom:1px solid #DFDFDF;">
<b>'.$jumlah_ber_kat.'</b> <a href="?opsi=berita&modul=yes">Kategori</a></td>';
$tengah .='</tr>';

$total =  $koneksi_db->sql_query("SELECT * FROM berita_komentar");
$jumlah = $koneksi_db->sql_numrows($total);
$tengah .='<tr>
<td style="padding:4px 10px 4px 10px; border-bottom:1px solid #DFDFDF;">
<b>'.$jumlah.'</b> <a href="?opsi=berita&modul=yes&aksi=listkomentar">Komentar</a></td>';
$tengah .='</tr>';

$total =  $koneksi_db->sql_query( "SELECT * FROM users" );
$jumlah = $koneksi_db->sql_numrows( $total );
$tengah .='<tr>
<td style="padding:4px 10px 4px 10px; border-bottom:1px solid #DFDFDF;">
<b>'.$jumlah.'</b> <a href="?opsi=users"><font style="color:#FF0000;">Member</font></a></td>';
$tengah .='</tr>';

$tengah .='<tr><td style="padding:4px 10px 4px 10px;">&nbsp;</td></tr>
</table></div>';

$tengah .='<div class="col-md-6">';
$tengah .='<div class="panel panel-default">
<div class="panel-heading"><span class="text-primary">Berita Terbaru</span></div>';

$hasil = $koneksi_db->sql_query( "SELECT * FROM berita WHERE publikasi=1 ORDER BY id DESC LIMIT 5");
$tengah .='<ul class="list-group">';
while ($data = $koneksi_db->sql_fetchrow($hasil)) {
$id 	= $data['id'];
$judul 	= $data['judul'];
$tengah .='<a href="#" class="list-group-item">'.$judul.'</a>';
}
$tengah .='</ul></div>';

$tengah .='</div>';
$tengah .='</div>';
}

#############################################
# User
#############################################
if ($_SESSION['LevelAkses']=="User"){

$tengah .='<div class="alert alert-info">Last Login : '.$last_ping.'</div>';

$total =  $koneksi_db->sql_query( "SELECT * FROM berita WHERE user='$username'" );
$jumlah = $koneksi_db->sql_numrows( $total );
$tengah .='<br>-&nbsp;<a href="?opsi=berita&modul=yes">Berita ('.$jumlah.')</a>';
}

}

echo $tengah;

?>