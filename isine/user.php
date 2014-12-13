<?php


if (!defined('CMS_CONTENT')) {
	Header("Location: ../index.php");
	exit;
}

global $koneksi_db,$error;

#############################################
# Daftar
#############################################
if($_GET['aksi']=="daftar"){

$tengah .='<h4 class="bg">Formulir Pendaftaran Member</h4>';
//title 
$judul_situs = 'Daftar | '.$judul_situs.'';

if(isset($_POST['submit'])){
$nama	= text_filter($_POST['nama']);
$user	= $_POST['user'];
$email	= $_POST['email'];
if(!isset($_POST['cekperaturan'])){
$cekperaturan = '0';
}else{
$cekperaturan = $_POST['cekperaturan'];
}
$password     = md5($_POST['password']);
$rpassword    = md5($_POST['rpassword']);
$confirm_code = md5(uniqid(rand()));
$mail_blocker = explode(",", $mail_blocker);
foreach ($mail_blocker as $key => $val) {
if ($val == strtolower($email) && $val != "") $error .= "Given E-Mail the address is forbidden to use!<br />";
}
$name_blocker = explode(",", $name_blocker);
foreach ($name_blocker as $key => $val) {
if ($val == strtolower($user) && $val != "") $error .= "Named it is forbidden to use!<br />";
}

if (!$user || preg_match("/[^a-zA-Z0-9_-]/", $user)) $error .= "Error: Karakter Username tidak diizinkan kecuali a-z,A-Z,0-9,-, dan _<br />";
if (strlen($user) > 10) $error .= "Username terlalu panjang maksimal 10 karakter<br />";
if (strrpos($user, " ") > 0) $error .= "Username Tidak Boleh Menggunakan Spasi";
if ($koneksi_db->sql_numrows($koneksi_db->sql_query("SELECT user FROM users WHERE user='$user'")) > 0) $error .= "Error: Username ".$nama." sudah terdaftar, silahkan ulangi.<br />";
if ($koneksi_db->sql_numrows($koneksi_db->sql_query("SELECT email FROM users WHERE email='$email'")) > 0) $error .= "Error: Email ".$email." sudah terdaftar, silahkan ulangi.<br />";
if (!$user)  $error .= "Error: Formulir username belum diisi, silahkan ulangi.<br />";
if (!$nama)  $error .= "Error: Formulir nama lengkap belum diisi, silahkan ulangi.<br />";
if ($cekperaturan != '1') $error .= "Syarat dan ketentuan dalam website ini belum disetujui!<br />";
if (empty($_POST['password']))  $error .= "Error: Formulir password belum diisi, silahkan ulangi!<br />";
if ($_POST['password'] != $_POST['rpassword'])  $error .= "Password pertama dan kedua tidak cocok!<br />";
if (!is_valid_email($email)) $error .= "Error: E-Mail address invalid!<br />";
if ($_POST['gfx_check'] != $_SESSION['Var_session'] or !isset($_SESSION['Var_session'])) {$error .= "Security Code Invalid <br />";}
if ($error){
$tengah.='<div class="alert alert-danger">'.$error.'</div>';
}else{
$hasil1 = $koneksi_db->sql_query("INSERT INTO users (user,email,password,nama,level,tipe) VALUES ('$user','$email','$password','$nama','User','aktif')" );

if($hasil1){
$s = $koneksi_db->sql_query( "SELECT * FROM setting WHERE id=1");
$datas = $koneksi_db->sql_fetchrow($s);
$nama_email = $datas['nama_email'];

$subject  ="Informasi Pendaftaran Akun Anda";
$header   = $email_master;
$message  ='Anda telah melakukan pendaftaran akun di <b>'.$judul_situs.'</b><br>
Berikut ini informasi akun Anda :<br><br>
<table border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td style="padding:3px 0px 3px 0px;">Username</td>
    <td style="padding:3px 10px 3px 10px;">:</td>
    <td style="padding:3px 0px 3px 0px;">'.$user.'</td>
  </tr>
  <tr>
    <td style="padding:3px 0px 3px 0px;">Password</td>
    <td style="padding:3px 10px 3px 10px;">:</td>
    <td style="padding:3px 0px 3px 0px;">'.$_POST['password'].'</td>
  </tr>
</table><br>
Semoga informasi ini bermanfaat bagi Anda.<br>
Terima kasih.<br><br><br>
 
Hormat Kami,<br><br><br>
 
'.$nama_email.'<br>
---------------------------------------------------------------------------------------------------<br>
Mohon tidak membalas email ini, karena email ini dikirim otomatis.';
$sentmail = mail_send($email, $nama_email, $email_master, $subject, $message, 1, 3);
$tengah.='<div class="alert alert-success">Silahkan login dengan Username dan Password Anda</div>';
unset($_POST);
}
}
}

$user         = !isset($user) ? '' : $user;
$email        = !isset($email) ? '' : $email;
$password     = !isset($passwordn) ? '' : $password;
$rpassword    = !isset($rpassword) ? '' : $rpassword;
$checkperaturan = isset($_POST['cekperaturan']) ? ' checked="checked"' : '';
$alamat			!isset($alamat) ? '' : $alamat;

$tengah .='<p>Nikmati aneka fasilitas yang tersedia dalam portal ini dengan menjadi member.
Untuk menjadi member, Anda hanya perlu melakukan pendaftaran dengan mengisi formulir singkat berikut ini.</p>';

$tengah .='<form class="form-horizontal" method="post" action="">
<div class="form-group">
	<label class="col-sm-3 control-label">Username</label>
	<div class="col-sm-9"><input type="text" name="user" value="'.cleantext(stripslashes(@$_POST['user'])).'" class="form-control" required="" placeholder="Masukkan username"></div>
</div>
<div class="form-group">
	<label class="col-sm-3 control-label">E-mail</label>
	<div class="col-sm-9"><input type="email" name="email" value="'.cleantext(stripslashes(@$_POST['email'])).'" class="form-control" required="" placeholder="Masukkan email"></div>
</div>
<div class="form-group">
	<label class="col-sm-3 control-label">Password</label>
	<div class="col-sm-9"><input type="password" name="password" class="form-control" required="" placeholder="Masukkan password"></div>
</div>
<div class="form-group">
	<label class="col-sm-3 control-label">Ulangi Password</label>
	<div class="col-sm-9"><input type="password" name="rpassword" class="form-control" required="" placeholder="Ulangi password"></div>
</div>
<div class="form-group">
	<label class="col-sm-3 control-label">Nama Lengkap</label>
	<div class="col-sm-9"><input type="text" name="nama" value="'.$nama.'" class="form-control" required="" placeholder="Masukkan nama lengkap"></div>
</div>
<div class="form-group">
	<label class="col-sm-3 control-label">Alamat</label>
	<div class="col-sm-9"><input type="text" name="alamat" value="'.$alamat.'" class="form-control" required="" placeholder="Masukkan alamat lengkap"></div>
</div>
';
if (extension_loaded("gd")) {
$tengah .= '<div class="form-group">
	<label class="col-sm-3 control-label">Kode Keamanan</label>
	<div class="col-sm-9"><img src="id-includes/code_image.php" border="1" alt="Kode Keamanan"></div>
</div>
<div class="form-group">
	<label class="col-sm-3 control-label">Tulis Kode</label>
	<div class="col-sm-9"><input name="gfx_check" type="text" size="10" maxlength="6" class="form-control" required="" placeholder="Tulus kode"></div>
</div>';
}
$tengah .= '
<div class="form-group">
	<label class="col-sm-3 control-label">Peraturan</label>
	<div class="col-sm-9"><textarea rows="3" class="form-control">
Aturan umum dari portal
1. Portal kami dibuka untuk mengunjungi oleh semua orang tertarik. Untuk menggunakan semua ukuran layanan dari sebuah situs, perlu bagi Anda untuk mendaftar.
2. Pengguna portal bisa menjadi setiap orang, setuju untuk mematuhi aturan yang diberikan.
3. Setiap peserta dialog memiliki hak untuk kerahasiaan informasi. Oleh karena itu tidak membahas keuangan, keluarga dan kepentingan peserta lainnya tanpa izin di atasnya peserta.
</textarea></div>
</div>
<div class="form-group">
	<label class="col-sm-3 control-label"></label>
	<div class="col-sm-9"><label><input type="checkbox" name="cekperaturan" value="1" id="setuju'.$checkperaturan.'"> Saya setuju dengan persyaratan yang ditetapkan dalam website ini.</label></div>
</div>
<div class="form-group">
	<label class="col-sm-3 control-label"></label>
	<div class="col-sm-9"><input type="submit" name="submit" value="Daftar" class="btn btn-success"> 
	<input type="reset" name="Reset" value="Batal" class="btn btn-default"></div>
</div>
</form>';
}

#############################################
# Lupa Password
#############################################
if($_GET['aksi']=="lupa_password"){

$tengah .='<h4 class="bg">Lupa Password / Username ?</h4>';
//title 
$judul_situs = 'Lupa Password | '.$judul_situs.'';

if(isset($_POST['submit'])){
$email = $_POST['email'];
if (!$email)  $error .= "Error: Formulir Email belum diisi, silahkan ulangi.<br />";
if ($error){
$tengah.='<div class="alert alert-danger">'.$error.'</div>';
}else{
$jumlah = $koneksi_db->sql_numrows($koneksi_db->sql_query("SELECT * FROM users WHERE email='$email' AND tipe='aktif'"));
if($jumlah<1) { 
$tengah.='<div class="alert alert-danger">Maaf, email Anda belum terdaftar</div>';
} else {             
$newpass 		= genpass();
$userdata		= "SELECT * FROM users WHERE email = '$email'";
$userdata 		= $koneksi_db->sql_query( $userdata );
$datauser 		= $koneksi_db->sql_fetchrow($userdata);
$user			= $datauser['user'];	
$emailuser		= $datauser['email'];	
$newpassword	= md5($newpass);
$update			= "UPDATE users SET password='$newpassword' WHERE email='$emailuser'";
$updatedata 	= $koneksi_db->sql_query($update);

$s = $koneksi_db->sql_query( "SELECT * FROM setting WHERE id=1");
$datas = $koneksi_db->sql_fetchrow($s);
$nama_email = $datas['nama_email'];

$subject = 'Reset Password - '.$judul_situs.'';
$msg  ='Anda telah melakukan permintaan reset password akun di <a href="'.$url_situs.'">'.$url_situs.'</a><br>
Berikut ini informasi akun Anda :<br><br>
<table border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td style="padding:3px 0px 3px 0px;">Username</td>
    <td style="padding:3px 10px 3px 10px;">:</td>
    <td style="padding:3px 0px 3px 0px;">'.$user.'</td>
  </tr>
  <tr>
    <td style="padding:3px 0px 3px 0px;">Password</td>
    <td style="padding:3px 10px 3px 10px;">:</td>
    <td style="padding:3px 0px 3px 0px;">'.$newpass.'</td>
  </tr>
</table><br>
Semoga informasi ini bermanfaat bagi Anda.<br>
Terima kasih.<br><br><br>
 
Hormat Kami,<br><br><br>
 
'.$nama_email.'<br>
---------------------------------------------------------------------------------------------------<br>
Mohon tidak membalas email ini, karena email ini dikirim otomatis.';
mail_send($emailuser,$nama_email,$email_master,$subject,$msg,1,3);
Posted('contact');
$tengah.='<div class="alert alert-success">Terima kasih, permintaan Anda telah dikirim ke <strong>'.$emailuser.'</strong></div>';	
}
}
}	

$tengah .='<p>Lupa password / Username? Bukan masalah.</p>
<p>Masukkan email Anda, dan klik Kirim Password</p>';

$tengah .='<form class="form-horizontal" action="" method="post">
<div class="form-group">
	<label class="col-sm-2 control-label">Email</label>
	<div class="col-sm-10"><input type="email" name="email" size="26" class="form-control" placeholder="Masukkan email Anda"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label"></label>
	<div class="col-sm-10"><input type="submit" name="submit" value="Kirim Password" class="btn btn-warning"></div>
</div>
</form>';
}

echo $tengah;

?>