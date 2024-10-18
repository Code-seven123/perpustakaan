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
    try {
        if($_POST['password'] == $_POST['vPasword']) {
            $sql = "INSERT INTO user (Username, Password, Email, NamaLengkap, Alamat) 
                    VALUES (:username, :password, :email, :namaLengkap, :alamat)";
            $stmt = $conn->prepare($sql);
            if($stmt->execute([
                ':username' => $_POST['username'],
                ':password' => password_hash($_POST['password'], PASSWORD_DEFAULT), // Hash password
                ':email' => $_POST['email'],
                ':namaLengkap' => $_POST['namaLengkap'],
                ':alamat' => $_POST['alamat']
            ])) {
                $searchUsers = $conn->prepare("select * from user where username=:user OR email=:email");
                $searchUsers->bindParam(':user', $_POST['username']);
                $searchUsers->bindParam(':email', $_POST['email']);
                if ($searchUsers->execute()) {
                    if ($searchUsers->rowCount() > 0) {
                        $data = $searchUsers->fetch();
                        $encSesi = encSesi($data["UserID"], $data["Username"], $data["Email"], $data["permission"], $config['key']);
                        $_SESSION["loginsesi"] = $encSesi;
                        if($data['permission'] == 1) {
                            redirect("admin");
                        } else if($data['permission'] == 2) {
                            redirect("staff");
                        } else if($data['permission'] == 3) {
                            redirect("user");
                        }
                    } else {
                        $msg = "Akun tidak ditemukan";
                    }
                }
            }
        } else {
            $msg = "Password tidak sama";
        }
    } catch (Exception $e) {
        $msg = "Error registration request, unknown error";
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Form Registrasi</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
  <div class="flex items-center justify-center min-h-screen">
    <form action="" method="POST" class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md">
      <h1 class="text-2xl font-bold text-center mb-6">Form Registrasi</h1>
      <?php if (isset($msg)) { ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
          <?= $msg ?>
        </div>
      <?php } ?>
      <div class="mb-4">
        <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
        <input type="text" id="username" name="username" required class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Masukkan Username">
      </div>

      <div class="mb-4">
        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
        <input type="password" id="password" name="password" required class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Masukkan Password">
      </div>
      <div class="mb-4">
        <label for="vPasword" class="block text-sm font-medium text-gray-700">Verifikasi Pasword</label>
        <input type="password" id="vPasword" name="vPasword" required class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Masukkan Lagi Password">
      </div>

      <div class="mb-4">
        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
        <input type="email" id="email" name="email" required class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Masukkan Email">
      </div>

      <div class="mb-4">
        <label for="namaLengkap" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
        <input type="text" id="namaLengkap" name="namaLengkap" required class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Masukkan Nama Lengkap">
      </div>

      <div class="mb-4">
        <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat</label>
        <textarea id="alamat" name="alamat" required class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" rows="3" placeholder="Masukkan Alamat"></textarea>
      </div>

      <button type="submit" class="w-full bg-blue-600 text-white font-bold py-2 rounded-md hover:bg-blue-700 transition duration-200">Daftar</button>
      
      <p class="mt-4 text-center text-sm text-gray-600">
        Sudah punya akun? <a href="login.php" class="text-blue-600 hover:underline">Login di sini</a>
      </p>
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
