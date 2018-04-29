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
    // form telah disubmit, cek apakah berasal dari edit_siswa.php 
    // atau update data dari form_edit.php
    
    if ($_POST["submit"]=="Edit") {
      //nilai form berasal dari halaman edit_siswa.php
    
      // ambil nilai nis 
      $nis = htmlentities(strip_tags(trim($_POST["nis"])));
      // filter data
      $nis = mysqli_real_escape_string($link,$nis);
    
      // ambil semua data dari database untuk menjadi nilai awal form
      $query = "SELECT * FROM siswa WHERE nis='$nis'";
      $result = mysqli_query($link, $query);
    
      if(!$result){
        die ("Query Error: ".mysqli_errno($link).
             " - ".mysqli_error($link));
      }
    
      // tidak perlu pakai perulangan while, karena hanya ada 1 record
      $data = mysqli_fetch_assoc($result);    
       
      $nama = $data["nama"];
      $tempat_lahir = $data["tempat_lahir"];
      $kelas = $data["kelas"];
      $jurusan = $data["jurusan"];
      $nilai = $data["nilai"];
    
      // untuk tanggal harus dipecah
      $tgl = substr($data["tanggal_lahir"],8,2);
      $bln = substr($data["tanggal_lahir"],5,2);
      $thn = substr($data["tanggal_lahir"],0,4);
    
    // bebaskan memory 
    mysqli_free_result($result);
    }
    
    else if ($_POST["submit"]=="Update Data") {
      // nilai form berasal dari halaman form_edit.php    
      // ambil nilai form 
      $nis = htmlentities(strip_tags(trim($_POST["nis"])));
      $nama = htmlentities(strip_tags(trim($_POST["nama"])));
      $tempat_lahir = htmlentities(strip_tags(trim($_POST["tempat_lahir"])));
      $kelas = htmlentities(strip_tags(trim($_POST["kelas"])));
      $jurusan = htmlentities(strip_tags(trim($_POST["jurusan"])));
      $nilai = htmlentities(strip_tags(trim($_POST["nilai"])));
      $tgl = htmlentities(strip_tags(trim($_POST["tgl"])));
      $bln = htmlentities(strip_tags(trim($_POST["bln"])));
      $thn = htmlentities(strip_tags(trim($_POST["thn"])));
    }

    // proses validasi form
    // siapkan variabel untuk menampung pesan error
    $pesan_error="";
    
    // cek apakah "nis" sudah diisi atau tidak
    if (empty($nis)) {
      $pesan_error .= "nis belum diisi <br>";
    }
   // nis harus angka dengan 8 digit
    elseif (!preg_match("/^[0-9]{8}$/",$nis) ) {
      $pesan_error .= "nis harus berupa 8 digit angka <br>";
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
      $pesan_error .= "nilai harus diisi dengan angka";
    }   
    
    // jika tidak ada error, input ke database
    if (($pesan_error === "") AND ($_POST["submit"]=="Update Data")) {
      
      // buka koneksi dengan MySQL
      include("connection.php");
      
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
      
      //buat dan jalankan query UPDATE
      $query  = "UPDATE siswa SET ";
      $query .= "nama = '$nama', tempat_lahir = '$tempat_lahir', ";
      $query .= "tanggal_lahir = '$tgl_lhr', kelas='$kelas', ";
      $query .= "jurusan = '$jurusan', nilai=$nilai ";
      $query .= "WHERE nis = '$nis'";
      
      $result = mysqli_query($link, $query);

      //periksa hasil query
      if($result) {
      // INSERT berhasil, redirect ke tampil_siswa.php + pesan
        $pesan = "siswa dengan nama = \"<b>$nama</b>\" sudah berhasil di update";
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
    // form diakses secara langsung! 
    // redirect ke edit_siswa.php
    header("Location: edit_siswa.php");
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
  <h1 id="logo">Sistem Informasi Siswa<span> SMKN 1 Rongga</span></h1>
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
<h2>Edit Data siswa</h2>
<?php
  // tampilkan error jika ada
  if ($pesan_error !== "") {
      echo "<div class=\"error\">$pesan_error</div>";
  }
?>
<form id="form_siswa" action="form_edit.php" method="post">
<fieldset>
<legend>siswa Baru</legend>
  <p>
    <label for="nis">NIS : </label> 
    <input type="text" name="nis" id="nis" value="<?php echo $nis ?>" readonly>
    (tidak bisa diubah di menu edit)
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
    <label for="nilai">nilai : </label> 
    <input type="text" name="nilai" id="nilai" value="<?php echo $nilai ?>">
    (angka desimal dipisah dengan karakter titik ".")
  </p>
  
</fieldset>
  <br>
  <p>
    <input type="submit" name="submit" value="Update Data">
  </p>
</form> 

</div>

</body>
</html>
<?php
  // tutup koneksi dengan database mysql
  mysqli_close($link);
?>