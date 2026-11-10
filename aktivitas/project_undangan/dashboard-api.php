<?php
declare(strict_types=1);

require __DIR__ . '/config.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

function jsonResponse(int $status, array $payload): never
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function jsonBody(): array
{
    $body = json_decode(file_get_contents('php://input'), true);
    return is_array($body) ? $body : [];
}

function requireDashboardLogin(): void
{
    if (empty($_SESSION['dashboard_admin_id'])) {
        jsonResponse(401, ['message' => 'Silakan login kembali.']);
    }
}

function requireDashboardCsrf(array $body): void
{
    $token = $body['csrf'] ?? '';
    if (!is_string($token) || empty($_SESSION['dashboard_csrf'])
        || !hash_equals($_SESSION['dashboard_csrf'], $token)) {
        jsonResponse(419, ['message' => 'Sesi keamanan kedaluwarsa. Muat ulang dashboard.']);
    }
}

function saveGalleryUpload(array $file): string
{
    if ((int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || !is_uploaded_file((string) ($file['tmp_name'] ?? ''))) {
        jsonResponse(422, ['message' => 'Unggahan foto gagal. Pilih foto lalu coba lagi.']);
    }
    if ((int) ($file['size'] ?? 0) < 1 || (int) $file['size'] > 12 * 1024 * 1024) {
        jsonResponse(422, ['message' => 'Ukuran foto maksimal 12 MB.']);
    }

    $allowedTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file((string) $file['tmp_name']);
    if (!isset($allowedTypes[$mime])) {
        jsonResponse(422, ['message' => 'Format foto harus JPG, PNG, atau WebP.']);
    }

    $uploadDirectory = __DIR__ . DIRECTORY_SEPARATOR . 'uploads';
    if (!is_dir($uploadDirectory) && !@mkdir($uploadDirectory, 0755, true) && !is_dir($uploadDirectory)) {
        jsonResponse(500, ['message' => 'Folder unggahan tidak dapat dibuat. Periksa izin folder proyek.']);
    }
    $filename = bin2hex(random_bytes(16)) . '.' . $allowedTypes[$mime];
    if (!move_uploaded_file((string) $file['tmp_name'], $uploadDirectory . DIRECTORY_SEPARATOR . $filename)) {
        jsonResponse(500, ['message' => 'Foto gagal disimpan ke folder uploads.']);
    }
    return 'uploads/' . $filename;
}

$action = $_GET['action'] ?? '';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'state') {
        jsonResponse(200, [
            'authenticated' => !empty($_SESSION['dashboard_admin_id']),
            'csrf' => $_SESSION['dashboard_csrf'] ?? null,
        ]);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'login') {
        $body = jsonBody();
        $now = time();
        $attempts = $_SESSION['dashboard_login_attempts'] ?? ['count' => 0, 'started' => $now];
        if ($now - (int) $attempts['started'] > 900) {
            $attempts = ['count' => 0, 'started' => $now];
        }
        if ((int) $attempts['count'] >= 5) {
            jsonResponse(429, ['message' => 'Terlalu banyak percobaan. Tunggu 15 menit lalu coba lagi.']);
        }

        $username = trim(is_string($body['username'] ?? null) ? $body['username'] : '');
        $password = is_string($body['password'] ?? null) ? $body['password'] : '';
        $stmt = $pdo->prepare('SELECT id, password_hash FROM dashboard_admins WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if (!$admin || !password_verify($password, $admin['password_hash'])) {
            $attempts['count'] = (int) $attempts['count'] + 1;
            $_SESSION['dashboard_login_attempts'] = $attempts;
            jsonResponse(401, ['message' => 'Nama pengguna atau kata sandi salah.']);
        }

        session_regenerate_id(true);
        unset($_SESSION['dashboard_login_attempts']);
        $_SESSION['dashboard_admin_id'] = (int) $admin['id'];
        $_SESSION['dashboard_csrf'] = bin2hex(random_bytes(32));
        jsonResponse(200, ['authenticated' => true, 'csrf' => $_SESSION['dashboard_csrf']]);
    }

    requireDashboardLogin();

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'event') {
        $event = $pdo->query('SELECT id, nama_pria, nama_wanita, tanggal, kota, lokasi, alamat, kutipan,
            waktu_akad_mulai, waktu_akad_selesai, waktu_resepsi_mulai, waktu_resepsi_selesai,
            ayah_pria, ibu_pria, keterangan_pria, ayah_wanita, ibu_wanita, keterangan_wanita,
            foto_sampul, foto_pria, foto_wanita, musik_file
            FROM acara WHERE id = 1')->fetch();
        if (!$event) {
            jsonResponse(404, ['message' => 'Data acara dengan id 1 tidak ditemukan. Import database undangan terlebih dahulu.']);
        }
        jsonResponse(200, ['event' => $event]);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'save') {
        $body = jsonBody();
        requireDashboardCsrf($body);

        $groom = trim(is_string($body['groom'] ?? null) ? $body['groom'] : '');
        $bride = trim(is_string($body['bride'] ?? null) ? $body['bride'] : '');
        $date = trim(is_string($body['date'] ?? null) ? $body['date'] : '');
        $city = trim(is_string($body['city'] ?? null) ? $body['city'] : '');
        $venue = trim(is_string($body['venue'] ?? null) ? $body['venue'] : '');
        $address = trim(is_string($body['address'] ?? null) ? $body['address'] : '');
        $quote = trim(is_string($body['quote'] ?? null) ? $body['quote'] : '');
        $akadStart = trim(is_string($body['akadStart'] ?? null) ? $body['akadStart'] : '');
        $akadEnd = trim(is_string($body['akadEnd'] ?? null) ? $body['akadEnd'] : '');
        $resepsiStart = trim(is_string($body['resepsiStart'] ?? null) ? $body['resepsiStart'] : '');
        $resepsiEnd = trim(is_string($body['resepsiEnd'] ?? null) ? $body['resepsiEnd'] : '');
        $groomFather = trim(is_string($body['groomFather'] ?? null) ? $body['groomFather'] : '');
        $groomMother = trim(is_string($body['groomMother'] ?? null) ? $body['groomMother'] : '');
        $groomBirthOrder = trim(is_string($body['groomBirthOrder'] ?? null) ? $body['groomBirthOrder'] : '');
        $brideFather = trim(is_string($body['brideFather'] ?? null) ? $body['brideFather'] : '');
        $brideMother = trim(is_string($body['brideMother'] ?? null) ? $body['brideMother'] : '');
        $brideBirthOrder = trim(is_string($body['brideBirthOrder'] ?? null) ? $body['brideBirthOrder'] : '');

        $dateObject = DateTimeImmutable::createFromFormat('!Y-m-d', $date);
        $dateIsValid = $dateObject && $dateObject->format('Y-m-d') === $date;
        $validTime = static fn(string $time): bool => preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $time) === 1;
        $coupleName = $groom . ' & ' . $bride;

        if ($groom === '' || $bride === '' || !$dateIsValid || $city === '' || $venue === '' || $address === ''
            || !$validTime($akadStart) || !$validTime($akadEnd) || !$validTime($resepsiStart) || !$validTime($resepsiEnd)
            || strlen($groom) > 100 || strlen($bride) > 100 || strlen($coupleName) > 100
            || strlen($city) > 100 || strlen($venue) > 150 || strlen($address) > 5000 || strlen($quote) > 4000
            || strlen($groomFather) > 150 || strlen($groomMother) > 150 || strlen($brideFather) > 150 || strlen($brideMother) > 150
            || strlen($groomBirthOrder) > 100 || strlen($brideBirthOrder) > 100) {
            jsonResponse(422, ['message' => 'Periksa nama, tanggal, jadwal, lokasi, alamat, dan data keluarga.']);
        }

        $exists = $pdo->query('SELECT id FROM acara WHERE id = 1')->fetchColumn();
        if (!$exists) {
            jsonResponse(404, ['message' => 'Data acara dengan id 1 tidak ditemukan. Import database undangan terlebih dahulu.']);
        }

        $stmt = $pdo->prepare(
            'UPDATE acara SET mempelai = ?, nama_pria = ?, nama_wanita = ?, tanggal = ?, kota = ?, lokasi = ?, alamat = ?, kutipan = ?,
                waktu_akad_mulai = ?, waktu_akad_selesai = ?, waktu_resepsi_mulai = ?, waktu_resepsi_selesai = ?,
                ayah_pria = ?, ibu_pria = ?, keterangan_pria = ?, ayah_wanita = ?, ibu_wanita = ?, keterangan_wanita = ? WHERE id = 1'
        );
        $stmt->execute([
            $coupleName, $groom, $bride, $date, $city, $venue, $address, $quote,
            $akadStart, $akadEnd, $resepsiStart, $resepsiEnd,
            $groomFather, $groomMother, $groomBirthOrder, $brideFather, $brideMother, $brideBirthOrder,
        ]);
        jsonResponse(200, ['saved' => true, 'message' => 'Perubahan berhasil disimpan ke database.']);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'upload-media') {
        requireDashboardCsrf($_POST);
        $kind = is_string($_POST['kind'] ?? null) ? $_POST['kind'] : '';
        $file = $_FILES['file'] ?? null;
        $mediaFields = [
            'cover' => ['column' => 'foto_sampul', 'type' => 'image'],
            'groom' => ['column' => 'foto_pria', 'type' => 'image'],
            'bride' => ['column' => 'foto_wanita', 'type' => 'image'],
            'music' => ['column' => 'musik_file', 'type' => 'audio'],
        ];
        if (!isset($mediaFields[$kind]) || !is_array($file) || !isset($file['error'], $file['tmp_name'], $file['size'])) {
            jsonResponse(422, ['message' => 'Pilih file media yang ingin diunggah.']);
        }
        if ((int) $file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file((string) $file['tmp_name'])) {
            jsonResponse(422, ['message' => 'Unggahan gagal. Periksa ukuran file lalu coba lagi.']);
        }

        $type = $mediaFields[$kind]['type'];
        $maxBytes = $type === 'image' ? 12 * 1024 * 1024 : 25 * 1024 * 1024;
        if ((int) $file['size'] < 1 || (int) $file['size'] > $maxBytes) {
            jsonResponse(422, ['message' => $type === 'image' ? 'Ukuran foto maksimal 12 MB.' : 'Ukuran musik maksimal 25 MB.']);
        }
        $allowedTypes = $type === 'image'
            ? ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp']
            : ['audio/mpeg' => 'mp3', 'audio/mp3' => 'mp3', 'audio/ogg' => 'ogg', 'application/ogg' => 'ogg',
               'audio/wav' => 'wav', 'audio/x-wav' => 'wav', 'audio/wave' => 'wav', 'audio/mp4' => 'm4a', 'audio/x-m4a' => 'm4a'];
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file((string) $file['tmp_name']);
        if (!isset($allowedTypes[$mime])) {
            jsonResponse(422, ['message' => $type === 'image' ? 'Format foto harus JPG, PNG, atau WebP.' : 'Format musik harus MP3, OGG, WAV, atau M4A.']);
        }

        $uploadDirectory = __DIR__ . DIRECTORY_SEPARATOR . 'uploads';
        if (!is_dir($uploadDirectory) && !@mkdir($uploadDirectory, 0755, true) && !is_dir($uploadDirectory)) {
            jsonResponse(500, ['message' => 'Folder unggahan tidak dapat dibuat. Periksa izin folder proyek.']);
        }
        $filename = bin2hex(random_bytes(16)) . '.' . $allowedTypes[$mime];
        $destination = $uploadDirectory . DIRECTORY_SEPARATOR . $filename;
        if (!move_uploaded_file((string) $file['tmp_name'], $destination)) {
            jsonResponse(500, ['message' => 'File gagal disimpan ke folder uploads.']);
        }

        $relativePath = 'uploads/' . $filename;
        $column = $mediaFields[$kind]['column'];
        $stmt = $pdo->prepare("UPDATE acara SET `$column` = ? WHERE id = 1");
        $stmt->execute([$relativePath]);
        if ($stmt->rowCount() === 0) {
            jsonResponse(404, ['message' => 'Data acara belum ditemukan.']);
        }
        jsonResponse(200, ['uploaded' => true, 'path' => $relativePath, 'message' => 'Media berhasil diunggah.']);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'gallery') {
        $photos = $pdo->query('SELECT id, foto_path, alt_text, sort_order FROM gallery_foto ORDER BY sort_order ASC, id ASC')->fetchAll();
        jsonResponse(200, ['photos' => $photos]);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'gallery-add') {
        requireDashboardCsrf($_POST);
        $altText = trim(is_string($_POST['alt_text'] ?? null) ? $_POST['alt_text'] : '');
        if (strlen($altText) > 150) jsonResponse(422, ['message' => 'Teks deskripsi foto maksimal 150 karakter.']);
        $path = saveGalleryUpload($_FILES['file'] ?? []);
        $sortOrder = (int) $pdo->query('SELECT COALESCE(MAX(sort_order), 0) + 1 FROM gallery_foto')->fetchColumn();
        $stmt = $pdo->prepare('INSERT INTO gallery_foto (foto_path, alt_text, sort_order) VALUES (?, ?, ?)');
        $stmt->execute([$path, $altText, $sortOrder]);
        jsonResponse(200, ['added' => true, 'message' => 'Foto berhasil ditambahkan ke galeri.']);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'gallery-update') {
        requireDashboardCsrf($_POST);
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        $altText = trim(is_string($_POST['alt_text'] ?? null) ? $_POST['alt_text'] : '');
        $sortOrder = filter_var($_POST['sort_order'] ?? null, FILTER_VALIDATE_INT);
        if (!$id || strlen($altText) > 150 || $sortOrder === false || $sortOrder < 0 || $sortOrder > 9999) {
            jsonResponse(422, ['message' => 'Periksa teks deskripsi dan urutan foto.']);
        }

        $file = $_FILES['file'] ?? null;
        if (is_array($file) && (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $path = saveGalleryUpload($file);
            $stmt = $pdo->prepare('UPDATE gallery_foto SET foto_path = ?, alt_text = ?, sort_order = ? WHERE id = ?');
            $stmt->execute([$path, $altText, $sortOrder, $id]);
        } else {
            $stmt = $pdo->prepare('UPDATE gallery_foto SET alt_text = ?, sort_order = ? WHERE id = ?');
            $stmt->execute([$altText, $sortOrder, $id]);
        }
        if ($stmt->rowCount() === 0) {
            $check = $pdo->prepare('SELECT id FROM gallery_foto WHERE id = ?');
            $check->execute([$id]);
            if (!$check->fetchColumn()) jsonResponse(404, ['message' => 'Foto galeri tidak ditemukan. Muat ulang halaman.']);
        }
        jsonResponse(200, ['updated' => true, 'message' => 'Perubahan foto galeri berhasil disimpan.']);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'gallery-delete') {
        $body = jsonBody();
        requireDashboardCsrf($body);
        $id = filter_var($body['id'] ?? null, FILTER_VALIDATE_INT);
        if (!$id) jsonResponse(422, ['message' => 'ID foto tidak valid.']);
        $stmt = $pdo->prepare('DELETE FROM gallery_foto WHERE id = ?');
        $stmt->execute([$id]);
        if ($stmt->rowCount() === 0) jsonResponse(404, ['message' => 'Foto galeri tidak ditemukan. Muat ulang halaman.']);
        jsonResponse(200, ['deleted' => true, 'message' => 'Foto berhasil dihapus dari galeri.']);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'rsvp') {
        $rows = $pdo->query(
            'SELECT id, nama, kehadiran, jumlah, ucapan, dibuat_pada FROM rsvp WHERE acara_id = 1 ORDER BY id DESC'
        )->fetchAll();
        $summary = $pdo->query(
            "SELECT COUNT(*) AS total, COALESCE(SUM(kehadiran = 'hadir'), 0) AS hadir,
                    COALESCE(SUM(ucapan <> ''), 0) AS ucapan FROM rsvp WHERE acara_id = 1"
        )->fetch();
        jsonResponse(200, ['rsvp' => $rows, 'summary' => $summary]);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'rsvp-update') {
        $body = jsonBody();
        requireDashboardCsrf($body);
        $id = filter_var($body['id'] ?? null, FILTER_VALIDATE_INT);
        $name = trim(is_string($body['nama'] ?? null) ? $body['nama'] : '');
        $attendance = $body['kehadiran'] ?? '';
        $count = filter_var($body['jumlah'] ?? null, FILTER_VALIDATE_INT);
        $wish = trim(is_string($body['ucapan'] ?? null) ? $body['ucapan'] : '');

        if (!$id || $name === '' || strlen($name) > 100 || !in_array($attendance, ['hadir', 'tidak'], true)
            || strlen($wish) > 500 || ($attendance === 'hadir' && ($count === false || $count < 1 || $count > 5))) {
            jsonResponse(422, ['message' => 'Periksa nama, kehadiran, jumlah (1–5 jika hadir), dan ucapan (maks. 500 karakter).']);
        }

        $count = $attendance === 'hadir' ? $count : 0;
        $stmt = $pdo->prepare('UPDATE rsvp SET nama = ?, kehadiran = ?, jumlah = ?, ucapan = ? WHERE id = ? AND acara_id = 1');
        $stmt->execute([$name, $attendance, $count, $wish, $id]);
        if ($stmt->rowCount() === 0) {
            $check = $pdo->prepare('SELECT id FROM rsvp WHERE id = ? AND acara_id = 1');
            $check->execute([$id]);
            if (!$check->fetchColumn()) jsonResponse(404, ['message' => 'Data RSVP tidak ditemukan. Muat ulang daftar.']);
        }
        jsonResponse(200, ['saved' => true, 'message' => 'Data RSVP berhasil diperbarui.']);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'rsvp-delete') {
        $body = jsonBody();
        requireDashboardCsrf($body);
        $id = filter_var($body['id'] ?? null, FILTER_VALIDATE_INT);
        if (!$id) jsonResponse(422, ['message' => 'ID RSVP tidak valid.']);
        $stmt = $pdo->prepare('DELETE FROM rsvp WHERE id = ? AND acara_id = 1');
        $stmt->execute([$id]);
        if ($stmt->rowCount() === 0) jsonResponse(404, ['message' => 'Data RSVP tidak ditemukan. Muat ulang daftar.']);
        jsonResponse(200, ['deleted' => true, 'message' => 'Data RSVP berhasil dihapus.']);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'logout') {
        $body = jsonBody();
        requireDashboardCsrf($body);
        $_SESSION = [];
        session_regenerate_id(true);
        jsonResponse(200, ['authenticated' => false]);
    }

    jsonResponse(404, ['message' => 'Aksi tidak ditemukan.']);
} catch (PDOException $exception) {
    error_log('Dashboard API database error: ' . $exception->getMessage());
    jsonResponse(500, ['message' => 'Tidak dapat menyimpan atau membaca data. Periksa database dan jalankan setup-dashboard.php dari terminal.']);
}
