<?php
  // periksa apakah user sudah login, cek kehadiran session name 
  // jika tidak ada, redirect ke login.php
  session_start();
  if (!isset($_SESSION["nama"])) {
     header("Location: login.php");
  }
  
  // buka koneksi dengan MySQL
  include("connection.php");
  
  // cek apakah form telah di submit
  if (isset($_POST["submit"])) {
    // form telah disubmit, proses data
    
    // ambil semua nilai form
    $nis = htmlentities(strip_tags(trim($_POST["nis"])));
    $nama = htmlentities(strip_tags(trim($_POST["nama"])));
    $tempat_lahir = htmlentities(strip_tags(trim($_POST["tempat_lahir"])));
    $kelas = htmlentities(strip_tags(trim($_POST["kelas"])));
    $jurusan = htmlentities(strip_tags(trim($_POST["jurusan"])));
    $nilai = htmlentities(strip_tags(trim($_POST["nilai"])));
    $tgl = htmlentities(strip_tags(trim($_POST["tgl"])));
    $bln = htmlentities(strip_tags(trim($_POST["bln"])));
    $thn = htmlentities(strip_tags(trim($_POST["thn"])));
    
    // siapkan variabel untuk menampung pesan error
    $pesan_error="";
    
    // cek apakah "nis" sudah diisi atau tidak
    if (empty($nis)) {
      $pesan_error .= "NIS belum diisi <br>";
    }
    // nis harus angka dengan 8 digit
    elseif (!preg_match("/^[0-9]{8}$/",$nis) ) {
      $pesan_error .= "NIS harus berupa 8 digit angka <br>";
    }
    
    // cek ke database, apakah sudah ada nomor nis yang sama    
    // filter data $nis
    $nis = mysqli_real_escape_string($link,$nis);
    $query = "SELECT * FROM siswa WHERE nis='$nis'";
    $hasil_query = mysqli_query($link, $query);
  
    // cek jumlah record (baris), jika ada, $nis tidak bisa diproses
    $jumlah_data = mysqli_num_rows($hasil_query);
     if ($jumlah_data >= 1 ) {
       $pesan_error .= "NIS yang sama sudah digunakan <br>";  
    }

    // cek apakah "nama" sudah diisi atau tidak
    if (empty($nama)) {
      $pesan_error .= "Nama belum diisi <br>";
    }
    
    // cek apakah "tempat lahir" sudah diisi atau tidak
    if (empty($tempat_lahir)) {
      $pesan_error .= "Tempat lahir belum diisi <br>";
    }
    
    // cek apakah "jurusan" sudah diisi atau tidak
    if (empty($jurusan)) {
      $pesan_error .= "Jurusan belum diisi <br>";
    }
           
    // siapkan variabel untuk menggenerate pilihan kelas
    $select_11rpl1=""; $select_11rpl2=""; $select_11rpl3="";
    $select_12rpl1=""; $select_12rpl2=""; $select_12rpl3="";
    
    switch($kelas) {
     case "11 RPL 1" : $select_11rpl1 = "selected";  break;
     case "11 RPL 2" : $select_11rpl2 = "selected";  break;
     case "11 RPL 3" : $select_11rpl3 = "selected";  break;
     case "12 RPL 1" : $select_12rpl1 = "selected";  break;
     case "12 RPL 2" : $select_12rpl2 = "selected";  break;
     case "12 RPL 3" : $select_12rpl3 = "selected";  break;
    } 
    
    
    // nilai harus berupa angka dan tidak boleh negatif
    if (!is_numeric($nilai) OR ($nilai <=0)) {
      $pesan_error .= "Nilai harus diisi dengan angka";
    }   
    
    // jika tidak ada error, input ke database
    if ($pesan_error === "") {
      
      // filter semua data
      $nis = mysqli_real_escape_string($link,$nis);
      $nama = mysqli_real_escape_string($link,$nama );
      $tempat_lahir = mysqli_real_escape_string($link,$tempat_lahir);
      $kelas = mysqli_real_escape_string($link,$kelas);
      $jurusan = mysqli_real_escape_string($link,$jurusan);
      $tgl = mysqli_real_escape_string($link,$tgl);
      $bln = mysqli_real_escape_string($link,$bln);
      $thn = mysqli_real_escape_string($link,$thn);
      $nilai = (float) $nilai;
      
      //gabungkan format tanggal agar sesuai dengan date MySQL
      $tgl_lhr = $thn."-".$bln."-".$tgl;
      
      //buat dan jalankan query INSERT
      $query = "INSERT INTO siswa VALUES ";
      $query .= "('$nis', '$nama', '$tempat_lahir', ";
      $query .= "'$tgl_lhr','$kelas','$jurusan',$nilai)";

      $result = mysqli_query($link, $query);
      
      //periksa hasil query
      if($result) {
      // INSERT berhasil, redirect ke tampil_siswa.php + pesan
        $pesan = "Siswa dengan nama = \"<b>$nama</b>\" sudah berhasil di tambah";
        $pesan = urlencode($pesan);
        header("Location: tampil_siswa.php?pesan={$pesan}");
      } 
      else { 
      die ("Query gagal dijalankan: ".mysqli_errno($link).
           " - ".mysqli_error($link));
      }    
    }
  }
  else {
    // form belum disubmit atau halaman ini tampil untuk pertama kali 
    // berikan nilai awal untuk semua isian form
    $pesan_error = "";
    $nis = "";
    $nama = "";
    $tempat_lahir = "";
    $select_11rpl1="selected"; 
    $select_11rpl2=""; $select_11rpl3="";
    $select_12rpl1=""; $select_12rpl2=""; $select_12rpl3="";
    $jurusan = "";
    $nilai="";
    $tgl=1;$bln="1";$thn=1996;
  }

  // siapkan array untuk nama bulan
  $arr_bln = array( "1"=>"Januari",
                    "2"=>"Februari",
                    "3"=>"Maret",
                    "4"=>"April",
                    "5"=>"Mei",
                    "6"=>"Juni",
                    "7"=>"Juli",
                    "8"=>"Agustus",
                    "9"=>"September",
                    "10"=>"Oktober",
                    "11"=>"Nopember",
                    "12"=>"Desember" );
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Sistem Informasi siswa</title>
  <link href="style.css" rel="stylesheet" >
  <link rel="icon" href="favicon.png" type="image/png" >
</head>
<body>
<div class="container">
<div id="header">
  <h1 id="logo">Sistem Informasi Siswa<span>  SMKN 1 Rongga</span></h1>
  <p id="tanggal"><?php echo date("d M Y"); ?></p>
</div>
<hr>
  <nav>
  <ul>
    <li><a href="tampil_siswa.php">Tampil</a></li>
    <li><a href="tambah_siswa.php">Tambah</a>
    <li><a href="edit_siswa.php">Edit</a>
    <li><a href="hapus_siswa.php">Hapus</a></li>
    <li><a href="logout.php">Logout</a>
  </ul>
  </nav>
  <form id="search" action="tampil_siswa.php" method="get">
    <p>
      <label for="nis">Nama : </label> 
      <input type="text" name="nama" id="nama" placeholder="search..." >
      <input type="submit" name="submit" value="Search">
    </p>
  </form>
<h2>Tambah Data Siswa</h2>
<?php
  // tampilkan error jika ada
  if ($pesan_error !== "") {
      echo "<div class=\"error\">$pesan_error</div>";
  }
?>
<form id="form_siswa" action="tambah_siswa.php" method="post">
<fieldset>
<legend>Siswa Baru</legend>
  <p>
    <label for="nis">NIS : </label> 
    <input type="text" name="nis" id="nis" value="<?php echo $nis ?>"
    placeholder="Contoh: 12345678">
    (8 digit angka)
  </p>
  <p>
    <label for="nama">Nama : </label> 
    <input type="text" name="nama" id="nama" value="<?php echo $nama ?>">
  </p>
  <p>
    <label for="tempat_lahir">Tempat Lahir : </label> 
    <input type="text" name="tempat_lahir" id="tempat_lahir" 
    value="<?php echo $tempat_lahir ?>">
  </p>
  <p>
    <label for="tgl" >Tanggal Lahir : </label> 
      <select name="tgl" id="tgl">
        <?php
          for ($i = 1; $i <= 31; $i++) {
            if ($i==$tgl){
              echo "<option value = $i selected>";
            }
            else {
              echo "<option value = $i >";
            }
            echo str_pad($i,2,"0",STR_PAD_LEFT);
            echo "</option>";
          }
        ?>
      </select>
        <select name="bln">
        <?php 
        foreach ($arr_bln as $key => $value) {
          if ($key==$bln){
            echo "<option value=\"{$key}\" selected>{$value}</option>";
          }
          else {
            echo "<option value=\"{$key}\">{$value}</option>";
          } 
        } 
        ?>
      </select>
      <select name="thn">
        <?php
          for ($i = 1990; $i <= 2005; $i++) {
          if ($i==$thn){
              echo "<option value = $i selected>";
            }
            else {
              echo "<option value = $i >";
            }
            echo "$i </option>";
          }
        ?>
      </select>
  </p>
  <p>
    <label for="kelas" >Kelas : </label> 
      <select name="kelas" id="kelas">
        <option value="11 RPL 1" <?php echo $select_11rpl1 ?>>
        11 RPL 1 </option>
        <option value="11 RPL 2" <?php echo $select_11rpl2 ?>>
        11 RPL 2</option>
        <option value="11 RPL 3" <?php echo $select_11rpl3 ?>>
        11 RPL 3</option>
        <option value="12 RPL 1" <?php echo $select_12rpl1 ?>>
        12 RPL 1</option>
        <option value="12 RPL 2" <?php echo $select_12rpl2 ?>>
        12 RPL 2</option>
        <option value="12 RPL 3" <?php echo $select_12rpl3 ?>>
        12 RPL 3</option>
      </select>
  </p>
  <p>
    <label for="jurusan">Jurusan : </label> 
    <input type="text" name="jurusan" id="jurusan" value="<?php echo $jurusan ?>">
  </p>
  <p >
    <label for="nilai">Nilai : </label> 
    <input type="text" name="nilai" id="nilai" value="<?php echo $nilai ?>"
    placeholder="Contoh: 70">
    (angka desimal dipisah dengan karakter titik ".")
  </p>
  
</fieldset>
  <br>
  <p>
    <input type="submit" name="submit" value="Tambah Data">
  </p>
</form> 
  
  <div id="footer">
    Copyright © <?php echo date("Y"); ?> Suhendar Aryadi
  </div>
  
</div>

</body>
</html>
<?php
  // tutup koneksi dengan database mysql
  mysqli_close($link);
?>