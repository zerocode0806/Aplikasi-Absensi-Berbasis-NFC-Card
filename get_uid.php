<?php
require 'koneksi.php';
$q = mysqli_query($koneksi, "SELECT uid FROM uid_temp ORDER BY id DESC LIMIT 1");
$d = mysqli_fetch_assoc($q);
echo $d ? $d['uid'] : "";
?>
