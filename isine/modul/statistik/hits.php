<?php

#############################################
# Teamworks Content Management System
# http://www.teamworks.co.id
# 23 Februari 2014
# Author: webmaster@teamworks.co.id
#############################################

$perintah = "SELECT * FROM usercounter WHERE id=1";
$hasil = mysql_query( $perintah );
while ($data = mysql_fetch_row($hasil)) {
$hits=$data[3];
}

?>
