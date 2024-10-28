<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        if($_POST['password'] == $_POST['vPasword']) {
            $sql = "INSERT INTO user (Username, Password, Email, NamaLengkap, Alamat) 
                    VALUES (:username, :password, :email, :namaLengkap, :alamat)";
            $stmt = $conn->prepare($sql);
            if($stmt->execute([
                ':username' => $_POST['username'],
                ':password' => password_hash($_POST['password'], PASSWORD_BCRYPT), // Hash password
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
                        redirect('?page=staff');
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
  </form>
</div>
