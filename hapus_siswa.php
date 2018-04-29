<?php
  // periksa apakah user sudah login, cek kehadiran session name 
  // jika tidak ada, redirect ke login.php
  session_start();
  if (!isset($_SESSION["nama"])) {
     header("Location: login.php");
  }
 
  // buka koneksi dengan MySQL
  include("connection.php");

  // cek apakah form telah di submit (untuk menghapus data)
  if (isset($_POST["submit"])) {
    // form telah disubmit, proses data
    
    // ambil nilai nis 
    $nis = htmlentities(strip_tags(trim($_POST["nis"])));
    // filter data
    $nis = mysqli_real_escape_string($link,$nis);
    
    //jalankan query DELETE
    $query = "DELETE FROM siswa WHERE nis='$nis' ";
    $hasil_query = mysqli_query($link, $query);
  
    //periksa query, tampilkan pesan kesalahan jika gagal
    if($hasil_query) {
      // DELETE berhasil, redirect ke tampil_siswa.php + pesan
        $pesan = "siswa dengan nis = \"<b>$nis</b>\" sudah berhasil di hapus";
      $pesan = urlencode($pesan);
        header("Location: tampil_siswa.php?pesan={$pesan}");
    } 
    else { 
      die ("Query gagal dijalankan: ".mysqli_errno($link).
           " - ".mysqli_error($link));
    }
  }
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
<h2>Hapus Data siswa</h2>
<?php
  // tampilkan pesan jika ada
  if ((isset($_GET["pesan"]))) {
      echo "<div class=\"pesan\">{$_GET["pesan"]}</div>";
  }
?>
 <table border="1">
  <tr>
  <th>NIS</th>
  <th>Nama</th>
  <th>Tempat Lahir</th>
  <th>Tanggal Lahir</th>
  <th>kelas</th>
  <th>Jurusan</th>
  <th>nilai</th>
  <th>Aksi</th>
  </tr>
  <?php
  // buat query untuk menampilkan seluruh data tabel siswa
  $query = "SELECT * FROM siswa ORDER BY nama ASC";
  $result = mysqli_query($link, $query);
  
  if(!$result){
      die ("Query Error: ".mysqli_errno($link).
           " - ".mysqli_error($link));
  }
  
  //buat perulangan untuk element tabel dari data siswa
  while($data = mysqli_fetch_assoc($result))
  {
    echo "<tr>";
    echo "<td>$data[nis]</td>";
    echo "<td>$data[nama]</td>";
    echo "<td>$data[tempat_lahir]</td>";
    echo "<td>$data[tanggal_lahir]</td>";
    echo "<td>$data[kelas]</td>";
    echo "<td>$data[jurusan]</td>";
    echo "<td>$data[nilai]</td>";
    echo "<td>";
    ?>
      <form action="hapus_siswa.php" method="post" >
      <input type="hidden" name="nis" value="<?php echo "$data[nis]"; ?>" >
      <input type="submit" name="submit" value="Hapus" >
      </form>
    <?php
    echo "</td>";
    echo "</tr>";
  }
  
  // bebaskan memory 
  mysqli_free_result($result);
  
  // tutup koneksi dengan database mysql
  mysqli_close($link);
  ?>
  </table>
  <div id="footer">
    Copyright © <?php echo date("Y"); ?> Suhendar Aryadi
  </div>
</div>
</body>
</html>