<?php
require __DIR__ . '/config.php';

$acara = $pdo->query('SELECT * FROM acara WHERE id = 1')->fetch();
if (!$acara) {
    http_response_code(404);
    exit('Data acara belum ada. Periksa tabel acara di phpMyAdmin.');
}

$namaPria = trim((string) ($acara['nama_pria'] ?? ''));
$namaWanita = trim((string) ($acara['nama_wanita'] ?? ''));
if ($namaPria === '' && $namaWanita === '') {
    $namaParts = preg_split('/\s+(?:&|dan|and)\s+/iu', trim((string) $acara['mempelai']), 2);
    $namaPria = trim($namaParts[0] ?? '');
    $namaWanita = trim($namaParts[1] ?? '');
}
$pasangan = $namaPria !== '' && $namaWanita !== '' ? "$namaPria & $namaWanita" : $acara['mempelai'];
$kota = trim((string) ($acara['kota'] ?? ''));
$namaTamu = isset($_GET['to']) && is_string($_GET['to']) ? trim(substr($_GET['to'], 0, 150)) : '';
$bulanIndonesia = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$timestampTanggal = strtotime((string) $acara['tanggal']);
$tanggal = date('j', $timestampTanggal) . ' ' . $bulanIndonesia[(int) date('n', $timestampTanggal)] . ' ' . date('Y', $timestampTanggal);
$linkUndangan = 'undangan.php' . ($namaTamu !== '' ? '?to=' . rawurlencode($namaTamu) : '');
$musikFile = trim((string) ($acara['musik_file'] ?? ''));
if (!preg_match('#^uploads/[a-f0-9]{32}\.(?:mp3|ogg|wav|m4a)$#', $musikFile)) $musikFile = '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f5f2eb">
    <title><?= e($pasangan) ?> — Undangan Pernikahan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root { --paper: #f5f2eb; --cream: #fbf9f4; --ink: #30342f; --muted: #777c73; --green: #4f5e53; --line: #d9d8cb; }
        * { box-sizing: border-box; }
        body { min-height: 100vh; min-height: 100svh; margin: 0; padding: clamp(16px, 4vw, 44px); display: grid; place-items: center; color: var(--ink); background: radial-gradient(ellipse at 12% 10%, #e7e9dc 0, transparent 32%), radial-gradient(ellipse at 90% 90%, #ebe7da 0, transparent 30%), var(--paper); font-family: 'Plus Jakarta Sans', sans-serif; }
        .invitation-card { position: relative; isolation: isolate; width: min(760px, 100%); min-height: min(720px, calc(100svh - 40px)); display: grid; place-items: center; padding: clamp(48px, 9vw, 100px) clamp(22px, 8vw, 76px); overflow: hidden; border: 1px solid rgba(79,94,83,.2); background: rgba(251,249,244,.9); box-shadow: 0 24px 70px rgba(40,47,40,.12); text-align: center; }
        .invitation-card[hidden] { display: none; }
        .invitation-card::before { position: absolute; z-index: -1; inset: 14px; border: 1px solid rgba(79,94,83,.16); content: ''; pointer-events: none; }
        .floral-corner { position: absolute; z-index: -1; width: clamp(100px, 20vw, 175px); color: #81907d; opacity: .28; pointer-events: none; }
        .floral-corner svg { display: block; width: 100%; height: auto; }
        .floral-top { top: 24px; right: 24px; }
        .floral-bottom { bottom: 24px; left: 24px; transform: rotate(180deg); }
        .welcome-content { width: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; animation: welcome-arrive .9s cubic-bezier(.2,.7,.2,1) both; }
        @keyframes welcome-arrive { from { opacity: 0; transform: translateY(18px); } to { opacity: 1; transform: translateY(0); } }
        .ornament-top { margin: 0 0 24px; color: var(--green); font: 1.2rem 'Playfair Display', serif; }
        .eyebrow { margin: 0 0 24px; color: var(--green); font-size: .7rem; letter-spacing: 4px; text-transform: uppercase; }
        h1 { display: flex; flex-direction: column; align-items: center; gap: 2px; margin: 0; color: #354239; font: 500 clamp(2.8rem, 9vw, 5.5rem)/1.03 'Playfair Display', serif; }
        .couple-name { max-width: 100%; overflow-wrap: anywhere; }
        .ampersand { margin: 5px 0; color: #8c9a83; font: italic 400 clamp(1.5rem, 4vw, 2.2rem)/1.2 'Playfair Display', serif; }
        .date { margin: 22px 0 24px; color: var(--muted); font-size: .82rem; letter-spacing: 2px; text-transform: uppercase; }
        .ornament { display: flex; align-items: center; gap: 12px; width: min(260px, 75%); margin: 0 auto 28px; color: #899481; font-size: .8rem; }
        .ornament::before, .ornament::after { height: 1px; flex: 1; background: var(--line); content: ''; }
        .recipient { margin: 0 0 8px; color: var(--muted); font-size: .78rem; letter-spacing: 1px; }
        .recipient-name { margin: 0 0 14px; color: var(--ink); font: 500 clamp(1.15rem, 3.5vw, 1.45rem) 'Playfair Display', serif; }
        .welcome-message { max-width: 400px; margin: 0 0 27px; color: #686e66; font-size: .87rem; line-height: 1.85; }
        .open-link { display: inline-flex; align-items: center; justify-content: center; gap: 10px; min-height: 50px; padding: 0 30px; border: 1px solid var(--green); border-radius: 999px; color: #fff; background: var(--green); font-size: .82rem; font-weight: 600; letter-spacing: .4px; text-decoration: none; transition: transform .2s, background .2s, box-shadow .2s; }
        .open-link:hover { transform: translateY(-2px); background: #3d4a40; box-shadow: 0 8px 20px rgba(61,74,64,.2); }
        .music-toggle { position: absolute; top: 30px; left: 30px; z-index: 2; display: inline-flex; align-items: center; gap: 8px; min-height: 38px; padding: 0 13px; border: 1px solid rgba(79,94,83,.28); border-radius: 999px; color: var(--green); background: rgba(251,249,244,.86); font: 500 .72rem 'Plus Jakarta Sans', sans-serif; cursor: pointer; }
        .music-toggle:hover:not(:disabled) { background: #e9ecdf; }
        .music-toggle:disabled { opacity: .55; cursor: not-allowed; }
        body.is-opening .invitation-card { opacity: 0; transform: translateY(10px) scale(.99); filter: blur(2px); transition: opacity .28s ease, transform .28s ease, filter .28s ease; }
        .invite-frame-shell { position: fixed; z-index: 20; inset: 0; width: 100%; height: 100vh; height: 100dvh; background: #eeece5; animation: invitation-frame-arrive .35s ease both; }
        .invite-frame-shell[hidden] { display: none; }
        .invite-frame-shell iframe { display: block; width: 100%; height: 100%; border: 0; background: #eeece5; }
        @keyframes invitation-frame-arrive { from { opacity: 0; } to { opacity: 1; } }
        .footer-note { margin: 25px 0 0; color: #96998f; font-size: .66rem; letter-spacing: .8px; }
        @media (max-width: 600px) {
            body { padding: 12px; }
            .invitation-card { min-height: calc(100svh - 24px); padding: 48px 26px; }
            .invitation-card::before { inset: 9px; }
            .eyebrow { margin-bottom: 20px; letter-spacing: 3px; }
            .date { margin: 18px 0 21px; font-size: .72rem; }
            .welcome-message { font-size: .82rem; }
            .music-toggle { top: 24px; left: 24px; min-height: 34px; padding: 0 10px; font-size: .67rem; }
        }
        @media (prefers-reduced-motion: reduce) { *, *::before, *::after { animation: none !important; scroll-behavior: auto !important; transition: none !important; } }
    </style>
</head>
<body>
    <main class="invitation-card" id="welcomeCard">
        <button class="music-toggle" id="musicToggle" type="button" aria-label="Putar musik" aria-pressed="false" <?= $musikFile === '' ? 'disabled title="Unggah lagu melalui dashboard terlebih dahulu"' : '' ?>>
            <span aria-hidden="true">♫</span><span id="musicLabel"><?= $musikFile === '' ? 'Musik belum tersedia' : 'Putar musik' ?></span>
        </button>
        <div class="floral-corner floral-top" aria-hidden="true">
            <svg viewBox="0 0 160 180" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 168C41 139 51 104 79 75C101 52 122 35 150 13" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
                <path d="M42 132C20 129 11 115 14 97C32 98 44 109 42 132Z" stroke="currentColor" stroke-width="1.1"/>
                <path d="M67 95C62 74 71 61 88 55C94 72 87 88 67 95Z" stroke="currentColor" stroke-width="1.1"/>
                <path d="M101 61C97 43 106 30 123 25C128 42 120 56 101 61Z" stroke="currentColor" stroke-width="1.1"/>
                <path d="M48 126C58 130 65 136 70 145M78 83C94 82 104 87 112 98M112 49C126 49 136 55 141 64" stroke="currentColor" stroke-width="1" stroke-linecap="round"/>
                <circle cx="150" cy="13" r="3" stroke="currentColor" stroke-width="1.1"/>
            </svg>
        </div>
        <div class="floral-corner floral-bottom" aria-hidden="true">
            <svg viewBox="0 0 160 180" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 168C41 139 51 104 79 75C101 52 122 35 150 13" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
                <path d="M42 132C20 129 11 115 14 97C32 98 44 109 42 132Z" stroke="currentColor" stroke-width="1.1"/>
                <path d="M67 95C62 74 71 61 88 55C94 72 87 88 67 95Z" stroke="currentColor" stroke-width="1.1"/>
                <path d="M101 61C97 43 106 30 123 25C128 42 120 56 101 61Z" stroke="currentColor" stroke-width="1.1"/>
                <path d="M48 126C58 130 65 136 70 145M78 83C94 82 104 87 112 98M112 49C126 49 136 55 141 64" stroke="currentColor" stroke-width="1" stroke-linecap="round"/>
                <circle cx="150" cy="13" r="3" stroke="currentColor" stroke-width="1.1"/>
            </svg>
        </div>
        <section class="welcome-content" aria-labelledby="coupleNames">
            <div class="ornament-top" aria-hidden="true">✦</div>
            <p class="eyebrow">Undangan Pernikahan</p>
            <h1 id="coupleNames">
                <?php if ($namaPria !== '' && $namaWanita !== ''): ?>
                    <span class="couple-name"><?= e($namaPria) ?></span>
                    <span class="ampersand">&amp;</span>
                    <span class="couple-name"><?= e($namaWanita) ?></span>
                <?php else: ?>
                    <span class="couple-name"><?= e($pasangan) ?></span>
                <?php endif; ?>
            </h1>
            <p class="date"><?= e($tanggal . ($kota !== '' ? ' · ' . $kota : '')) ?></p>
            <div class="ornament" aria-hidden="true">✧</div>
            <p class="recipient">Kepada Yth.</p>
            <p class="recipient-name"><?= e($namaTamu !== '' ? $namaTamu : 'Bapak/Ibu/Saudara/i') ?></p>
            <p class="welcome-message">Dengan penuh kebahagiaan, kami mengundang Anda untuk hadir dan berbagi doa pada hari istimewa kami.</p>
            <a class="open-link" href="<?= e($linkUndangan) ?>">✉ <span>Buka Undangan</span></a>
            <p class="footer-note">Merupakan kehormatan bagi kami atas kehadiran Anda</p>
        </section>
    </main>
    <div class="invite-frame-shell" id="inviteFrameShell" hidden>
        <iframe id="invitationFrame" title="Halaman utama undangan" src="about:blank"></iframe>
    </div>
    <?php if ($musikFile !== ''): ?>
        <audio id="invitationMusic" src="<?= e($musikFile) ?>" loop preload="none"></audio>
    <?php endif; ?>
    <script>
        const invitationMusic = document.getElementById('invitationMusic');
        const musicToggle = document.getElementById('musicToggle');
        const musicLabel = document.getElementById('musicLabel');
        const musicPreferenceKey = 'projectUndanganMusicOn';
        const welcomeCard = document.getElementById('welcomeCard');
        const inviteFrameShell = document.getElementById('inviteFrameShell');
        const invitationFrame = document.getElementById('invitationFrame');
        const reducedMotionPreference = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        function updateMusicButton() {
            const playing = invitationMusic && !invitationMusic.paused;
            musicToggle.setAttribute('aria-pressed', playing ? 'true' : 'false');
            musicToggle.setAttribute('aria-label', playing ? 'Matikan musik' : 'Putar musik');
            if (playing) musicLabel.textContent = 'Matikan musik';
            else if (musicLabel.textContent !== 'Klik Buka Undangan untuk memulai musik') musicLabel.textContent = 'Putar musik';
        }
        async function toggleWelcomeMusic() {
            if (!invitationMusic) return;
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
            updateMusicButton();
        }
        if (invitationMusic) {
            invitationMusic.addEventListener('play', updateMusicButton);
            invitationMusic.addEventListener('pause', updateMusicButton);
            musicToggle.addEventListener('click', toggleWelcomeMusic);
            invitationMusic.addEventListener('play', () => sessionStorage.setItem(musicPreferenceKey, 'on'));
            invitationMusic.addEventListener('pause', () => sessionStorage.setItem(musicPreferenceKey, 'off'));
            if (sessionStorage.getItem(musicPreferenceKey) !== 'off') {
                invitationMusic.play().catch(() => {
                    musicLabel.textContent = 'Klik Buka Undangan untuk memulai musik';
                });
            }

            invitationMusic.addEventListener('play', () => {
                if (!inviteFrameShell.hidden) invitationFrame.contentWindow?.postMessage({
                    channel: 'project-undangan', action: 'music-state', playing: true
                }, window.location.origin);
            });
            invitationMusic.addEventListener('pause', () => {
                if (!inviteFrameShell.hidden) invitationFrame.contentWindow?.postMessage({
                    channel: 'project-undangan', action: 'music-state', playing: false
                }, window.location.origin);
            });
        }

        window.addEventListener('message', event => {
            if (event.origin !== window.location.origin || event.source !== invitationFrame.contentWindow
                || event.data?.channel !== 'project-undangan') return;
            if (event.data.action === 'request-music-state') {
                invitationFrame.contentWindow.postMessage({
                    channel: 'project-undangan', action: 'music-state',
                    playing: invitationMusic ? !invitationMusic.paused : false
                }, window.location.origin);
            } else if (event.data.action === 'toggle-music') {
                toggleWelcomeMusic();
            } else if (event.data.action === 'show-cover') {
                inviteFrameShell.hidden = true;
                welcomeCard.hidden = false;
                document.body.classList.remove('is-opening');
                welcomeCard.querySelector('.open-link').focus();
            }
        });

        const openLink = document.querySelector('.open-link');
        openLink.addEventListener('click', event => {
            if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
            event.preventDefault();
            if (invitationMusic && sessionStorage.getItem(musicPreferenceKey) !== 'off' && invitationMusic.paused) {
                invitationMusic.play().then(() => sessionStorage.setItem(musicPreferenceKey, 'on')).catch(() => {});
            }
            const showInvitation = () => {
                welcomeCard.hidden = true;
                inviteFrameShell.hidden = false;
                const invitationUrl = new URL(openLink.href);
                invitationUrl.searchParams.set('embed', '1');
                invitationFrame.onload = () => {
                    invitationFrame.contentWindow.postMessage({
                        channel: 'project-undangan', action: 'music-state',
                        playing: invitationMusic ? !invitationMusic.paused : false
                    }, window.location.origin);
                };
                invitationFrame.src = invitationUrl.href;
            };
            if (reducedMotionPreference) {
                showInvitation();
            } else {
                document.body.classList.add('is-opening');
                window.setTimeout(showInvitation, 280);
            }
        });
    </script>
</body>
</html>
