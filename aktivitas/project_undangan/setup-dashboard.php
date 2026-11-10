<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require __DIR__ . '/config.php';

$columns = [
    'nama_pria' => "VARCHAR(100) NOT NULL DEFAULT '' AFTER `mempelai`",
    'nama_wanita' => "VARCHAR(100) NOT NULL DEFAULT '' AFTER `nama_pria`",
    'kota' => "VARCHAR(100) NOT NULL DEFAULT '' AFTER `tanggal`",
    'kutipan' => 'TEXT NULL AFTER `alamat`',
    'ayah_pria' => "VARCHAR(150) NOT NULL DEFAULT ''",
    'ibu_pria' => "VARCHAR(150) NOT NULL DEFAULT ''",
    'keterangan_pria' => "VARCHAR(100) NOT NULL DEFAULT ''",
    'ayah_wanita' => "VARCHAR(150) NOT NULL DEFAULT ''",
    'ibu_wanita' => "VARCHAR(150) NOT NULL DEFAULT ''",
    'keterangan_wanita' => "VARCHAR(100) NOT NULL DEFAULT ''",
    'foto_sampul' => "VARCHAR(255) NOT NULL DEFAULT ''",
    'foto_pria' => "VARCHAR(255) NOT NULL DEFAULT ''",
    'foto_wanita' => "VARCHAR(255) NOT NULL DEFAULT ''",
    'musik_file' => "VARCHAR(255) NOT NULL DEFAULT ''",
];

$familyColumnsAdded = false;
foreach ($columns as $column => $definition) {
    $check = $pdo->prepare(
        'SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?'
    );
    $check->execute(['acara', $column]);
    if ((int) $check->fetchColumn() === 0) {
        $pdo->exec("ALTER TABLE `acara` ADD COLUMN `$column` $definition");
        echo "Added acara.$column\n";
        if (in_array($column, ['ayah_pria', 'ibu_pria', 'keterangan_pria', 'ayah_wanita', 'ibu_wanita', 'keterangan_wanita'], true)) {
            $familyColumnsAdded = true;
        }
    }
}

if ($familyColumnsAdded) {
    $pdo->exec("UPDATE acara SET
        ayah_pria = IF(ayah_pria = '', 'Bimo Adinata', ayah_pria),
        ibu_pria = IF(ibu_pria = '', 'Larasati', ibu_pria),
        keterangan_pria = IF(keterangan_pria = '', 'Putra kedua', keterangan_pria),
        ayah_wanita = IF(ayah_wanita = '', 'Hadi Santoso', ayah_wanita),
        ibu_wanita = IF(ibu_wanita = '', 'Ratna Wulandari', ibu_wanita),
        keterangan_wanita = IF(keterangan_wanita = '', 'Putri pertama', keterangan_wanita)
        WHERE id = 1");
}

$pdo->exec(
    "CREATE TABLE IF NOT EXISTS `dashboard_admins` (
        `id` int unsigned NOT NULL AUTO_INCREMENT,
        `username` varchar(100) NOT NULL,
        `password_hash` varchar(255) NOT NULL,
        `dibuat_pada` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `dashboard_admins_username_unique` (`username`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
);

$galleryExistsCheck = $pdo->prepare(
    'SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?'
);
$galleryExistsCheck->execute(['gallery_foto']);
$galleryTableExisted = (int) $galleryExistsCheck->fetchColumn() > 0;

$pdo->exec(
    "CREATE TABLE IF NOT EXISTS `gallery_foto` (
        `id` int unsigned NOT NULL AUTO_INCREMENT,
        `foto_path` varchar(255) NOT NULL,
        `alt_text` varchar(150) NOT NULL DEFAULT '',
        `sort_order` int unsigned NOT NULL DEFAULT 0,
        `dibuat_pada` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `gallery_foto_path_unique` (`foto_path`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
);

if (!$galleryTableExisted) {
    $seedGallery = [
        ['nokia.jpeg', 'Momen bahagia bersama'],
        ['shadow.jpeg', 'Kenangan indah'],
        ['sit.jpeg', 'Kebersamaan kami'],
        ['throwing_flower.jpeg', 'Momen lempar bunga'],
    ];
    $insertPhoto = $pdo->prepare('INSERT INTO gallery_foto (foto_path, alt_text, sort_order) VALUES (?, ?, ?)');
    foreach ($seedGallery as $position => [$path, $altText]) {
        if (is_file(__DIR__ . DIRECTORY_SEPARATOR . $path)) {
            $insertPhoto->execute([$path, $altText, $position + 1]);
        }
    }
}

$event = $pdo->query('SELECT id, mempelai, nama_pria, nama_wanita FROM acara WHERE id = 1')->fetch();
if (!$event) {
    fwrite(STDERR, "No acara row with id 1. Import db_undangan.sql first, then run this setup again.\n");
    exit(1);
}

if ($event['nama_pria'] === '' && $event['nama_wanita'] === '') {
    $parts = preg_split('/\s+(?:&|dan|and)\s+/iu', trim($event['mempelai']), 2);
    if (count($parts) === 2) {
        $update = $pdo->prepare('UPDATE acara SET nama_pria = ?, nama_wanita = ? WHERE id = 1');
        $update->execute([trim($parts[0]), trim($parts[1])]);
    }
}

if (in_array('--migrate-only', $argv, true)) {
    echo "Dashboard fields and admin table are ready. Run php setup-dashboard.php to create the admin account.\n";
    exit(0);
}

$username = trim((string) readline('Admin username: '));
$password = (string) readline('Admin password (input is visible): ');
$confirm = (string) readline('Confirm password (input is visible): ');

if ($username === '' || $password === '' || $password !== $confirm || strlen($username) > 100 || strlen($password) < 12) {
    fwrite(STDERR, "Username is required; passwords must match and be at least 12 characters. Run setup again.\n");
    exit(1);
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$save = $pdo->prepare(
    'INSERT INTO dashboard_admins (username, password_hash) VALUES (?, ?) ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash)'
);
$save->execute([$username, $hash]);

echo "Dashboard database setup complete. Store the password safely; it is not saved in the project files.\n";
