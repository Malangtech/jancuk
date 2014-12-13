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

//$index_hal=1;

global $koneksi_db,$email_master,$judul_situs,$alamatkantor;

$tengah ='<h4 class="bg">Data Pendidikan</h4>';

$tengah .='Data Dunia Pendidikan di Malang Raya dan Sekitarnya';

echo $tengah;

?>
