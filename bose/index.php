<?php
include "../id-includes/session.php";
include "../id-includes/config.php";
include "../id-includes/fungsi.php";

if (!cek_login ()){
header("location:../id-login.php");
exit;
}else{

if ($_SESSION['LevelAkses'] &&  $_SESSION['LevelAkses']=="Administrator"){
header("location:../main.php");
exit;
}else{
header("location:../id-login.php");
}
}
?>