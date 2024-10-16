<h2 class="text-5xl font-bold text-center text-gray-800 mb-6">Detail Peminjaman</h2>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10">
    <?php 
    $sql = "SELECT
            peminjaman.PeminjamID,
            user.Username,
            user.NamaLengkap,
            buku.Judul,
            peminjaman.TanggalPeminjaman,
            peminjaman.TanggalPengembalian,
            peminjaman.StatusPeminjaman
        FROM 
            peminjaman
        LEFT JOIN
            user ON user.UserID = peminjaman.UserID
        LEFT JOIN
            buku ON buku.BukuID = peminjaman.BukuID
        WHERE
            user.UserID = :userid
    ";
    $stmt = $conn->prepare($sql);
    $sesi = verifikasiSession($_SESSION["loginsesi"] ?? false, $config['key']);
    $stmt->bindParam(":userid", $sesi['id']);
    $stmt->execute();
    $peminjamanData = $stmt->fetchAll();
    foreach ($peminjamanData as $data):
        $tanggal = new DateTime($data['TanggalPeminjaman']);
        $tanggal->modify("+".$data['TanggalPengembalian']." days");
        $tanggalPengembalian = $tanggal->format('j F, Y'); ?>
        <div class="bg-white rounded-lg shadow-lg p-8 w-96">
            <div class="mb-4">
                <p class="font-medium text-gray-700">Dipinjam Oleh: <span class="font-normal"><?= htmlspecialchars($data['Username']) ?> | <?= htmlspecialchars($data['NamaLengkap']) ?></span></p>
                <p class="font-medium text-gray-700">Judul Buku: <span class="font-normal"><?= htmlspecialchars($data['Judul']) ?></span></p>
                <p class="font-medium text-gray-700">Tanggal Peminjaman: <span class="font-normal"><?= htmlspecialchars(date("j F, Y", strtotime($data['TanggalPeminjaman']))) ?></span></p>
                <p class="font-medium text-gray-700">Tanggal Pengembalian: <span class="font-normal"><?= htmlspecialchars($tanggalPengembalian) ?></span></p>
                <p class="font-medium text-gray-700">Status Peminjaman: <span class="font-normal"><?= htmlspecialchars($data['StatusPeminjaman']) ?></span></p>
            </div>

            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-2">Berikan Rating/Ulasan:</h3>
                <div class="flex items-center space-x-2">
                    <input type="number" min="1" max="5" class="border border-gray-300 rounded-md p-2 w-12" placeholder="1-5">
                    <textarea class="border border-gray-300 rounded-md p-2 w-full" rows="3" placeholder="Tulis ulasan..."></textarea>
                </div>
            </div>

            <div class="flex space-x-2 mb-6">
                <button class="flex-1 bg-yellow-500 text-white py-2 rounded-md hover:bg-yellow-600 transition duration-200">Dibatalkan</button>
                <button class="flex-1 bg-green-600 text-white py-2 rounded-md hover:bg-green-700 transition duration-200">Dikembalikan</button>
            </div>

            <hr class="my-4">
        </div>
    <?php endforeach; ?>
</div>