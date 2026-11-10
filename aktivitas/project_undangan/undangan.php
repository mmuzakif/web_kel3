<?php
require __DIR__ . '/config.php';

$stmt = $pdo->prepare(
    "SELECT nama, ucapan FROM rsvp
     WHERE acara_id = ? AND ucapan <> ''
     ORDER BY id DESC LIMIT 20"
);
$stmt->execute([1]);
$ucapanList = $stmt->fetchAll();
$galleryList = $pdo->query('SELECT id, foto_path, alt_text FROM gallery_foto ORDER BY sort_order ASC, id ASC')->fetchAll();

$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);
$acara = $pdo->query('SELECT * FROM acara WHERE id = 1')->fetch();
if (!$acara) {
    http_response_code(404);
    exit('Data acara belum ada. Periksa tabel acara di phpMyAdmin.');
}

$fotoSampul = trim((string) ($acara['foto_sampul'] ?? ''));
$fotoPria = trim((string) ($acara['foto_pria'] ?? ''));
$fotoWanita = trim((string) ($acara['foto_wanita'] ?? ''));
$musikFile = trim((string) ($acara['musik_file'] ?? ''));
if (!preg_match('#^uploads/[a-f0-9]{32}\.(?:jpg|png|webp)$#', $fotoSampul)) $fotoSampul = 'bunga_final2.jpeg';
if (!preg_match('#^uploads/[a-f0-9]{32}\.(?:jpg|png|webp)$#', $fotoPria)) $fotoPria = 'bunga_final2_fahrul.jpeg';
if (!preg_match('#^uploads/[a-f0-9]{32}\.(?:jpg|png|webp)$#', $fotoWanita)) $fotoWanita = 'bunga_final2_riva.jpeg';
if (!preg_match('#^uploads/[a-f0-9]{32}\.(?:mp3|ogg|wav|m4a)$#', $musikFile)) $musikFile = '';

$namaPria = trim((string) ($acara['nama_pria'] ?? ''));
$namaWanita = trim((string) ($acara['nama_wanita'] ?? ''));
if ($namaPria === '' && $namaWanita === '') {
    $namaParts = preg_split('/\s+(?:&|dan|and)\s+/iu', trim((string) $acara['mempelai']), 2);
    $namaPria = trim($namaParts[0] ?? '');
    $namaWanita = trim($namaParts[1] ?? '');
}
$pasangan = $namaPria !== '' && $namaWanita !== '' ? "$namaPria & $namaWanita" : $acara['mempelai'];
$namaTamu = isset($_GET['to']) && is_string($_GET['to']) ? trim(substr($_GET['to'], 0, 150)) : '';
$kutipan = trim((string) ($acara['kutipan'] ?? '')) ?: 'Dan di antara tanda-tanda kebesaran-Nya, Dia menciptakan pasangan-pasangan untukmu agar kamu cenderung dan merasa tenteram kepadanya.';
$kota = trim((string) ($acara['kota'] ?? ''));
$mapQuery = urlencode($acara['lokasi'] . ', ' . $acara['alamat']);
$mapEmbedUrl = $acara['map_url'] ?: "https://www.google.com/maps?q=$mapQuery&output=embed";
$mapDirectionsUrl = "https://www.google.com/maps/dir/?api=1&destination=$mapQuery";
$keluargaPria = [];
$keluargaWanita = [];
foreach ([
    'pria' => ['nama' => 'ayah_pria', 'ibu' => 'ibu_pria', 'keterangan' => 'keterangan_pria'],
    'wanita' => ['nama' => 'ayah_wanita', 'ibu' => 'ibu_wanita', 'keterangan' => 'keterangan_wanita'],
] as $gender => $fields) {
    $parents = [];
    $father = trim((string) ($acara[$fields['nama']] ?? ''));
    $mother = trim((string) ($acara[$fields['ibu']] ?? ''));
    if ($father !== '') $parents[] = 'Bapak ' . $father;
    if ($mother !== '') $parents[] = 'Ibu ' . $mother;
    $description = [];
    $birthOrder = trim((string) ($acara[$fields['keterangan']] ?? ''));
    if ($birthOrder !== '') $description[] = $birthOrder;
    if ($parents) $description[] = 'dari ' . implode(' & ', $parents);
    if ($gender === 'pria') $keluargaPria = $description;
    else $keluargaWanita = $description;
}
$deskripsiKeluargaPria = implode(' ', $keluargaPria);
$deskripsiKeluargaWanita = implode(' ', $keluargaWanita);

$bulanIndonesia = [
    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];

$hariIndonesia = [
    'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'
];

$timestampTanggal = strtotime($acara['tanggal']);
$nomorBulan = (int) date('n', $timestampTanggal);

$tanggalLengkap = $hariIndonesia[(int) date('w', $timestampTanggal)]
    . ', ' . date('j', $timestampTanggal)
    . ' ' . $bulanIndonesia[$nomorBulan]
    . ' ' . date('Y', $timestampTanggal);

$tanggalPendek = date('d · m · Y', $timestampTanggal);

$tanggalHighlight = strtoupper(
    date('j', $timestampTanggal)
    . ' ' . $bulanIndonesia[$nomorBulan]
    . ' ' . date('Y', $timestampTanggal)
);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pasangan) ?> - Undangan Pernikahan</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;1,400&amp;family=Plus+Jakarta+Sans:wght@300;400;500;600&amp;display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #f8f6f1; --text-dark: #2c2c2c; --text-light: #6b6b6b;
            --accent-green: #4f5e53; --card-bg: #ffffff; --border-color: #e6e3db;
            --font-serif: 'Playfair Display', serif; --font-sans: 'Plus Jakarta Sans', sans-serif;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }

        html { min-height: 100%; scroll-behavior: smooth; }
        body {
            background: radial-gradient(ellipse at 12% 4%, #e8eadd 0, transparent 30%), #eeece5;
            font-family: var(--font-sans);
            color: var(--text-dark);
            line-height: 1.6;
            min-height: 100vh;
            width: 100%;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: clamp(10px, 2.5vw, 24px) 0;
        }

        .frame-container {
            width: min(100%, 1120px);
            min-height: 667px;
            height: auto;
            background-color: var(--bg-color);
            box-shadow: 0 18px 55px rgba(40,47,40,0.12);
            border: 1px solid rgba(79,94,83,.12);
            border-radius: 16px;
            overflow: hidden;
            position: relative;
            animation: invitation-arrive .7s cubic-bezier(.2,.7,.2,1) both;
        }
        @keyframes invitation-arrive { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: translateY(0); } }

        .app-content { width: 100%; height: 100%; position: relative; }

        .top-nav {
            display: flex; justify-content: space-between; align-items: center;
            padding: clamp(16px, 2.2vw, 24px) clamp(20px, 5vw, 60px);
            border-bottom: 1px solid var(--border-color);
            font-size: 0.7rem; letter-spacing: 1.5px; color: var(--text-light);
            text-transform: uppercase; background: var(--bg-color);
            position: sticky; top: 0; z-index: 100;
        }
        .logo { font-family: var(--font-serif); font-size: 1.4rem; font-weight: 600; color: var(--text-dark); letter-spacing: 2px; }
        .nav-center { text-align: center; flex: 1; }
        .nav-right { display: flex; align-items: center; gap: 15px; }
        .cover-return { display: inline-flex; align-items: center; gap: 5px; padding: 7px 10px; border: 1px solid var(--border-color); border-radius: 999px; color: var(--text-light); background: rgba(255,255,255,.7); font: 500 .7rem var(--font-sans); cursor: pointer; }
        .cover-return[hidden] { display: none; }
        .cover-return:hover { background: #e9ecdf; color: var(--accent-green); }
        .music-control { display: inline-flex; align-items: center; gap: 6px; padding: 8px 11px; border: 1px solid var(--border-color); border-radius: 999px; color: var(--accent-green); background: rgba(255,255,255,.7); font: 500 .72rem var(--font-sans); cursor: pointer; }
        .music-control:hover:not(:disabled) { background: #e9ecdf; }
        .music-control:disabled { opacity: .55; cursor: not-allowed; }
        .admin-link { text-decoration: none; color: var(--text-light); font-size: 1rem; opacity: 0.3; transition: opacity 0.3s; }
        .admin-link:hover { opacity: 1; }

        section {
            position: relative;
            padding: clamp(58px, 7vw, 92px) clamp(22px, 6vw, 74px);
            text-align: center;
            border-bottom: 1px solid var(--border-color);
            display: flex; flex-direction: column; align-items: center;
        }
        section:last-of-type { border-bottom: none; }

        .section-subtitle { font-size: .68rem; letter-spacing: 2.8px; text-transform: uppercase; color: var(--text-light); margin-bottom: 14px; }
        .section-title { font-family: var(--font-serif); font-size: clamp(2rem, 4.4vw, 3.2rem); font-weight: 400; margin-bottom: 18px; line-height: 1.16; }
        .section-title::after { display: block; margin-top: 13px; color: #899481; content: '❧  ✧  ❧'; font: 400 .85rem 'Playfair Display', serif; letter-spacing: 5px; }

        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 10px;
            background-color: var(--accent-green); color: white; padding: 14px 28px;
            border-radius: 40px; text-decoration: none; font-size: 0.8rem; font-weight: 500;
            margin-top: 25px; transition: all 0.3s ease; border: none; cursor: pointer;
            font-family: var(--font-sans); letter-spacing: 0.5px;
        }
        .btn:hover { background-color: #3d4a40; transform: translateY(-2px); }

        .cover-section { padding-top: clamp(44px, 7vw, 76px); }
        .cover-section::before { margin-bottom: 22px; color: #899481; content: '✧  ❧  ✧'; font: 1rem 'Playfair Display', serif; letter-spacing: 5px; }
        .cover-sprig { position: absolute; top: 16%; right: 7%; width: clamp(56px, 9vw, 100px); color: #81907d; opacity: .32; pointer-events: none; }
        .cover-sprig.left { top: auto; right: auto; bottom: 12%; left: 7%; transform: rotate(180deg); }
        .cover-image-wrapper { width: min(68%, 330px); aspect-ratio: 3/4; border: 5px solid rgba(255,255,255,.8); border-radius: 180px 180px 16px 16px; overflow: hidden; margin-bottom: 32px; box-shadow: 0 16px 36px rgba(0,0,0,0.1); }
        .cover-image-wrapper img { width: 100%; height: 100%; object-fit: cover; }
        .cover-title { font-family: var(--font-serif); font-size: clamp(2.5rem, 7vw, 4.8rem); font-weight: 500; margin-bottom: 10px; line-height: 1.08; color: #354239; }
        .cover-date { font-size: .78rem; color: var(--text-light); letter-spacing: 2px; margin-bottom: 26px; text-transform: uppercase; }
        .guest-card { background: rgba(255,255,255,.72); padding: 17px 30px; border-radius: 12px; border: 1px solid var(--border-color); display: inline-block; text-align: center; margin-bottom: 10px; }
        .guest-card .small { font-size: 0.7rem; color: var(--text-light); margin-bottom: 3px; }
        .guest-card .name { font-family: var(--font-serif); font-size: 1.1rem; }

        .quote-text { max-width: 760px; font-family: var(--font-serif); font-size: clamp(1.25rem, 2.7vw, 1.85rem); line-height: 1.7; margin: 22px 0; font-style: italic; color: var(--text-dark); }
        .opening-text { max-width: 680px; margin: 0 auto; color: var(--text-light); font-size: .94rem; line-height: 1.9; }
        .signature { font-family: var(--font-serif); font-size: clamp(1.5rem, 3vw, 2rem); margin-top: 28px; color: #354239; }

        .couple-grid { display: grid; grid-template-columns: 1fr; gap: 40px; max-width: 100%; margin: 30px auto 0; width: 100%; }
        .couple-card { text-align: center; }
        .couple-img { width: 80px; height: 80px; border-radius: 50%; overflow: hidden; margin: 0 auto 16px; border: 3px solid var(--card-bg); box-shadow: 0 6px 16px rgba(0,0,0,0.07); }
        .couple-img img { width: 100%; height: 100%; object-fit: cover; }
        .couple-name { font-family: var(--font-serif); font-size: 1.8rem; margin-bottom: 5px; }
        .couple-fullname { font-size: 0.9rem; font-weight: 600; margin-bottom: 10px; }
        .couple-parents { font-size: 0.8rem; color: var(--text-light); line-height: 1.7; }
        .couple-footer { font-family: var(--font-serif); font-style: italic; margin-top: 40px; font-size: 1.1rem; color: var(--text-dark); }

        .date-highlight { font-family: var(--font-serif); font-size: 1.5rem; color: var(--text-dark); margin-bottom: 30px; letter-spacing: 2px; }
        .event-grid { display: grid; grid-template-columns: 1fr; gap: 20px; max-width: 100%; margin: 0 auto; width: 100%; }
        .event-card { background: rgba(255,255,255,.72); padding: clamp(24px, 4vw, 38px); border-radius: 16px; border: 1px solid var(--border-color); text-align: left; box-shadow: 0 8px 24px rgba(40,47,40,.04); }
        .event-icon { font-size: 1.5rem; margin-bottom: 15px; color: var(--accent-green); }
        .event-title { font-family: var(--font-serif); font-size: 1.5rem; margin-bottom: 10px; }
        .event-time { font-weight: 600; font-size: 0.95rem; margin-bottom: 5px; color: var(--accent-green); }
        .event-location { font-size: 0.85rem; color: var(--text-light); }

        .map-wrapper { width: 100%; max-width: 100%; height: 250px; border-radius: 15px; overflow: hidden; margin: 30px 0; border: 1px solid var(--border-color); }
        .map-wrapper iframe { width: 100%; height: 100%; border: none; display: block; }
        .location-name { font-family: var(--font-serif); font-size: 1.8rem; margin-bottom: 8px; }
        .location-address { color: var(--text-light); font-size: 0.9rem; margin-bottom: 10px; }
        .location-note { font-size: 0.75rem; color: var(--text-light); margin-top: 12px; }

        .gallery-grid { display: grid; grid-template-columns: 1fr; gap: 18px; width: 100%; max-width: 100%; margin: 30px auto 0; overflow: visible; }
        .gallery-item { position: relative; border: 1px solid rgba(79,94,83,.12); border-radius: 14px; overflow: hidden; aspect-ratio: 4/3; background-color: #e8eadd; box-shadow: 0 5px 15px rgba(40,47,40,.06); transition: transform .35s cubic-bezier(.2,.7,.2,1), box-shadow .35s ease; }
        .gallery-item img { display: block; width: 100%; height: 100%; object-fit: cover; transition: transform .55s cubic-bezier(.2,.7,.2,1), filter .35s ease; }
        .gallery-item:hover { z-index: 1; transform: translateY(-5px) scale(1.02); box-shadow: 0 18px 32px rgba(40,47,40,.18); }
        .gallery-item:hover img { transform: scale(1.055); filter: saturate(1.05); }
        .gallery-footer { font-family: var(--font-serif); font-style: italic; margin-top: 30px; font-size: 1.1rem; }

        .timeline { max-width: 100%; margin: 30px auto 0; text-align: left; position: relative; padding-left: 20px; border-left: 2px solid var(--border-color); }
        .timeline-item { margin-bottom: 30px; position: relative; padding-left: 25px; }
        .timeline-item::before { content: ''; position: absolute; left: -27px; top: 5px; width: 12px; height: 12px; border-radius: 50%; background-color: var(--accent-green); border: 3px solid var(--bg-color); box-shadow: 0 0 0 2px var(--accent-green); }
        .timeline-year { font-size: 0.75rem; font-weight: 700; color: var(--accent-green); letter-spacing: 2px; margin-bottom: 3px; }
        .timeline-title { font-family: var(--font-serif); font-size: 1.3rem; margin-bottom: 5px; }
        .timeline-desc { font-size: 0.85rem; color: var(--text-light); line-height: 1.6; }

        .rsvp-container { display: grid; grid-template-columns: 1fr; gap: 30px; max-width: 100%; margin: 30px auto 0; text-align: left; width: 100%; }
        .rsvp-form { background: rgba(255,255,255,.8); padding: clamp(22px, 4vw, 34px); border-radius: 16px; border: 1px solid var(--border-color); box-shadow: 0 8px 24px rgba(40,47,40,.04); }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 0.7rem; margin-bottom: 8px; color: var(--text-light); text-transform: uppercase; letter-spacing: 1.5px; font-weight: 600; }
        .form-group input, .form-group textarea { width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 8px; font-family: var(--font-sans); font-size: 0.9rem; background: #fafafa; }
        .form-group textarea { resize: vertical; min-height: 100px; }
        .radio-group { display: flex; gap: 15px; margin-bottom: 20px; }
        .radio-group label { display: flex; align-items: center; gap: 5px; font-size: 0.8rem; text-transform: none; letter-spacing: 0; cursor: pointer; font-weight: 400; }
        .wishes-list { display: flex; flex-direction: column; gap: 15px; max-height: 400px; overflow-y: auto; padding-right: 10px; }
        .wish-card { background: var(--card-bg); padding: 18px; border-radius: 12px; border: 1px solid var(--border-color); }
        .wish-header { display: flex; align-items: center; gap: 12px; margin-bottom: 10px; }
        .wish-avatar { width: 35px; height: 35px; border-radius: 50%; background-color: #e6e3db; display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--accent-green); font-size: 0.7rem; text-transform: uppercase; }
        .wish-name { font-weight: 600; font-size: 0.85rem; }
        .wish-text { font-size: 0.8rem; color: var(--text-light); line-height: 1.6; }
        .wish-count { font-size: 0.7rem; color: var(--text-light); margin-bottom: 15px; text-align: left; }

        @media (min-width: 760px) {
            .logo { font-size: 1.7rem; }
            .couple-grid { grid-template-columns: 1fr 1fr; gap: 80px; max-width: 900px; }
            .couple-img { width: 105px; height: 105px; }
            .event-grid { grid-template-columns: 1fr 1fr; gap: 40px; max-width: 900px; }
            .map-wrapper { height: 400px; max-width: 900px; }
            .gallery-grid { grid-template-columns: repeat(2, 1fr); gap: 20px; max-width: 1000px; }
            .timeline { max-width: 800px; padding-left: 30px; }
            .timeline-item { padding-left: 40px; }
            .rsvp-container { grid-template-columns: 1fr; gap: 30px; max-width: 1000px; }
        }

        .reveal-section { transition: opacity .7s ease, transform .7s cubic-bezier(.2,.7,.2,1); }
        @media (max-width: 420px) {
            body { padding: 0; }
            .frame-container { border-radius: 0; border-left: 0; border-right: 0; }
            .top-nav { padding: 15px 18px; }
            section { padding: 54px 20px; }
            .cover-image-wrapper { width: min(78%, 260px); }
            .cover-sprig { opacity: .2; }
            .rsvp-form { padding: 20px 16px; }
        }
        body.reveal-ready .reveal-section { opacity: 0; transform: translateY(18px); }
        body.reveal-ready .reveal-section.is-visible { opacity: 1; transform: translateY(0); }
        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            .frame-container, .reveal-section { animation: none; transition: none; opacity: 1 !important; transform: none !important; }
            .gallery-item, .gallery-item img { transition: none; }
            .gallery-item:hover, .gallery-item:hover img { transform: none; }
        }
    </style>
</head>
<body>
<div class="frame-container">
    <div class="app-content">
        <header class="top-nav">
            <div class="logo">L·O</div>
            <div class="nav-center">Undangan</div>
            <div class="nav-right">
                <button class="cover-return" id="coverReturn" type="button" hidden>← Sampul</button>
                <button class="music-control" id="mainMusicToggle" type="button" aria-label="Putar musik" aria-pressed="false" <?= $musikFile === '' ? 'disabled title="Unggah lagu melalui dashboard terlebih dahulu"' : '' ?>>
                    <span aria-hidden="true">♫</span><span id="mainMusicLabel"><?= $musikFile === '' ? 'Musik belum tersedia' : 'Putar musik' ?></span>
                </button>
                <a href="dashboard.html" target="_blank" rel="noopener" class="admin-link" title="Admin Dashboard">⚙</a>
            </div>
        </header>

        <section class="cover-section">
            <svg class="cover-sprig" viewBox="0 0 100 130" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                <path d="M9 121C26 96 42 76 53 54C64 34 77 19 92 9" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
                <path d="M27 96C11 93 5 82 8 69C22 70 31 79 27 96Z" stroke="currentColor" stroke-width="1.1"/>
                <path d="M46 67C41 52 47 42 60 38C64 51 58 62 46 67Z" stroke="currentColor" stroke-width="1.1"/>
                <path d="M66 37C64 24 71 16 83 13C86 25 79 34 66 37Z" stroke="currentColor" stroke-width="1.1"/>
            </svg>
            <svg class="cover-sprig left" viewBox="0 0 100 130" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                <path d="M9 121C26 96 42 76 53 54C64 34 77 19 92 9" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
                <path d="M27 96C11 93 5 82 8 69C22 70 31 79 27 96Z" stroke="currentColor" stroke-width="1.1"/>
                <path d="M46 67C41 52 47 42 60 38C64 51 58 62 46 67Z" stroke="currentColor" stroke-width="1.1"/>
                <path d="M66 37C64 24 71 16 83 13C86 25 79 34 66 37Z" stroke="currentColor" stroke-width="1.1"/>
            </svg>
            <div class="cover-image-wrapper">
                <img src="<?= e($fotoSampul) ?>" alt="Foto sampul undangan">
            </div>
            <div class="section-subtitle">Pernikahan</div>
           <h1 class="cover-title"><?= e($pasangan) ?></h1>
           <div class="cover-date"><?= e($tanggalPendek . ($kota !== '' ? ' — ' . strtoupper($kota) : '')) ?></div>
            <div class="guest-card">
                <div class="small">Kepada Yth.</div>
                <div class="name"><?= e($namaTamu !== '' ? $namaTamu : 'Bapak/Ibu/Saudara/i') ?></div>
            </div>
            <!-- <button class="btn">✉ BUKA UNDANGAN</button> -->
        </section>

        <section>
            <div class="section-subtitle">Assalamualaikum Wr. Wb.</div>
            <h2 class="section-title">Dengan penuh syukur</h2>
            <div class="quote-text">"<?= e($kutipan) ?>"</div>
            <div class="section-subtitle" style="margin-bottom: 40px;">AR-RUM · 21</div>
            <p class="opening-text">Dengan memohon rahmat dan ridho Allah SWT, kami bermaksud mengundang Bapak/Ibu/Saudara/i untuk hadir dan memberikan doa restu pada hari bahagia kami.</p>
          <p class="signature"><?= e($pasangan) ?></p>
        </section>

        <section>
            <div class="section-subtitle">Yang Berbahagia</div>
            <h2 class="section-title">Dua hati, satu tujuan</h2>
            <div class="couple-grid">
                <div class="couple-card">
                    <div class="couple-img"><img src="<?= e($fotoWanita) ?>" alt="<?= e($namaWanita ?: 'Mempelai wanita') ?>"></div>
                    <h3 class="couple-name"><?= e($namaWanita ?: $pasangan) ?></h3>
                    <div class="couple-fullname"><?= e($namaWanita ?: $pasangan) ?></div>
                    <?php if ($deskripsiKeluargaWanita !== ''): ?>
                        <div class="couple-parents"><?= e($deskripsiKeluargaWanita) ?></div>
                    <?php endif; ?>
                </div>
                <div class="couple-card">
                    <div class="couple-img"><img src="<?= e($fotoPria) ?>" alt="<?= e($namaPria ?: 'Mempelai pria') ?>"></div>
                    <h3 class="couple-name"><?= e($namaPria ?: $pasangan) ?></h3>
                    <div class="couple-fullname"><?= e($namaPria ?: $pasangan) ?></div>
                    <?php if ($deskripsiKeluargaPria !== ''): ?>
                        <div class="couple-parents"><?= e($deskripsiKeluargaPria) ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <p class="couple-footer">Dengan cinta, we start our journey together, once and forever.</p>
        </section>

        <section>
            <div class="section-subtitle">Save The Date</div>
            <div class="date-highlight"><?= e($tanggalHighlight) ?></div>
            <div class="section-subtitle"><?= e($tanggalLengkap) ?><?= $kota !== '' ? ' · ' . e($kota) : '' ?></div>
            <div class="event-grid">
                <div class="event-card">
                    <div class="event-icon">⏰</div>
                    <h3 class="event-title">Akad Nikah</h3>
                  <div class="event-time">
                     <?= e(date('H.i', strtotime($acara['waktu_akad_mulai']))) ?> —
                      <?= e(date('H.i', strtotime($acara['waktu_akad_selesai']))) ?> WIB
                  </div>
                    <div class="event-location"><?= e($acara['lokasi']) ?></div>
                </div>
                <div class="event-card">
                    <div class="event-icon">🥂</div>
                    <h3 class="event-title">Resepsi</h3>
                    <div class="event-time">
                       <?= e(date('H.i', strtotime($acara['waktu_resepsi_mulai']))) ?> —
                        <?= e(date('H.i', strtotime($acara['waktu_resepsi_selesai']))) ?> WIB
                    </div>
                    <div class="event-location"><?= e($acara['lokasi']) ?></div>
                </div>
            </div>
            <button class="btn" style="margin-top: 40px;">📅 SIMPAN TANGGAL</button>
        </section>

        <section>
            <div class="section-subtitle">Tempat Kami Menunaikan janji suci</div>
            <h2 class="section-title"><?= e($acara['lokasi']) ?></h2>
            <div class="map-wrapper">
                <iframe src="<?= e($mapEmbedUrl) ?>" width="600" height="450" style="border:0;" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
            <h3 class="location-name"><?= e($acara['lokasi']) ?></h3>
            <p class="location-address"><?= e($acara['alamat']) ?></p>
            <a href="<?= e($mapDirectionsUrl) ?>" target="_blank" rel="noopener noreferrer" class="btn">➔ PETUNJUK ARAH</a>
            <p class="location-note">Parkir tersedia · Akses transportasi umum tersedia</p>
        </section>

        <section>
            <div class="section-subtitle">Gallery</div>
            <h2 class="section-title">Our Memories</h2>
            <div class="gallery-grid">
                <?php foreach ($galleryList as $photo): ?>
                    <div class="gallery-item">
                        <img src="<?= e($photo['foto_path']) ?>" alt="<?= e($photo['alt_text'] ?: 'Foto galeri undangan') ?>" loading="lazy" decoding="async">
                    </div>
                <?php endforeach; ?>
                <?php if (!$galleryList): ?>
                    <p>Galeri foto akan segera hadir.</p>
                <?php endif; ?>
            </div>
            <p class="gallery-footer">Picture speaks a thousand words</p>
        </section>

        <section>
            <div class="section-subtitle">Our Story</div>
            <h2 class="section-title">Our brief story</h2>
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-year">2019</div>
                    <div class="timeline-title">Pertemuan pertama</div>
                    <div class="timeline-desc">Berawal dari secangkir kopi dan percakapan yang terasa terlalu singkat, akward, but there is something special about it.</div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-year">2020-2024</div>
                    <div class="timeline-title">Menumbuhkan cerita</div>
                    <div class="timeline-desc">Kami bertumbuh belajar bersama menghadapi kehidupan dan rintangan, ups and downs namun selalu tetap bersama .</div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-year">2025</div>
                    <div class="timeline-title">Satu keputusan</div>
                    <div class="timeline-desc">Di bawah langit sore dan kkeheningan kota, kami memilih untuk bersama selamanya.</div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-year">2026</div>
                    <div class="timeline-title">Hari yang dinanti</div>
                    <div class="timeline-desc">Kini kami mengundang Anda menjadi bagian dari awal perjalanan baru.</div>
                </div>
            </div>
        </section>

       <section id="rsvp">
    <div class="section-subtitle">Titipkan Doa</div>
    <h2 class="section-title">Ucapan untuk kami</h2>
    <div class="wish-count">UCAPAN TERBARU</div>

    <?php if ($flash): ?>
        <p role="status"><?= e($flash) ?></p>
    <?php endif; ?>

    <div class="rsvp-container">
        <form class="rsvp-form" method="post" action="simpan.php">
            <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">

            <div class="form-group">
                <label for="nama">Nama</label>
                <input id="nama" name="nama" type="text"
                       placeholder="Nama Anda" required maxlength="100">
            </div>

            <div class="form-group">
                <label for="ucapan">Ucapan</label>
                <textarea id="ucapan" name="ucapan"
                          placeholder="Tuliskan doa dan ucapan hangat..."
                          maxlength="500"></textarea>
            </div>

            <div class="form-group">
                <label>Konfirmasi Kehadiran</label>
                <div class="radio-group">
                    <label>
                        <input type="radio" name="kehadiran" value="hadir" checked>
                        Hadir
                    </label>
                    <label>
                        <input type="radio" name="kehadiran" value="tidak">
                        Tidak
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label for="jumlah">Jumlah orang jika hadir (1–5)</label>
                <input id="jumlah" name="jumlah" type="number"
                       min="1" max="5" value="1" required>
            </div>

            <button class="btn" type="submit" style="width: 100%;">
                ➔ KIRIM UCAPAN
            </button>
        </form>

        <div class="wishes-list">
            <?php foreach ($ucapanList as $item): ?>
                <div class="wish-card">
                    <div class="wish-header">
                        <div class="wish-avatar">
                            <?= e(strtoupper(substr($item['nama'], 0, 2))) ?>
                        </div>
                        <div class="wish-name"><?= e($item['nama']) ?></div>
                    </div>
                    <div class="wish-text"><?= nl2br(e($item['ucapan'])) ?></div>
                </div>
            <?php endforeach; ?>

            <?php if (!$ucapanList): ?>
                <p>Belum ada ucapan. Jadilah yang pertama!</p>
            <?php endif; ?>
        </div>
    </div>
</section>
    </div>
</div>
<?php if ($musikFile !== ''): ?>
    <audio id="invitationMusic" src="<?= e($musikFile) ?>" loop preload="none"></audio>
<?php endif; ?>
<script>
    const invitationMusic = document.getElementById('invitationMusic');
    const musicToggle = document.getElementById('mainMusicToggle');
    const musicLabel = document.getElementById('mainMusicLabel');
    const coverReturn = document.getElementById('coverReturn');
    const musicPreferenceKey = 'projectUndanganMusicOn';
    const embeddedInWelcome = window.parent !== window;
    function updateMainMusicButton() {
        const playing = invitationMusic && !invitationMusic.paused;
        musicToggle.setAttribute('aria-pressed', playing ? 'true' : 'false');
        musicToggle.setAttribute('aria-label', playing ? 'Matikan musik' : 'Putar musik');
        musicLabel.textContent = playing ? 'Matikan musik' : 'Putar musik';
    }
    if (embeddedInWelcome) {
        coverReturn.hidden = false;
        coverReturn.addEventListener('click', () => {
            window.parent.postMessage({ channel: 'project-undangan', action: 'show-cover' }, window.location.origin);
        });
        musicToggle.addEventListener('click', () => {
            window.parent.postMessage({ channel: 'project-undangan', action: 'toggle-music' }, window.location.origin);
        });
        window.addEventListener('message', event => {
            if (event.origin !== window.location.origin || event.source !== window.parent
                || event.data?.channel !== 'project-undangan' || event.data.action !== 'music-state') return;
            musicToggle.setAttribute('aria-pressed', event.data.playing ? 'true' : 'false');
            musicToggle.setAttribute('aria-label', event.data.playing ? 'Matikan musik' : 'Putar musik');
            musicLabel.textContent = event.data.playing ? 'Matikan musik' : 'Putar musik';
        });
        window.parent.postMessage({ channel: 'project-undangan', action: 'request-music-state' }, window.location.origin);
    } else if (invitationMusic) {
        invitationMusic.addEventListener('play', updateMainMusicButton);
        invitationMusic.addEventListener('pause', updateMainMusicButton);
        musicToggle.addEventListener('click', async () => {
            if (invitationMusic.paused) {
                try {
                    await invitationMusic.play();
                    sessionStorage.setItem(musicPreferenceKey, 'on');
                } catch (error) {
                    sessionStorage.removeItem(musicPreferenceKey);
                    musicLabel.textContent = 'Tekan untuk memutar';
                }
            } else {
                invitationMusic.pause();
                sessionStorage.setItem(musicPreferenceKey, 'off');
            }
            updateMainMusicButton();
        });
        if (sessionStorage.getItem(musicPreferenceKey) !== 'off') {
            invitationMusic.play().catch(() => { musicLabel.textContent = 'Tekan untuk memutar'; });
        }
    }

    const inviteSections = document.querySelectorAll('.app-content > section');
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (reducedMotion || !('IntersectionObserver' in window)) {
        inviteSections.forEach(section => section.classList.add('reveal-section', 'is-visible'));
    } else {
        document.body.classList.add('reveal-ready');
        const revealObserver = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    revealObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.08, rootMargin: '0px 0px -24px 0px' });
        inviteSections.forEach(section => {
            section.classList.add('reveal-section');
            revealObserver.observe(section);
        });
    }
</script>
</body>
</html>
