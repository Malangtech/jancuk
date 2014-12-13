<?php

#############################################
# Teamworks Content Management System
# http://www.teamworks.co.id
# 23 Februari 2014
# Author: webmaster@teamworks.co.id
#############################################

$perintah = "SELECT * FROM useraura WHERE is_online = '1'";
$hasil = mysql_query( $perintah );
$jumlah = $koneksi_db->sql_numrows( $perintah );

?>