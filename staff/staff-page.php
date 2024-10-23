<?php
    if(isset($_GET['action'])) {
        if($_GET['action'] == "demote" || $_GET['action'] == "promote" && isset($_GET['id'])) {
            $stmt = $conn->prepare("UPDATE user SET permission=? WHERE UserID=?");
            if($stmt->execute([$_GET['action'] == "promote" ? 2 : 3, $_GET['id']])) {
                $msg = "Sukses mengangkat menjadi admin";
                $colormsg = "bg-green-300";
            } else {
                $msg = "Gagal mengangkat menjadi admin";
            }
        }
    }
?>

<h1 class="text-4xl font-bold mb-7 text-center">Daftar User</h1>

<?php if (isset($msg)) { ?>
<div class="flex justify-center align-center <?= $colormsg ?? 'bg-red-100' ?> w-full border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
    <?= $msg ?>
</div>
<?php } ?>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
    <?php
        $userqry = $conn->query("SELECT UserID, Username, Email, permission FROM user");
        $user = $userqry->fetchAll();

        foreach($user as $data) {
    ?>
    <div class="max-w-60 p-4 shadow">
        <ul>
            <li>Username : <?= $data["Username"] ?></li>
            <li>Email : <?= $data["Email"] ?></li>
            <li>Role : <?php
                if($data["permission"] == 2) {
                    echo "staff/admin";
                } else if($data["permission"] == 3) {
                    echo "Pengguna";
                } else {
                    echo "unknown";
                }
            ?></li>
            <li class="flex justify-around mt-4">
                <a title="Promote" href="?page=staff&action=promote&id=<?= $data['UserID'] ?>"><img title="Promote" class="w-8 p-2 rounded-full bg-red-400" src="https://www.svgrepo.com/show/535182/arrow-up.svg" alt=""></a>
                <a title="Deemote" href="?page=staff&action=demote&id=<?= $data['UserID'] ?>"><img title="Deemote" class="w-8 p-2 rounded-full bg-green-400" src="https://www.svgrepo.com/show/535141/arrow-down.svg" alt=""></a>
            </li>
        </ul>
    </div>
    <?php } ?>
</div>