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

$index_hal=1;

if($_GET['aksi']=="detail"){
$tengah .='<h4 class="bg">Profile</h4>';
$UserId = text_filter(cleanText($_GET['UserId']));

$hasil  = $koneksi_db->sql_query("SELECT * FROM useraura WHERE UserId='$UserId'");

$data = $koneksi_db->sql_fetchrow($hasil);

$UserId =$data['UserId'];
$nama = $data['nama'];
$website = $data['website'];
$ym = $data['ym'];
$avatar = $data['avatar'];

//title 
$judul_situs = ''.$data['nama'].' | '.$judul_situs.'';

$avatar = ($data['avatar'] == '') ? '<img style="float:left; margin-right:10px;" src="'.$url_situs.'/mod/profile/images/profile-default.jpg" width="100" border="0" alt="'.$data['judul'].'" />' : '<img style="float:left; margin-right:10px;" src="'.$url_situs.'/mod/profile/images/'.$data['avatar'].'" width="100" border="0" alt="'.$data['judul'].'" />';

$tengah .='<div class="border"><table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="110" rowspan="3" valign="top">'.$avatar.'</td>
    <td width="75" valign="top">Nama</td>
    <td valign="top">: '.$data['nama'].'</td>
  </tr>
  <tr>
    <td valign="top">Website</td>
    <td valign="top">: '.$website.'</td>
  </tr>
  <tr>
    <td valign="top">YM</td>
    <td valign="top">: '.$ym.'</td>
  </tr>
</table></div>';

// Komentar FB
$tengah .='<h4 class="bg">Komentar Facebook</h4>';
$tengah .='<div class="fb-comments" data-href="'.$url_situs.'/profile/'.$UserId.'/'.AuraCMSSEO($nama).'.html" data-num-posts="5" data-width="610"></div>';
}
echo $tengah;

?>