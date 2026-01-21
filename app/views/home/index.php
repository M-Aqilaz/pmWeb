<div class="dashboard-container">
    <header class="top-bar">
        <div class="logo-section">
            <i class="fa-solid fa-shield-halved logo-icon"></i>
            <div class="brand-text">
                <h1>Dashboard Kerawanan Nasional</h1>
                <span>POLRI - <span><span>Indonesian Security Monitoring System</span>
            </div>
        </div>

        <div class="status-section">
            <span class="status-badge">STATUS: <?= strtoupper($data['nasional']['overall_status']); ?></span>
            <span style="font-size:0.9rem; font-weight:bold; color:#fff; margin-left:15px; font-family:'Courier New';"
                id="current-time">...</span>
        </div>

        <div class="ticker-wrap">
            <div class="ticker">
                <div class="ticker-item"><i class="fa-solid fa-circle-info"></i> MONITORING STABILITAS NASIONAL AKTIF
                </div>
                <div class="ticker-item"><i class="fa-solid fa-chart-line"></i> INFLASI TERKENDALI DI ANGKA
                    <?= $data['nasional']['inflation_rate']; ?>%
                </div>
                <div class="ticker-item"><i class="fa-solid fa-triangle-exclamation"></i> WASPADA CUACA EKSTREM DI
                    WILAYAH JAWA & SUMATERA</div>
                <div class="ticker-item"><i class="fa-solid fa-users"></i> SENTIMEN PUBLIK: CENDERUNG NEGATIF PADA ISU
                    LAHAN</div>
                <div class="ticker-item"><i class="fa-solid fa-shield"></i> PENGAMANAN PERBATASAN DIPERKETAT</div>
            </div>
        </div>
    </header>

    <main class="main-grid">
        <aside class="sidebar-left">

            <div class="card-compact">
                <div class="card-header">INDIKATOR MAKRO</div>
                <div class="data-row">
                    <span class="data-label">UMR Avg</span>
                    <span class="data-value">Rp 3.75jt</span>
                </div>
                <div class="data-row">
                    <span class="data-label">Inflasi</span>
                    <span class="data-value red"><?= $data['nasional']['inflation_rate']; ?>%</span>
                </div>
                <div class="data-row">
                    <span class="data-label">Devisa</span>
                    <span class="data-value green">$140 M</span>
                </div>

                <div
                    style="height: 60px; width: 100%; margin-top: 15px; border-top: 1px dashed #334155; padding-top: 10px;">
                    <canvas id="macroMiniChart"></canvas>
                </div>
            </div>

            <div class="card-compact">
                <div class="card-header">HARGA PANGAN (AVG)</div>
                <div class="data-row">
                    <span class="data-label">Beras</span>
                    <span class="data-value">Rp
                        <?= number_format($data['nasional']['avg_price_rice'], 0, ',', '.'); ?></span>
                </div>
                <div class="data-row">
                    <span class="data-label">Minyak</span>
                    <span class="data-value">Rp
                        <?= number_format($data['nasional']['avg_price_oil'], 0, ',', '.'); ?></span>
                </div>
                <div class="data-row">
                    <span class="data-label">Cabai</span>
                    <span class="data-value red">Rp
                        <?= number_format($data['nasional']['avg_price_chili'], 0, ',', '.'); ?></span>
                </div>

                <div
                    style="height: 60px; width: 100%; margin-top: 15px; border-top: 1px dashed #334155; padding-top: 10px;">
                    <canvas id="foodMiniChart"></canvas>
                </div>
            </div>
            <div class="province-detail-container" id="province-detail-box">
                <div id="province-detail-content" style="height: 100%;">
                </div>
            </div>
        </aside>

        <section class="center-panel">
            <div class="map-container">
                <div id="map"></div>
                <div
                    style="position:absolute; bottom:20px; right:20px; background:rgba(0,0,0,0.8); padding:10px; border-radius:4px; border:1px solid #334155; z-index:999; font-size:0.7rem;">
                    <div style="display:flex; align-items:center; gap:5px; margin-bottom:3px;"><span
                            style="width:10px; height:10px; background:#10b981; display:block; border-radius:50%;"></span>
                        Aman (0-4)</div>
                    <div style="display:flex; align-items:center; gap:5px; margin-bottom:3px;"><span
                            style="width:10px; height:10px; background:#facc15; display:block; border-radius:50%;"></span>
                        Waspada (5-7)</div>
                    <div style="display:flex; align-items:center; gap:5px;"><span
                            style="width:10px; height:10px; background:#ef4444; display:block; border-radius:50%;"></span>
                        Bahaya (8-10)</div>
                </div>
            </div>

 <div class="bottom-panels">
    
    <div class="card ai-card-container" style="position:relative; overflow:hidden; min-height: 180px; margin-bottom: 20px;">
        <div class="ai-grid-bg"></div>
        <div class="ai-scan-line"></div>

        <div class="card-header" style="border-bottom: 1px solid rgba(6, 182, 212, 0.3); color: #06b6d4; display:flex; justify-content:space-between; align-items:center;">
            <span><i class="fa-solid fa-microchip"></i> AI INTELLIGENCE CORE</span>
            <span style="font-size:0.7rem; font-family:'Courier New'; opacity:0.8;">SYS_V.2.0.4</span>
        </div>

        <div class="ai-body" style="padding: 15px; position:relative; z-index:2;">
            <div style="display:flex; gap:15px; margin-bottom:15px; font-size:0.75rem; font-family:'Courier New'; color:#64748b;">
                <div>STATUS: <span style="color:#22c55e;">ONLINE</span></div>
                <div>CONFIDENCE: <span style="color:#fbbf24;">98.4%</span></div>
                <div>LATENCY: <span style="color:#06b6d4;">12ms</span></div>
            </div>

            <div class="ai-terminal-window">
                <span class="prompt-sign">>></span>
                <span id="ai-typing-text" data-text="<?= htmlspecialchars($data['nasional']['intelligence_analysis']); ?>">INITIALIZING...</span>
                <span class="cursor-blink">_</span>
            </div>
        </div>

        <div class="corner-dec top-right"></div>
        <div class="corner-dec bottom-left"></div>
    </div>

    <div class="card" style="display:flex; flex-direction:column; overflow:hidden; min-height: 250px;">
        <div class="card-header" style="color:var(--danger); display:flex; justify-content:space-between; align-items:center;">
            <span><i class="fa-solid fa-house-crack"></i> LOG BENCANA TERKINI</span>
            <span class="live-indicator"><span class="blink">●</span> LIVE</span>
        </div>

        <div class="disaster-table-container">
            <table class="table-disaster">
                <thead>
                    <tr>
                        <th width="25%">Waktu</th>
                        <th width="45%">Kejadian</th>
                        <th width="30%">Lokasi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['disasters'])): ?>
                        <?php foreach ($data['disasters'] as $index => $disaster): ?>
                            <?php
                            // Logika Ikon Otomatis
                            $icon = 'fa-circle-exclamation';
                            $color = '#cbd5e1';
                            $evt = strtolower($disaster['event_name']);

                            if (strpos($evt, 'banjir') !== false) {
                                $icon = 'fa-water';
                                $color = '#3b82f6';
                            } elseif (strpos($evt, 'gempa') !== false) {
                                $icon = 'fa-house-crack';
                                $color = '#f59e0b';
                            } elseif (strpos($evt, 'longsor') !== false) {
                                $icon = 'fa-hill-rockslide';
                                $color = '#854d0e';
                            } elseif (strpos($evt, 'kebakaran') !== false) {
                                $icon = 'fa-fire';
                                $color = '#ef4444';
                            }

                            $time = date('d M - H:i', strtotime($disaster['created_at']));
                            ?>
                            <tr class="<?= $index === 0 ? 'row-highlight' : '' ?>">
                                <td class="text-mono"><?= $time; ?></td>
                                <td>
                                    <i class="fa-solid <?= $icon; ?>"
                                        style="color:<?= $color; ?>; margin-right:8px; width:20px; text-align:center;"></i>
                                    <?= $disaster['event_name']; ?>
                                </td>
                                <td>
                                    <span class="loc-badge"><?= $disaster['location']; ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" style="text-align:center; padding:20px; color:#64748b;">
                                Tidak ada laporan bencana terkini.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
        </section>

        <aside class="sidebar-right">
            <div class="card">
                <div class="card-header">INDEKS KECEMASAN PUBLIK</div>
                <div class="chart-container-gauge">
                    <canvas id="emotionChart"></canvas>
                </div>
            </div>

            <div class="card">
                <div class="card-header">ISU DOMINAN (NASIONAL)</div>

                <div class="issue-list-container"
                    style="margin-top:15px; display:flex; flex-direction:column; gap:12px;">
                    <?php
                    // Total provinsi (untuk menghitung persentase bar)
                    $total_regions = 38;

                    // Warna bar untuk variasi (Merah, Kuning, Oranye, Biru)
                    $colors = ['#ef4444', '#f59e0b', '#f97316', '#3b82f6'];
                    $i = 0;
                    ?>

                    <?php if (!empty($data['top_issues'])): ?>
                        <?php foreach ($data['top_issues'] as $issue): ?>
                            <?php
                            $pct = ($issue['count'] / $total_regions) * 100;
                            // Agar bar tidak kepanjangan/kependekan secara visual
                            $visual_pct = $pct < 10 ? 10 : ($pct > 100 ? 100 : $pct);
                            $color = $colors[$i % count($colors)];
                            $i++;
                            ?>
                            <div class="issue-item">
                                <div style="display:flex; justify-content:space-between; margin-bottom:4px; font-size:0.8rem;">
                                    <span style="color:#f1f5f9; font-weight:500;">
                                        <i class="fa-solid fa-tag" style="color:<?= $color; ?>; margin-right:5px;"></i>
                                        <?= $issue['issue']; ?>
                                    </span>
                                    <span style="color:#94a3b8;"><?= $issue['count']; ?> Wilayah</span>
                                </div>
                                <div style="background:rgba(255,255,255,0.1); height:6px; border-radius:3px; width:100%;">
                                    <div
                                        style="background:<?= $color; ?>; width:<?= $visual_pct; ?>%; height:100%; border-radius:3px; box-shadow: 0 0 10px <?= $color; ?>50;">
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="text-align:center; color:#64748b; font-size:0.8rem; padding:20px;">
                            Data isu belum tersedia
                        </div>
                    <?php endif; ?>
                </div>

                <div
                    style="margin-top:15px; pt-2; border-top:1px dashed #334155; font-size:0.7rem; color:#64748b; text-align:right;">
                    *Berdasarkan agregasi laporan daerah
                </div>
            </div>
            <div class="card" style="flex:1;">
                <div class="card-header">TREN KRIMINALITAS</div>
                <div class="chart-container">
                    <canvas id="crimeTrendChart"></canvas>
                </div>
            </div>
        </aside>
    </main>
</div>
<div class="-page-container">
    <div class="stats-grid-top">
        <div class="card full-height">
            <div class="card-header" style="color:#ef4444;">
                <i class="fa-solid fa-circle-exclamation"></i> Top 10 Provinsi Rawan
            </div>
            <div class="chart-container-large" style="height: 320px;">
                <canvas id="topRawanChart"></canvas>
            </div>
        </div>

        <div class="card">
            <div class="card-header" style="color:var(--text-primary);">
                <i class="fa-solid fa-chart-simple" style="color:#fbbf24;"></i> Statistik Nasional
            </div>

            <div class="summary-grid-2x2">
                <div class="stat-box">
                    <span class="stat-label">Rata-rata Inflasi</span>
                    <span
                        class="stat-value text-yellow"><?= number_format($data['nasional']['inflation_rate'], 2); ?>%</span>
                    <span class="stat-sub">Year-on-Year</span>
                </div>

                <div class="stat-box">
                    <span class="stat-label">Indeks Emosi Nasional</span>
                    <span
                        class="stat-value text-white"><?= number_format($data['nasional']['emotion_index'], 0); ?></span>
                    <div class="progress-container">
                        <div class="progress-fill" style="width: <?= $data['nasional']['emotion_index']; ?>%;"></div>
                    </div>
                </div>

                <div class="stat-box">
                    <span class="stat-label">Total Bencana (5 Tahun)</span>
                    <span
                        class="stat-value text-red"><?= number_format($data['nasional']['total_disasters'], 0, ',', '.'); ?></span>
                    <span class="stat-sub">Kejadian tercatat</span>
                </div>

                <div class="stat-box">
                    <span class="stat-label">Status Keamanan</span>
                    <?php
                    $total_prov = 38; // Total provinsi
                    $p_aman = ($data['nasional']['count_aman'] / $total_prov) * 100;
                    $p_waspada = ($data['nasional']['count_waspada'] / $total_prov) * 100;
                    $p_siaga = ($data['nasional']['count_siaga'] / $total_prov) * 100;
                    ?>
                    <div class="status-list">
                        <div class="status-item">
                            <span class="status-label text-green">Aman</span>
                            <div class="status-track">
                                <div class="track-fill bg-green" style="width: <?= $p_aman; ?>%"></div>
                            </div>
                            <span class="status-val"><?= $data['nasional']['count_aman']; ?></span>
                        </div>
                        <div class="status-item">
                            <span class="status-label text-yellow">Waspada</span>
                            <div class="status-track">
                                <div class="track-fill bg-yellow" style="width: <?= $p_waspada; ?>%"></div>
                            </div>
                            <span class="status-val"><?= $data['nasional']['count_waspada']; ?></span>
                        </div>
                        <div class="status-item">
                            <span class="status-label text-red">Siaga</span>
                            <div class="status-track">
                                <div class="track-fill bg-red" style="width: <?= $p_siaga; ?>%"></div>
                            </div>
                            <span class="status-val"><?= $data['nasional']['count_siaga']; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card table-card">
        <div class="table-controls">
            <div class="card-header">Data Provinsi</div>
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="tableSearch" placeholder="Cari provinsi...">
            </div>
        </div>

        <div class="table-wrapper">
            <table id="provinceTable">
                <thead>
                    <tr>
                        <th width="30%">Provinsi</th>
                        <th width="10%">Skor</th>
                        <th width="15%">Status</th>
                        <th width="10%">Insiden</th>
                        <th width="15%">Inflasi</th>
                        <th width="20%">Konflik</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['provinces'] as $p): ?>
                        <tr>
                            <td class="fw-bold"><?= $p['name']; ?></td>
                            <td class="fw-bold text-red"><?= $p['score']; ?></td>
                            <td>
                                <span class="badge-pill"
                                    style="border: 1px solid <?= $p['color']; ?>; color: <?= $p['color']; ?>; background: rgba(0,0,0,0.2);">
                                    <?= $p['status']; ?>
                                </span>
                            </td>
                            <td><?= $p['incidents']; ?></td>
                            <td><?= $p['inflation']; ?>%</td>
                            <td><?= $p['conflict']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>