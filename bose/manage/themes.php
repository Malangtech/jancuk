<?php

#############################################
# Teamworks Content Management System
# http://www.teamworks.co.id
# 23 Februari 2014
# Author: webmaster@teamworks.co.id
#############################################

if (!defined('CMS_admin')) {
	Header("Location: ../index.php");
	exit;
}

global $koneksi_db,$email_master,$judul_situs,$theme;

if (isset($_POST['submit'])) {
$theme  	= $_POST['theme'];
$hasil = $koneksi_db->sql_query( "UPDATE setting SET theme='$theme'WHERE id='1'" );
$admin.='<div class="border"><b>Theme telah di Ganti</b></div>';
}
// read isi readme 
$fileComment = "id-content/themes/$theme/$theme.txt"; 
$f = fopen($fileComment, "r"); 
$isi = fread($f, filesize($fileComment)); 
fclose($f); 
$readmetheme = $isi;
//-----------------
$admin ='<h3 class="page-header">Themes Manager</h3>';
/*
$admin .='<div class="border">
<a href="?opsi=admin_themes"><b>Home</b></a> | 
<a href="?opsi=admin_themes&amp;aksi=add"><b>Upload Themes</b></a>
</div>';
*/
if ($_SESSION['LevelAkses'] &&  $_SESSION['LevelAkses']=="Administrator"){
if($_GET['aksi'] == ''){
$admin .= '<h4 class="bg">Current Theme</h4>';
$admin .='<div class="border">';
$admin .= '<img class="img-thumbnail pull-left" src="id-content/themes/'.$theme.'/'.$theme.'.jpg" width="180"><h4>'.$theme.'</h4>'.$readmetheme.'';
$admin .='</div>';
$admin .= '<h4 class="bg">Available Themes</h4>';
$admin .='<div class="border">';
$admin .= '<table  width="100%"><tr>';
$no =0;
$myDir = "id-content/themes/"; 
$dir = opendir($myDir);	
while($tmp = readdir($dir)){
	if($tmp != '..' && $tmp !='.' && $tmp !=''&& $tmp !='.htaccess'&& $tmp !='index.html'&& $tmp !='user'&& $tmp !='administrator'){
$urutan = $no + 1;
// read isi readme 
$fileComment = "id-content/themes/$tmp/$tmp.txt"; 
$f = fopen($fileComment, "r"); 
$isi = fread($f, filesize($fileComment)); 
fclose($f); 
$readmetmp = $isi;
//-----------------
$admin .= '<td valign="top"><img class="img-thumbnail pull-left" src="id-content/themes/'.$tmp.'/'.$tmp.'.jpg" width="200"><h4>'.$tmp.'</h4>'.$readmetmp.'';
$admin .='<form method="post" action=""><input type="hidden" name="theme" value="'.$tmp.'">
<input type="submit" name="submit" value="Ubah Theme" class="btn btn-success">
</form></td>';
if ($urutan  % 3 == 0) {
$admin .= '</tr></tr>';
}
$no++;
}
}
$admin .= '</table></div>';	
}

echo $admin;
}

?>