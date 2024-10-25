<?php
session_start();
require "connection.php";
require "utils.php";
# $sesi = encSesi(2, "manu", "manu@n.c", "1", $config['key']);
$sesi = verifikasiSession($_SESSION["loginsesi"] ?? false, $config['key']);
if ($sesi['status'] ?? $sesi == true ) {
    if($sesi['role'] == 2) {
        redirect("staff");
    } else if($sesi['role'] == 3) {
        redirect("user");
    }
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $pass = $_POST["pass"];
  $user = $_POST["user"];
  $searchUsers = $conn->prepare("select * from user where username=:user OR email=:email");
  $searchUsers->bindParam(':user', $user);
  $searchUsers->bindParam(':email', $user);
  if ($searchUsers->execute()) {
    if ($searchUsers->rowCount() > 0) {
      $data = $searchUsers->fetch();
      if (password_verify($pass, $data["Password"])) {
        $encSesi = encSesi($data["UserID"], $data["Username"], $data["Email"], $data["permission"], $config['key']);
        $_SESSION["loginsesi"] = $encSesi;
        if($data['permission'] == 2) {
            redirect("staff");
        } else if($data['permission'] == 3) {
            redirect("user");
        }
      } else {
        $msg = "Password salah!!";
      }
    } else {
      $msg = "Akun tidak ditemukan";
    }
  }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Registrasi</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
  <div class="container mx-auto flex justify-center items-center h-screen">
    <form action="" method="post" class="rounded text-center p-6 flex flex-col bg-gray-200 shadow-md" style="width: 400px;">
      <h1 class="text-2xl font-bold mb-5">Sign Up</h1>
      <?php if (isset($msg)) { ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
          <?= $msg ?>
        </div>
      <?php } ?>
      <div class="mb-4">
        <label for="username" class="sr-only">Username / Email</label>
        <input type="text" id="username" name="user" required class="block w-full p-2 border border-gray-300 rounded-md focus:border-blue-500 focus:ring focus:ring-blue-200" placeholder="Username atau Email">
      </div>
      <div class="mb-4">
        <label for="password" class="sr-only">Password</label>
        <input type="password" id="password" name="pass" required class="block w-full p-2 border border-gray-300 rounded-md focus:border-blue-500 focus:ring focus:ring-blue-200" placeholder="Password">
      </div>
      <button type="submit" class="bg-yellow-500 text-white font-bold py-2 rounded-md hover:bg-yellow-600 transition duration-200" style="width: 100%;">Login</button>
    </form>
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
    </script>
</body>
</html>
