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
function createOTP($length = 6) {
    $char = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $otp = "";
    for ($i=0; $i < $length; $i++) { 
        $otp .= $char[rand(0, strlen($char) - 1)];
    }
    return $otp;
}
if(isset($_GET["otpsend"])) {
    if($_GET["otpsend"] == "true") {
        if(empty($_COOKIE['otp'])) {
            setcookie("otp", createOTP(6), time() + (1000 * 60), "/");
        }
    }
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $pass = $_POST["pass"] ?? "";
  $rpass = $_POST["rpass"] ?? "";
  $user = $_POST["user"] ?? "";
  $otp = $_POST["otp"] ?? "";
  $searchUsers = $conn->prepare("select * from user where username=:user OR email=:email");
  $searchUsers->bindParam(':user', $user);
  $searchUsers->bindParam(':email', $user);
  if ($searchUsers->execute()) {
    if ($searchUsers->rowCount() > 0) {
      $data = $searchUsers->fetch();
      if($otp == $_COOKIE['otp']) {
        if($rpass == $pass) {
            setcookie("otp", "", time() - 3600, "/");
            $query = $conn->prepare("UPDATE user SET Password=?  WHERE UserID=?");
            if($query->execute([password_hash($pass ?? $rpass, PASSWORD_BCRYPT), $data["UserID"]])) {
                $encSesi = encSesi($data["UserID"], $data["Username"], $data["Email"], $data["permission"], $config['key']);
                $_SESSION["loginsesi"] = $encSesi;
                if($data['permission'] == 2) {
                    redirect("staff");
                } else if($data['permission'] == 3) {
                    redirect("user");
                }
            } else {
                $msg = "Gagal mengupdate password";
            }
        } else {
            $msg = "Password tidak cocok";
        }
      } else {
        $msg = "otp tidak ditemukan atau tidak cocok";
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
  <title>Reset Password</title>
  <link rel="shortcut icon" href="https://www.svgrepo.com/show/498972/people.svg" type="image/x-icon">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
        <?php if (isset($_COOKIE['otp'])) { ?>
        <div class="mx-5 bg-lime-100 border border-black text-black px-4 py-3 rounded fixed mb-4" class="top: 0;" role="alert">
          OTP kamu adalah <?= $_COOKIE['otp'] ?>
        </div>
        <span id="otp" class="hidden">true</span>
      <?php } ?>
  <div class="container mx-auto flex justify-center items-center h-screen">
    <form action="" method="post" class="rounded text-center p-6 flex flex-col bg-gray-200 shadow-md" style="width: 400px;">
      <h1 class="text-2xl font-bold mb-5">Reset Password</h1>
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
        <label for="otp" class="sr-only">Otp</label>
        <div class="flex">
            <input type="text" maxlength="6" id="otp" name="otp" required class="flex-initial w-3/5 p-2 border border-gray-300 rounded-md focus:border-blue-500 focus:ring focus:ring-blue-200" placeholder="Otp">
            <a href="?otpsend=true" class="bg-white p-2 border border-gray-300 rounded-md flex-initial w-2/5">Send Otp</a>
        </div>
      </div>
      <div class="mb-4">
        <label for="password" class="sr-only">Password</label>
        <input type="password" id="password" name="pass" required class="block w-full p-2 border border-gray-300 rounded-md focus:border-blue-500 focus:ring focus:ring-blue-200" placeholder="Password">
      </div>
      <div>
        <label for="rpassword" class="sr-only">Retry Password</label>
        <input type="password" id="rpassword" name="rpass" required class="block w-full p-2 border border-gray-300 rounded-md focus:border-blue-500 focus:ring focus:ring-blue-200" placeholder="Password">
      </div>
      <a href="password-change.php" class="underline text-blue-600 my-4">Forgot my Password</a>
      <button type="submit" class="bg-yellow-500 text-white font-bold py-2 rounded-md hover:bg-yellow-600 transition duration-200" style="width: 100%;">Login</button>
      <?php if (isset($_COOKIE['otp'])) { ?>
      <p>Otp dikirim ulang dalam <span id="time">01:<span id="times">00</span></span> </p>
      <?php } ?>
    </form>
  </div>
  <footer style="width: 100vw; height: 10vh" class="flex justify-center items-center bg-slate-500">
        &copy;<span id="th"></span>. All Rights Reserved.
    </footer>
    <script>
        const delay = async (ms) => new Promise((resolve) => setTimeout(resolve, ms))
        document.getElementById("th").innerHTML = new Date().getFullYear()

        const otp = document.getElementById("otp") || false
        if(otp.innerHTML == "true") {
            delay(1000)
            document.getElementById("time").innerHTML = '00:<span id="times">00</span>'
            const s = document.getElementById("times")
            s.innerHTML = 60
            let intervallID = setInterval(() => {
                s.innerHTML = parseInt(s.innerHTML) - 1
                if(s.innerHTML == 0) {
                    clearInterval(intervallID)
                    window.location.href = "?otpsend=true"
                }
            }, 1000);
        }
    </script>
</body>
</html>
