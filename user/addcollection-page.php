<?php
    $id = $_GET['id'];
    $query = $conn->prepare("select * from buku where BukuID=:id");
    $query->bindParam(":id", $id);
    $query->execute();
    $buku = $query->fetch();
    $sesi = verifikasiSession($_SESSION["loginsesi"] ?? false, $config['key']);
    $query1 = $conn->prepare("select * from koleksipribadi where UserID=:id");
    $query1->bindValue(":id", $sesi["id"] ?? "");
    $query1->execute();
    if($query->rowCount() > 0) {
        if($query1->rowCount() <= 0) {
            $adding = $conn->prepare("INSERT INTO `koleksipribadi`(`UserID`, `BukuID`) VALUES (:userid, :bukuid)");
            $adding->bindParam(":bukuid", $buku['BukuID']);
            $adding->bindParam(":userid", $sesi['id']);
            if($adding->execute()) {
                echo "<script>history.back()</script>";
            } else {
                $msg = "Mohon maaf, terjadi kesalahan saat memproses permintaan Anda. Silakan coba lagi.";
            }
        } else {
            $msg = "buku tersebut sudah masuk ke koleksi, mohon untuk memilik buku yang lain.";
        }
    } else {
        $msg = "Buku tidak tersedia.";
    }
?>
<div class="bg-white rounded-lg shadow-lg p-6 max-w-sm">
    <div class="flex items-center mb-4">
        <div class="bg-red-100 text-red-500 rounded-full p-2 mr-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h1m-1 4v-4m0 4h-1m6 0h-1m0 0V8m0 8h1M8 7h8m-8 0v1m0 0H7m1 0h8m-8 0v-1" />
            </svg>
        </div>
        <h2 class="text-lg font-semibold">Terjadi Kesalahan</h2>
    </div>
    <p class="text-gray-700"><?= $msg ?? "tidak diketahui" ?></p>
    <div class="mt-6">
        <a href="javascript:history.back()" class="bg-blue-500 text-white rounded-lg px-4 py-2 hover:bg-blue-600">Kembali ke Halaman</a>
    </div>
</div>