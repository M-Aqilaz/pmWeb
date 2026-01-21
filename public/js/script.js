document.addEventListener('DOMContentLoaded', () => {
    // --- 1. CHART SETUP (Top 10) ---
    const ctxTop = document.getElementById('topRawanChart').getContext('2d');
    // Urutkan data berdasarkan skor tertinggi, ambil 10 besar
    const sortedData = [...appData].sort((a, b) => b.score - a.score).slice(0, 10);

    new Chart(ctxTop, {
        type: 'bar',
        data: {
            labels: sortedData.map(d => d.name),
            datasets: [{
                label: 'Skor Kerawanan',
                data: sortedData.map(d => d.score),
                backgroundColor: '#ef4444', // Warna Merah sesuai Gambar 2
                borderRadius: 4,
                barPercentage: 0.6,
                categoryPercentage: 0.8
            }]
        },
        options: {
            indexAxis: 'y', // MEMBUAT CHART HORIZONTAL
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleColor: '#fff',
                    bodyColor: '#cbd5e1'
                }
            },
            scales: {
                x: {
                    grid: { color: '#334155' },
                    ticks: { color: '#94a3b8' },
                    max: 10,
                    beginAtZero: true
                },
                y: {
                    grid: { display: false },
                    ticks: { color: '#f1f5f9', font: { size: 11 } }
                }
            }
        }
    });

    // Fitur Search Table
    const searchInput = document.getElementById('tableSearch');
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#provinceTable tbody tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    }

    // --- 2. MAP LOGIC (PEMBARUAN PENTING) ---
    let selectedLayer = null;

    const indonesiaBounds = [
        [-11.0, 95.0],
        [6.0, 141.0]
    ];

    const map = L.map('map', {
        center: [-2.5, 118],
        zoom: 5,
        minZoom: 4,
        zoomControl: false,
        attributionControl: false,
        maxBounds: [[-15.0, 90.0], [10.0, 145.0]],
        maxBoundsViscosity: 0.9,
        zoomSnap: 0.1,
        zoomDelta: 0.5
    });

    map.fitBounds(indonesiaBounds, { padding: [20, 20] });

    L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri',
        maxZoom: 17,
        opacity: 0.6
    }).addTo(map);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_only_labels/{z}/{x}/{y}{r}.png', {
        opacity: 1,
        pane: 'shadowPane'
    }).addTo(map);

    // Perhatikan path ini:
    // Browser mengakses dari 'public/', jadi pathnya adalah 'data/indonesia.json'
    fetch('data/indonesia.json')
        .then(response => {
            if (!response.ok) {
                throw new Error(`Gagal memuat file lokal (Status: ${response.status})`);
            }
            return response.json();
        })
        .then(geoData => {
            // console.log("Peta berhasil dimuat:", geoData); // Uncomment untuk debug
            L.geoJSON(geoData, {
                style: styleFeature,
                onEachFeature: onEachFeature
            }).addTo(map);
        })
        .catch(err => {
            console.error("Error Map:", err);
            alert("GAGAL MEMUAT PETA LOKAL!\n\nPastikan file 'indonesia.json' sudah ada di dalam folder 'public/data/'.");
        });


    // --- 3. DATA MAPPING (Mencocokkan Nama di JSON dengan Database) ---
    const provinceMapping = {
        "Dki Jakarta": "DKI Jakarta",
        "Daerah Istimewa Yogyakarta": "DI Yogyakarta",
        "Nusa Tenggara Barat": "Nusa Tenggara Barat",
        "Nusa Tenggara Timur": "Nusa Tenggara Timur",
        "Kepulauan Bangka Belitung": "Kepulauan Bangka Belitung",
        "Bangka Belitung": "Kepulauan Bangka Belitung",
        "Kepulauan Riau": "Kepulauan Riau",

        // Pastikan nama provinsi baru ada di sini
        "Papua Selatan": "Papua Selatan",
        "Papua Tengah": "Papua Tengah",
        "Papua Pegunungan": "Papua Pegunungan",
        "Papua Barat Daya": "Papua Barat Daya",
        "Papua Barat": "Papua Barat",
        "Papua": "Papua",
        "Kalimantan Utara": "Kalimantan Utara"
    };

    function getProvinceData(provName) {
        if (!provName) return null;
        let cleanName = provName.trim();
        // Cek mapping, jika tidak ada pakai nama asli
        let searchName = provinceMapping[cleanName] || cleanName;

        return appData.find(d => {
            const dbName = d.name.toLowerCase();
            const mapName = searchName.toLowerCase();
            return dbName === mapName || dbName.includes(mapName) || mapName.includes(dbName);
        });
    }
    // --- 4. MINI CHARTS (DATA DARI DATABASE) ---

    // Siapkan data dari variable global 'trendData'
    // Kita ambil Labels (Bulan) dan Data (Nilai)

    // Format tanggal jadi nama bulan singkat (Jan, Feb, dst)
    const trendLabels = trendData.map(item => {
        const date = new Date(item.record_date);
        return date.toLocaleDateString('id-ID', { month: 'short' });
    });

    // Ambil data spesifik
    const dataInflasi = trendData.map(item => parseFloat(item.inflation_rate));

    // Untuk pangan, kita ambil rata-rata harga gabungan (Beras + Minyak + Cabai) / 3
    // Atau bisa pilih salah satu. Di sini saya buat rata-rata gabungan.
    const dataPangan = trendData.map(item => {
        return (parseFloat(item.avg_price_rice) + parseFloat(item.avg_price_oil) + parseFloat(item.avg_price_chili)) / 3;
    });

    const commonMiniOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false }, tooltip: { enabled: true, intersect: false } },
        scales: { x: { display: false }, y: { display: false } },
        elements: { point: { radius: 0, hitRadius: 10 }, line: { tension: 0.4, borderWidth: 2 } }
    };

    // A. Chart Indikator Makro (Inflasi)
    const ctxMacroMini = document.getElementById('macroMiniChart').getContext('2d');
    let gradientMacro = ctxMacroMini.createLinearGradient(0, 0, 0, 60);
    gradientMacro.addColorStop(0, 'rgba(239, 68, 68, 0.5)');
    gradientMacro.addColorStop(1, 'rgba(239, 68, 68, 0.0)');

    new Chart(ctxMacroMini, {
        type: 'line',
        data: {
            labels: trendLabels, // Label dari Database
            datasets: [{
                data: dataInflasi, // Data Inflasi dari Database
                borderColor: '#ef4444',
                backgroundColor: gradientMacro,
                fill: true
            }]
        },
        options: commonMiniOptions
    });

    // B. Chart Harga Pangan
    const ctxFoodMini = document.getElementById('foodMiniChart').getContext('2d');
    let gradientFood = ctxFoodMini.createLinearGradient(0, 0, 0, 60);
    gradientFood.addColorStop(0, 'rgba(251, 191, 36, 0.5)');
    gradientFood.addColorStop(1, 'rgba(251, 191, 36, 0.0)');

    new Chart(ctxFoodMini, {
        type: 'line',
        data: {
            labels: trendLabels, // Label dari Database
            datasets: [{
                data: dataPangan, // Data Pangan dari Database
                borderColor: '#fbbf24',
                backgroundColor: gradientFood,
                fill: true
            }]
        },
        options: commonMiniOptions
    });
    function styleFeature(feature) {
        const props = feature.properties;

        console.log("Properti Peta:", props);
        const provName = props.PROVINSI || props.state || props.Provinsi || props.name || props.NAME_1;
        const data = getProvinceData(provName);
        return {
            fillColor: data ? data.color : '#475569', // Warna abu-abu jika data null
            weight: 1,
            opacity: 1,
            color: '#0f172a',
            fillOpacity: 0.75
        };
    }

    function onEachFeature(feature, layer) {
        const props = feature.properties;
        const provName = props.PROVINSI || props.state || props.Provinsi || props.name || props.NAME_1;
        layer.on({
            mouseover: (e) => {
                const currentLayer = e.target;
                currentLayer.setStyle({ weight: 2, color: '#fbbf24', fillOpacity: 0.9 });
                currentLayer.bringToFront();
                updateDetailPanel(provName);
            },
            mouseout: (e) => {
                const currentLayer = e.target;
                if (currentLayer !== selectedLayer) {
                    const data = getProvinceData(provName);
                    currentLayer.setStyle({
                        weight: 1,
                        color: '#0f172a',
                        fillColor: data ? data.color : '#475569',
                        fillOpacity: 0.75
                    });
                }
                if (selectedLayer) {
                    const sProps = selectedLayer.feature.properties;
                    const sName = sProps.PROVINSI || sProps.state || sProps.Provinsi || sProps.name;
                    updateDetailPanel(sName);
                } else {
                    resetProvinceDetail();
                }
            },
            click: (e) => {
                const clickedLayer = e.target;
                L.DomEvent.stopPropagation(e);

                if (selectedLayer && selectedLayer !== clickedLayer) {
                    const prevProps = selectedLayer.feature.properties;
                    const prevName = prevProps.PROVINSI || prevProps.state || prevProps.Provinsi || prevProps.name;
                    const prevData = getProvinceData(prevName);
                    selectedLayer.setStyle({
                        weight: 1,
                        color: '#0f172a',
                        fillColor: prevData ? prevData.color : '#475569',
                        fillOpacity: 0.75
                    });
                }

                selectedLayer = clickedLayer;
                clickedLayer.setStyle({ weight: 2, color: '#fbbf24', fillOpacity: 0.9 });
                clickedLayer.bringToFront();
                updateDetailPanel(provName);
                map.fitBounds(clickedLayer.getBounds());
            }
        });
    }

    map.on('click', () => {
        if (selectedLayer) {
            const prevProps = selectedLayer.feature.properties;
            const prevName = prevProps.PROVINSI || prevProps.state || prevProps.Provinsi || prevProps.name;
            const prevData = getProvinceData(prevName);

            selectedLayer.setStyle({
                weight: 1,
                color: '#0f172a',
                fillColor: prevData ? prevData.color : '#475569',
                fillOpacity: 0.75
            });
            selectedLayer = null;
            resetProvinceDetail();
        }
    });
    
// --- 5. AI TYPING EFFECT ---
    const aiTextElement = document.getElementById('ai-typing-text');
    if (aiTextElement) {
        const fullText = aiTextElement.getAttribute('data-text');
        aiTextElement.innerText = ''; // Kosongkan dulu
        
        let i = 0;
        const speed = 20; // Kecepatan mengetik (ms)

        function typeWriter() {
            if (i < fullText.length) {
                aiTextElement.innerHTML += fullText.charAt(i);
                i++;
                setTimeout(typeWriter, speed);
            }
        }
        
        // Mulai mengetik setelah jeda sedikit (biar halaman load dulu)
        setTimeout(typeWriter, 500);
    }
    function updateDetailPanel(provName) {
        const data = getProvinceData(provName);
        const container = document.getElementById('province-detail-content');

        if (!data) {
            container.innerHTML = `<div style="padding:10px; color:#94a3b8; text-align:center;">Data ${provName} belum tersedia di database.</div>`;
            return;
        }

        const totalSentimen = (parseInt(data.sentiment_pos) || 0) + (parseInt(data.sentiment_neg) || 0);
        const posPct = totalSentimen === 0 ? 0 : Math.round((data.sentiment_pos / totalSentimen) * 100);
        const negPct = totalSentimen === 0 ? 0 : Math.round((data.sentiment_neg / totalSentimen) * 100);

        // Ikon Cuaca sederhana berdasarkan teks database
        let weatherIcon = '<i class="fa-solid fa-cloud"></i>';
        const weatherText = (data.weather || '').toLowerCase();
        if (weatherText.includes('hujan')) weatherIcon = '<i class="fa-solid fa-cloud-showers-heavy text-blue-400"></i>';
        else if (weatherText.includes('cerah')) weatherIcon = '<i class="fa-solid fa-sun text-yellow-400"></i>';
        else if (weatherText.includes('badai') || weatherText.includes('petir')) weatherIcon = '<i class="fa-solid fa-bolt text-red-400"></i>';

        container.innerHTML = `
        <div style="border-bottom:1px solid rgba(255,255,255,0.1); margin-bottom:10px; padding-bottom:5px; display:flex; justify-content:space-between; align-items:center;">
            <h3 style="color:${data.color}; font-size:1rem; margin:0;">
                <i class="fa-solid fa-map-location-dot"></i> ${data.name}
            </h3>
            <div style="font-size:0.8rem; color:#cbd5e1;" title="Cuaca: ${data.weather}">
                ${weatherIcon} ${data.weather || '-'}
            </div>
        </div>
        
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px; font-size:0.85rem;">
            <div>
                <div style="color:#94a3b8; font-size:0.7rem;">Status Wilayah</div>
                <span class="status-badge" style="background:${data.color}; color:#fff; padding:2px 8px; font-size:0.7rem; border:none;">
                    ${data.status}
                </span>
            </div>
            <div>
                <div style="color:#94a3b8; font-size:0.7rem;">Total Insiden</div>
                <div style="color:#fff; font-weight:bold;">${data.incidents} Kasus</div>
            </div>
            <div>
                <div style="color:#94a3b8; font-size:0.7rem;">Inflasi Daerah</div>
                <div style="color:${data.inflation > 3 ? '#ef4444' : '#10b981'}; font-weight:bold;">${data.inflation}%</div>
            </div>
            <div>
                <div style="color:#94a3b8; font-size:0.7rem;">Est. UMR 2026</div>
                <div style="color:#fff;">${(data.umr / 1000000).toFixed(1)} Jt</div>
            </div>
        </div>

        <div style="margin-top:10px; padding-top:5px; border-top:1px dashed #334155;">
             <div style="color:#94a3b8; font-size:0.7rem; margin-bottom:2px;">Sentimen Media</div>
             <div style="display:flex; height:6px; width:100%; background:#334155; border-radius:3px; overflow:hidden;">
                <div style="width:${posPct}%; background:#10b981;" title="Positif: ${posPct}%"></div>
                <div style="width:${negPct}%; background:#ef4444;" title="Negatif: ${negPct}%"></div>
             </div>
             <div style="display:flex; justify-content:space-between; font-size:0.7rem; margin-top:2px;">
                <span style="color:#10b981;">Positif: ${posPct}%</span>
                <span style="color:#ef4444;">Negatif: ${negPct}%</span>
             </div>
        </div>

        <div style="margin-top:8px; padding-top:5px; border-top:1px dashed #334155;">
            <div style="color:#94a3b8; font-size:0.7rem; margin-bottom:4px;">Potensi Konflik Utama:</div>
            <div style="color:#fbbf24; font-size:0.85rem;"><i class="fa-solid fa-triangle-exclamation"></i> ${data.conflict}</div>
        </div>
    `;
    }
    function resetProvinceDetail() {
        const container = document.getElementById('province-detail-content');
        if (container) {
            container.innerHTML = `
                <div style="height:100%; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; opacity:0.5; min-height:140px;">
                    <i class="fa-solid fa-globe" style="font-size:2.5rem; margin-bottom:10px; color:#3b82f6;"></i>
                    <div style="font-size:0.8rem; color:#94a3b8;">Arahkan kursor ke peta untuk<br>melihat detail provinsi</div>
                </div>
            `;
        }
    }
    resetProvinceDetail();

    // --- OTHER CHARTS (GAUGE & CLOCK) ---
    const gaugeNeedle = {
        id: 'gaugeNeedle',
        afterDatasetDraw(chart, args, options) {
            const { ctx, chartArea: { left, right, top, bottom, width, height } } = chart;
            ctx.save();
            const score = 65;
            const min = 0; const max = 100;
            const angleSpan = Math.PI;
            const angle = Math.PI + ((score - min) / (max - min) * angleSpan);
            const cx = (left + right) / 2;
            const cy = bottom - 20;
            const needleLen = height - 40;
            ctx.translate(cx, cy);
            ctx.rotate(angle);
            ctx.beginPath();
            ctx.moveTo(0, -6); ctx.lineTo(needleLen, 0); ctx.lineTo(0, 6);
            ctx.fillStyle = '#475569';
            ctx.fill();
            ctx.rotate(-angle);
            ctx.beginPath(); ctx.arc(0, 0, 8, 0, Math.PI * 2); ctx.fillStyle = '#475569'; ctx.fill();
            ctx.beginPath(); ctx.arc(0, 0, 4, 0, Math.PI * 2); ctx.fillStyle = '#1e293b'; ctx.fill();
            ctx.restore();
            ctx.textAlign = 'center'; ctx.textBaseline = 'middle';
            ctx.font = 'bold 16px "Outfit", sans-serif'; ctx.fillStyle = '#fbbf24';
            ctx.fillText('SEDANG', cx, cy + 25);
            ctx.font = '400 12px "Outfit", sans-serif'; ctx.fillStyle = '#94a3b8';
            ctx.fillText('(65/100)', cx, cy + 45);
        }
    };

    const ctxEmotion = document.getElementById('emotionChart').getContext('2d');
    new Chart(ctxEmotion, {
        type: 'doughnut',
        data: {
            labels: ['Aman', 'Waspada', 'Siaga', 'Bahaya'],
            datasets: [{
                data: [25, 25, 25, 25],
                backgroundColor: ['#22c55e', '#eab308', '#f97316', '#ef4444'],
                borderColor: '#1e293b', borderWidth: 4,
                circumference: 180, rotation: 270, cutout: '70%',
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            layout: { padding: { bottom: 50, top: 10 } },
            plugins: { legend: { display: false }, tooltip: { enabled: false } }
        },
        plugins: [gaugeNeedle]
    });

    const ctxTrend = document.getElementById('crimeTrendChart').getContext('2d');
    new Chart(ctxTrend, {
        type: 'bar',
        data: {
            labels: ['2023', '2024', '2025'],
            datasets: [{
                label: 'Kasus',
                data: [1200, 1450, 980],
                backgroundColor: ['#334155', '#334155', '#ef4444'],
                borderRadius: 2, barThickness: 15
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { display: false, grid: { display: false } },
                x: { grid: { display: false }, ticks: { color: '#64748b', font: { size: 10 } } }
            }
        }
    });

    setInterval(() => {
        const now = new Date();
        const timeEl = document.getElementById('current-time');
        if (timeEl) timeEl.innerText = `${now.toLocaleTimeString('id-ID', { hour12: false })} WIB`;
    }, 1000);
});