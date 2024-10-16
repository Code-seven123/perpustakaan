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
<div id="kategori" class="mt-6  ">
    <form action="" method="post">
        <div  class="columns-1 gap-20">
            <?php
                $query = $conn->query("select * from kategoribuku");
                $kategoriData = $query->fetchAll();
                foreach($kategoriData as $value) {
            ?>
            <label class="flex items-center justify-center border border-gray-300 rounded-full bg-white shadow-md hover:shadow-lg pe-5 transition duration-200 cursor-pointer inline-flex">
                <input type="checkbox" name="kategori[]" value="<?= $value['NamaKategori'] ?>" class="hidden">
                <span class="ml-3 text-md relative"></span>
                <?= kapital($value['NamaKategori']) ?>
            </label>
            <?php } ?>
        </div>
        <button type="submit" class="w-full mt-6 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition duration-200">
            Cari
        </button>
        <hr class="mt-7 mb-7">
    </form>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
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
                buku.TahunTerbit LIKE :polaTahun
            ");
            $searchQuery = isset($_GET["q"]) ? $_GET["q"] : "";
            $queryBuku->bindValue(":polaJudul", "%$searchQuery%", PDO::PARAM_STR);
            $queryBuku->bindValue(":polaPenulis", "%$searchQuery%", PDO::PARAM_STR);
            $queryBuku->bindValue(":polaPenerbit", "%$searchQuery%", PDO::PARAM_STR);
            $queryBuku->bindValue(":polaTahun", "%$searchQuery%", PDO::PARAM_STR);
            $queryBuku->execute();
            $dataBuku = $queryBuku->fetchAll(PDO::FETCH_ASSOC);
        } else if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['kategori'])){
            $kategori = $_POST['kategori'] ?? [];
            $kategoriArray = array_filter($kategori);

            // Memastikan bahwa ada kategori yang dicari
            if (!empty($kategoriArray)) {
                // Mengubah kategori menjadi string untuk digunakan dalam query
                $placeholders = rtrim(str_repeat('?,', count($kategoriArray)), ','); // Membuat placeholder untuk setiap kategori

                // Query untuk mencari buku berdasarkan kategori
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
                        kategoribuku.NamaKategori IN ($placeholders)
                ");

                // Mengikat parameter kategori
                $queryBuku->execute($kategoriArray); // Menggunakan array langsung untuk execute

                // Mengambil semua data
                $dataBuku = $queryBuku->fetchAll(PDO::FETCH_ASSOC);
            }
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
            <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition"><a href="?page=addcollection&id=<?= $value['BukuID'] ?>">Tambah ke Koleksi</a></button>
        </div>
    </div>
    <?php }} else { ?>
        <h1 class="text-4xl">Data buku tidak ditemukan</h1>
    <?php } ?>
</div>