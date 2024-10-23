<?php
session_start();
require_once "../connection.php";
require_once "../utils.php";
$sesi = verifikasiSession($_SESSION["loginsesi"] ?? false, $config['key']);
if ($sesi['status'] ?? $sesi == true ) {
    if($sesi['role'] != 2) {
        redirect("..");
    }
} else {
    redirect("..");
}
try {
    $sql = "
    SELECT 
        b.BukuID,
        b.Judul,
        b.Penulis,
        b.Penerbit,
        b.TahunTerbit,
        COALESCE(AVG(u.Rating), 0) AS RataRataUlasan,
        COALESCE(COUNT(u.UlasanID), 0) AS JumlahUlasan,
        COALESCE(SUM(CASE WHEN p.StatusPeminjaman = 'dibatalkan' THEN 1 ELSE 0 END), 0) AS PeminjamanDibatalkan,
        COALESCE(SUM(CASE WHEN p.StatusPeminjaman = 'expired' THEN 1 ELSE 0 END), 0) AS PeminjamanExpired,
        COALESCE(SUM(CASE WHEN p.StatusPeminjaman = 'dipinjam' THEN 1 ELSE 0 END), 0) AS PeminjamanDipinjam,
        COALESCE(SUM(CASE WHEN p.StatusPeminjaman = 'dikembalikan' THEN 1 ELSE 0 END), 0) AS PeminjamanDikembalikan,
        COALESCE(COUNT(kp.KoleksiID), 0) AS JumlahKoleksi,
        GROUP_CONCAT(DISTINCT kb.NamaKategori) AS Kategori
    FROM buku b
    LEFT JOIN ulasanbuku u ON b.BukuID = u.BukuID
    LEFT JOIN peminjaman p ON b.BukuID = p.BukuID
    LEFT JOIN koleksipribadi kp ON b.BukuID = kp.BukuID
    LEFT JOIN kategoribuku_relasi kr ON b.BukuID = kr.BukuID
    LEFT JOIN kategoribuku kb ON kr.KategoriID = kb.KategoriID
    GROUP BY b.BukuID
    ";

    // Eksekusi query
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    // Ambil data
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Tampilkan data
    echo "<h1>Laporan Buku</h1>";
    echo "<table border='1'>
            <tr>
                <th>Buku ID</th>
                <th>Judul</th>
                <th>Penulis</th>
                <th>Penerbit</th>
                <th>Tahun Terbit</th>
                <th>Rata-Rata Ulasan</th>
                <th>Jumlah Ulasan</th>
                <th>Peminjaman Dibatalkan</th>
                <th>Peminjaman Expired</th>
                <th>Peminjaman Dipinjam</th>
                <th>Peminjaman Dikembalikan</th>
                <th>Jumlah Dikoleksi</th>
                <th>Kategori</th>
            </tr>";

    foreach ($data as $row) {
        echo "<tr>
                <td>{$row['BukuID']}</td>
                <td>{$row['Judul']}</td>
                <td>{$row['Penulis']}</td>
                <td>{$row['Penerbit']}</td>
                <td>{$row['TahunTerbit']}</td>
                <td>{$row['RataRataUlasan']}</td>
                <td>{$row['JumlahUlasan']}</td>
                <td>{$row['PeminjamanDibatalkan']}</td>
                <td>{$row['PeminjamanExpired']}</td>
                <td>{$row['PeminjamanDipinjam']}</td>
                <td>{$row['PeminjamanDikembalikan']}</td>
                <td>{$row['JumlahKoleksi']}</td>
                <td>{$row['Kategori']}</td>
              </tr>";
    }

    echo "</table>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Menutup koneksi
$pdo = null;
?>

<style>
    table {
        border: 1px solid #ccc;
        margin: 20px 0;
    }
    th, td {
        border: 1px solid #ccc;
        padding: 10px;
        text-align: left;
    }
    th {
        background-color: #f2f2f2;
    }
    h2 {
        margin-bottom: 20px;
    }
</style>
<button onclick="window.print();">Simpan sebagai PDF</button>
<button onclick="history.back();">Kembali</button>