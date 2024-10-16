<?php
    session_start();
    require_once "./connection.php";
    require_once "./utils.php";
    $query = $conn->query("SELECT 
        buku.Judul AS Judul,
        COUNT(ulasanbuku.UlasanID) AS JumlahUlasan,
        AVG(ulasanbuku.Rating) AS Rating
    FROM 
        ulasanbuku
    LEFT JOIN 
        buku ON buku.BukuID = ulasanbuku.BukuID
    GROUP BY 
        buku.BukuID
    ORDER BY 
        JumlahUlasan DESC
        LIMIT 10
    ");
    $datas = $query->fetchAll();
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
    </style>
</head>
<body>
    <nav class="fixed bg-blue-200 justify-between flex flex-row px-20 shadow-md" style="left:0;z-index: 9999999;width: 100vw; height: 12vh; top: 0">
        <div class="flex justify-around flex-row items-center">
            <img src="https://www.svgrepo.com/show/486715/library.svg" alt="" style="width: 70px">
            <h1 class="text-3xl">Maulana Perpus</h1>
        </div>
        <ul class="list-none flex justify-start flex-row items-center gap-4">
            <li><a href="./">Beranda</a></li>
            <li><a href="./populer.php">Populer</a></li>
            <?php 
                $dataSesi = verifikasiSession($_SESSION["loginsesi"] ?? false, $config['key']);
                if($dataSesi != false) {
                    if($dataSesi['status'] == true || $dataSesi['status'] == 1) {
                        if($dataSesi['role'] == 2) {
                            $url = "./staff";
                        } else if($dataSesi['role'] == 3) {
                            $url = "./user";
                        }
            ?>
            <li><a href="<?= $url ?? './' ?>">Dashboard</a></li>
            <li class="p-2 shadow-md border-black hover:scale-125 rounded-full bg-red-500"><a href="./logout.php">Logout</a></li>
            <?php }} else { ?> 
            <li class="p-2 shadow-md border-black hover:scale-125 rounded-full bg-red-100"><a href="./login.php">Sign Up</a></li>
            <?php } ?>
        </ul>
    </nav>
    <div class="p-5 mt-28 rounded-md shadow m-8 px-20">
        <h1 class="text-2xl font-bold text-center p-4 border-b">Rangking Buku Teratas</h1>
        <ol class="list-decimal p-4">
            <?php
                if(!empty($datas)) {
                foreach($datas as $i => $value) {
            ?>
            <li class="flex items-center justify-between py-2">
                <span class="text-lg"><span class="me-2 p-2 shadow-inner rounded-md bg-slate-100"><?= $i + 1 ?>.</span> <?= $value['Judul'] ?></span>
                <span class="font-semibold text-blue-600"><?= $value['JumlahUlasan'] ?> Ulasan | <?= number_format($value['Rating'], 2) ?></span>
            </li>
            <?php }} else  {
                echo <<<'HTML'
                <div>
                    <h1>Tidak ada ulasan</h1>
                </div>
                HTML;
            } ?>
        </ol>
    </div>
    <footer style="width: 100vw; height: 10vh" class="flex justify-center items-center bg-slate-500">
        &copy;<span id="th"></span>. All Rights Reserved.
    </footer>
</body>
</html>
