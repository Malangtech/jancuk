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

$tengah  = '<h3 class="page-header">Statistik Website</h3>';

$hasil = mysql_query("SELECT * FROM stat_browse");
$a =1;
while ($data = mysql_fetch_array($hasil)){
$PJUDUL = $data["pjudul"];
$PPILIHAN = explode("#", $data["ppilihan"]);
$PJAWABAN = explode("#", $data["pjawaban"]);
$jmlpil = count($PPILIHAN);
$JMLVOTE = array();
for($i=0;$i<$jmlpil;$i++){
$JMLVOTE[$a] = $JMLVOTE[$a] + $PJAWABAN[$i];
}
// Jika tidak ada vote, tetapkan jumlah vote = 1 untuk menghindari pembagian dengan nol
if($JMLVOTE[$a] == 0){
$JMLVOTE[$a] = 1;
}
$tengah .= '<div class="border"><strong>'.$PJUDUL.' :</strong></div>';
$tengah .= '<table class="table table-striped">';
for($i=0;$i<$jmlpil;$i++){
$persentase = round($PJAWABAN[$i] / $JMLVOTE[$a] * 100, 2);
$tengah .= '<tr><td>'.$PPILIHAN[$i].'</td>';
$loop = floor($persentase)* 2;
$tengah .= '<td><div class="progress progress-striped">
<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="'.$loop.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$loop.'%">
<span class="sr-only">'.$loop.'% Complete (success)</span></div></div></td>';
$tengah .= '<td>'.$PJAWABAN[$i] . ' = ('.$persentase.'%)</td></tr>';
}
$tengah .= '</table>';
$tengah .= '<div class="border">Total '.$JMLVOTE[$a].'</div>';
$a++;
}

echo $tengah;

?>