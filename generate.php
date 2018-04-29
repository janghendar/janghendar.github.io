<?php
  // buat koneksi dengan database mysql
  $dbhost = "localhost";
  $dbuser = "root";
  $dbpass = "J4n9h3nd4r24";
  $link = mysqli_connect($dbhost,$dbuser,$dbpass);
  
  //periksa koneksi, tampilkan pesan kesalahan jika gagal
  if(!$link){
    die ("Koneksi dengan database gagal: ".mysqli_connect_errno().
         " - ".mysqli_connect_error());
  }
  
  //buat database data_siswa jika belum ada
  $query = "CREATE DATABASE IF NOT EXISTS data_siswa";
  $result = mysqli_query($link, $query);
  
  if(!$result){
    die ("Query Error: ".mysqli_errno($link).
         " - ".mysqli_error($link));
  }
  else {
    echo "Database <b>'data_siswa'</b> berhasil dibuat... <br>";
  }
  
  //pilih database data_siswa
  $result = mysqli_select_db($link, "data_siswa");
  
  if(!$result){
    die ("Query Error: ".mysqli_errno($link).
         " - ".mysqli_error($link));
  }
  else {
    echo "Database <b>'data_siswa'</b> berhasil dipilih... <br>";
  }
 
  // cek apakah tabel siswa sudah ada. jika ada, hapus tabel
  $query = "DROP TABLE IF EXISTS siswa";
  $hasil_query = mysqli_query($link, $query);
  
  if(!$hasil_query){
    die ("Query Error: ".mysqli_errno($link).
         " - ".mysqli_error($link));
  }
  else {
    echo "Tabel <b>'siswa'</b> berhasil dihapus... <br>";
  }
  
  // buat query untuk CREATE tabel siswa
  $query  = "CREATE TABLE siswa (nis CHAR(8), nama VARCHAR(100), "; 
  $query .= "tempat_lahir VARCHAR(50), tanggal_lahir DATE, ";
  $query .= "kelas VARCHAR(50), jurusan VARCHAR(50), ";
  $query .= "nilai INT(3), PRIMARY KEY (nis))";

  $hasil_query = mysqli_query($link, $query);
  
  if(!$hasil_query){
      die ("Query Error: ".mysqli_errno($link).
           " - ".mysqli_error($link));
  }
  else {
    echo "Tabel <b>'siswa'</b> berhasil dibuat... <br>";
  }
  
  // buat query untuk INSERT data ke tabel siswa
  $query  = "INSERT INTO siswa VALUES "; 
  $query .= "('14005011', 'Riana Putria', 'Padang', '1996-11-23', ";
  $query .= "'FMIPA', 'Kimia', 3.1), ";
  $query .= "('15021044', 'Rudi Permana', 'Bandung', '1994-08-22', ";
  $query .= "'FASILKOM', 'Ilmu Komputer', 2.9), ";
  $query .= "('15003036', 'Sari Citra Lestari', 'Jakarta', '1997-12-31', ";
  $query .= "'Ekonomi', 'Manajemen', 3.5), ";
  $query .= "('15002032', 'Rina Kumala Sari', 'Jakarta', '1997-06-28', ";
  $query .= "'Ekonomi', 'Akuntansi', 3.4), ";
  $query .= "('13012012', 'James Situmorang', 'Medan', '1995-04-02', ";
  $query .= "'Kedokteran','Kedokteran Gigi', 2.7)";

  $hasil_query = mysqli_query($link, $query);
  
  if(!$hasil_query){
      die ("Query Error: ".mysqli_errno($link).
           " - ".mysqli_error($link));
  }
  else {
    echo "Tabel <b>'siswa'</b> berhasil diisi... <br>";
  }
    
  // cek apakah tabel admin sudah ada. jika ada, hapus tabel
  $query = "DROP TABLE IF EXISTS admin";
  $hasil_query = mysqli_query($link, $query);
  
  if(!$hasil_query){
    die ("Query Error: ".mysqli_errno($link).
         " - ".mysqli_error($link));
  }
  else {
    echo "Tabel <b>'admin'</b> berhasil dihapus... <br>";
  }
  
  // buat query untuk CREATE tabel admin
  $query  = "CREATE TABLE admin (username VARCHAR(50), password CHAR(40))"; 
  $hasil_query = mysqli_query($link, $query);
  
  if(!$hasil_query){
      die ("Query Error: ".mysqli_errno($link).
           " - ".mysqli_error($link));
  }
  else {
    echo "Tabel <b>'admin'</b> berhasil dibuat... <br>";
  }
  
  // buat username dan password untuk admin
  $username = "admin123";
  $password = sha1("rahasia");
  
  // buat query untuk INSERT data ke tabel admin
  $query  = "INSERT INTO admin VALUES ('$username','$password')"; 

  $hasil_query = mysqli_query($link, $query);
  
  if(!$hasil_query){
      die ("Query Error: ".mysqli_errno($link).
           " - ".mysqli_error($link));
  }
  else {
    echo "Tabel <b>'admin'</b> berhasil diisi... <br>";
  }
  
  // tutup koneksi dengan database mysql
  mysqli_close($link);
?>
