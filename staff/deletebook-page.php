<?php
if (isset($_GET['id'])) {
    $stmt = $conn->prepare("DELETE FROM buku WHERE BukuID = ?");
    $stmt->execute([$_GET['id']]);
}
redirect("./");
?>