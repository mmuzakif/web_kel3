<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ e($acara['mempelai']) }} - Undangan Pernikahan</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;1,400&amp;family=Plus+Jakarta+Sans:wght@300;400;500;600&amp;display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #f8f6f1; --text-dark: #2c2c2c; --text-light: #6b6b6b;
            --accent-green: #4f5e53; --card-bg: #ffffff; --border-color: #e6e3db;
            --font-serif: 'Playfair Display', serif; --font-sans: 'Plus Jakarta Sans', sans-serif;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }

        html, body {
            background-color: #e8e5de;
            font-family: var(--font-sans);
            color: var(--text-dark);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 20px 0;
        }

        .frame-container {
            width: 320px;
            min-height: 667px;
            height: auto;
            background-color: var(--bg-color);
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            border-radius: 12px;
            overflow: hidden;
            position: relative;
        }

        @media (min-width: 1024px) {
            .frame-container { width: 1024px; }
        }
        @media (min-width: 2560px) {
            .frame-container { width: 2560px; }
        }

        .app-content { width: 100%; height: 100%; position: relative; }

        .top-nav {
            display: flex; justify-content: space-between; align-items: center;
            padding: 20px 25px;
            border-bottom: 1px solid var(--border-color);
            font-size: 0.7rem; letter-spacing: 1.5px; color: var(--text-light);
            text-transform: uppercase; background: var(--bg-color);
            position: sticky; top: 0; z-index: 100;
        }
        .logo { font-family: var(--font-serif); font-size: 1.4rem; font-weight: 600; color: var(--text-dark); letter-spacing: 2px; }
        .nav-center { text-align: center; flex: 1; }
        .nav-right { display: flex; align-items: center; gap: 15px; }
        .admin-link { text-decoration: none; color: var(--text-light); font-size: 1rem; opacity: 0.3; transition: opacity 0.3s; }
        .admin-link:hover { opacity: 1; }

        section {
            padding: 60px 20px;
            text-align: center;
            border-bottom: 1px solid var(--border-color);
            display: flex; flex-direction: column; align-items: center;
        }
        section:last-of-type { border-bottom: none; }

        .section-subtitle { font-size: 0.65rem; letter-spacing: 2.5px; text-transform: uppercase; color: var(--text-light); margin-bottom: 15px; }
        .section-title { font-family: var(--font-serif); font-size: 2rem; font-weight: 400; margin-bottom: 15px; line-height: 1.2; }

        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 10px;
            background-color: var(--accent-green); color: white; padding: 14px 28px;
            border-radius: 40px; text-decoration: none; font-size: 0.8rem; font-weight: 500;
            margin-top: 25px; transition: all 0.3s ease; border: none; cursor: pointer;
            font-family: var(--font-sans); letter-spacing: 0.5px;
        }
        .btn:hover { background-color: #3d4a40; transform: translateY(-2px); }

        .cover-section { padding-top: 30px; }
        .cover-image-wrapper { width: 100%; max-width: 260px; aspect-ratio: 3/4; border-radius: 130px 130px 15px 15px; overflow: hidden; margin-bottom: 30px; box-shadow: 0 15px 30px rgba(0,0,0,0.08); }
        .cover-image-wrapper img { width: 100%; height: 100%; object-fit: cover; }
        .cover-title { font-family: var(--font-serif); font-size: 2.8rem; margin-bottom: 8px; line-height: 1.1; }
        .cover-date { font-size: 0.85rem; color: var(--text-light); letter-spacing: 2px; margin-bottom: 30px; }
        .guest-card { background: var(--card-bg); padding: 18px 30px; border-radius: 12px; border: 1px solid var(--border-color); display: inline-block; text-align: center; margin-bottom: 10px; }
        .guest-card .small { font-size: 0.7rem; color: var(--text-light); margin-bottom: 3px; }
        .guest-card .name { font-family: var(--font-serif); font-size: 1.1rem; }

        .quote-text { font-family: var(--font-serif); font-size: 1.3rem; line-height: 1.5; margin: 30px 0; font-style: italic; color: var(--text-dark); }
        .opening-text { max-width: 100%; margin: 0 auto; color: var(--text-light); font-size: 0.95rem; }
        .signature { font-family: var(--font-serif); font-size: 1.6rem; margin-top: 30px; }

        .couple-grid { display: grid; grid-template-columns: 1fr; gap: 40px; max-width: 100%; margin: 30px auto 0; width: 100%; }
        .couple-card { text-align: center; }
        .couple-img { width: 160px; height: 160px; border-radius: 50%; overflow: hidden; margin: 0 auto 20px; border: 5px solid var(--card-bg); box-shadow: 0 8px 20px rgba(0,0,0,0.06); }
        .couple-img img { width: 100%; height: 100%; object-fit: cover; }
        .couple-name { font-family: var(--font-serif); font-size: 1.8rem; margin-bottom: 5px; }
        .couple-fullname { font-size: 0.9rem; font-weight: 600; margin-bottom: 10px; }
        .couple-parents { font-size: 0.8rem; color: var(--text-light); line-height: 1.7; }
        .couple-footer { font-family: var(--font-serif); font-style: italic; margin-top: 40px; font-size: 1.1rem; color: var(--text-dark); }

        .date-highlight { font-family: var(--font-serif); font-size: 1.5rem; color: var(--text-dark); margin-bottom: 30px; letter-spacing: 2px; }
        .event-grid { display: grid; grid-template-columns: 1fr; gap: 20px; max-width: 100%; margin: 0 auto; width: 100%; }
        .event-card { background: var(--card-bg); padding: 25px; border-radius: 15px; border: 1px solid var(--border-color); text-align: left; }
        .event-icon { font-size: 1.5rem; margin-bottom: 15px; color: var(--accent-green); }
        .event-title { font-family: var(--font-serif); font-size: 1.5rem; margin-bottom: 10px; }
        .event-time { font-weight: 600; font-size: 0.95rem; margin-bottom: 5px; color: var(--accent-green); }
        .event-location { font-size: 0.85rem; color: var(--text-light); }

        .map-wrapper { width: 100%; max-width: 100%; height: 250px; border-radius: 15px; overflow: hidden; margin: 30px 0; border: 1px solid var(--border-color); }
        .map-wrapper iframe { width: 100%; height: 100%; border: none; display: block; }
        .location-name { font-family: var(--font-serif); font-size: 1.8rem; margin-bottom: 8px; }
        .location-address { color: var(--text-light); font-size: 0.9rem; margin-bottom: 10px; }
        .location-note { font-size: 0.75rem; color: var(--text-light); margin-top: 12px; }

        .gallery-grid { display: grid; grid-template-columns: 1fr; gap: 15px; width: 100%; max-width: 100%; margin: 30px auto 0; }
        .gallery-item { border-radius: 12px; overflow: hidden; aspect-ratio: 4/3; background-color: #ddd; }
        .gallery-item img { width: 100%; height: 100%; object-fit: cover; }
        .gallery-footer { font-family: var(--font-serif); font-style: italic; margin-top: 30px; font-size: 1.1rem; }

        .timeline { max-width: 100%; margin: 30px auto 0; text-align: left; position: relative; padding-left: 20px; border-left: 2px solid var(--border-color); }
        .timeline-item { margin-bottom: 30px; position: relative; padding-left: 25px; }
        .timeline-item::before { content: ''; position: absolute; left: -27px; top: 5px; width: 12px; height: 12px; border-radius: 50%; background-color: var(--accent-green); border: 3px solid var(--bg-color); box-shadow: 0 0 0 2px var(--accent-green); }
        .timeline-year { font-size: 0.75rem; font-weight: 700; color: var(--accent-green); letter-spacing: 2px; margin-bottom: 3px; }
        .timeline-title { font-family: var(--font-serif); font-size: 1.3rem; margin-bottom: 5px; }
        .timeline-desc { font-size: 0.85rem; color: var(--text-light); line-height: 1.6; }

        .rsvp-container { display: grid; grid-template-columns: 1fr; gap: 30px; max-width: 100%; margin: 30px auto 0; text-align: left; width: 100%; }
        .rsvp-form { background: var(--card-bg); padding: 25px; border-radius: 15px; border: 1px solid var(--border-color); }
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

        @media (min-width: 1024px) {
            .top-nav { padding: 24px 60px; font-size: 0.8rem; }
            .logo { font-size: 1.8rem; }
            section { padding: 80px 60px; }
            .section-title { font-size: 3rem; }
            .cover-image-wrapper { max-width: 500px; border-radius: 250px 250px 20px 20px; }
            .cover-title { font-size: 4.5rem; }
            .guest-card { padding: 24px 60px; }
            .quote-text { font-size: 1.8rem; }
            .couple-grid { grid-template-columns: 1fr 1fr; gap: 80px; max-width: 900px; }
            .couple-img { width: 220px; height: 220px; }
            .couple-name { font-size: 2.2rem; }
            .event-grid { grid-template-columns: 1fr 1fr; gap: 40px; max-width: 900px; }
            .map-wrapper { height: 400px; max-width: 900px; }
            .gallery-grid { grid-template-columns: repeat(2, 1fr); gap: 20px; max-width: 1000px; }
            .timeline { max-width: 800px; padding-left: 30px; }
            .timeline-item { padding-left: 40px; }
            .rsvp-container { grid-template-columns: 1fr; gap: 30px; max-width: 1000px; }
        }

        @media (min-width: 2560px) {
            .top-nav { padding: 30px 120px; }
            section { padding: 120px 120px; }
            .section-title { font-size: 4rem; }
            .cover-image-wrapper { max-width: 600px; }
            .cover-title { font-size: 5.5rem; }
            .quote-text { font-size: 2.2rem; }
            .couple-grid { gap: 120px; max-width: 1200px; }
            .couple-img { width: 280px; height: 280px; }
            .event-grid { gap: 60px; max-width: 1200px; }
            .map-wrapper { height: 500px; max-width: 1200px; }
            .gallery-grid { grid-template-columns: repeat(4, 1fr); gap: 30px; max-width: 1600px; }
            .timeline { max-width: 1000px; }
            .rsvp-container { max-width: 1400px; }
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
                <span>🔊</span>
                <a href="/admin" class="admin-link" title="Admin Dashboard">⚙</a>
            </div>
        </header>

        <section class="cover-section">
            <div class="cover-image-wrapper">
                <img src="bunga_final2.jpeg" alt="Cover">
            </div>
            <div class="section-subtitle">Pernikahan</div>
           <h1 class="cover-title"><?= e($acara['mempelai']) ?></h1>
             <?= e($tanggalPendek) ?>
            <div class="guest-card">
                <div class="small">Kepada Yth.</div>
                <div class="name">Bapak Andi &amp; Keluarga</div>
            </div>
            <!-- <button class="btn">✉ BUKA UNDANGAN</button> -->
        </section>

        <section>
            <div class="section-subtitle">Assalamualaikum Wr. Wb.</div>
            <h2 class="section-title">Dengan penuh syukur</h2>
            <div class="quote-text">"Dan di antara tanda-tanda kebesaran-Nya, Dia menciptakan pasangan-pasangan untukmu agar kamu cenderung dan merasa tenteram kepadanya."</div>
            <div class="section-subtitle" style="margin-bottom: 40px;">AR-RUM · 21</div>
            <p class="opening-text">Dengan memohon rahmat dan ridho Allah SWT, kami bermaksud mengundang Bapak/Ibu/Saudara/i untuk hadir dan memberikan doa restu pada hari bahagia kami.</p>
          <p class="signature"><?= e($acara['mempelai']) ?></p>
        </section>

        <section>
            <div class="section-subtitle">Yang Berbahagia</div>
            <h2 class="section-title">Dua hati, satu tujuan</h2>
            <div class="couple-grid">
                <div class="couple-card">
                    <div class="couple-img"><img src="bunga_final2_riva.jpeg" alt="Siti"></div>
                    <h3 class="couple-name">Siti</h3>
                    <div class="couple-fullname">Siti</div>
                    <div class="couple-parents">Putri pertama Bapak Hadi Santoso &amp; Ibu Ratna Wulandari</div>
                </div>
                <div class="couple-card">
                    <div class="couple-img"><img src="bunga_final2_fahrul.jpeg" alt="Budi"></div>
                    <h3 class="couple-name">Budi</h3>
                    <div class="couple-fullname">Budi</div>
                    <div class="couple-parents">Putra kedua Bapak Bimo Adinata &amp; Ibu Larasati</div>
                </div>
            </div>
            <p class="couple-footer">Dengan cinta, we start our journey together, once and forever.</p>
        </section>

        <section>
            <div class="section-subtitle">Save The Date</div>
             <?= e($tanggalLengkap) ?>
             <?= e($tanggalHighlight) ?>
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
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3329.9144195974427!2d101.45047277403701!3d0.5048245637111075!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31d5aebce81ef37f%3A0x9b837fcba8f106e9!2sHotel%20Pangeran!5e1!3m2!1sen!2sus!4v1791464160985!5m2!1sen!2sus" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin=<?= urlencode($acara['lokasi'] . ', ' . $acara['alamat']) ?>&amp;output=embed"></iframe>
            </div>
            <h3 class="location-name"><?= e($acara['lokasi']) ?></h3>
            <p class="location-address"><?= e($acara['alamat']) ?></p>
            <a href="https://www.google.com/maps/dir/?api=1&amp;destination=<?= urlencode($acara['lokasi'] . ', ' . $acara['alamat']) ?>" target="_blank" class="btn">➔ PETUNJUK ARAH</a>
            <p class="location-note">Parkir tersedia · Akses transportasi umum tersedia</p>
        </section>

        <section>
            <div class="section-subtitle">Gallery</div>
            <h2 class="section-title">Our Memories</h2>
            <div class="gallery-grid">
                <div class="gallery-item"><img src="nokia.jpeg" alt="Gallery 1"></div>
                <div class="gallery-item"><img src="shadow.jpeg" alt="Gallery 2"></div>
                <div class="gallery-item"><img src="sit.jpeg" alt="Gallery 3"></div>
                <div class="gallery-item"><img src="throwing_flower.jpeg" alt="Gallery 4"></div>
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

    @if ($errors->any())
        <p role="alert">{{ $errors->first() }}</p>
    @endif

    <?php if ($flash): ?>
        <p role="status"><?= e($flash) ?></p>
    <?php endif; ?>

    <div class="rsvp-container">
        <form class="rsvp-form" method="post" action="{{ route('rsvp.store') }}">
            @csrf

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
<script>
    window.addEventListener("message", function (event) {
        if (
            event.source !== window.opener ||
            !event.data ||
            event.data.type !== "wedding-preview-update"
        ) {
            return;
        }

        const data = event.data.data;
        const coupleNames = document.querySelectorAll(".couple-name");
        const fullNames = document.querySelectorAll(".couple-fullname");
        const eventSection = document.querySelector(".event-grid").closest("section");
        eventSection.querySelector(".section-title").textContent = data.date;
        eventSection.querySelector(".date-highlight").textContent = data.date.toUpperCase();

        document.querySelector(".cover-title").textContent =
            `${data.groom} & ${data.bride}`;

        document.querySelector(".cover-date").textContent =
            `${data.date} — ${data.city.toUpperCase()}`;

        document.querySelector(".quote-text").textContent = data.quote;

        document.querySelectorAll(".event-location").forEach(element => {
            element.textContent = data.venue;
        });

        document.querySelector(".location-name").textContent = data.venue;
        document.querySelector(".location-address").textContent = data.address;

        if (coupleNames.length >= 2) {
            coupleNames[0].textContent = data.bride;
            coupleNames[1].textContent = data.groom;
        }

        if (fullNames.length >= 2) {
            fullNames[0].textContent = data.bride;
            fullNames[1].textContent = data.groom;
        }

        const query = encodeURIComponent(`${data.venue}, ${data.address}`);

        document.querySelector(".map-wrapper iframe").src =
            `https://www.google.com/maps?q=${query}&output=embed`;

        const directionsLink = document
            .querySelector(".location-name")
            .parentElement.querySelector("a.btn");

        directionsLink.href =
            `https://www.google.com/maps/dir/?api=1&destination=${query}`;
    });
</script>
</body>
</html>