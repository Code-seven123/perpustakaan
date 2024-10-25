<?php
    if(isset($_GET["action"])) {
        if(isset($_GET['id'])){
            $action = $_GET['action'];
            $query = $conn->prepare("UPDATE `peminjaman` SET `StatusPeminjaman`=:status WHERE PeminjamID=:id");
            $query->bindParam(":id", $_GET['id']);
            if($action == "close") {
                $query->bindValue(":status", "dibatalkan");
                $query->execute();
            } else if($action == "cancel") {
                $query->bindValue(":status", "dikembalikan");
                $query->execute();
            }
        } else {
            $msg = "Operasi dibatalkan, tidak ada pinjaman dipilih";
        }
    }
?> 
<h2 class="text-5xl font-bold text-center text-gray-800 mb-6">Detail Peminjaman</h2>
<?php if (isset($msg)) { ?>
<div class="flex justify-center align-center <?= $colormsg ?? 'bg-red-100' ?> w-full border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
    <?= $msg ?>
</div>
<?php } ?>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10">
    <?php
    if(isset($_GET['id'])) {
        $sql = "SELECT
                peminjaman.PeminjamID,
                user.Username,
                user.NamaLengkap,
                buku.BukuID,
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
                buku.BukuID=?
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$_GET['id']]);
        $peminjamanData = $stmt->fetchAll();
    } else {
        $sql = "SELECT
                peminjaman.PeminjamID,
                user.Username,
                user.NamaLengkap,
                buku.BukuID,
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
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $peminjamanData = $stmt->fetchAll();
    }
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

            <div class="flex space-x-2 mb-6">
                <button onclick="window.location.href = '?page=borrow&action=close&id=<?= $data['PeminjamID'] ?>'" class="flex-1 bg-yellow-500 text-white py-2 rounded-md hover:bg-yellow-600 transition duration-200">Dibatalkan</button>
                <button onclick="window.location.href = '?page=borrow&action=cancel&id=<?= $data['PeminjamID'] ?>'" class="flex-1 bg-green-600 text-white py-2 rounded-md hover:bg-green-700 transition duration-200">Dikembalikan</button>
            </div>

            <hr class="my-4">
        </div>
    <?php endforeach; ?>
</div>