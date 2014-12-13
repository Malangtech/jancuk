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

global $url_situs,$style_include;

$style_include[] = '<link href="'.$url_situs.'/id-content/modul/statistik/css/statistik.css" rel="stylesheet">';

ob_start();

echo '<table width="100%" border="0" cellspacing="0" cellpadding="0">';
include "id-content/modul/statistik/counter.php";
include "id-content/modul/statistik/online.php";
include "id-content/modul/statistik/hits.php";
include "id-content/modul/statistik/useronline.php";
echo '
<tr>
	<td><div class="pengunjung"></div></td>
	<td>Pengunjung</td>
	<td><b>'.$theCount.'</b></td>
</tr>
<tr>
	<td><div class="hits"></div></td>
	<td>Hits</td>
	<td><b>'.$hits.'</b></td>
</tr>
<tr>
	<td><div class="bulan"></div></td>
	<td>Bulan ini</td>
	<td><b>'.month().'</b></td>
</tr>
<tr>
	<td><div class="hari"></div></td>
	<td>Hari ini</td>
	<td><b>'.day().'</b></td>
</tr>
<tr>
	<td><div class="sekarang"></div></td>
	<td>Sekarang</td>
	<td><b>'.now().'</b></td>
</tr>
</table>';

$out = ob_get_contents();
ob_end_clean();

?>
