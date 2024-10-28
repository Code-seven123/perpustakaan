<?php
    $borrow = $conn->prepare("SELECT 
            buku.BukuID,
            buku.Judul,
            user.Username,
            user.NamaLengkap,
            peminjaman.StatusPeminjaman,
            peminjaman.TanggalPeminjaman,
            peminjaman.TanggalPengembalian,
            peminjaman.PeminjamID
        FROM peminjaman
        LEFT JOIN
            buku ON buku.BukuID = peminjaman.BukuID
        LEFT JOIN
            user ON user.UserID = peminjaman.UserID
        WHERE user.UserID = :id
        ORDER BY peminjaman.TanggalPeminjaman DESC
    ");
    $sesi = verifikasiSession($_SESSION["loginsesi"] ?? false, $config['key']);
    $borrow->bindParam(":id", $sesi['id']);
    $borrow->execute();
    $data = $borrow->fetchAll();
?>
<h1 class="text-4xl font-bold mb-7 text-center">Daftar Peminjaman Buku</h1>
<div class="flex flex-col gap-y-4">
    <?php foreach($data as $index => $value) { 
        // Hitung tanggal batas pengembalian
        $tanggalPeminjaman = new DateTime($value['TanggalPeminjaman']);
        $tanggalPengembalian = (int)$value['TanggalPengembalian']; // Pastikan ini adalah integer
        $tanggalBatas = $tanggalPeminjaman->modify("+$tanggalPengembalian days");

        if ($sekarang > $tanggalBatas) {
            $selisih = $tanggalBatas->diff($sekarang);
            $dendaInt = $config['denda'] * $selisih->days;
            $dendaIDR = number_format($dendaInt, 2, ',', '.');
        }
    ?>
    <div class="flex flex-col border-2 border-black rounded-md <?= $value['StatusPeminjaman'] == "expired" ? 'bg-red-100' : '' ?>">
        <table>
            <thead class="bg-lime-100 bg-cyan-100 <?= $value['StatusPeminjaman'] == "expired" ? 'bg-red-500' : '' ?> rounded-lg">
                <th>BukuID</th>
                <th>Nama Buku</th>
                <th>Dipinjam oleh Username/Nama Lengkap</th>
                <th>Status</th>
            </thead>
            <tbody>
                <tr class="text-center">
                    <td><?= $value['BukuID'] ?></td>
                    <td><?= $value['Judul'] ?></td>
                    <td><?= $value['Username'] ?>/<?= $value['NamaLengkap'] ?></td>
                    <td><?= $value['StatusPeminjaman'] ?></td>
                </tr>
            </tbody>
        </table>
        <hr class="my-4 border-2 border-black mx-2">
        <?php if($value['StatusPeminjaman'] == "expired") { ?>
            <p class="text-bold text-red-500 text-center">Mohon untuk dikembalikan buku ini. Kalau tidak akan didenda Rp<?= number_format($config['denda'], 2, ',', '.') ?> per-hari tidak dikembalikan </p>
        <?php } ?>
        <div class="flex justify-between">
            <h1 class="text-slate-400 text-lg mb-2">
                Transaksi : <?= date("d F Y", strtotime($value['TanggalPeminjaman'])) ?> 
                / Expired : <?= $tanggalBatas->format("d F Y") ?>
                <?php if(isset($dendaIDR)) { ?>
                <span class="text-red-600">/ Denda Rp<?= $dendaIDR ?></span>
                <?php } ?>
            </h1>
            <h1 class="text-slate-400 text-lg mb-2"><a href="?page=rating&id=<?= $value['PeminjamID'] ?>" class="flex underline">Rating <img src="https://www.svgrepo.com/show/398391/star.svg" class="w-5 ms-2" alt=""></a></h1>
        </div>
    </div>
    <?php } ?>
</div>