<?php
    function validateArray($arr) {
        if (count($arr) > 5) {
            return false;
        }
        foreach ($arr as $value) {
            if ($value !== 'star') {
                return false;
            }
        }
      
        return true;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") { 
        $ulasan = $_POST['ulasan'] ?? "";
        $stars = $_POST['stars'] ?? [];
        $bukuid = $_POST['BukuID'] ?? 0;
        $sesi = verifikasiSession($_SESSION["loginsesi"] ?? false, $config['key']);
        if(validateArray($stars) && !empty($ulasan)){
            $query = $conn->prepare("INSERT INTO ulasanbuku VALUES ('', :userid, :bukuid, :ulasan, :rating)");
            $query->bindParam(":userid", $sesi['id']);
            $query->bindParam(":bukuid", $bukuid);
            $query->bindParam(":ulasan", $ulasan);
            $query->bindValue(":rating", count($stars));
            if($query->execute()) {
                $msg = "Sukses memberi rating";
                $colormsg = "bg-green-300";
            } else {
                $msg = "Gagal untuk memberikan rating";
            }
        } else {
            $msg = "Bintang tidak valid atau ulasan kosong";
        }
    }
    if(isset($_POST["action"])) {
        if(isset($_POST['id'])){
            $action = $_POST['action'];
            $query = $conn->prepare("UPDATE `peminjaman` SET `StatusPeminjaman`=:status WHERE PeminjamID=:id");
            $query->bindParam(":id", $_POST['id']);
            if($action == "close") {
                $query->bindParam(":status", "dibatalkan");
                $query->execute();
            } else if($action == "cancel") {
                $query->bindParam(":status", "dikembalikan");
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
                <form action="" method="post">
                    <input type="hidden" name="BukuID" value="<?= $data['BukuID'] ?? '' ?>">
                    <div class="flex flex-col items-center space-x-2" id="rating">
                        <div class="flex justify-around gap-5 mb-3">
                            <label class="cursor-pointer">
                                <input type="checkbox" name="stars[]" value="star" class="hidden star" />
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400 fill-current hover:text-yellow-500" viewBox="0 0 20 20">
                                    <path d="M10 15.27L16.18 19 15.64 12.97 20 8.24l-6.91-.58L10 2 6.91 7.66 0 8.24l4.36 4.73L3.82 19z"/>
                                </svg>
                            </label>
                            <label class="cursor-pointer">
                                <input type="checkbox" name="stars[]" value="star" class="hidden star" />
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400 fill-current hover:text-yellow-500" viewBox="0 0 20 20">
                                    <path d="M10 15.27L16.18 19 15.64 12.97 20 8.24l-6.91-.58L10 2 6.91 7.66 0 8.24l4.36 4.73L3.82 19z"/>
                                </svg>
                            </label>
                            <label class="cursor-pointer">
                                <input type="checkbox" name="stars[]" value="star" class="hidden star" />
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400 fill-current hover:text-yellow-500" viewBox="0 0 20 20">
                                    <path d="M10 15.27L16.18 19 15.64 12.97 20 8.24l-6.91-.58L10 2 6.91 7.66 0 8.24l4.36 4.73L3.82 19z"/>
                                </svg>
                            </label>
                            <label class="cursor-pointer">
                                <input type="checkbox" name="stars[]" value="star" class="hidden star" />
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400 fill-current hover:text-yellow-500" viewBox="0 0 20 20">
                                    <path d="M10 15.27L16.18 19 15.64 12.97 20 8.24l-6.91-.58L10 2 6.91 7.66 0 8.24l4.36 4.73L3.82 19z"/>
                                </svg>
                            </label>
                            <label class="cursor-pointer">
                                <input type="checkbox" name="stars[]" value="star" class="hidden star" />
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400 fill-current hover:text-yellow-500" viewBox="0 0 20 20">
                                    <path d="M10 15.27L16.18 19 15.64 12.97 20 8.24l-6.91-.58L10 2 6.91 7.66 0 8.24l4.36 4.73L3.82 19z"/>
                                </svg>
                            </label>
                        </div>

                        <textarea name="ulasan" class="border border-gray-300 rounded-md p-2 w-full" rows="3" placeholder="Tulis ulasan..."></textarea>
                        <button class="p-2 w-full rounded-lg bg-lime-300"  type="submit">Kirim</button>
                    </div>
                </form>
            </div>

            <div class="flex space-x-2 mb-6">
                <button onclick="window.location.href = '?page=borrow&action=close&id=<?= $data['PeminjamID'] ?>'" class="flex-1 bg-yellow-500 text-white py-2 rounded-md hover:bg-yellow-600 transition duration-200">Dibatalkan</button>
                <button onclick="window.location.href = '?page=borrow&action=cancel&id=<?= $data['PeminjamID'] ?>'" class="flex-1 bg-green-600 text-white py-2 rounded-md hover:bg-green-700 transition duration-200">Dikembalikan</button>
            </div>

            <hr class="my-4">
        </div>
    <?php endforeach; ?>
</div>