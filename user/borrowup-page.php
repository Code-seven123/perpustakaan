<?php 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $stmt = $conn->prepare("INSERT INTO peminjaman (UserID, BukuID, TanggalPeminjaman, TanggalPengembalian, StatusPeminjaman) 
        VALUES (:userid, :bukuid, :tgltake, :tglkembali, :status)");
        $stmt->execute([
            ':userid' => $_POST['userid'],
            ':bukuid' => $_POST['bukuid'],
            ':tgltake' => $_POST['tgltake'],
            ':tglkembali' => ($_POST['tglkembalioption'] == "custom") ? $_POST['tglkembali'] : $_POST['tglkembalioption'],
            ':status' => "dipinjam",
        ]);
        echo "<script>history.back()</script>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
<?php if (isset($msg)) { ?>
<div class="flex justify-center align-center bg-red-100 w-full border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
    <?= $msg ?>
</div>
<?php } ?>
<div class="w-full flex justify-center gap-10">
    <div class="bg-white rounded-lg shadow-lg p-8 w-96">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">Informasi Buku</h2>
        <hr class="mb-5">
        <?php 
            $id = $_GET['id'];
            $query = $conn->prepare("
                SELECT 
                    buku.BukuID, 
                    buku.Judul, 
                    buku.Penulis, 
                    buku.Penerbit, 
                    buku.TahunTerbit, 
                    kategoribuku.NamaKategori 
                FROM 
                    buku
                LEFT JOIN 
                    kategoribuku_relasi ON buku.BukuID = kategoribuku_relasi.BukuID
                LEFT JOIN 
                    kategoribuku ON kategoribuku.KategoriID = kategoribuku_relasi.KategoriID 
                WHERE
                    buku.BukuID=:id
            ");
            $query->bindParam(":id", $id);
            $query->execute();
            $buku = $query->fetch();
            $sesi = verifikasiSession($_SESSION["loginsesi"] ?? false, $config['key']);
            $query1 = $conn->prepare("
                SELECT
                    UserID,
                    NamaLengkap,
                    Username
                FROM
                    user
                WHERE 
                    UserID = :id
            ");
            $query1->bindParam(":id", $sesi['id']);
            $query1->execute();
            $user = $query1->fetch();
        ?>
        <h2 class="text-2xl font-semibold mb-2">Judul: <?= $buku['Judul'] ?></h2>
        <p class="text-gray-600">Penulis: <?= $buku['Penulis'] ?></p>
        <p class="text-gray-600">Penerbit: <?= $buku['Penerbit'] ?></p>
        <p class="text-gray-600">Tahun Terbit: <?= $buku['TahunTerbit'] ?></p>
        <p class="text-gray-600 mb-2">Kategori: <?= $buku['NamaKategori'] ?></p>
        <p class="text-gray-600 font-bold">Dipinjam Oleh: <?= $user['Username'] ?> | <?= $user['NamaLengkap'] ?></p>
        <button class="w-full mt-4 bg-green-500 text-white py-2 rounded-md hover:bg-green-700 transition duration-200 transform hover:scale-105">Kembali</button>
    </div>
    <div class="bg-white rounded-lg shadow-lg p-8 w-96">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">Konfirmasi peminjaman</h2>
        <form method="post">
            <input type="hidden" name="userid" value="<?= $user['UserID'] ?>">
            <input type="hidden" name="bukuid" value="<?= $buku['BukuID'] ?>">
            <div class="mb-4">
                <label for="tgltake" class="block text-sm font-medium text-gray-700">Tanggal Pinjam</label>
                <input value="<?= date('Y-m-d') ?>" type="date" id="tgltake" name="tgltake" readonly required class="input-field mt-1 block w-full border border-gray-300 rounded-md p-2 focus:outline-none">
            </div>

            <div class="mb-4">
                <label for="option" class="block text-sm font-medium text-gray-700">Tanggal Pengembalian</label>
                <select id="option" name="tglkembalioption" class="input-field mt-1 block w-full border border-gray-300 rounded-md p-2 focus:outline-none" onchange="toggleCustomInput()">
                    <option value="">-- Pilih Opsi --</option>
                    <option value="30">7 hari | 1 minggu</option>
                    <option value="30">30 hari | 1 bulan</option>
                    <option value="30">60 hari | 2 bulan</option>
                    <option value="custom">Masukkan Kustom</option>
                </select>
            </div>

            <div id="custom-input-container" class="hidden mb-4">
                <label for="custom-option" class="block text-sm font-medium text-gray-700">Masukkan Pilihan Kustom</label>
                <input type="number" id="custom-option" name="tglkembali" class="input-field mt-1 block w-full border border-gray-300 rounded-md p-2 focus:outline-none">
            </div>

            <button type="submit" class="w-full bg-blue-300 text-white py-2 rounded-md hover:bg-blue-700 transition duration-200 transform hover:scale-105">Daftar</button>
        </form>
    </div>
</div>