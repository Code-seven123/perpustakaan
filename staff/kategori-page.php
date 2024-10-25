<?php
    if(isset($_GET['id']) && isset($_GET['action'])) {
        $id = $_GET['id'];
        if ($_GET['action'] == "remove") {
            $qry = $conn->prepare("DELETE FROM `kategoribuku` WHERE KategoriID=?");
            if($qry->execute([$id])) {
                redirect("?page=kategori");
            }
        }
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $kategoriID = $_POST['id'] ?? false;
        $nama = $_POST['kategori'] ?? '';
        if($kategoriID) {
            $qry = $conn->prepare("UPDATE kategoribuku SET NamaKategori=? WHERE KategoriID=?");
            if($qry->execute([$nama, $kategoriID])) {
                redirect("?page=kategori");
            }
        } else {
            $qry = $conn->prepare("INSERT INTO kategoribuku (NamaKategori) VALUES (?)");
            if($qry->execute([$nama])) {
                redirect("?page=kategori");
            }
        }
    }
?>
<h1 class="text-4xl font-bold mb-7 text-center">Laporan</h1>
<div class="w-full p-3 bg-slate-300 mb-3">
    <form action="" method="post">
        <label for="id">Tambah Data</label>
        <div class="flex justify-start">
            <input type="text" name="kategori" value="" class="border-black focus:outline-none text-center border-b-2 bg-slate-300" id="id">
            <button class="bg-green-400 p-2 rounded-md"><img class="w-5" src="https://www.svgrepo.com/show/521819/save.svg" alt=""></button>
        </div>
    </form>
</div>
<div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-5">
    <?php
        $stmt = $conn->query("SELECT * FROM kategoribuku");
        $data = $stmt->fetchAll();
        foreach($data as $value){
    ?>
    <div class="p-3 rounded-md shadow w-56 flex flex-col gap-2">
        <form action="" method="post" class="flex justify-start">
            <input type="hidden" name="id" value="<?= $value['KategoriID'] ?>">
            <input type="text" name="kategori" value="<?= $value['NamaKategori'] ?>" class="w-full text-center border-b-2" <?= (isset($_GET['id']) && isset($_GET['action']) && $_GET['action'] == "edit") ? "" : "disabled" ?>>
            <?php if(isset($_GET['id']) && isset($_GET['action']) && $_GET['action'] == "edit") { ?>
                <button class="bg-green-400 p-2 rounded-md"><img class="w-5" src="https://www.svgrepo.com/show/521819/save.svg" alt=""></button>
            <?php } ?>
        </form>
        <div class="flex justify-around">
            <a href="?page=kategori&id=<?= $value['KategoriID'] ?>&action=edit" class="rounded-full border-2 p-2"><img class="w-5" src="https://www.svgrepo.com/show/532986/pen-clip.svg" alt=""></a>
            <a href="?page=kategori&id=<?= $value['KategoriID'] ?>&action=remove" class="rounded-full border-2 p-2"><img class="w-5" src="https://www.svgrepo.com/show/502614/delete.svg" alt=""></a>
        </div>
    </div>
    <?php } ?>
</div>