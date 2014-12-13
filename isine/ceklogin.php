<?php


global $koneksi_db;
if (isset ($_POST['submit_login']) && @$_POST['loguser'] == 1){
$login .= cms_login ();
}
if (cek_login ()){

$user = $_SESSION['UserName'];
$levelakses =$_SESSION['LevelAkses'];
if ($levelakses=="Administrator"){
$hasil = $koneksi_db->sql_query( "SELECT * FROM useraura WHERE user='$user'" );
$data = $koneksi_db->sql_fetchrow($hasil);
$user = $data['user'];
$avatar = $data['avatar'];
echo '<div class="border" align="right">Howdy <a href="admin.php" target="_blank"><b>'.$user.'</b></a>, <a href="?aksi=logout"><b>Log Out</b></a><img src="mod/profile/images/'.$avatar.'" width="16" height="16" style="float:right; border:1px solid #dddddd; margin-left:5px;"></div>';
}else{
$hasil = $koneksi_db->sql_query( "SELECT * FROM useraura WHERE user='$user'" );
$data = $koneksi_db->sql_fetchrow($hasil);
$user = $data['user'];
$avatar = $data['avatar'];
echo '<div class="border" align="right">Howdy <a href="admin.php" target="_blank"><b>'.$user.'</b></a>, <a href="?aksi=logout"><b>Log Out</b></a><img src="mod/profile/images/'.$avatar.'" width="16" height="16" style="float:right; border:1px solid #dddddd; margin-left:5px;"></div>';
}
}else{
$login .= '
<div class=border>
<form method="post" action="">
<table><tr valign=top><td width=70%><b>Anda belum melakukan registrasi</b><br><b><a href="./register.html">registrasi disini</a></b> atau <b><a href="./forgotpassword.html">lupa password</a></b></td>
    <td><b>user</b><br /><input type="text" name="username" size="10" /></td>
	<td><b>password</b><br /><input type="password" name="password" size="10" /></td>
	<td><br /><input type="hidden" value="1" name="loguser" /><input type="submit" value="Login" name="submit_login" /></td>
  </tr>
</table></form></div>';
echo $login;
}
?>