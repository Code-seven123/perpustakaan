
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php
        $queryBuku = $conn->query("
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
                kategoribuku ON kategoribuku.KategoriID = kategoribuku_relasi.KategoriID;
        ");
        $dataBuku = $queryBuku->fetchAll();
        if(isset($dataBuku)) {
            foreach($dataBuku as $value) {
        
    ?>
    <div class="bg-white rounded-lg shadow-lg p-6 transition-transform transform hover:scale-105">
        <h2 class="text-2xl font-semibold mb-2">Judul: <?= $value['Judul'] ?></h2>
        <p class="text-gray-600">Penulis: <?= $value['Penulis'] ?></p>
        <p class="text-gray-600">Penerbit: <?= $value['Penerbit'] ?></p>
        <p class="text-gray-600">Tahun Terbit: <?= $value['TahunTerbit'] ?></p>
        <p class="text-gray-600">Kategori: <?= $value['NamaKategori'] ?></p>
        <button class="w-full bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition"><a href="?page=ulasandetail&id=<?= $value['BukuID'] ?>">Ulasan Data</a></button>
    </div>
    <?php }} else { ?>
        <h1 class="text-4xl">Data buku tidak ditemukan</h1>
    <?php } ?>
</div>