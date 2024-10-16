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
function kapital($text) {
  // Daftar kata yang tidak perlu dikapitalisasi (kecuali di awal kalimat)
  $kata_kecil = [
    'dan',
    'atau',
    'tetapi',
    'dengan',
    'serta',
    'ke',
    'di',
    'dari',
    'pada',
    'untuk',
    'oleh',
    'yang',
    'karena',
    'bagi',
    'seperti',
    'dalam',
    'atas',
    'ke',
    'dengan'
  ];

  // Pisahkan kalimat berdasarkan tanda titik
  $kalimat_array = preg_split('/([.?!])\s*/', $text, -1, PREG_SPLIT_DELIM_CAPTURE);

  // Variabel untuk hasil akhir
  $hasil = '';

  // Loop setiap bagian dari teks yang sudah dipisahkan
  for ($i = 0; $i < count($kalimat_array); $i += 2) {
    if (isset($kalimat_array[$i])) {
      $kalimat = trim($kalimat_array[$i]);
      if (!empty($kalimat)) {
        // Pecah kalimat menjadi kata
        $kata_array = explode(' ', $kalimat);

        // Kapitalisasi huruf pertama kata pertama
        $kata_array[0] = ucfirst(strtolower($kata_array[0]));

        // Loop setiap kata mulai dari kata kedua
        for ($j = 1; $j < count($kata_array); $j++) {
          $kata = strtolower($kata_array[$j]);

          // Jika kata ada dalam daftar kata kecil dan bukan di awal kalimat
          if (in_array($kata, $kata_kecil)) {
            $kata_array[$j] = $kata;
          } else {
            // Kapitalisasi kata lainnya
            $kata_array[$j] = ucfirst($kata);
          }
        }

        // Gabungkan kembali kata-kata menjadi kalimat
        $hasil .= implode(' ', $kata_array);
      }
    }

    // Tambahkan kembali tanda baca yang terpisah
    if (isset($kalimat_array[$i + 1])) {
      $hasil .= $kalimat_array[$i + 1] . ' ';
    }
  }

  return trim($hasil);
}