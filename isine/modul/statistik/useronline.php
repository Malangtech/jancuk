<?php

#############################################
# Teamworks Content Management System
# http://www.teamworks.co.id
# 23 Februari 2014
# Author: webmaster@teamworks.co.id
#############################################

function useronline(){
$qw = mysql_query("SELECT count(user) as total FROM useraura where is_online='1'");
$countdataquery = mysql_fetch_assoc($qw);
$jumlah= $countdataquery['total'];
return "<b>$jumlah</b>";
}

?>