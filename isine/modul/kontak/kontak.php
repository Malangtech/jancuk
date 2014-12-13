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

//$index_hal=1;

global $koneksi_db,$email_master,$judul_situs,$alamatkantor;

$tengah ='<h4 class="bg">Form Kontak</h4>';

//title 
$judul_situs = 'Kontak | '.$judul_situs.'';

if (isset($_POST['submit'])) {
$nama = text_filter($_POST['nama']);
$email = text_filter($_POST['email']);
$pesan = nl2br(text_filter($_POST['pesan'], 2));
$error = '';
if (!is_valid_email($email)) $error .= "<strong>Gagal!</strong> Penulisan format E-Mail salah!<br />";
$gfx_check = $_POST['gfx_check'];
if (!$nama)  $error .= "<strong>Gagal!</strong> Mohon isi nama lengkap Anda!<br />";
if (!$pesan) $error .= "<strong>Gagal!</strong> Mohon isi pesan Anda!<br />";
// $code = substr(hexdec(md5("".date("F j")."".$_POST['random_num']."".$sitekey."")), 2, 6);
if ($gfx_check != $_SESSION['Var_session'] or !isset($_SESSION['Var_session'])) {$error .= "<strong>Gagal!</strong> Kode keamanan salah!<br />";}
/*if (cek_posted('contact')){
	$error .= 'Anda Telah Memposting, Tunggu beberapa Saat';
}*/

if ($error) {
$tengah.='<div class="alert alert-danger">'.$error.'</div>';
}else{
$subject = "Form Kontak - $judul_situs";
$msg = '
Nama Pengirim: '.$nama.' <br />
Email Pengirim: '.$email.' <br />
Pesan: '.$pesan.'';

mail_send($email_master, $nama, $email, $subject, $msg, 1, 3);
$tengah.='<div class="alert alert-success">Terima kasih, pesan Anda telah terkirim!</div>';
unset($nama);
unset($email);
unset($pesan);
}
}

$nama 	= !isset($nama) ? '' : $nama;
$email 	= !isset($email) ? '' : $email;
$pesan 	= !isset($pesan) ? '' : $pesan;

$tengah .= '<p>'.$alamatkantor.'</p>
<p>Anda bisa menghubungi kami melalui formulir di bawah ini</p>
<br /><br />
<form class="form-horizontal" method="post" action="#">
<div class="form-group">
	<label class="col-sm-2 control-label">Nama</label>
    <div class="col-sm-10"><input type="text" name="nama" value="'.$nama.'" class="form-control" placeholder="Masukkan nama lengkap"></div>
</div>
<div class="form-group">
   <label class="col-sm-2 control-label">Email</label>
    <div class="col-sm-10"><input type="email" name="email" size="25" value="'.$email.'" class="form-control" placeholder="Masukkan email"></div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">Pesan</label>
    <div class="col-sm-10"><textarea name="pesan" rows="5" class="form-control" placeholder="Masukkan pesan">'.$pesan.'</textarea></div>
</div>';
if (extension_loaded("gd")) {
$tengah .= '
<div class="form-group">
    <label class="col-sm-2 control-label">Kode Keamanan</label>
    <div class="col-sm-10"><img src="id-includes/code_image.php" border="1" alt="Kode Keamanan"></div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">Tulis Kode</label>
    <div class="col-sm-10 form-inline"><input type="text" name="gfx_check" maxlength="6" class="form-control" placeholder="Tulis Kode"></div>
</div>';
}
$tengah .= '
<div class="form-group">
    <label class="col-sm-2 control-label"></label>
    <div class="col-sm-10"><input type="submit" name="submit" value="Kirim Pesan" class="btn btn-success"></div>
</div>
</form>';

echo $tengah;

?>
