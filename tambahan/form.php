<?php


if (!defined('CMS_CONTENT')) {
	Header("Location: ../index.php");
	exit;
}

function select_value ($name, $selected, $value = array (),$opt='',$alert='',$pilihan='-pilih-') {

$bose ="<select name='$name' size='1' $opt $alert>"; 
$bose .="<option value=''>$pilihan</option>";
if (is_array ($value)){
foreach ($value as $k=>$v) {
if (strtolower($k) == strtolower($selected)){
$bose .="<option value=\"".$k."\" selected>$v</option>";
}else {
$bose .="<option value=\"".$k."\">$v</option>";
}
}
}  
$bose .="</select>";
return $bose;	
}

function input_form ($alert, $nama, $value, $size=28, $type='text',$option=''){
if (!empty($value)) {$values = 'value="'.$value.'"';}else {$values='';}
$txt = "<input $alert onblur=\"$nama.style.color='#6A8FB1'; this.className='inputblur'\" onfocus=\"$nama.style.color='#FB6101'; this.className='inputfocus'\" type='$type' name='$nama' size='$size' $values $option>";
return $txt;	
}

?>