<form action="" method="get">
    <label for=""></label>
    <form action="#" method="GET">
            <div class="flex items-center border-b border-gray-300">
                <input 
                    type="text" 
                    name="q" 
                    placeholder="Cari buku..."
                    class="flex-grow py-2 px-4 focus:outline-none focus:border-blue-500"
                >
                <button 
                    type="submit" 
                    class="bg-transparant border text-white px-4 py-2 rounded hover:bg-blue-600 transition duration-200"
                >
                    <img class="w-9" src="https://www.svgrepo.com/show/479898/magnifying-glass-10.svg" alt="">
                </button>
            </div>
        </form>
</form>
<div class="container mx-auto mt-10">
        <h1 class="text-2xl font-bold mb-5 text-center">Daftar Buku</h1>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white shadow-md rounded-lg overflow-auto">
                <thead>
                    <tr class="bg-gray-800 text-white">
                        <th class="py-3 px-4 text-left">NO</th>
                        <th class="py-3 px-4 text-left">BukuID</th>
                        <th class="py-3 px-4 text-left">Judul</th>
                        <th class="py-3 px-4 text-left">Kategori</th>
                        <th class="py-3 px-4 text-left">Penulis</th>
                        <th class="py-3 px-4 text-left">Penerbit</th>
                        <th class="py-3 px-4 text-left">Tahun Terbit</th>
                        <th class="py-3 px-4 text-left">Opsi</th>
                        <th class="py-3 px-4 text-left"><a href="?page=addbook" class="w-full text-white p-3 rounded-full bg-green-500">Tambah</a></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        
                        if(isset($_GET["q"])) {
                            $queryBuku = $conn->prepare("
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
                                buku.Judul LIKE :polaJudul OR
                                buku.Penulis LIKE :polaPenulis OR
                                buku.Penerbit LIKE :polaPenerbit OR
                                buku.TahunTerbit LIKE :polaTahun OR
                                kategoribuku.NamaKategori LIKE :polakategori
                            ");
                            $searchQuery = isset($_GET["q"]) ? $_GET["q"] : "";
                            $queryBuku->bindValue(":polaJudul", "%$searchQuery%", PDO::PARAM_STR);
                            $queryBuku->bindValue(":polaPenulis", "%$searchQuery%", PDO::PARAM_STR);
                            $queryBuku->bindValue(":polaPenerbit", "%$searchQuery%", PDO::PARAM_STR);
                            $queryBuku->bindValue(":polaTahun", "%$searchQuery%", PDO::PARAM_STR);
                            $queryBuku->bindValue(":polakategori", "%$searchQuery%", PDO::PARAM_STR);
                            $queryBuku->execute();
                            $dataBuku = $queryBuku->fetchAll(PDO::FETCH_ASSOC);
                        } else {
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
                        }
                        if(isset($dataBuku)) {
                            foreach($dataBuku as $index => $value) {
                    ?>
                    <tr class="hover:bg-gray-100">
                        <td class="py-3 px-4"><?= $index + 1 ?></td>
                        <td class="py-3 px-4"><?= $value['BukuID'] ?></td>
                        <td class="py-3 px-4"><?= $value['Judul'] ?></td>
                        <td class="py-3 px-4"><?= $value['NamaKategori'] ?></td>
                        <td class="py-3 px-4"><?= $value['Penulis'] ?></td>
                        <td class="py-3 px-4"><?= $value['Penerbit'] ?></td>
                        <td class="py-3 px-4"><?= $value['TahunTerbit'] ?></td>
                        <td class="py-3 px-4" colspan="2">
                            <a href="?page=deletebook&id=<?= $value['BukuID'] ?>" class="w-full text-white p-3 rounded-full bg-red-500">Hapus</a>
                            <a href="?page=peminjam" class="w-full text-white p-3 rounded-full bg-indigo-400">Peminjam</a>
                            <a href="?page=addbook&id=<?= $value['BukuID'] ?>" class="w-full text-white p-3 rounded-full bg-yellow-600">Edit</a>
                        </td>
                    </tr>
                    <?php }} else { ?>
                        <tr>
                            <td colspan="8" class="text-center text-2xl">Data buku tidak ditemukan</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>