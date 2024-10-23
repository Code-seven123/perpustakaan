<?php
$buku = null;
if (isset($_GET['id'])) {
    $stmt = $conn->prepare("SELECT * FROM buku 
    LEFT JOIN
        kategoribuku_relasi ON buku.KategoriBukuID = kategoribuku_relasi.KategoriBukuID
    WHERE buku.BukuID = ?");
    $stmt->execute([$_GET['id']]);
    $buku = $stmt->fetch(PDO::FETCH_ASSOC);
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bukuID = $_POST['bukuID'] ?? null;
    $judul = $_POST['judul'];
    $kategoriID = $_POST['kategoriBukuID'];
    $kategoriBukuID = $_POST['kategoriID'];
    $penulis = $_POST['penulis'];
    $penerbit = $_POST['penerbit'];
    $tahunTerbit = $_POST['tahunTerbit'];

    if ($bukuID) {
        // Mengupdate table buku yang dengan column KategoriBukuID yang lama
        $stmt = $conn->prepare("UPDATE buku SET Judul = ?, KategoriBukuID = ?, Penulis = ?, Penerbit = ?, TahunTerbit = ? WHERE BukuID = ?");
        if($stmt->execute([$judul, $kategoriBukuID, $penulis, $penerbit, $tahunTerbit, $bukuID])){
            // Mengupdate data kategoribuku_relasi dengan KategoriBukuID yang lama
            $urelasi = $conn->prepare("UPDATE kategoribuku_relasi SET BukuID=?, KategoriID=?");
            if($urelasi->execute([$bukuID, $kategoriID])) {
                redirect("./");
            }
        }

    } else {
        // Membuat data buku sesuai data form(POST) dengan column KategoriBukuID kosong
        $stmt = $conn->prepare("INSERT INTO buku (Judul, KategoriBukuID, Penulis, Penerbit, TahunTerbit) VALUES (?, ?, ?, ?, ?)");
        if($stmt->execute([$judul, "", $penulis, $penerbit, $tahunTerbit])) {
            // Mendapatkan id dari data pada table buku yang baru di tambahkan
            $lastBukuID = $conn->lastInsertId();
    
            // Mendapatkan data lengkap dari data buku yang baru ditambahkan
            $query = $conn->prepare("SELECT * FROM buku WHERE BukuID = ?");
            if($query->execute([$lastBukuID])) {
                $buku = $query->fetch(PDO::FETCH_ASSOC);
                
                // Membuat data relasi
                $relasi = $conn->prepare("INSERT INTO kategoribuku_relasi VALUES ('', ?, ?)");
                if($relasi->execute([$buku["BukuID"], $kategoriID])){
                    // Mendapatkan id dari data pada table kategoribuku_relasi yang baru di tambahkan
                    $lastInsertIdrelasi = $conn->lastInsertId();
                    
                    // Mengupdate column KategoriBukuID dari table buku dengan data kategoribuku_relasi yang baru
                    $stmt = $conn->prepare("UPDATE buku SET KategoriBukuID = ? WHERE BukuID=?");
                    if($stmt->execute([$lastInsertIdrelasi, $buku["BukuID"]])) {
                        redirect("./");
                    }
                }
            }
        }
    }
    //redirect("./");
}
?>
<div class="max-w-lg mx-auto bg-white p-6 rounded shadow-md">
    <h1 class="text-2xl font-bold mb-4"><?= $buku ? 'Edit Buku' : 'Tambah Buku' ?></h1>
    
    <form action="" method="POST">
        <input type="hidden" name="bukuID" value="<?= $buku['BukuID'] ?? '' ?>">

        <div class="mb-4">
            <label for="judul" class="block text-sm font-medium text-gray-700">Judul</label>
            <input type="text" name="judul" id="judul" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200" value="<?= $buku['Judul'] ?? '' ?>">
        </div>
        <input type="hidden" name="kategoriID" value="<?= $buku['KategoriBukuID'] ?>">
        <div class="mb-4">
            <label for="kategoriBukuID" class="block text-sm font-medium text-gray-700">Kategori Buku ID</label>
            <?php
                $query = $conn->query("select * from kategoribuku");
                $kategoriBuku = $query->fetchAll();
            ?>
            <select name="kategoriBukuID" id="kategoriBukuID" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                <option value="" disabled <?= !$buku ? 'selected' : '' ?>>Pilih Kategori</option>
                <?php foreach ($kategoriBuku as $kategori): ?>
                    <option value="<?= $kategori['KategoriID'] ?>" <?= ($buku && $buku['KategoriID'] == $kategori['KategoriID']) ? 'selected' : '' ?>>
                        <?= $kategori['NamaKategori'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-4">
            <label for="penulis" class="block text-sm font-medium text-gray-700">Penulis</label>
            <input type="text" name="penulis" id="penulis" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200" value="<?= $buku['Penulis'] ?? '' ?>">
        </div>

        <div class="mb-4">
            <label for="penerbit" class="block text-sm font-medium text-gray-700">Penerbit</label>
            <input type="text" name="penerbit" id="penerbit" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200" value="<?= $buku['Penerbit'] ?? '' ?>">
        </div>

        <div class="mb-4">
            <label for="tahunTerbit" class="block text-sm font-medium text-gray-700">Tahun Terbit</label>
            <input type="number" name="tahunTerbit" id="tahunTerbit" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200" value="<?= $buku['TahunTerbit'] ?? '' ?>">
        </div>

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded"><?= $buku ? 'Update Buku' : 'Tambah Buku' ?></button>
    </form>
</div>
