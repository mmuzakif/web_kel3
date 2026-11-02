<?php
require __DIR__ . '/config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); header('Allow: POST'); exit('Gunakan formulir RSVP.');
}
$token = $_POST['csrf'] ?? '';
if (!is_string($token) || !hash_equals($_SESSION['csrf'], $token)) {
    http_response_code(403); exit('Formulir kedaluwarsa. Muat ulang halaman undangan.');
}
function field(string $key): string {
    $v = $_POST[$key] ?? '';
    return is_string($v) ? trim($v) : '';
}
$nama = field('nama');
$kehadiran = field('kehadiran');
$ucapan = field('ucapan');
$jumlah = filter_var(field('jumlah'), FILTER_VALIDATE_INT);
// Batas byte bersifat konservatif dan tidak membutuhkan ekstensi mbstring.
if ($nama === '' || strlen($nama) > 100 || strlen($ucapan) > 500
    || !in_array($kehadiran, ['hadir', 'tidak'], true)
    || ($kehadiran === 'hadir' && ($jumlah === false || $jumlah < 1 || $jumlah > 5))) {
    $_SESSION['flash'] = 'Periksa nama (maks. 100 byte), ucapan (maks. 500 byte), dan jumlah 1–5 jika hadir.';
    header('Location: index.php#rsvp', true, 303); exit;
}
$jumlah = $kehadiran === 'hadir' ? $jumlah : 0;
try {
    $stmt = $pdo->prepare('INSERT INTO rsvp (acara_id,nama,kehadiran,jumlah,ucapan) VALUES (?,?,?,?,?)');
    $stmt->execute([1, $nama, $kehadiran, $jumlah, $ucapan]);
    $_SESSION['flash'] = 'Terima kasih! RSVP berhasil disimpan.';
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
} catch (PDOException $e) {
    error_log($e->getMessage());
    $_SESSION['flash'] = 'RSVP belum tersimpan. Coba lagi setelah koneksi diperiksa.';
}
header('Location: undangan.php#rsvp', true, 303);
exit;
