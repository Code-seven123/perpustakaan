<h1 class="text-6xl">Koleksi Kamu</h1>
<hr class="mb-6">
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php
        $sesi = verifikasiSession($_SESSION["loginsesi"] ?? false, $config['key']);
        $queryBuku = $conn->prepare("
            SELECT 
                *
            FROM 
                koleksipribadi
            LEFT JOIN 
                buku ON buku.BukuID = koleksipribadi.BukuID
            LEFT JOIN 
                kategoribuku_relasi ON buku.BukuID = kategoribuku_relasi.BukuID
            LEFT JOIN 
                kategoribuku ON kategoribuku.KategoriID = kategoribuku_relasi.KategoriID
            WHERE koleksipribadi.UserID = :id
        ");
        $queryBuku->bindValue(":id", $sesi["id"], PDO::PARAM_INT);
        $queryBuku->execute();
        $dataBuku = $queryBuku->fetchAll();
        if(isset($dataBuku) && !empty($dataBuku)) {
            foreach($dataBuku as $value) {
        
    ?>
    <div class="bg-white rounded-lg shadow-lg p-6 transition-transform transform hover:scale-105">
        <h2 class="text-2xl font-semibold mb-2">Judul: <?= $value['Judul'] ?></h2>
        <p class="text-gray-600">Penulis: <?= $value['Penulis'] ?></p>
        <p class="text-gray-600">Penerbit: <?= $value['Penerbit'] ?></p>
        <p class="text-gray-600">Tahun Terbit: <?= $value['TahunTerbit'] ?></p>
        <p class="text-gray-600">Kategori: <?= $value['NamaKategori'] ?></p>
        <div class="mt-4 flex justify-between">
            <button class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition"><a href="?page=borrowup&id=<?= $value['BukuID'] ?>">Pinjam</a></button>
            <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition"><a href="?page=deletecollection&id=<?= $value['BukuID'] ?>">Hapus dari Koleksi</a></button>
        </div>
    </div>
    <?php }} ?>
</div>
<?php if(!isset($dataBuku) || empty($dataBuku)) { ?>
    <h1 class="text-4xl text-center">Data buku tidak ditemukan</h1>
<?php } ?>