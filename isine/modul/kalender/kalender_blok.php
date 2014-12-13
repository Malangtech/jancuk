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

ob_start();

global $theme,$style_include,$script_include,$url_situs;

$style_include[] = '<style type="text/css">
/*<![CDATA[*/
@import url("'.$url_situs.'/id-content/modul/kalender/css/kalender.css");
/*]]>*/
</style>';
include 'id-content/modul/kalender/kalender_function.php';
echo '<div id="mod_calendar">'.$cals.'</div>
<div id="_loading_calendar" style="display:none;background:red;color:#fff;width:80px;padding-top:2px;padding-bottom:2px;position:absolute;">Loading ...</div>';

echo <<<cal


<script language="javascript" type="text/javascript">
//<![CDATA[
function requestCalendarUrl(url){
var req = false;
try {
req = new ActiveXObject("Msxml2.XMLHTTP");
} catch (e) {
try {
req = new ActiveXObject("Microsoft.XMLHTTP");
} catch (E) {
req = false;
}
}
if (!req && typeof XMLHttpRequest != 'undefined') {
req = new XMLHttpRequest();
}
document.getElementById('_loading_calendar').style.display = 'inline';
req.open('get','/id-content/modul/kalender/kalender_response.php?'+url,true);
req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
req.onreadystatechange = function() {
	if (req.readyState == 4 && req.status == 200) {
document.getElementById('_loading_calendar').style.display = 'none';
document.getElementById('mod_calendar').innerHTML = req.responseText;
		}
	};
req.send(null);
}
function kukiover(calid){
var theform = document.getElementById(calid).style.display="block";
}
function kukiout(calid){
var theform = document.getElementById(calid).style.display="none";
}
//]]>
</script>
cal;

$out = ob_get_contents();
ob_end_clean();

if (isset ($toolstips_cal_EVENT) && (@$_GET['act'] != 'calendar_view' or !isset ($_GET['sel_date']))){
kotakjudul ('Event','<span class="boxisimenu">'.$toolstips_cal_EVENT.'</span>');
}

?>