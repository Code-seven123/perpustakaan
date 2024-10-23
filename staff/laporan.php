<?php
require_once "../connection.php";

// Mendapatkan nilai parameter 'page'
$page = isset($_GET['page']) ? $_GET['page'] : '';

// Laporan berdasarkan parameter 'page'
if ($page === 'peminjaman') {
    // Mengambil data peminjaman
    $query = "
        SELECT p.PeminjamID, p.TanggalPeminjaman, p.TanggalPengembalian, p.StatusPeminjaman,
               b.Judul, u.NamaLengkap
        FROM peminjaman p
        JOIN buku b ON p.BukuID = b.BukuID
        JOIN user u ON p.UserID = u.UserID
    ";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $peminjaman = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Menampilkan laporan peminjaman
    echo "<h1>Laporan Peminjaman</h1>";
    echo "<table border='1'>
            <tr>
                <td>Peminjam ID</td>
                <td>Tanggal Peminjaman</td>
                <td>Tanggal Pengembalian</td>
                <td>Status Peminjaman</td>
                <td>Judul Buku</td>
                <td>Nama Peminjam</td>
            </tr>";
    foreach ($peminjaman as $row) {
        echo "<tr>
                <td>{$row['PeminjamID']}</td>
                <td>{$row['TanggalPeminjaman']}</td>
                <td>{$row['TanggalPengembalian']}</td>
                <td>{$row['StatusPeminjaman']}</td>
                <td>{$row['Judul']}</td>
                <td>{$row['NamaLengkap']}</td>
              </tr>";
    }
    echo "</table>";
    if($_GET["action"] ?? false == true) {
        echo "<button onclick='history.back()'>Kembali</button>";
    }

} elseif ($page === 'buku') {
    // Mengambil daftar buku
    $queryBuku = "
        SELECT b.BukuID, b.Judul, b.Penulis, b.Penerbit, b.TahunTerbit, k.NamaKategori
        FROM buku b
        JOIN kategoribuku_relasi kr ON b.BukuID = kr.BukuID
        JOIN kategoribuku k ON kr.KategoriID = k.KategoriID
    ";
    $stmtBuku = $conn->prepare($queryBuku);
    $stmtBuku->execute();
    $daftarBuku = $stmtBuku->fetchAll(PDO::FETCH_ASSOC);

    // Menampilkan daftar buku
    echo "<h1>Daftar Buku</h1>";
    echo "<table border='1'>
            <tr>
                <td>ID Buku</td>
                <td>Judul</td>
                <td>Penulis</td>
                <td>Penerbit</td>
                <td>Tahun Terbit</td>
                <td>Kategori</td>
            </tr>";
    foreach ($daftarBuku as $row) {
        echo "<tr>
                <td>{$row['BukuID']}</td>
                <td>{$row['Judul']}</td>
                <td>{$row['Penulis']}</td>
                <td>{$row['Penerbit']}</td>
                <td>{$row['TahunTerbit']}</td>
                <td>{$row['NamaKategori']}</td>
              </tr>";
    }
    echo "</table>";
    if($_GET["action"] ?? false == true) {
        echo "<button onclick='history.back()'>Kembali</button>";
    }

} elseif ($page === 'ulasan') {
    // Mengambil daftar buku dan ulasan
    $queryUlasan = "
        SELECT b.Judul, b.Penulis, b.Penerbit, u.Ulasan, u.Rating
        FROM buku b
        LEFT JOIN ulasanbuku u ON b.BukuID = u.BukuID
    ";
    $stmtUlasan = $conn->prepare($queryUlasan);
    $stmtUlasan->execute();
    $bukuUlasan = $stmtUlasan->fetchAll(PDO::FETCH_ASSOC);

    // Menampilkan daftar buku dan ulasan
    echo "<h1>Daftar Buku dan Ulasan</h1>";
    echo "<table border='1'>
            <tr>
                <td>Judul Buku</td>
                <td>Penulis</td>
                <td>Penerbit</td>
                <td>Ulasan</td>
                <td>Rating</td>
            </tr>";
    foreach ($bukuUlasan as $row) {
        echo "<tr>
                <td>{$row['Judul']}</td>
                <td>{$row['Penulis']}</td>
                <td>{$row['Penerbit']}</td>
                <td>{$row['Ulasan']}</td>
                <td>{$row['Rating']}</td>
              </tr>";
    }
    echo "</table>";
    if($_GET["action"] ?? false == true) {
        echo "<button onclick='history.back()'>Kembali</button>";
    }
} else {
    echo "<h1>Halaman tidak ditemukan</h1>";
}

$conn = null; // Tutup koneksi
?>
