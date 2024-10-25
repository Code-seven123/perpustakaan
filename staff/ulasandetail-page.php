<?php 

$BukuID = $_GET['id']; 
    if(isset($_GET['action'])) {
        if($_GET['action'] == "remove") {
            $ulasanID = $_GET['idUlasan'];
            $delete = $conn->prepare("DELETE FROM ulasanbuku WHERE UlasanID=?");
            $delete->execute([$ulasanID]);
        }
    }
?>
<div class="mb-6">
    <h2 class="text-xl font-semibold mb-4">Filter Ulasan</h2>
    <form action="" method="post" class="flex space-x-4">
        <div class="w-1/3">
            <label class="block text-sm font-medium text-gray-700">Jumlah Bintang</label>
            <select name="star" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                <option value="">Semua</option>
                <option value="5">⭐⭐⭐⭐⭐</option>
                <option value="4">⭐⭐⭐⭐</option>
                <option value="3">⭐⭐⭐</option>
                <option value="2">⭐⭐</option>
                <option value="1">⭐</option>
            </select>
        </div>
        <div class="w-1/3">
            <label class="block text-sm font-medium text-gray-700">Dari Siapa</label>
            <select name="user" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                <option value="">Semua</option>
                <?php 
                    $userInBookQuery = $conn->prepare("SELECT
                            user.UserID,
                            user.Username,
                            user.NamaLengkap
                        FROM
                            user
                        JOIN
                            ulasanbuku ON ulasanbuku.UserID = user.UserID
                        WHERE
                            ulasanbuku.BukuID = ?
                    ");
                    $userInBookQuery->execute([$BukuID]);
                    $dataBookUser = $userInBookQuery->fetchAll();
                    $uniqueArray = array_reduce($dataBookUser, function($carry, $item) {
                        $carry[$item['UserID']] = $item;
                        return $carry;
                    }, []);
                    
                    $uniqueArray = array_values($uniqueArray);
                    foreach($uniqueArray as $data) {
                ?>
                <option value="<?= $data['UserID'] ?>"><?= $data['UserID'] ?> : <?= kapital($data['Username']) ?> / <?= kapital($data['NamaLengkap']) ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="w-1/3 flex items-end">
            <button class="mt-2 w-full bg-blue-500 text-white font-semibold py-2 rounded-md hover:bg-blue-600">Terapkan Filter</button>
        </div>
    </form>
</div>
<div class="overflow-x-auto">
    <table class="min-w-full bg-white border border-gray-200">
        <thead>
            <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                <th class="py-3 px-6 text-left">Username/Nama</th>
                <th class="py-3 px-6 text-left">Judul Buku</th>
                <th class="py-3 px-6 text-left">Ulasan</th>
                <th class="py-3 px-6 text-left">Rating</th>
                <th class="py-3 px-6 text-left">Option</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 text-sm font-light">
            <?php
                if($_SERVER["REQUEST_METHOD"] == "POST") {
                    $ulasanqry = $conn->prepare("SELECT
                            user.Username,
                            user.NamaLengkap,
                            buku.Judul,
                            ulasanbuku.UlasanID,
                            ulasanbuku.Ulasan,
                            ulasanbuku.Rating
                        FROM
                            ulasanbuku
                        LEFT JOIN
                            user ON user.UserID = ulasanbuku.UserID
                        LEFT JOIN
                            buku ON buku.BukuID = ulasanbuku.BukuID
                        WHERE
                            buku.BukuID = ? AND
                            ulasanbuku.Rating = ? OR
                            ulasanbuku.UserID = ?
                    ");
                    $ulasanqry->execute([$BukuID, $_POST['star'], $_POST['user']]);
                    $data = $ulasanqry->fetchAll();
                } else {
                    $ulasanqry = $conn->prepare("SELECT
                            user.Username,
                            user.NamaLengkap,
                            buku.Judul,
                            ulasanbuku.UlasanID,
                            ulasanbuku.Ulasan,
                            ulasanbuku.Rating
                        FROM
                            ulasanbuku
                        LEFT JOIN
                            user ON user.UserID = ulasanbuku.UserID
                        LEFT JOIN
                            buku ON buku.BukuID = ulasanbuku.BukuID
                        WHERE
                        buku.BukuID = ?
                    ");
                    $ulasanqry->execute([$BukuID]);
                    $data = $ulasanqry->fetchAll();
                }

                if(isset($data) && $ulasanqry->rowCount() > 0) {
                    foreach($data as $value) {
            ?>
            <tr class="border-b border-gray-200 hover:bg-gray-100">
                <td class="py-3 px-6"><?= kapital($value['Username']) ?> / <?= kapital($value['NamaLengkap']) ?></td>
                <td class="py-3 px-6"><?= $value['Judul'] ?></td>
                <td class="py-3 px-6"><?= $value['Ulasan'] ?></td>
                <td class="py-3 px-6"><?= str_repeat("⭐", (int)$value['Rating']) ?></td>
                <td class="py-3 px-6">
                    <a href="?page=ulasandetail&id=<?= $BukuID ?>&action=remove&idUlasan=<?= $value['UlasanID'] ?>" class="bg-red-400 p-2 rounded-md border border-green-200">delete</a>
                </td>
            </tr>
            <?php }} else { ?>
            <tr class="border-b border-gray-200 hover:bg-gray-100">
                <td class="py-3 px-6 text-center" colspan="5">Tidak Ada Ulasan</td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>