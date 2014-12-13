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

global $script_include,$style_include;

$script_include[] = '
<script type="text/javascript" language="javascript" src="mod/download/ajaxstarrater_v122/js/behavior.js"></script>
<script type="text/javascript" language="javascript" src="mod/download/ajaxstarrater_v122/js/rating.js"></script>';

$style_include[] = '<link rel="stylesheet" type="text/css" href="mod/download/ajaxstarrater_v122/css/rating.css" />';
$tengah ='<h4 class="bg">Download</h4>';
$tengah .= <<<ajax


<div id="load" style="display: none; width: 100px; color: #fff;  height: 17px; background-color: red;position:absolute;top:50%;left:50%;padding:2px;"> Loading<span id="ellipsis">...</span></div>
<div id="headerdownload"></div>
<div id="respon"></div>

<script type="text/javascript" src="mod/download/js/download.js"></script>
<script type="text/javascript">
window.onload = download.indexs;
</script>
ajax;


echo $tengah;

?>