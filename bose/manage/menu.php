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
	
$admin .='<p class="judul">Access Denied !!!!!!</p>';
}else{
	
$admin .='<h3 class="page-header">Menu Manager</h3>';

$admin .='<ol class="breadcrumb">
<li><a href="?opsi=menu">Menu</a></li>
<li><a href="?opsi=menu&amp;aksi=tambah_menu">Buat Menu</a></li>
<li><a href="?opsi=menu&amp;aksi=menuuser">Menu Users</a></li>
<li><a href="?opsi=menu&amp;aksi=menu_admin">Menu Admin</a></li>
</ol>';

#############################################
# List Menu
#############################################
if($_GET['aksi']==""){
	
$hasil = $koneksi_db->sql_query( "SELECT * FROM menu WHERE kid=0 ORDER BY ordering" );

$querymax = mysql_query ("SELECT MAX(ordering) FROM menu");
$alhasil = mysql_fetch_row($querymax);	
$numbers_parent = $alhasil[0];

$admin .='<div class="table-responsive"><table class="table table-striped table-hover">
<thead><tr>
<th>No</th>
<th>Menu</th>
<th>Link</th>
<th class="text-center">Order</th>
<th class="text-center">Publikasi</th>
<th>Aksi</th>
</tr></thead><tbody>';
$no = 1;
while ($data = $koneksi_db->sql_fetchrow($hasil)) {
$parent	= $data['id'];
$publikasi = ($data['publikasi'] == 1) ? '<a class="text-success" href="?opsi=menu&amp;aksi=pub&amp;pub=tidak&amp;id='.$parent.'"><span class="glyphicon glyphicon-ok"></span></a>' : '<a class="text-danger" href="?opsi=menu&amp;aksi=pub&amp;pub=ya&amp;id='.$parent.'"><span class="glyphicon glyphicon-remove"></span></a>';
	
$orderd = '<a class="text-danger" href="'.$adminfile.'.php?opsi=menu&amp;aksi=mdown&amp;id='.$data['ordering'].'"><span class="glyphicon glyphicon-circle-arrow-down"></span></a>';    
$orderu = '<a class="text-info" href="'.$adminfile.'.php?opsi=menu&amp;aksi=mup&amp;id='.$data['ordering'].'"><span class="glyphicon glyphicon-circle-arrow-up"></span></a>'; 

$ordering_down = $orderd;    
$ordering_up = $orderu;        

if ($data['ordering'] == 1) $ordering_up = '&nbsp;&nbsp;&nbsp;&nbsp;';
if ($data['ordering'] == $numbers_parent) $ordering_down = '&nbsp;';		

$admin .='<tr>
<td>'.$no.'</td>
<td>'.$data['menu'].'</td>
<td>'.$data['url'].'</td>
<td class="text-center">'.$ordering_up.'  '.$ordering_down.'</td>
<td class="text-center">'.$publikasi.'</td>
<td><a class="text-info" href="?opsi=menu&amp;aksi=edit_menu&amp;id='.$data['id'].'">Edit</a> - 
<a class="text-danger" href="?opsi=menu&amp;aksi=hapus_menu&amp;id='.$data['id'].'" onclick="return confirm(\'Apakah Anda yakin ingin menghapus menu ini ?\')">Hapus</a></td>
</tr>';

$subhasil = $koneksi_db->sql_query("SELECT * FROM menu WHERE kid='$parent' ORDER BY ordering");		
$jmlsub = $koneksi_db->sql_numrows($subhasil);
	
$querymax = $koneksi_db->sql_query("SELECT MAX(ordering) FROM menu WHERE kid=$parent");
$alhasil = $koneksi_db->sql_numrows($querymax);	
$numbers = $alhasil['id'];
		
$i = 1;
while ($subdata = $koneksi_db->sql_fetchrow($subhasil)) {            
$spublikasi = ($subdata['publikasi'] == 1) ? '<a class="text-success" href="?opsi=menu&amp;aksi=pub&amp;pub=tidak&amp;id='.$subdata['id'].'"><span class="glyphicon glyphicon-ok"></span></a>' : '<a class="text-danger" href="?opsi=menu&amp;aksi=pub&amp;pub=ya&amp;id='.$subdata['id'].'"><span class="glyphicon glyphicon-remove"></span></a>';

$orderd = '<a class="text-warning" href="'.$adminfile.'.php?opsi=menu&amp;aksi=down&amp;id='.$subdata['ordering'].'&amp;parent='.$parent.'"><span class="glyphicon glyphicon-circle-arrow-down"></span></a>';    
$orderu = '<a class="text-success" href="'.$adminfile.'.php?opsi=menu&amp;aksi=up&amp;id='.$subdata['ordering'].'&amp;parent='.$parent.'"><span class="glyphicon glyphicon-circle-arrow-up"></span></a>'; 

$ordering_down = $orderd;    
$ordering_up = $orderu;        

if ($subdata['ordering'] == 1) $ordering_up = '&nbsp;&nbsp;&nbsp;&nbsp;';
if ($subdata['ordering'] == $numbers) $ordering_down = '&nbsp;';			

$admin .='<tr>
<td></td>
<td>'.$subdata['menu'].'</td>
<td>'.$subdata['url'].'</td>
<td class="text-center">'.$ordering_up.' '.$ordering_down.'</td>
<td class="text-center">'.$spublikasi.'</td>
<td><a class="text-info" href="?opsi=menu&amp;aksi=edit_menu&amp;id='.$subdata['id'].'">Edit</a> - 
<a class="text-danger" href="?opsi=menu&amp;aksi=hapus_menu&amp;id='.$subdata['id'].'" onclick="return confirm(\'Apakah Anda yakin ingin menghapus menu ini ?\')">Hapus</td>
</tr>';
$i++;		
}
unset($numbers);
$no++;
}
$admin .= '</tbody></table></div>';
}

#############################################
# Tambah Menu
#############################################
if($_GET['aksi']=="tambah_menu"){
	
if (isset($_POST['submit'])) {
$kid	= $_POST['kid'];
$menu	= $_POST['menu'];
$url	= $_POST['url'];

$error = '';
if (!$menu)	$error .= "<strong>Gagal!</strong> Nama menu belum diisi!<br />";
if (!$url) 	$error .= "<strong>Gagal!</strong> URL menu belum diisi!<br />";

if ($error){
$admin.='<div class="alert alert-danger">'.$error.'</div>';
}else {
$url = str_replace('&amp;','&',$url);
$url = str_replace('&','&amp;',$url);

if($kid==0){
$query = $koneksi_db->sql_query("SELECT max(ordering) as maxOR FROM menu");
$data  = $koneksi_db->sql_fetchrow($query);
$maxOR = $data['maxOR']+1;
}else{
$query = $koneksi_db->sql_query("SELECT max(ordering) as maxOR FROM menu WHERE kid=$kid");
$data  = $koneksi_db->sql_fetchrow($query);
$maxOR = $data['maxOR']+1;
}

$hasil = $koneksi_db->sql_query("INSERT INTO menu (kid,menu,url,ordering) VALUES ('$kid','$menu','$url','$maxOR')");
if($hasil){
$admin.='<div class="alert alert-success"><strong>Berhasil!</strong> Menu baru berhasil disimpan</div>';
$style_include[] ='<meta http-equiv="refresh" content="1; url=?opsi=menu">';
}		
}
}

$url = isset($_POST['submit']) ? $_POST['url'] : @$_GET['url'];
$menu = isset($_POST['submit']) ? $_POST['menu'] : @$_GET['menu'];
	
$admin .='<form class="form-horizontal" method="post" action="">    
<div class="form-group">          
	<label class="col-sm-2 control-label">Menu</label>
	<div class="col-sm-10"><input type="text" name="menu" value="'.$menu.'" class="form-control" placeholder="Masukkan nama menu"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Parent</label>
	<div class="col-sm-10"><select name="kid" class="form-control">';
$s = $koneksi_db->sql_query("SELECT * FROM menu WHERE kid=0 ORDER BY id ASC");
$admin .= '<option value="0">None</option>';
while ($data = $koneksi_db->sql_fetchrow($s)) {
$admin .= '<option value="'.$data['id'].'">'.$data['menu'].'</option>';
}
$admin .= '</select></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Link</label>        
	<div class="col-sm-10"><input type="text" size="50" name="url" value="'.$url.'" class="form-control" placeholder="Masukkan URL menu">
	<span class="help-block">contoh : <i>http://www.google.com</i></span></div>
</div>        
<div class="form-group">
	<label class="col-sm-2 control-label"></label>
	<div class="col-sm-10"><input type="submit" name="submit" value="Simpan" class="btn btn-success"></div>
</div></form>';
}

#############################################
# Edit Menu
#############################################
if($_GET['aksi']=="edit_menu"){

$id     = int_filter($_GET['id']);
	
if (isset($_POST['submit'])) {
$menu	= $_POST['menu'];
$kid	= $_POST['kid'];
$url	= $_POST['url'];
	
if (!$menu)  $error .= "Error: Silahkan Masukkan Nama Menunya!<br />";
if (!$url) $error .= "Error: Silahkan Masukkan Url Menunya!<br />";
	
if ($error){
$admin.='<div class="error>'.$error.'</div>';
}else{
		
$url = str_replace('&amp;','&',$url);
$url = str_replace('&','&amp;',$url);

if($kid==0){
$query = $koneksi_db->sql_query("SELECT max(ordering) as maxOR FROM menu");
$data  = $koneksi_db->sql_fetchrow($query);
$maxOR = $data['maxOR']+1;
}else{
$query = $koneksi_db->sql_query("SELECT max(ordering) as maxOR FROM menu WHERE kid=$kid");
$data  = $koneksi_db->sql_fetchrow($query);
$maxOR = $data['maxOR']+1;
}

$hasil = $koneksi_db->sql_query( "UPDATE menu SET menu='$menu',kid='$kid',url='$url',ordering='$maxOR' WHERE id='$id'" );
if($hasil){
$admin.='<div class="alert alert-success"><strong>Berhasil!</strong> Menu telah disimpan</div>';
$style_include[] ='<meta http-equiv="refresh" content="3; url=?opsi=menu">';
}
}
}else{
$hasil = $koneksi_db->sql_query( "SELECT * FROM menu WHERE id=$id" );
while ($data = $koneksi_db->sql_fetchrow($hasil)) {    
$kid	= $data['kid'];    
$menu	= $data['menu'];    
$url	= $data['url'];    
}

$admin .='<form class="form-horizontal" method="post" action="">    
<div class="form-group">
	<label class="col-sm-2 control-label">Menu</label>            
	<div class="col-sm-10"><input type="text" name="menu" value="'.$menu.'" class="form-control"></div>        
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Kategori</label>
	<div class="col-sm-10"><select name="kid" class="form-control">';
$hasil = $koneksi_db->sql_query("SELECT * FROM menu WHERE kid=0 ORDER BY id ASC");
$admin .= '<option value="0">None</option>';
while ($datas =  $koneksi_db->sql_fetchrow ($hasil)){
$pilihan = ($datas['id']==$kid)? "selected":'';
$admin .='<option value="'.$datas['id'].'" '.$pilihan.'>'.$datas['menu'].'</option>';
}
$admin .='</select></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Link</label>            
	<div class="col-sm-10"><input type="text" name="url" value="'.$url.'" class="form-control"></div>        
</div>        
<div class="form-group">
	<label class="col-sm-2 control-label"></label>
	<div class="col-sm-10"><input type="submit" name="submit" value="Simpan" class="btn btn-success"></div>        
</div>    
</form>';
}
}

#############################################
# Hapus Menu
#############################################
if($_GET['aksi']=="hapus_menu"){    
   
$id     = int_filter($_GET['id']);    
	
$hasil = $koneksi_db->sql_query("DELETE FROM menu WHERE id='$id'");    
if($hasil){    
$admin.='<div class="alert alert-success"><strong>Berhasil!<strong> Menu telah dihapus!</div>';    
$style_include[] ='<meta http-equiv="refresh" content="3; url=?opsi=menu">';    
}
}

#############################################
# Up
#############################################
if($_GET['aksi']=="up"){

$ID = int_filter ($_GET['id']);
$parent = int_filter ($_GET['parent']);
$select = mysql_query ("SELECT MAX(ordering) as sc FROM submenu WHERE parent='$parent'");
$data = mysql_fetch_array ($select);

if ($data['sc'] <= 0){
$qquery = mysql_query ("SELECT `id` FROM `submenu` WHERE parent='$parent'");
$integer = 1;
while ($getsql = mysql_fetch_assoc($qquery)){
mysql_query ("UPDATE `submenu` SET `ordering` = $integer WHERE `id` = '".$getsql['id']."'");
$integer++;	
}	
header ("location:?opsi=menu");
exit;	
}

$total = $data['sc'] + 1;
$update = mysql_query ("UPDATE submenu SET ordering='$total' WHERE ordering='".($ID-1)."' AND parent='$parent'"); 
$update = mysql_query ("UPDATE submenu SET ordering=ordering-1 WHERE ordering='$ID' AND parent='$parent'");
$update = mysql_query ("UPDATE submenu SET ordering='$ID' WHERE ordering='$total' AND parent='$parent'");   
header ("location:?opsi=menu");
}

if($_GET['aksi']=="down"){
$ID = int_filter ($_GET['id']);
$parent = int_filter ($_GET['parent']);
$select = mysql_query ("SELECT MAX(ordering) as sc FROM submenu WHERE parent='$parent'");
$data = mysql_fetch_array ($select);

if ($data['sc'] <= 0){
$qquery = mysql_query ("SELECT `id` FROM `submenu` WHERE parent='$parent'");
$integer = 1;
while ($getsql = mysql_fetch_assoc($qquery)){
mysql_query ("UPDATE `submenu` SET `ordering` = $integer WHERE `id` = '".$getsql['id']."'");
$integer++;	
}	
	
header ("location:?opsi=menu");
exit;	
}

$total = $data['sc'] + 1;
$update = mysql_query ("UPDATE submenu SET ordering='$total' WHERE ordering='".($ID+1)."' AND parent='$parent'"); 
$update = mysql_query ("UPDATE submenu SET ordering=ordering+1 WHERE ordering='$ID' AND parent='$parent'");
$update = mysql_query ("UPDATE submenu SET ordering='$ID' WHERE ordering='$total' AND parent='$parent'");

header ("location:?opsi=menu");
}

if($_GET['aksi']=="mup"){

$ID = int_filter ($_GET['id']);
$select = $koneksi_db->sql_query ("SELECT MAX(ordering) as sc FROM menu");
$data = $koneksi_db->sql_fetchrow ($select);

if ($data['sc'] <= 0){
$qquery = mysql_query ("SELECT `id` FROM `submenu`");
$integer = 1;
while ($getsql = mysql_fetch_assoc($qquery)){
mysql_query ("UPDATE `menu` SET `ordering` = $integer WHERE `id` = '".$getsql['id']."'");
$integer++;	
}	
	
header ("location:".$adminfile.".php?opsi=menu");
exit;	
}

$total = $data['sc'] + 1;
$update = $koneksi_db->sql_query ("UPDATE menu SET ordering='$total' WHERE ordering='".($ID-1)."'"); 
$update = $koneksi_db->sql_query ("UPDATE menu SET ordering=ordering-1 WHERE ordering='$ID'");
$update = $koneksi_db->sql_query ("UPDATE menu SET ordering='$ID' WHERE ordering='$total'");   
header ("location:".$adminfile.".php?opsi=menu");
}

if($_GET['aksi']=="mdown"){
$ID = int_filter ($_GET['id']);
$select = $koneksi_db->sql_query ("SELECT MAX(ordering) as sc FROM menu");
$data = $koneksi_db->sql_fetchrow ($select);

if ($data['sc'] <= 0){
$qquery = mysql_query ("SELECT `id` FROM `menu`");
$integer = 1;
while ($getsql = mysql_fetch_assoc($qquery)){
mysql_query ("UPDATE `menu` SET `ordering` = $integer WHERE `id` = '".$getsql['id']."'");
$integer++;	
}	
	
header ("location:".$adminfile.".php?opsi=menu");
exit;	
}

$total = $data['sc'] + 1;
$update = $koneksi_db->sql_query ("UPDATE menu SET ordering='$total' WHERE ordering='".($ID+1)."'"); 
$update = $koneksi_db->sql_query ("UPDATE menu SET ordering=ordering+1 WHERE ordering='$ID'");
$update = $koneksi_db->sql_query ("UPDATE menu SET ordering='$ID' WHERE ordering='$total'");

header ("location:".$adminfile.".php?opsi=menu");
}

#############################################
# Publikasi
#############################################
if ($_GET['aksi'] == 'pub'){
if ($_GET['pub'] == 'tidak'){
	$id = int_filter ($_GET['id']);
	$koneksi_db->sql_query ("UPDATE menu SET publikasi=0 WHERE id='$id'");
}

if ($_GET['pub'] == 'ya'){
	$id = int_filter ($_GET['id']);
	$koneksi_db->sql_query ("UPDATE menu SET publikasi=1 WHERE id='$id'");
}
	header ("location:?opsi=menu");
}

#############################################
# Hapus Menu Admin
#############################################
if($_GET['aksi']== 'delma'){    
	global $koneksi_db;    
	$id     = int_filter($_GET['id']);    
	$hasil = $koneksi_db->sql_query("DELETE FROM `admin` WHERE `id`='$id'");    
	if($hasil){    
		$admin.='<div class="alert alert-success">Menu Admin berhasil dihapus! .</div>';    
		$style_include[] ='<meta http-equiv="refresh" content="1; url=?opsi=menu&aksi=menu_admin">';    
	}
}

#############################################
# Hapus Menu User
#############################################
if($_GET['aksi']== 'delmu'){    
   
$id     = int_filter($_GET['id']);
  
$hasil = $koneksi_db->sql_query("DELETE FROM `menu_users` WHERE `id`='$id'");    
if($hasil){    
$admin.='<div class="alert alert-success">Menu User berhasil dihapus! .</div>';    
$style_include[] ='<meta http-equiv="refresh" content="1; url=?opsi=menu&aksi=menuuser">';    
}
}

#############################################
# Edit Menu Admin
#############################################
if($_GET['aksi'] == 'editma'){
$id = int_filter ($_GET['id']);
$tengah = '';
if(isset($_POST['submit'])){
	$menu 		= $_POST['menu'];
	$url 		= $_POST['url'];
	$mod		= $_POST['mod'];
	$parent   = $_POST['parent'];
	$error 	= '';
	if (!$menu)  	$error .= "Error: Silahkan Isi Nama Menunya<br />";
	if (!$url)   	$error .= "Error: Silahkan Isi Urlnya<br />";
	if ($error){
		$tengah .= '<div class="alert alert-danger">'.$error.'</div>';
	}else{
		$hasil  = mysql_query( "UPDATE `admin` SET `menu`='$menu' ,`url`='$url' ,`mod`='$mod',`parent`='$parent' WHERE `id`='$id'" );
		if($hasil){
			$tengah .= '<div class="alert alert-success"><b>Menu Berhasil di Update.</b></div>';
			$style_include[] ='<meta http-equiv="refresh" content="1; url=?opsi=menu&aksi=menu_admin">';	
		}else{
			$tengah .= '<div class="alert alert-danger"><b>Menu Gagal di Update.</b></div>';
		}
	}

}
$query 		= mysql_query ("SELECT * FROM `admin` WHERE `id`='$id'");
$data 		= mysql_fetch_array($query);
$cekmod		= $data['mod'];
$parent		= $data['parent'];

$tengah .= '<form class="form-horizontal" method="post" action="">
<div class="form-group">
	<label class="col-sm-2 control-label">Nama Menu</label>
	<div class="col-sm-10"><input type="text" name="menu" value="'.$data['menu'].'" class="form-control"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">URL</label>
	<div class="col-sm-10"><input type="text" name="url" value="'.$data['url'].'" class="form-control"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Status Modul</label>
	<div class="col-sm-10"><select name="mod" class="form-control">';
if($cekmod == 1){
	$tengah .= '<option value="0">Tidak</option><option value="1" selected>Ya</option>';
}else{
	$tengah .= '<option value="0" selected>Tidak</option><option value="1">Ya</option>';
}
$tengah .= '</select></div>
</div>
<div class="form-group">       
	<label class="col-sm-2 control-label">Parent</label>
	<div class="col-sm-10"><select name="parent" class="form-control">';
$tengah .='<option value="0" selected> None </option>';
$hasil = $koneksi_db->sql_query( "SELECT * FROM admin where parent='0' ORDER BY ordering" );
while ($data = $koneksi_db->sql_fetchrow($hasil)) {
	$pilihan = ($data[0]==$parent)?"selected":'';
	$tengah .='<option value="'.$data['0'].'" '.$pilihan.'>'.$data[1].'</option>';
}

$tengah .='</select></div>        
</div> 
<div class="form-group">
	<label class="col-sm-2 control-label"></label>
	<div class="col-sm-10"><button type="submit" name="submit" class="btn btn-success">Simpan</button></div>
</div>
</form>';	
$admin .= $tengah;
}

if($_GET['aksi'] == 'editmu'){
$id = int_filter ($_GET['id']);
$tengah = '';
if(isset($_POST['submit'])){
	$menu 		= $_POST['menu'];
	$url 		= $_POST['url'];

	$error 	= '';
	if (!$menu)  	$error .= "Error: Silahkan Isi Nama Menunya<br />";
	if (!$url)   	$error .= "Error: Silahkan Isi Urlnya<br />";
	if ($error){
		$tengah .= '<div class="alert alert-danger">'.$error.'</div>';
	}else{
		$hasil  = mysql_query( "UPDATE `menu_users` SET `menu`='$menu' ,`url`='$url' WHERE `id`='$id'" );
		if($hasil){
			$tengah .= '<div class="alert alert-success"><b>Menu Berhasil di Update.</b></div>';
			$style_include[] ='<meta http-equiv="refresh" content="1; url=?opsi=menu&aksi=menuuser">';	
		}else{
			$tengah .= '<div class="alert alert-danger"><b>Menu Gagal di Update.</b></div>';
		}
	}

}
$query 		= mysql_query ("SELECT * FROM `menu_users` WHERE `id`='$id'");
$data 		= mysql_fetch_array($query);

$tengah .= '
<div class="border">
<form method="post" action="">
<table border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td style="padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 0">Nama Menu</td>
		<td style="padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 0">:</td>
		<td style="padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 0"><input type="text" name="menu" value="'.$data['menu'].'" size="25"></td>
	</tr>
	<tr>
		<td style="padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 0">URL</td>
		<td style="padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 0">:</td>
		<td style="padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 0"><input type="text" name="url" value="'.$data['url'].'" size="25"></td>
	</tr>
	<tr>
		<td style="padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 0"></td>
		<td style="padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 0"></td>
		<td style="padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 0">
		<input type="submit" value="Submit" name="submit"></td>
	</tr>
</table>
</form>
</div>';	
$admin .= $tengah;
}

if($_GET['aksi'] == 'editme'){
$id = int_filter ($_GET['id']);
$tengah = '';
if(isset($_POST['submit'])){
	$menu 		= $_POST['menu'];
	$url 		= $_POST['url'];

	$error 	= '';
	if (!$menu)  	$error .= "Error: Silahkan Isi Nama Menunya<br />";
	if (!$url)   	$error .= "Error: Silahkan Isi Urlnya<br />";
	if ($error){
		$tengah .= '<div class="alert alert-danger">'.$error.'</div>';
	}else{
		$hasil  = mysql_query( "UPDATE `menu_editor` SET `menu`='$menu' ,`url`='$url' WHERE `id`='$id'" );
		if($hasil){
			$tengah .= '<div class="alert alert-success"><b>Menu Berhasil di Update.</b></div>';
			$style_include[] ='<meta http-equiv="refresh" content="1; url=?opsi=menu&aksi=menueditor">';	
		}else{
			$tengah .= '<div class="alert alert-danger"><b>Menu Gagal di Update.</b></div>';
		}
	}

}
$query 		= mysql_query ("SELECT * FROM `menu_editor` WHERE `id`='$id'");
$data 		= mysql_fetch_array($query);

$tengah .= '
<div class="border">
<form method="post" action="">
<table border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td style="padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 0">Nama Menu</td>
		<td style="padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 0">:</td>
		<td style="padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 0"><input type="text" name="menu" value="'.$data['menu'].'" size="25"></td>
	</tr>
	<tr>
		<td style="padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 0">URL</td>
		<td style="padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 0">:</td>
		<td style="padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 0"><input type="text" name="url" value="'.$data['url'].'" size="25"></td>
	</tr>
	<tr>
		<td style="padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 0"></td>
		<td style="padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 0"></td>
		<td style="padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 0">
		<input type="submit" value="Submit" name="submit"></td>
	</tr>
</table>
</form>
</div>';	
$admin .= $tengah;
}

if($_GET['aksi'] == 'menuuser'){
	
$tengah = '';
if($_GET['op']== 'up'){

$ID = int_filter ($_GET['id']);
$select = $koneksi_db->sql_query ("SELECT MAX(ordering) as sc FROM menu_users");
$data = $koneksi_db->sql_fetchrow ($select);
$total = $data['sc'] + 1;
$update = $koneksi_db->sql_query ("UPDATE menu_users SET ordering='$total' WHERE ordering='".($ID-1)."'"); 
$update = $koneksi_db->sql_query ("UPDATE menu_users SET ordering=ordering-1 WHERE ordering='$ID'");
$update = $koneksi_db->sql_query ("UPDATE menu_users SET ordering='$ID' WHERE ordering='$total'");   
header ("location:?opsi=menu&aksi=menuuser");
}

if($_GET['op']== 'down'){
$ID = int_filter ($_GET['id']);
$select = $koneksi_db->sql_query ("SELECT MAX(ordering) as sc FROM menu_users");
$data = $koneksi_db->sql_fetchrow ($select);
$total = $data['sc'] + 1;
$update = $koneksi_db->sql_query ("UPDATE menu_users SET ordering='$total' WHERE ordering='".($ID+1)."'"); 
$update = $koneksi_db->sql_query ("UPDATE menu_users SET ordering=ordering+1 WHERE ordering='$ID'");
$update = $koneksi_db->sql_query ("UPDATE menu_users SET ordering='$ID' WHERE ordering='$total'");    
header ("location:?opsi=menu&aksi=menuuser");
}

if(isset($_POST['submit'])){
	$menu 		= $_POST['menu'];
	$url 		= $_POST['url'];
	$ceks 		= mysql_query ("SELECT MAX(ordering) as ordering FROM menu_users");
    $hasil 		= mysql_fetch_array ($ceks);
    $ordering 	= $hasil['ordering'] + 1;
	$error 	= '';
	if (!$menu)  	$error .= "Error: Silahkan Isi Nama Menunya<br />";
	if (!$url)   	$error .= "Error: Silahkan Isi Urlnya<br />";
	if ($error){
		$tengah .= '<div class="alert alert-danger">'.$error.'</div>';
	}else{
		$hasil  = mysql_query( "INSERT INTO `menu_users` (`menu` ,`url` ,`ordering`) VALUES ('$menu','$url','$ordering')" );
		if($hasil){
			$tengah .= '<div class="alert alert-success"><b>Menu Berhasil di Buat.</b></div>';
		}else{
			$tengah .= '<div class="alert alert-danger"><b>Menu Gagal di Buat.</b></div>';
		}
		unset($menu);
		unset($url);
	}

}
$menu     		= !isset($menu) ? '' : $menu;
$url     		= !isset($url) ? '' : $url;

$tengah .= '
<div class="border">
<form method="post" action="">
<table border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td style="padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 0">Nama Menu</td>
		<td style="padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 0">:</td>
		<td style="padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 0"><input type="text" name="menu" value="'.$menu.'" size="30"></td>
	</tr>
	<tr>
		<td style="padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 0">URL</td>
		<td style="padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 0">:</td>
		<td style="padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 0"><input type="text" name="url" value="'.$url.'" size="50"></td>
	</tr>
	<tr>
		<td style="padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 0"></td>
		<td style="padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 0"></td>
		<td style="padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 0">
		<input type="submit" value="Publish" name="submit" class="button"></td>
	</tr>
</table>
</form>
</div>';
$tengah .= '<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border:1px solid #DFDFDF; border-radius:5px;">
<tr style="background:url(images/bg-tabel.png) top center repeat-x; font-family:Georgia; font-size:14px;">
<td style="width:20px; text-align:center;padding:9px 10px 9px 10px; border-bottom:1px solid #DFDFDF;">No</td>
<td style="text-align:left; padding:9px 10px 9px 10px; border-bottom:1px solid #DFDFDF;">Nama Menu</td>
<td style="text-align:center; padding:9px 10px 9px 10px; border-bottom:1px solid #DFDFDF;">Ordering</td>
<td style="text-align:center; padding:9px 10px 9px 10px; border-bottom:1px solid #DFDFDF;">Aksi</td>
</tr>';

$no =1;
$query 		= mysql_query ("SELECT * FROM `menu_users` ORDER BY `ordering` ASC");
$cekmax 	= mysql_query ("SELECT MAX(`ordering`) FROM `menu_users`");
$datacekmax = mysql_fetch_row($cekmax);
$numbers 	= $datacekmax[0];
while($data = mysql_fetch_array($query)) {
$orderd = '<a class="image" href="?opsi=menu&amp;aksi=menuuser&amp;op=down&amp;id='.$data['ordering'].'"><img src="images/downarrow.png" border="0" alt="down" /></a>';    
$orderu = '<a class="image" href="?opsi=menu&amp;aksi=menuuser&amp;op=up&amp;id='.$data['ordering'].'"><img src="images/uparrow.png" border="0" alt="up" /></a>';    
$ordering_down = $orderd;    
$ordering_up = $orderu;        

if ($data['ordering'] == 1) $ordering_up = '&nbsp;&nbsp;&nbsp;';
if ($data['ordering'] == $numbers) $ordering_down = '&nbsp;';

$warna = empty ($warna) ? 'bgcolor="#f5f5f5"' : '';
$tengah .= '<tr '.$warna.'>
<td style="text-align:center; padding:4px 10px 4px 10px; border-bottom:1px solid #DFDFDF;">'.$no.'</td>
<td style="padding:4px 10px 4px 10px; border-bottom:1px solid #DFDFDF;">'.$data['menu'].'</td>
<td style="text-align:center; padding:4px 10px 4px 10px; border-bottom:1px solid #DFDFDF;">'.$ordering_up.'  '.$ordering_down.'</td>
<td style="text-align:center; padding:4px 10px 4px 10px; border-bottom:1px solid #DFDFDF;"><a href="?opsi=menu&amp;aksi=delmu&amp;id='.$data['id'].'">Hapus</a> - <a href="?opsi=menu&amp;aksi=editmu&amp;id='.$data['id'].'">Edit</a></td>
</tr>';
$no++;		
}	
$tengah .= '<tr><td colspan="4" style="text-align:center; padding:4px 10px 4px 10px;">&nbsp;</td></tr></table>';
$admin .= $tengah;
}

if($_GET['aksi'] == 'menueditor'){
	
$tengah = '';
if($_GET['op']== 'up'){

$ID = int_filter ($_GET['id']);
$select = $koneksi_db->sql_query ("SELECT MAX(ordering) as sc FROM menu_editor");
$data = $koneksi_db->sql_fetchrow ($select);
$total = $data['sc'] + 1;
$update = $koneksi_db->sql_query ("UPDATE menu_editor SET ordering='$total' WHERE ordering='".($ID-1)."'"); 
$update = $koneksi_db->sql_query ("UPDATE menu_editor SET ordering=ordering-1 WHERE ordering='$ID'");
$update = $koneksi_db->sql_query ("UPDATE menu_editor SET ordering='$ID' WHERE ordering='$total'");   
header ("location:?opsi=menu&aksi=menueditor");
}

if($_GET['op']== 'down'){
$ID = int_filter ($_GET['id']);
$select = $koneksi_db->sql_query ("SELECT MAX(ordering) as sc FROM menu_editor");
$data = $koneksi_db->sql_fetchrow ($select);
$total = $data['sc'] + 1;
$update = $koneksi_db->sql_query ("UPDATE menu_editor SET ordering='$total' WHERE ordering='".($ID+1)."'"); 
$update = $koneksi_db->sql_query ("UPDATE menu_editor SET ordering=ordering+1 WHERE ordering='$ID'");
$update = $koneksi_db->sql_query ("UPDATE menu_editor SET ordering='$ID' WHERE ordering='$total'");    
header ("location:?opsi=menu&aksi=menueditor");
}

if(isset($_POST['submit'])){
	$menu 		= $_POST['menu'];
	$url 		= $_POST['url'];
	$ceks 		= mysql_query ("SELECT MAX(ordering) as ordering FROM menu_editor");
    $hasil 		= mysql_fetch_array ($ceks);
    $ordering 	= $hasil['ordering'] + 1;
	$error 	= '';
	if (!$menu)  	$error .= "Error: Silahkan Isi Nama Menunya<br />";
	if (!$url)   	$error .= "Error: Silahkan Isi Urlnya<br />";
	if ($error){
		$tengah .= '<div class="alert alert-danger">'.$error.'</div>';
	}else{
		$hasil  = mysql_query( "INSERT INTO `menu_editor` (`menu` ,`url` ,`ordering`) VALUES ('$menu','$url','$ordering')" );
		if($hasil){
			$tengah .= '<div class="alert alert-success"><b>Menu Berhasil di Buat.</b></div>';
		}else{
			$tengah .= '<div class="alert alert-danger"><b>Menu Gagal di Buat.</b></div>';
		}
		unset($menu);
		unset($url);
	}

}
$menu     		= !isset($menu) ? '' : $menu;
$url     		= !isset($url) ? '' : $url;

$tengah .= '
<div class="border">
<form method="post" action="">
<table border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td style="padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 0">Nama Menu</td>
		<td style="padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 0">:</td>
		<td style="padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 0"><input type="text" name="menu" value="'.$menu.'" size="30"></td>
	</tr>
	<tr>
		<td style="padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 0">URL</td>
		<td style="padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 0">:</td>
		<td style="padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 0"><input type="text" name="url" value="'.$url.'" size="50"></td>
	</tr>
	<tr>
		<td style="padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 0"></td>
		<td style="padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 0"></td>
		<td style="padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 0">
		<input type="submit" value="Publish" name="submit" class="button"></td>
	</tr>
</table>
</form>
</div>';
$tengah .= '<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border:1px solid #DFDFDF; border-radius:5px;">
<tr style="background:url(images/bg-tabel.png) top center repeat-x; font-family:Georgia; font-size:14px;">
<td style="width:20px; text-align:center; padding:9px 10px 9px 10px; border-bottom:1px solid #DFDFDF;">No</td>
<td style="text-align:left; padding:9px 10px 9px 10px; border-bottom:1px solid #DFDFDF;">Nama Menu</td>
<td style="text-align:center; padding:9px 10px 9px 10px; border-bottom:1px solid #DFDFDF;">Ordering</td>
<td style="text-align:center; padding:9px 10px 9px 10px; border-bottom:1px solid #DFDFDF;">Aksi</td>
</tr>';

$no =1;
$query 		= mysql_query ("SELECT * FROM `menu_editor` ORDER BY `ordering` ASC");
$cekmax 	= mysql_query ("SELECT MAX(`ordering`) FROM `menu_editor`");
$datacekmax = mysql_fetch_row($cekmax);
$numbers 	= $datacekmax[0];
while($data = mysql_fetch_array($query)) {
$orderd = '<a class="image" href="?opsi=menu&amp;aksi=menueditor&amp;op=down&amp;id='.$data['ordering'].'"><img src="images/downarrow.png" border="0" alt="down" /></a>';    
$orderu = '<a class="image" href="?opsi=menu&amp;aksi=menueditor&amp;op=up&amp;id='.$data['ordering'].'"><img src="images/uparrow.png" border="0" alt="up" /></a>';    
$ordering_down = $orderd;    
$ordering_up = $orderu;        

if ($data['ordering'] == 1) $ordering_up = '&nbsp;&nbsp;&nbsp;';
if ($data['ordering'] == $numbers) $ordering_down = '&nbsp;';

$warna = empty ($warna) ? 'bgcolor="#f5f5f5"' : '';
$tengah .= '<tr '.$warna.'>
<td style="text-align:center; padding:4px 10px 4px 10px; border-bottom:1px solid #DFDFDF;">'.$no.'</td>
<td style="padding:4px 10px 4px 10px; border-bottom:1px solid #DFDFDF;">'.$data['menu'].'</td>
<td style="text-align:center; padding:4px 10px 4px 10px; border-bottom:1px solid #DFDFDF;">'.$ordering_up.'  '.$ordering_down.'</td>
<td style="text-align:center; padding:4px 10px 4px 10px; border-bottom:1px solid #DFDFDF;"><a href="?opsi=menu&amp;aksi=delme&amp;id='.$data['id'].'">Hapus</a> - <a href="?opsi=menu&amp;aksi=editme&amp;id='.$data['id'].'">Edit</a></td>
</tr>';
$no++;		
}	
$tengah .= '<tr><td colspan="4" style="text-align:center; padding:4px 10px 4px 10px;">&nbsp;</td></tr></table>';
$admin .= $tengah;
}

#############################################
# Menu Admin
#############################################
if($_GET['aksi']=="menu_admin"){
if($_GET['op']== 'mup'){
$ID = int_filter ($_GET['id']);
$select = $koneksi_db->sql_query ("SELECT MAX(ordering) as sc FROM admin where parent ='0'");
$data = $koneksi_db->sql_fetchrow ($select);
$total = $data['sc'] + 1;
$update = $koneksi_db->sql_query ("UPDATE admin SET ordering='$total' WHERE ordering='".($ID-1)."'"); 
$update = $koneksi_db->sql_query ("UPDATE admin SET ordering=ordering-1 WHERE ordering='$ID'");
$update = $koneksi_db->sql_query ("UPDATE admin SET ordering='$ID' WHERE ordering='$total'");   
header ("location:?opsi=menu&aksi=menu_admin");
}
if($_GET['op']== 'mdown'){
$ID = int_filter ($_GET['id']);
$select = $koneksi_db->sql_query ("SELECT MAX(ordering) as sc FROM admin where parent ='0'");
$data = $koneksi_db->sql_fetchrow ($select);
$total = $data['sc'] + 1;
$update = $koneksi_db->sql_query ("UPDATE admin SET ordering='$total' WHERE ordering='".($ID+1)."'"); 
$update = $koneksi_db->sql_query ("UPDATE admin SET ordering=ordering+1 WHERE ordering='$ID'");
$update = $koneksi_db->sql_query ("UPDATE admin SET ordering='$ID' WHERE ordering='$total'");    
header ("location:?opsi=menu&aksi=menu_admin");
}
if(isset($_POST['submit'])){
	$menu 		= $_POST['menu'];
	$url 		= $_POST['url'];
	$mod		= $_POST['mod'];
	$parent		= $_POST['parent'];
	if($parent=='0'){
	$ceks 		= mysql_query ("SELECT MAX(ordering) as ordering FROM admin where parent='0'");
	}else{
	$ceks 		= mysql_query ("SELECT MAX(ordering) as ordering FROM admin where parent = $parent");
	}
    $hasil 		= mysql_fetch_array ($ceks);
    $ordering 	= $hasil['ordering'] + 1;
	$error 	= '';
	if (!$menu)  	$error .= "Error: Silahkan Isi Nama Menunya<br />";
	if (!$url)   	$error .= "Error: Silahkan Isi Urlnya<br />";
	if ($error){
		$admin .= '<div class="alert alert-danger">'.$error.'</div>';
	}else{
		$hasil  = mysql_query( "INSERT INTO `admin` (`menu` ,`url` ,`mod` ,`ordering`,`parent`) VALUES ('$menu','$url','$mod','$ordering','$parent')" );
		if($hasil){
			$admin .= '<div class="alert alert-success"><b>Menu Berhasil di Buat.</b></div>';
		}else{
			$admin .= '<div class="alert alert-danger"><b>Menu Gagal di Buat.</b></div>';
		}
		unset($menu);
		unset($url);
		unset($parent);
	}

}
$menu	= !isset($menu) 	? '' : $menu;
$url	= !isset($url) 		? '' : $url;
$parent	= !isset($parent)	? '' : $parent;

$selparent .= '<select name="parent" class="form-control">';
$hasil = $koneksi_db->sql_query("SELECT * FROM `admin` WHERE `parent`='0' ORDER BY `id` ASC");
$selparent .= '<option value="0">None</option>';
while ($data =  $koneksi_db->sql_fetchrow ($hasil)){
$id = $data['id'];
$selparent .= '<option value="'.$data['id'].'">'.$data['menu'].'</option>';
}
$selparent .= '</select>';

$admin .= '<form class="form-horizontal" method="post" action="">
<div class="form-group">
	<label class="col-sm-2 control-label">Nama Menu</label>
	<div class="col-sm-10"><input type="text" name="menu" value="'.$menu.'" class="form-control"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Link</label>
	<div class="col-sm-10"><input type="text" name="url" value="'.$url.'" class="form-control"></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Parent</label>
	<div class="col-sm-10">'.$selparent.'</div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Status Modul</label>
	<div class="col-sm-10"><select name="mod" class="form-control"><option value="0">Tidak</option><option value="1">Ya</option></select></div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label"></label>
	<div class="col-sm-10"><button type="submit" name="submit" class="btn btn-success">Tambah</button></div>
</div>
</form>';	
$hasil = $koneksi_db->sql_query( "SELECT * FROM admin WHERE parent='0' ORDER BY ordering" );

$querymax = mysql_query ("SELECT MAX(`ordering`) FROM `admin` WHERE parent='0'");
$alhasil = mysql_fetch_row($querymax);	
$numbers_parent = $alhasil[0];

$admin .='<div class="table-responsive"><table class="table table-striped table-hover">
<thead><tr>
<th>No</th>
<th>Menu</th>
<th>Link</th>
<th>Order</th>
<th>Aksi</th>
</tr></thead><tbody>';
$no = 1;
while ($data = $koneksi_db->sql_fetchrow($hasil)) {
$parentid=$data[0];

$orderd = '<a class="image" href="?opsi=menu&amp;aksi=menu_admin&amp;op=mdown&amp;id='.$data['ordering'].'"><span class="glyphicon glyphicon-circle-arrow-down"></span></a>';    
$orderu = '<a class="image" href="?opsi=menu&amp;aksi=menu_admin&amp;op=mup&amp;id='.$data['ordering'].'"><span class="glyphicon glyphicon-circle-arrow-up"></span></a>';    

   
$ordering_down = $orderd;    
$ordering_up = $orderu;        

if ($data['ordering'] == 1) $ordering_up = '&nbsp;&nbsp;&nbsp;';
if ($data['ordering'] == $numbers_parent) $ordering_down = '&nbsp;';	

$admin .='<tr>
<td>'.$no.'</td>
<td><b>'.$data['menu'].'</b></td>
<td>'.$data['url'].'</td>
<td>'.$ordering_up.'  '.$ordering_down.'</td>
<td><a class="text-info" href="?opsi=menu&amp;aksi=editma&amp;id='.$data['id'].'">Edit</a> - 
<a class="text-danger" href="?opsi=menu&amp;aksi=delma&amp;id='.$data['id'].'" onclick="return confirm(\'Apakah Anda Yakin Ingin Menghapus Data Ini ?\')">Hapus</a></td>
</tr>';

$subhasil = $koneksi_db->sql_query( "SELECT * FROM admin WHERE parent='$parentid' ORDER BY ordering ");		
$jmlsub = $koneksi_db->sql_numrows( $subhasil );	
$querymax = mysql_query ("SELECT MAX(`ordering`) FROM `admin` WHERE parent=$parentid");
$alhasil = mysql_fetch_row($querymax);	
$numbers = $alhasil[0];
if ($jmlsub>0) {
$warna = '';		
$i = 1;
while ($subdata = $koneksi_db->sql_fetchrow($subhasil)) {            
$orderd = '<a class="image" href="?opsi=menu&amp;aksi=menu_admin&amp;op=down&amp;id='.$data['ordering'].'"><span class="glyphicon glyphicon-circle-arrow-down"></span></a>';    
$orderu = '<a class="image" href="?opsi=menu&amp;aksi=menu_admin&amp;op=up&amp;id='.$data['ordering'].'"><span class="glyphicon glyphicon-circle-arrow-up"></span></a>'; 

$ordering_down = $orderd;    
$ordering_up = $orderu;        

if ($subdata['ordering'] == 1) $ordering_up = '&nbsp;&nbsp;&nbsp;';
if ($subdata['ordering'] == $numbers) $ordering_down = '&nbsp;';

if($subdata['mod']==1){
$url = '?opsi='.$subdata['url'].'&amp;modul=yes';
}else{
$url = '?opsi='.$subdata['url'].'';
}		

$admin .='<tr>
<td></td>
<td><a href="'.$url.'">'.$subdata['menu'].'</a></td>
<td>'.$url.'</td>
<td></td>
<td><a class="text-info" href="?opsi=menu&amp;aksi=editma&amp;id='.$subdata['id'].'">Edit</a> - 
<a class="text-danger" href="?opsi=menu&amp;aksi=delma&amp;id='.$subdata['id'].'" onclick="return confirm(\'Apakah Anda Yakin Ingin Menghapus Data Ini ?\')">Hapus</a>
</td>
</tr>';
$i++;		
}		
}
unset($numbers);
$no++;
}
$admin .= '</tbody></table></div>';
}
}

echo $admin;

?>