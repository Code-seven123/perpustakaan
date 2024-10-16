<?php
function redirect($url, $delay = 0) {
  if (!headers_sent()) {
    if ($delay > 0) {
      header("Refresh: $delay; url=$url");
    } else {
      header("Location: $url");
    }
    exit();
  } else {
    echo '<script type="text/javascript">';
    if ($delay > 0) {
      echo "setTimeout(function(){ window.location.href = '$url'; }, " . ($delay * 1000) . ");"; // Konversi delay ke milidetik
    } else {
      echo "window.location.href = '$url';";
    }
    echo '</script>';

    echo "<noscript>";
    echo "<meta http-equiv='refresh' content='$delay;url=$url' />";
    echo "</noscript>";
    exit();
  }
}
function verifikasiSession($sesi, $key) {
    if($sesi == false) return false;
    try {
        $decBase64 = base64_decode($sesi);
        $method = "AES-256-CBC";
        $ivLength = openssl_cipher_iv_length($method);
        $iv = substr($decBase64, 0, $ivLength);
        $encText = substr($decBase64, $ivLength);
        $dec = openssl_decrypt($encText, $method, $key, 0, $iv);
        $array = json_decode($dec, true);
        return $array;
    } catch (Exception $e) {
        return false;
    }
}
function encSesi($userID, $user, $email, $permi, $key) {
    $data = [
        "id" => $userID,
        "name" => $user,
        "status" => true,
        "email" => $email,
        "role" => $permi
    ];
    $json = json_encode($data);
    $method = "AES-256-CBC";
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($method));
    $enc = openssl_encrypt($json, $method, $key, 0, $iv);
    return base64_encode($iv . $enc);
}