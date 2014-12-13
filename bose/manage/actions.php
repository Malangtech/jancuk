<?
if (!defined('CMS_admin')) {
    Header("Location: ../index.php");
    exit;
}

if (!cek_login ()){
   exit;
}
if ($_SESSION['LevelAkses']!="Administrator") {
	exit;
}

//$index_hal = 1;

function form_select($name,$value = array()) {
	$t = '<select name="'.$name.'" size="1">';
	if (is_array($value)) {
		foreach($value as $key=>$val) {
				if (@$_POST[$name] == $key) {
				$t .= '<option value="'.$key.'" selected="selected">'.$val.'</option>';	
				}else {
				$t .= '<option value="'.$key.'">'.$val.'</option>';
				}
			}
	}
	$t .= '</select>';
	return $t;
}

$admin ='<h4 class="page-header">Modul Actions</h4>';

$admin .='<ol class="breadcrumb">
<li><a href="?opsi=actions">Home</a></li>
<li><a href="?opsi=actions&amp;aksi=tambah_actions">Tambah Action Modul Baru</a></li>
</ol>';

#############################################
# Hapus Actions
#############################################
if($_GET['aksi']=="delete_action"){

$modul = mysql_escape_string($_GET['modul']);
$query = mysql_query("DELETE FROM actions WHERE modul = '$modul'");
if ($query) {
	header("location: admin.php?opsi=actions");
	exit;
}else {
	$admin .= '<div class="alert alert-danger">'.mysql_error().'</div>';
}
}

#############################################
# Tambah Actions
#############################################
if($_GET['aksi']=="tambah_actions"){

if (isset($_POST['submit'])) {

$modul_id = intval($_POST['modul_id']);
$modul = mysql_escape_string($_POST['modul']);
$posisi = intval($_POST['posisi']);
	
//$error = null;
	
	if (empty($_POST['modul'])) {
		$error .= '- Nama modul tidak boleh kosong<br/>';
		}
	if (!file_exists('id-content/modul/'.$_POST['modul'])) {
		$error .= '- path id-content/modul/'.$_POST['modul'].' tidak ada<br/>';
		}

	$cek1 = mysql_num_rows(mysql_query("SELECT `id` FROM actions WHERE modul_id = '$modul_id' AND modul = '$modul'"));
	if ($cek1) {
		$error .= '- id id-content/modul/blok sudah ada<br/>';
	}
		
		if ($error != '') {
			$admin .= '<div class="alert alert-danger">'.$error.'</div>';
		}else {
			$cek2 = mysql_query("SELECT (MAX(ordering) + 1) AS ordering FROM actions WHERE posisi = '$posisi' AND modul = '$modul'");
			$data = mysql_fetch_assoc($cek2);
			$ordering = $data['ordering'];
			$inserts = mysql_query("INSERT INTO actions (modul,posisi,ordering,modul_id) VALUES ('$modul','$posisi','$ordering','$modul_id')");
			if ($inserts) {
				$admin .= '<div class="alert alert-success">Sukses add data</div>';
			}else {
				$admin .= '<div class="alert alert-danger">'.mysql_error().'</div>';
			}
		}
		
}

$handler = array();
$query = mysql_query("SELECT * FROM modul ORDER BY ordering");
while($data = mysql_fetch_assoc($query)) {
	$publish = $data['published'] ? 'publish' : 'no publish';
$handler[$data['id']] = $data['modul'] . ' - ' . $publish;	
}

$admin .= '<h4 class="border">Tambah Widgets</h4>';
$admin .= '<div class="border"><form method="post" action="">
<table>
<tr><td>Nama modul</td><td>:</td><td><input type="text" name="modul" value="'.htmlentities(stripslashes(@$_POST['modul'])).'" class="form-control"> contoh : news</td></tr>
<tr><td>Pilih blok/modul</td><td>:</td><td>'.form_select('modul_id',$handler).'</td></tr>
<tr><td>posisi</td><td>:</td><td>'.form_select('posisi',array('0'=>'kiri','1'=>'kanan')).'</td></tr>
<tr><td>&nbsp;</td><td>&nbsp;</td><td><br><input type="submit" name="submit" value="Tambah" class="btn btn-success"></td></tr>

</table>
</form></div>

<blockquote>
untuk nama modul di dapat dari action ,<br/> misal nya :<br/>
index.php?pilih=news&amp;mod=yes<br/>
Berarti nama modul nya : <b>news</b>

</blockquote>';
}
	
#############################################
# Lihat Actions
#############################################
if($_GET['aksi']=="lihat"){

$mod = mysql_escape_string(strip_tags($_GET['modul']));

$admin .= '<h4>'.$modul.'</h4><br>';

if (isset($_GET['delete'])) {
	$id = intval($_GET['id']);
	$del = mysql_query("DELETE FROM `actions` WHERE `id` = '$id'");
}


if (isset($_POST['submit'])) {
	if (is_array($_POST['ordering'])) {
	foreach($_POST['ordering'] as $key=>$val) {
		$posisi = $_POST['posisi'][$key];
		$ordering = $_POST['ordering'][$key];
		$update = mysql_query("UPDATE actions SET posisi = '$posisi',ordering = '$ordering' WHERE id = '$key'");
	}
	}
}

$admin .= '<b>Leftside</b>';
$admin .= '<form method="post" action="">
<table class="table table-striped table-hover">
<thead><tr>
<th>Block</th>
<th>Posisi</th>
<th>Ordering</th>
<th>Aksi</th>
</tr></thead><tbody>';
$query = mysql_query("SELECT actions.*,modul.modul FROM actions LEFT JOIN modul ON 
(modul.id = actions.modul_id) WHERE actions.modul = '$mod' AND actions.posisi = 0 ORDER BY actions.ordering");
while($data = mysql_fetch_assoc($query)) {
$select1 = '<select name="posisi['.$data['id'].']">';
if ($data['posisi'] == 0) {
$select1 .= '<option value="0" selected="selected">kiri</option>';
$select1 .= '<option value="1">kanan</option>';
}else {
$select1 .= '<option value="0">kiri</option>';
$select1 .= '<option value="1" selected="selected">kanan</option>';
}
$select1 .= '</select>';

$admin .= '<tr>
<td>'.$data['modul'].'</td>
<td>'.$select1.'</td>
<td><input type="text" name="order['.$data['id'].']" value="'.$data['ordering'].'" size="3" /></td>
<td><a href="?opsi=actions&amp;aksi=view&amp;modul='.$modul.'&amp;id='.$data['id'].'&amp;delete=1" onclick="return confirm(\'Apakah anda yakin ?\')">Hapus</a></td></tr>';
}
$admin .= '</tbody></table>';

$admin .= '<b>Rightside</b>';
$admin .= '<table class="table table-striped table-hover">
<thead><tr>
<th>Block</th>
<th>Posisi</th>
<th>Ordering</th>
<th>Aksi</th>
</tr></thead><tbody>';
$query = mysql_query("SELECT actions.*,modul.modul FROM actions LEFT JOIN modul ON 
(modul.id = actions.modul_id) WHERE actions.modul = '$mod' AND actions.posisi = 1 ORDER BY actions.ordering");
while($data = mysql_fetch_assoc($query)) {
$warna = empty ($warna) ? 'bgcolor="#f5f5f5"' : '';
$select1 = '<select name="posisi['.$data['id'].']">';
if ($data['posisi'] == 0) {
$select1 .= '<option value="0" selected="selected">kiri</option>';
$select1 .= '<option value="1">kanan</option>';
}else {
$select1 .= '<option value="0">kiri</option>';
$select1 .= '<option value="1" selected="selected">kanan</option>';
}
$select1 .= '</select>';
	
$admin .= '<tr>
<td>'.$data['modul'].'</td>
<td>'.$select1.'</td>
<td><input type="text" name="order['.$data['id'].']" value="'.$data['ordering'].'"></td>
<td><a href="admin.php?opsi=actions&amp;action=view&amp;modul='.$modul.'&amp;id='.$data['id'].'&amp;delete=1" onclick="return confirm(\'Apakah anda yakin ?\')">Hapus</a></td></tr>';
}
$admin .= '</tbody></table><br/><input type="submit" name="submit" value="save"></form>';
}
	
#############################################
# Data Actions
#############################################
if($_GET['aksi']==""){

$admin .= '<table class="table table-striped table-hover">
<thead><tr>
<th>Modul Action</th>
<th>View</th>
<th>Aksi</th>
</tr></thead>';
$query = mysql_query("SELECT * FROM actions GROUP BY `modul`");
while($data = mysql_fetch_assoc($query)) {
$admin .= '<tr class="isi">
<td>'.$data['modul'].'</td>
<td><a href="?opsi=actions&amp;aksi=lihat&amp;mod='.$data['modul'].'">Lihat</a></td>
<td><a href="?opsi=actions&amp;aksi=delete_action&amp;modul='.$data['modul'].'" onclick="return confirm(\'Apakah anda yakin ?\')">Hapus</a></td>
</tr>';
}
$admin .= '</tbody></table>';
}

echo $admin;

?>