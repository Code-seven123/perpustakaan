<?php
    $borrow = $conn->query("SELECT 
            buku.BukuID,
            buku.Judul,
            user.Username,
            user.NamaLengkap,
            peminjaman.StatusPeminjaman,
            peminjaman.TanggalPeminjaman,
            peminjaman.TanggalPengembalian
        FROM peminjaman
        LEFT JOIN
            buku ON buku.BukuID = peminjaman.BukuID
        LEFT JOIN
            user ON user.UserID = peminjaman.UserID
        ORDER BY peminjaman.TanggalPeminjaman DESC
    ");
    $data = $borrow->fetchAll();
?>
<h1 class="text-4xl font-bold mb-7 text-center">Daftar Peminjaman Buku</h1>
<div class="flex flex-col gap-y-4">
    <?php foreach($data as $index => $value) { 
        // Hitung tanggal batas pengembalian
        $tanggalPeminjaman = new DateTime($value['TanggalPeminjaman']);
        $tanggalPengembalian = (int)$value['TanggalPengembalian']; // Pastikan ini adalah integer
        $tanggalBatas = $tanggalPeminjaman->modify("+$tanggalPengembalian days");
    ?>
    <div class="flex flex-col border-b-2 border-black rounded-md">
        <table>
            <thead class="bg-lime-100 even:bg-cyan-100 rounded-lg">
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
        <h1 class="text-slate-400 text-lg mb-2">Transaksi : <?= date("d F Y", strtotime($value['TanggalPeminjaman'])) ?> / Expired : <?= $tanggalBatas->format("d F Y") ?></h1>
    </div>
    <?php } ?>
</div>