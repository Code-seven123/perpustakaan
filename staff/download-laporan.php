<?php
// URL dari halaman yang ingin diambil datanya
if(isset($_GET['page'])) {
    $currentUrl = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    if($_GET['page'] == "ulasan") {
        $url = dirname($currentUrl) . "./laporan.php?page=ulasan";
    } else if($_GET['page'] == "peminjaman") {
        $url = dirname($currentUrl) . "./laporan.php?page=peminjaman";
    } else if($_GET['page'] == "buku") {
        $url = dirname($currentUrl) . "./laporan.php?page=buku";
    } else {
        echo "<script>history.back()</script>";
    }
}
$html = file_get_contents($url);
if ($html === FALSE) {
    die("Error fetching the HTML content.");
}

// Mem-parsing HTML
$dom = new DOMDocument();
libxml_use_internal_errors(true); // Menghindari peringatan parsing
$dom->loadHTML($html);
libxml_clear_errors();

// Menyiapkan untuk mengekstrak data (misalnya dari tabel)
$data = [];
$rows = $dom->getElementsByTagName('tr'); // Mengambil semua baris tabel
foreach ($rows as $row) {
    $cols = $row->getElementsByTagName('td'); // Mengambil semua kolom
    if ($cols->length > 0) {
        $rowData = [];
        foreach ($cols as $col) {
            $rowData[] = trim($col->textContent); // Mengambil teks dari kolom
        }
        $data[] = $rowData; // Menyimpan data ke dalam array
    }
}

// Menentukan nama file CSV
$action = $_GET['page'] ?? "";
$filename = "laporan-$action.csv";

// Mengatur header untuk mengunduh file CSV
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Membuka output buffer untuk menulis CSV
$output = fopen('php://output', 'w');

// Menulis data ke dalam file CSV
foreach ($data as $row) {
    fputcsv($output, $row);
}

// Menutup output buffer
fclose($output);
exit();
?>
