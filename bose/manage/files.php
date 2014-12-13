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

if (!cek_login ()){
	Header("Location: index.php");
	exit;
}else{

$admin ='<h3 class="page-header">File Manager</h3>';

$admin .='<ol class="breadcrumb">
<li><a href="?opsi=files">Home</a></li>
<li><a href="?opsi=files&amp;aksi=upload_files">Upload File</a></li>
</ol>';

if ($_SESSION['LevelAkses'] &&  $_SESSION['LevelAkses']=="Administrator"){
#############################################
# List Files
#############################################
if($_GET['aksi'] == ''){

$admin .='<div class="alert alert-info">
Catatan:<br>
Gunakan url seperti dibawah ini untuk menyisipkan image di artikel atau halaman web : <br>
"id-content/files/nama_file.extension"</b><br>
<b>contoh :</b> &lt;img src="id-content/files/teamworks.jpg" alt="" border="0"&gt;</div>';

$admin .= '<table class="table table-striped table-hover">
<thead><tr>
<th>Nama File</th>
<th>Ukuran</th>
<th>Aksi</th>
</tr></thead><tbody>';
$folder = 'id-content/files/';
$handle = opendir($folder);
$no = 1;
while ($file = readdir($handle)) {
if($file != '..' && $file !='.' && $file !=''&& $file !='Thumbs.db'){
if (is_dir($file)){
continue;
}else {
$deleted = '<a href="?opsi=files&amp;aksi=hapus&amp;nama='.$file.'" onclick="return confirm(\'Apakah Anda Ingin Menghapus Data Ini ?\')" style="color:red">Hapus</a>';
if ($file !='index.php'){

$admin .= '<tr>
<td><a href="'.$folder.''.$file.'" target="_blank">'.$file.'</a></td>
<td>'.format_size($folder,$file).'</td>
<td><span class="del">'.$deleted.'</span></td>
</tr>';
$warna = empty($warna) ? 'bgcolor="#efefef"' : '';
}
}
}
$no++;
}
closedir($handle);
clearstatcache();
$admin .= '</tbody></table>';
}

#############################################
# Upload Files
#############################################
if ($_GET['aksi']=='upload_files'){

$admin .='<h4 class="page-header">Upload Files</h4>';

global $max_size,$allowed_exts,$allowed_mime;

if (isset($_POST['submit'])) {
    $image_name1=$_FILES['image1']['name'];
    $image_size1=$_FILES['image1']['size'];
    $image_name2=$_FILES['image2']['name'];
    $image_size2=$_FILES['image2']['size'];
    $image_name3=$_FILES['image3']['name'];
    $image_size3=$_FILES['image3']['size'];
    $image_name4=$_FILES['image4']['name'];
    $image_size4=$_FILES['image4']['size'];
    $image_name5=$_FILES['image5']['name'];
    $image_size5=$_FILES['image5']['size'];
	$error = '';
    if ($image_name1){
		@copy($_FILES['image1']['tmp_name'], "id-content/files/".$image_name1);
        //unlink($image);
        $admin.='<div class="alert alert-success">Upload file '.$image_name1.' berhasil!</div>';  
	}
	if ($image_name2){
		@copy($_FILES['image2']['tmp_name'], "id-content/files/".$image_name2);
        //unlink($image);
        $admin.='<div class="alert alert-success">Upload file '.$image_name2.' berhasil!</div>';  
	}
	if ($image_name3){
		@copy($_FILES['image3']['tmp_name'], "id-content/files/".$image_name3);
        //unlink($image);
        $admin.='<div class="alert alert-success">Upload file '.$image_name3.' berhasil!</div>';  
	}
	if ($image_name4){
		@copy($_FILES['image4']['tmp_name'], "id-content/files/".$image_name4);
        //unlink($image);
        $admin.='<div class="alert alert-success">Upload file '.$image_name4.' berhasil!</div>';  
	}
	if ($image_name5){
		@copy($_FILES['image5']['tmp_name'], "id-content/files/".$image_name5);
        //unlink($image);
        $admin.='<div class="alert alert-success">Upload file '.$image_name5.' berhasil!</div>';  
	}
	 $style_include[] ='<meta http-equiv="refresh" content="3; url=?opsi=files">';

}
$admin .='<form class="form-horizontal" method="post" enctype="multipart/form-data" action="">
<input type="hidden" name="MAX_FILE_SIZE" value="1000000">
<div class="form-group">
	<label class="col-sm-2 control-label">File 1</label>
	<div class="col-sm-10"><input type="file" name="image1"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">File 2</label>
	<div class="col-sm-10"><input type="file" name="image2"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">File 3</label>
	<div class="col-sm-10"><input type="file" name="image3"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">File 4</label>
	<div class="col-sm-10"><input type="file" name="image4"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">File 5</label>
	<div class="col-sm-10"><input type="file" name="image5"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label"></label>
	<div class="col-sm-10"><input type="submit" name="submit" value="Upload" class="btn btn-success"></div>
</div>
</form>';
}

#############################################
# Hapus Halaman
#############################################
if ($_GET['aksi']=='hapus'){
    $nama = $_GET['nama'];
	if ($nama){
	unlink ("id-content/files/".$nama);
    }
    $admin.='<div class="alert alert-success">File <b>'.$nama.'</b> telah dihapus</div>';
    $style_include[] ='<meta http-equiv="refresh" content="3; url=?opsi=files">';
}

}else{
	Header("Location: index.php");
	exit;
}

echo $admin;

}

?>