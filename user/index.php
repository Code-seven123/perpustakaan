<?php
    session_start();
    require_once "../connection.php";
    require_once "../utils.php";
    $sesi = verifikasiSession($_SESSION["loginsesi"] ?? false, $config['key']);
    if ($sesi['status'] ?? $sesi == true ) {
        if($sesi['role'] != 2 && $sesi['role'] != 3) {
            redirect("..");
        }
    } else {
        redirect("..");
    }
    
    $page = $_GET['page'] ?? 'bookshelf';

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
        }
        #kategori input[type="checkbox"]:checked + span::before {
            content: '✔';
            margin-right: 8px;
        }
        #rating input[type="checkbox"]:checked + svg {
            color: #facc15;
        }

    </style>
</head>
<body>
    <nav class="sticky bg-blue-200 justify-between flex flex-row px-20 shadow-md" style="z-index: 9999999;width: 100vw; height: 12vh; top: 0">
        <div class="flex justify-around flex-row items-center">
            <img src="https://www.svgrepo.com/show/486715/library.svg" alt="" style="width: 70px">
            <h1 class="text-3xl">Maulana Perpus</h1>
        </div>
        <ul class="list-none flex justify-start flex-row items-center gap-4">
            <li><a href="..">Beranda</a></li>
            <li><a href="../populer.php">Populer</a></li>
            <li><a href="./">Dashboard</a></li>
            <?php 
                $dataSesi = verifikasiSession($_SESSION["loginsesi"] ?? false, $config['key']);
                if($dataSesi != false) {
                    if($dataSesi['status'] == true || $dataSesi['status'] == 1) {
                        if($dataSesi['role'] == 2) {
                            echo <<<HTML
                                <li><a href="../staff">Dashboard Admin</a></li>
                            HTML;
                        }
                    }
                }
            ?>
            <li class="p-2 shadow-md border-black hover:scale-125 rounded-full bg-red-500"><a href="../logout.php">Logout</a></li>
        </ul>
    </nav>
    <div id="container" class="flex">
        <div class="w-64 bg-white shadow-md h-screen  fixed left-0">
            <div class="p-6">
                <h1 class="text-xl font-bold text-gray-800">Menu</h1>
            </div>
            <nav class="mt-6">
                <ul>
                    <li class="<?= ($page == 'bookshelf') ? 'bg-gray-200' : '' ?> hover:bg-gray-200">
                        <a href="?page=bookshelf" class="block px-4 py-2 text-gray-700 flex items-center"><img src="https://www.svgrepo.com/show/455392/bookshelf-library.svg" class="w-9 me-4" alt=""> Bookshelf</a>
                    </li>
                    <li class="<?= ($page == 'borrow') ? 'bg-gray-200' : '' ?> hover:bg-gray-200">
                        <a href="?page=borrow" class="block px-4 py-2 text-gray-700 flex items-center"><img src="https://www.svgrepo.com/show/58793/take-out-from-the-cart.svg" class="w-9 me-4" alt=""> pinjaman</a>
                    </li>
                    <li class="<?= ($page == 'collection') ? 'bg-gray-200' : '' ?> hover:bg-gray-200">
                        <a href="?page=collection" class="block px-4 py-2 text-gray-700 flex items-center"><img src="https://www.svgrepo.com/show/501146/collection.svg" class="w-9 me-4" alt=""> Koleksi</a>
                    </li>
                </ul>
            </nav>
        </div>
        <div class="flex-1 p-6 ms-64">
            <?php
                $valid_pages = ['borrow', 'collection', 'borrowup', 'bookshelf', 'addcollection', 'deletecollection'];
                if (in_array($page, $valid_pages) && file_exists("$page-page.php")) {
                    include "$page-page.php";
                } else {
                    echo <<<'HTML'
                        <div class="text-center">
                            <h1 class="text-6xl font-bold text-gray-800">404</h1>
                            <p class="mt-4 text-lg text-gray-600">Ooops... Halaman tidak ditemukan!</p>
                            <a href=".." class="mt-6 inline-block px-4 py-2 text-white bg-blue-500 rounded hover:bg-blue-600 transition duration-200">Kembali ke Beranda</a>
                        </div>
                    HTML;
                }
            ?>

        </div>
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
        function toggleCustomInput() {
            const select = document.getElementById('option');
            const customInputContainer = document.getElementById('custom-input-container');
            if (select.value === 'custom') {
                customInputContainer.classList.remove('hidden');
            } else {
                customInputContainer.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
