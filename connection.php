<?php
  // buat koneksi dengan database mysql
  $dbhost = "localhost";
  $dbuser = "root";
  $dbpass = "J4n9h3nd4r24";
  $dbname = "data_siswa";
  $link = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname);
  
  //periksa koneksi, tampilkan pesan kesalahan jika gagal
  if(!$link){
    die ("Koneksi dengan database gagal: ".mysqli_connect_errno().
         " - ".mysqli_connect_error());
  }
?>
