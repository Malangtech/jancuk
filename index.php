<?php

//cek_license ();
class microTimer {
function start() {
	global $starttime;
	$mtime = microtime ();
	$mtime = explode (' ', $mtime);
	$mtime = $mtime[1] + $mtime[0];
	$starttime = $mtime;
}
function stop() {
	global $starttime;
	$mtime = microtime ();
	$mtime = explode (' ', $mtime);
	$mtime = $mtime[1] + $mtime[0];
	$endtime = $mtime;
	$totaltime = round (($endtime - $starttime), 5);
	return $totaltime;
}
}

include 'tambahan/session.php';
@header("Content-type: text/html; charset=utf-8;");
ob_start("ob_gzhandler");
//session_register ('mod_ajax');
$_SESSION['mod_ajax'] = true;

$timer = new microTimer;
$timer->start();

if (file_exists("install.php")){
header ("location:install.php");
}

define('CMS_MODULE', true);
define('CMS_CONTENT', true);
define('CMS_THEMES', true);

include "tambahan/itile.php";
include "tambahan/mysql.php";
include "tambahan/configsitus.php";
include "tambahan/template.php";
//include "includes/excelreader2.php";
global $judul_situs,$theme;
cek_situs();
$_GET['aksi'] 	= !isset($_GET['aksi']) ? null : $_GET['aksi'];
$_GET['modul'] 	= !isset($_GET['modul']) ? null : $_GET['modul'];
$_GET['opsi'] 	= !isset($_GET['opsi']) ? null : $_GET['opsi'];
$_GET['act'] 	= !isset($_GET['act']) ? null : $_GET['act'];

$old_modules = !isset($old_modules) ? null : $old_modules;

ob_start();

$script_include[] = '';

switch($_GET['modul']) {
	
case 'yes':
if (file_exists('isine/modul/'.$_GET['opsi'].'/'.$_GET['opsi'].'.php') 
&& !isset($_GET['act']) 
&& !preg_match('/\.\./',$_GET['opsi'])) {
include 'isine/modul/'.$_GET['opsi'].'/'.$_GET['opsi'].'.php';
} 	else if (file_exists('isine/modul/'.$_GET['opsi'].'/act_'.$_GET['act'].'.php') 
&& !preg_match('/\.\./',$_GET['opsi'])
&& !preg_match('/\.\./',$_GET['act'])
) 
{
include 'isine/modul/'.$_GET['opsi'].'/act_'.$_GET['act'].'.php';
} else {
header("location:index.php");
exit;
} 
break;	
	
default:
if (!isset($_GET['opsi'])) {
//include 'content/normal.php';
include 'isine/themes/'.$theme.'/normal.php';
} else if (file_exists('isine/'.$_GET['opsi'].'.php') && !preg_match("/\.\./",$_GET['opsi'])){
include 'isine/'.$_GET['opsi'].'.php';	
} else {
header("location:index.php");
exit;		
}
break;	
}

$content = ob_get_contents();
ob_end_clean();

#############################################
# Left Side
#############################################
ob_start();
# Modul Kiri
modul(0);
# Blok Kiri
blok(0);
$leftside = ob_get_contents();
ob_end_clean(); 

#############################################
# Right Menu
#############################################
if (!isset($index_hal)){
ob_start();
# Modul Kanan
modul(1);
# Blok Kanan
blok(1);
$rightside = ob_get_contents();
ob_end_clean(); 
} else {
$style_include[] = '
<style type="text/css">
/*<![CDATA[*/
#main {
float: right;
margin-right: 10px;
padding: 0;
width: 750px;
}
/*]]>*/
</style>
';	
}

if ($_GET['aksi'] == 'logout') {
logout ();
}

#############################################
# Main Menu
#############################################
ob_start();
include "isine/mainmenu.php";
$mainmenu = ob_get_contents();
ob_end_clean(); 

#############################################
# Cek Login
#############################################
ob_start();
include "isine/ceklogin.php";
$ceklogin = ob_get_contents();
ob_end_clean(); 
#############################################
# index
#############################################
$style_include_out 	= !isset($style_include) 	? '' : implode("",$style_include);
$script_include_out = !isset($script_include) 	? '' : implode("",$script_include);
$rightside 			= !isset($rightside) 		? '' : $rightside;
$leftside 			= !isset($leftside) 		? '' : $leftside;
$ceklogin 			= !isset($ceklogin) 		? '' : $ceklogin;
$mainmenu 			= !isset($mainmenu) 		? '' : $mainmenu;

$define = array (
	'mainmenu'    		=> $mainmenu,
	'ceklogin'    		=> $ceklogin,
	'leftside'    		=> $leftside,
	'url'     		    => $url_cuk,
	'content'     		=> $content,
	'rightside'  		=> $rightside,
	'judul_situs' 		=> $judul_cuk,
	'style_include'		=> $style_include_out,
	'script_include' 	=> $script_include_out,
	'meta_description' 	=> $_META['description'],
	'meta_keywords' 	=> $_META['keywords'],
	'meta_author' 		=> $_META['author'],
	'timer' 			=> $timer->stop()
);

$tpl = new template ('isine/themes/'.$theme.'/default.html');
$tpl-> define_tag($define);

$tpl-> cetak();



?>