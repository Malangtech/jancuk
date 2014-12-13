<?php
if (!defined('CMS_CONTENT')) {
	Header("Location: ../index.php");
	exit;
}

global $koneksi_crot,$maxdata,$maxkonten,$maxgalleri,$maxadmindata;

$tengah .= '<img class="img-responsive" src="id-content/files/banner-web.jpg" width="699" height="259">';

# Berita
$hasil = $koneksi_crot->sql_query("SELECT * FROM berita WHERE publikasi=1 ORDER BY tanggal DESC LIMIT 10");	
$tengah .= '<table width="100%" border="0" cellspacing="0" cellpadding="0">';
while ($data = $koneksi_crot->sql_fetchrow($hasil)) {
$id 		= $data['id'];
$tanggal	= datetimes($data['tanggal']);
$judul 		= $data['judul'];
$konten 	= $data['konten'];
$gambar = ($data['gambar'] == '') ? 
'<img style="float:left; margin:0 10px 0 0;" src="id-content/modul/berita/images/thumb_default.jpg" width="150" height="95" alt="'.$data['judul'].'">' : 
'<img style="float:left; margin:0 10px 0 0;" src="id-content/modul/berita/images/thumb/'.$data['gambar'].'" width="150" height="95" alt="'.$data['judul'].'">';

$tot_komen =  $koneksi_crot->sql_query("SELECT * FROM berita_komentar WHERE berita=$id");
$jum_komen = $koneksi_crot->sql_numrows($tot_komen);

$tengah .= '<tr><td style="border-bottom:1px dashed #dddddd; padding:5px 0px 10px 0px;">
<a href="'.$url_situs.'/'.get_link($id,$judul,"lihat").'"><h4>'.$judul.'</h4></a>
<span class="text-muted">'.$tanggal.' - Dibaca '.$data['hits'].' kali - <a href="'.$url_situs.'/'.get_link($id,$judul,"lihat").'#respon">'.$jum_komen.' Komentar</a></span>
'.$gambar.'<p>'.limitTXT(strip_tags($konten),400).'</p></td></tr>';
}
$tengah .= '</table>';

// Galeri Foto
/*$tengah .= '<h4 class="bg">Galeri Foto</h4>';
$tengah .= '<div class="row">';
$no = 1;
$s = $koneksi_crot->sql_query("SELECT * FROM foto ORDER BY id DESC LIMIT 12");	
while($data = $koneksi_crot->sql_fetchrow($s)){
$id 	= $data['id'];
$nama 	= ($data['nama'] == '') ? ''.$data['gambar'].'' : ''.$data['nama'].'';
$kid 	= $data['kid'];
$gambar = '<img class="img-thumbnail" src="id-content/modul/foto/images/thumb/'.$data['gambar'].'" alt="'.$data['nama'].'" border="0">';
$urutan = $no + 1;
$tengah .= '<div class="col-md-3"><a href="'.$url_situs.'/'.get_link($id,$nama,"foto").'">'.$gambar.'</a></div>';
$no++;
}
$tengah .= '</div>';*/

echo $tengah;

?>