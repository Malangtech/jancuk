<?php

if (!defined('CMS_admin')) {
	Header("Location: ../index.php");
	exit;
}

global $koneksi_db,$maxadmindata,$theme,$style_include,$script_include,$url_situs;

$admin .= '<h3 class="page-header">Database MySQL</h1>';

$admin .= '<ol class="breadcrumb">
<li><a href="?opsi=database">Backup Database</a></li>
<li><a href="?opsi=database&amp;aksi=restore">Restore Database</a></li>
</ol>';

#############################################
# Backup Database
#############################################
if($_GET['aksi']==""){

$kid = int_filter($_GET['kid']);

$admin .='<h4 class="page-header">Backup Database</h4>';

if (isset($_POST['submit'])) {

$file = ''.$mysql_database.'_'.date('d-m-Y').'.sql';
$admin .= backup_tables("localhost",$mysql_user,$mysql_password,$mysql_database,$file);
$admin .='<div class="alert alert-success"><strong>Berhasil!</strong> Database berhasil disimpan</div>';
$style_include[] ='<meta http-equiv="refresh" content="1; url=?opsi=database">';
}

$admin .='<form class="form-horizontal" method="post" action="">
<div class="form-group">
    <label class="col-sm-2 control-label">Nama Database</label>
	<div class="col-sm-10"><input type="text" name="kategori" value="'.$mysql_database.'_'.date('d-m-Y').'" class="form-control" disabled>
    <input type="hidden" name="slug" value="'.$data['slug'].'"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label"></label>
    <div class="col-sm-10"><input type="submit" name="submit" value="Backup" class="btn btn-success"></div>
</div>
</form>';

$admin .= '<table class="table table-striped table-hover">
<thead><tr>
<th>Data Backup</th>
<th>Ukuran</th>
<th>Aksi</th>
</tr></thead><tbody>';
$folder = "id-content/db-backup/";
$handle = opendir($folder);
$extensi = array('sql');
while(false !== ($files = readdir($handle))){
$fileAndExt = explode('.', $files);
if(in_array(end($fileAndExt), $extensi)){

$admin .= '<tr>
<td>'.$files.'</td>
<td>'.format_size($folder,$files).'</td>
<td><a href="'.$folder.''.$files.'">Download</a> - 
<a href="?opsi=database&amp;aksi=hapus&amp;nama='.$files.'" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data backup ini ?\')" style="color:red">Hapus</a></td>
</tr>';
}
$no++;
}
$admin .= '</tbody></table>';
}

#############################################
# Restore Database
#############################################
if($_GET['aksi']=="restore"){

$kid = int_filter($_GET['kid']);

$admin .='<h4 class="page-header">Backup Database</h4>';

if (isset($_POST['submit'])) {

$file = ''.$mysql_database.'_'.date('d-m-Y').'.sql';
$admin .= backup_tables("localhost",$mysql_user,$mysql_password,$mysql_database,$file);
$admin .='<div class="alert alert-success"><strong>Berhasil!</strong> Database berhasil disimpan</div>';
$style_include[] ='<meta http-equiv="refresh" content="1; url=?opsi=database">';
}

$admin .='<form class="form-horizontal" method="post" action="">
<div class="form-group">
    <label class="col-sm-2 control-label">Nama Database</label>
	<div class="col-sm-10"><input type="text" name="kategori" value="'.$mysql_database.'_'.date('d-m-Y').'" class="form-control" disabled>
    <input type="hidden" name="slug" value="'.$data['slug'].'"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label"></label>
    <div class="col-sm-10"><input type="submit" name="submit" value="Backup" class="btn btn-success"></div>
</div>
</form>';

$admin .= '<table class="table table-striped table-hover">
<thead><tr>
<th>Data Backup</th>
<th>Ukuran</th>
<th>Aksi</th>
</tr></thead><tbody>';
$folder = "id-content/db-backup/";
$handle = opendir($folder);
$extensi = array('sql');
while(false !== ($files = readdir($handle))){
$fileAndExt = explode('.', $files);
if(in_array(end($fileAndExt), $extensi)){

$admin .= '<tr>
<td>'.$files.'</td>
<td>'.format_size($folder,$files).'</td>
<td><a href="'.$folder.''.$files.'">Download</a> - 
<a href="?opsi=database&amp;aksi=hapus&amp;nama='.$files.'" onclick="return confirm(\'Apakah Anda yakin ingin menghapus data backup ini ?\')" style="color:red">Hapus</a></td>
</tr>';
}
$no++;
}
$admin .= '</tbody></table>';
}

#############################################
# Hapus Backup Database
#############################################
if ($_GET['aksi']=='hapus'){
    $nama = $_GET['nama'];
	if ($nama){
	unlink ("./id-content/db-backup/".$nama);
    }
    $admin.='<div class="alert alert-success">File backup database <b>'.$nama.'</b> telah dihapus!</div>';
    $style_include[] ='<meta http-equiv="refresh" content="3; url=?opsi=database">';
}

echo $admin;

?>