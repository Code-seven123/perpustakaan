<?php
    session_start();
    require_once "./connection.php";
    require_once "./utils.php";
    $query = $conn->query("SELECT 
        COUNT(DISTINCT buku.BukuID) AS TotalBuku,
        COUNT(DISTINCT peminjaman.PeminjamID) AS TotalPinjam,
        COUNT(DISTINCT ulasanbuku.UlasanID) AS TotalUlasan
    FROM 
        buku
    LEFT JOIN 
        ulasanbuku ON ulasanbuku.BukuID = buku.BukuID
    LEFT JOIN 
        peminjaman ON buku.BukuID = peminjaman.BukuID;
    ");
    $countData = $query->fetch();
    
    $sql = "SELECT PeminjamID, TanggalPeminjaman, TanggalPengembalian, StatusPeminjaman FROM peminjaman";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $peminjamanData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Waktu sekarang
    $sekarang = new DateTime();

    foreach ($peminjamanData as $data) {
        // Hitung tanggal batas pengembalian
        $tanggalPeminjaman = new DateTime($data['TanggalPeminjaman']);
        $tanggalPengembalian = (int)$data['TanggalPengembalian']; // Pastikan ini adalah integer
        $tanggalBatas = $tanggalPeminjaman->modify("+$tanggalPengembalian days");

        if ($sekarang > $tanggalBatas && $data['StatusPeminjaman'] == 'dipinjam') {
            $updateSql = "UPDATE peminjaman SET StatusPeminjaman = 'expired' WHERE PeminjamID = :peminjamID";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bindParam(':peminjamID', $data['PeminjamID']);
            $updateStmt->execute();
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maulana Perpustakaan</title>
    <link rel="shortcut icon" href="https://www.svgrepo.com/show/486715/library.svg" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        clifford: '#da373d',
                    }
                }
            }
        }
    </script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            border: 1px solid black
        }
        .slide-in {
            transform: translateX(100%); /* Memulai dari luar layar di kanan */
            opacity: 0; /* Mulai dengan transparan */
            transition: transform 1s ease, opacity 1s ease; /* Mengatur durasi dan easing */
        }
        .slide-in.active {
            transform: translateX(0); /* Menggerakkan ke posisi normal */
            opacity: 1; /* Menampilkan gambar */
        }
        body {
            width: 100vw;
            height: 100vh;
        }
        html {
            box-sizing: border-box;
        }

        *, *::before, *::after {
            box-sizing: inherit;
        }
    </style>
</head>
<body class="flex justify-content items-center flex-col p-10">
    <nav class="fixed bg-blue-200 justify-between flex flex-row px-20 shadow-md" style="z-index: 9999999;width: 100vw; height: 12vh; top: 0">
        <div class="flex justify-around flex-row items-center">
            <img src="https://www.svgrepo.com/show/486715/library.svg" alt="" style="width: 70px">
            <h1 class="text-3xl">Maulana Perpus</h1>
        </div>
        <ul class="list-none flex justify-start flex-row items-center gap-4">
            <li><a href="">Beranda</a></li>
            <li><a href="./populer.php">Populer</a></li>
            <?php 
                $dataSesi = verifikasiSession($_SESSION["loginsesi"] ?? false, $config['key']);
                if($dataSesi != false) {
                    if($dataSesi['status'] == true || $dataSesi['status'] == 1) {
                        if($dataSesi['role'] == 2) {
                            $html = <<<HTML
                                <li><a href="./staff">Dashboard Admin</a></li>
                            HTML;
                            $url = "./user";
                        } else if($dataSesi['role'] == 3) {
                            $url = "./user";
                        }
            ?>
            <li><a href="<?= $url ?? './' ?>">Dashboard</a></li>
            <?= $html ?? '' ?>
            <li class="p-2 shadow-md border-black hover:scale-125 rounded-full bg-red-500"><a href="./logout.php">Logout</a></li>
            <?php }} else { ?> 
            <li class="p-2 shadow-md border-black hover:scale-125 rounded-full bg-red-100"><a href="./login.php">Sign Up</a></li>
            <?php } ?>
        </ul>
    </nav>
    <div class="container flex flex-row justify-center items-center border-b-2 border-t-2 border-black my-40 p-10 mx-60" style="height: 80vh">
        <div class="relative flex items-center justify-center px-50" style="width: 75%">
            <div style="width: 100%">
                <img src="https://www.svgrepo.com/show/492789/books-and-people.svg" class="slide-in bg-contain bg-center" alt="Gambar" width="200"> 
            </div>
            <div class="absolute p-9 bg-white text-wrap" style="max-width: 450px; right: 0">
                <h1 class="text-4xl">Jadilah Pribadi yang Kreatif dan Inovatif</h1>
                <p>Di mana pun kamu berada, Perpustakaan selalu hadir untuk menghubungkan dan merekomendasikan buku favoritmu.</p>
                <div class="flex flex-row justify-start mt-3">
                    <button class="rounded-full bg-cyan-300 p-2" onclick="window.location.href = 'login.php'">Mulai Sekarang!</button>
                </div>
            </div>
        </div>
    </div>
    <div class="bg-grey-100 shadow rounded-md p-10 flex justify-center" style="width: 100%;">
        <ul class="list-none flex justify-around gap-4 p-4 rounded-lg bg-amber-400">
            <li class="flex flex-col justify-center shadow-inner rounded-sm p-5 text-center">
                Buku
                <span><?= $countData['TotalBuku'] ?? '0' ?></span>
            </li>
            <li class="flex flex-col justify-center shadow-inner rounded-sm p-5 text-center">
                Dinilai
                <span><?= $countData['TotalUlasan'] ?? '0' ?></span>
            </li>
            <li class="flex flex-col justify-center shadow-inner rounded-sm p-5 text-center">
                Dipinjam
                <span><?= $countData['TotalPinjam'] ?? '0' ?></span>
            </li>
        </ul>
    </div>
    <footer style="width: 100vw; height: 10vh" class="flex justify-center items-center bg-slate-500">
        &copy;<span id="th"></span>. All Rights Reserved.
    </footer>
    <script>
        window.addEventListener('load', () => {
            const img = document.querySelector('.slide-in');
            img.classList.add('active'); // Menambahkan kelas aktif untuk memulai animasi
        });
        document.getElementById("th").innerHTML = new Date().getFullYear()
    </script>
</body>
</html>
