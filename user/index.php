<?php
    require_once "./connection.php";
    $query = $conn->query("SELECT 
        COUNT(DISTINCT buku.BukuID) AS TotalBuku,
        COUNT(DISTINCT peminjaman.PeminjamID) AS TotalPinjam,
        COUNT(DISTINCT kategoribuku.KategoriID) AS TotalKategori
    FROM 
        buku
    LEFT JOIN 
        kategoribuku_relasi ON buku.BukuID = kategoribuku_relasi.BukuID
    LEFT JOIN 
        kategoribuku ON kategoribuku_relasi.KategoriID = kategoribuku.KategoriID
    LEFT JOIN 
        peminjaman ON buku.BukuID = peminjaman.BukuID;
    ");
    $data = $query->fetch();
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
<body>
    <nav class="fixed bg-blue-200 justify-between flex flex-row px-20 shadow-md" style="z-index: 9999999;width: 100vw; height: 12vh; top: 0">
        <div class="flex justify-around flex-row items-center">
            <img src="https://www.svgrepo.com/show/486715/library.svg" alt="" style="width: 70px">
            <h1 class="text-3xl">Maulana Perpus</h1>
        </div>
        <ul class="list-none flex justify-start flex-row items-center gap-4">
            <li><a href="">Beranda</a></li>
            <li><a href="./populer.php">Populer</a></li>
            <li class="p-2 shadow-md border-black hover:scale-125 rounded-full bg-red-100"><a href="login.php">Sign Up</a></li>
        </ul>
    </nav>
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
