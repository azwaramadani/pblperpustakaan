<?php
// identitas database
$host = "localhost";
$user = "root";
$pass = "";
$database = "pblperpustakaan";

$connection = mysqli_connect($host, $user, $pass, $database);

if(!$connection){
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>