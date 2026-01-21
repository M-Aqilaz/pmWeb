<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Ambil data PHP ke JS dengan aman
    const appData = <?= json_encode($data['provinces'] ?? []); ?>;
    const trendData = <?= json_encode($data['trends'] ?? []); ?>;
</script>

<script src="/js/script.js"></script>
</body>

</html>
