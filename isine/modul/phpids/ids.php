<?php

#############################################
# Teamworks Content Management System
# http://www.teamworks.co.id
# 23 Februari 2014
# Author: webmaster@teamworks.co.id
#############################################

ob_start();
	$query = mysql_query("SELECT * FROM `intrusions` ORDER BY `id` DESC LIMIT 10");
	$nums = mysql_num_rows($query);
	echo '<ul>';
	if ($nums <= 0) {
	echo '<li>no data</li>';	
	}
	while($data = mysql_fetch_assoc($query)) {
		echo '<li><a href="#" title="'.htmlentities($data['page']).'" onclick="alert(this.title);return false;">'.$data['ip'].'</a><span style="color:gray;">'.$data['created'].'</span></li>';
		}
	echo '</ul>';

$out = ob_get_contents();
ob_end_clean();	
?>