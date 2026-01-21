<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<!-- Chart JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- App Logic -->
<script>
    const appData = <?= json_encode($data['provinces']); ?>;
    const trendData = <?= json_encode($data['trends']); ?>;
</script>
<script src="<?= 'http://' . $_SERVER['HTTP_HOST'] . '/pmWeb2/public/js/script.js'; ?>"></script>
</body>

</html>