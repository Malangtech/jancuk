<?php

if (!defined('CMS_CONTENT')) {
	Header("Location: ../index.php");
	exit;
}

global $koneksi_db;

$username = $_SESSION["UserName"];
$u = $koneksi_db->sql_query( "SELECT * FROM berita_kat WHERE kid=$kid");
$datau = $koneksi_db->sql_fetchrow($u);
$avatar = ($datau['avatar'] == '') ? 
'<img style="float:left; margin:0 10px 0 0; max-width:20px;" src="id-content/modul/profile/images/profile-default.jpg">' : 
'<img style="float:left; margin:0 10px 0 0; max-width:20px;" src="id-content/modul/profile/images/'.$datau['avatar'].'">';

if ($_SESSION['UserName'] && isset ($_SESSION['UserName']) && !empty ($_SESSION['UserName'])  ){
echo '<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="'.$url_situs.'" title="Lihat Website" target="_blank"><span class="glyphicon glyphicon-fire"></span></a>
        </div><div class="collapse navbar-collapse">';
#############################################
# Administrator
#############################################
if ($_SESSION['LevelAkses'] &&  $_SESSION['LevelAkses']=="Administrator"){
$m = $koneksi_db->sql_query( "SELECT * FROM admin where parent=0 ORDER BY ordering ASC" );
echo '<ul class="nav navbar-nav navbar-left">';
while ($datam = $koneksi_db->sql_fetchrow($m)) {
$id = $datam['id'];

echo '<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">'.$datam['menu'].' <b class="caret"></b></a>';

$s = $koneksi_db->sql_query( "SELECT * FROM admin WHERE parent=$id ORDER BY menu ASC" );
echo '<ul class="dropdown-menu">';
while ($datas = $koneksi_db->sql_fetchrow($s)) {
$mod = $datas['mod'] == 1 ? '&amp;modul=yes' : '';
$url = $datas['mod'] == 1 ? $adminfile.".php?opsi=".$datas['url'].$mod : $adminfile.'.php?opsi='.basename($datas['url'],'.php');
echo '<li><a href="'.$url.'">'.$datas['menu'].'</a></li>';
}
echo '</ul>';
}
echo '</li></ul>';
}
#############################################
# User
#############################################
if ($_SESSION['LevelAkses'] &&  $_SESSION['LevelAkses']=="User"){
$hasil = $koneksi_db->sql_query( "SELECT * FROM menu_users ORDER BY ordering ASC" );
echo '<ul class="nav navbar-nav navbar-left">';
while ($data = $koneksi_db->sql_fetchrow($hasil)) {
echo '<li><a href="'.$data['url'].'">'.$data['menu'].'</a></li>';
}
echo '</ul>';
}

echo '<ul class="nav navbar-nav navbar-right">
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">'.$avatar.' Halo, '.$username.' <b class="caret"></b></a>
          <ul class="dropdown-menu">
            <li><a href="?opsi=profile&amp;modul=yes">Profil</a></li>
            <li><a href="?opsi=user&amp;modul=yes&amp;aksi=ubah_password">Ubah Password</a></li>
            <li><a href="index.php?aksi=logout"><b>Keluar</b></a></li>
          </ul>
        </li>
      </ul>';

echo '</div></div></div>';
}

?>