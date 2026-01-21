<?php

class Provinces
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }
    public function getNationalTrends()
    {
        // Ambil 6 data terakhir diurutkan dari tanggal lama ke baru
        $query = "SELECT record_date, inflation_rate, avg_price_rice, avg_price_oil, avg_price_chili 
                  FROM national_trends 
                  ORDER BY record_date ASC 
                  LIMIT 6";

        $this->db->query($query);
        return $this->db->resultSet();
    }

    public function getAllProvincesData()
    {

        $query = "SELECT DISTINCT ON (r.id)
            r.id, r.name, r.risk_level as score, 
            e.umr, e.inflation_rate as inflation, e.weather_forecast as weather,
            s.social_sentiment_score as emotion_index, s.crime_theft, s.crime_robbery, s.crime_fraud, s.active_issues,
            s.disaster_count, s.sentiment_pos, s.sentiment_neg
          FROM regions r
          LEFT JOIN economic_metrics e ON r.id = e.region_id
          LEFT JOIN social_security s ON r.id = s.region_id
          ORDER BY r.id, r.name ASC";

        $this->db->query($query);
        $rawProvinces = $this->db->resultSet();

        $this->db->query("SELECT region_id, item_name, price FROM commodity_prices");
        $rawPrices = $this->db->resultSet();

        $pricesMap = [];
        if ($rawPrices) {
            foreach ($rawPrices as $p) {
                $key = 'rice';
                if (stripos($p['item_name'], 'Beras') !== false)
                    $key = 'rice';
                if (stripos($p['item_name'], 'Minyak') !== false)
                    $key = 'oil';
                if (stripos($p['item_name'], 'Cabai') !== false || stripos($p['item_name'], 'Cabe') !== false)
                    $key = 'chili';
                $pricesMap[$p['region_id']][$key] = (float) $p['price'];
            }
        }

        $finalData = [];
        if ($rawProvinces) {
            foreach ($rawProvinces as $row) {
                $score = (int) $row['score'];
                $issuesStr = str_replace(['{', '}', '"'], '', $row['active_issues'] ?? '');
                $conflict = empty($issuesStr) ? 'Nihil' : explode(',', $issuesStr)[0];
                $incidents = (int) $row['crime_theft'] + (int) $row['crime_robbery'] + (int) $row['crime_fraud'];

                $sent_pos = (int) ($row['sentiment_pos'] ?? 50);
                $sent_neg = (int) ($row['sentiment_neg'] ?? 50);
                $sent_net = 100 - ($sent_pos + $sent_neg);
                if ($sent_net < 0)
                    $sent_net = 0;

                $finalData[] = [
                    'name' => $row['name'],
                    'score' => $score,
                    'status' => $this->getStatus($score),
                    'color' => $this->getColor($score),
                    'incidents' => $incidents,
                    'inflation' => $row['inflation'],
                    'prices' => $pricesMap[$row['id']] ?? ['rice' => 0, 'oil' => 0, 'chili' => 0],
                    'umr' => (int) $row['umr'],
                    'sentiment' => ['positive' => $sent_pos, 'negative' => $sent_neg, 'neutral' => $sent_net],
                    'emotion_index' => (int) $row['emotion_index'],
                    'conflict' => $conflict,
                    'weather' => $row['weather'],
                    'disasters' => (int) ($row['disaster_count'] ?? 0)
                ];
            }
        }
        usort($finalData, function ($a, $b) {
            // Jika score B lebih besar dari A, maka B ditaruh di atas (return positif)
            return $b['score'] - $a['score'];
        });
        return $finalData;
    }

    /**
     * PERBAIKAN UTAMA DISINI
     * Menambahkan kolom-kolom yang diminta View: harga pangan, analisis, konflik, dll.
     */
    public function getNationalStats()
    {
        $query = "SELECT 
                    -- Ambil kolom data riil dari tabel national_statistics
                    n.inflation_rate,
                    n.avg_price_rice,
                    n.avg_price_oil,
                    n.avg_price_chili,
                    n.dominant_conflict,
                    n.intelligence_analysis,
                    n.overall_status,
                    n.ai_recommendation,
                    n.last_updated,

                    -- Ambil data agregat hitungan dari tabel lain
                    (SELECT AVG(social_sentiment_score) FROM social_security) as emotion_index,
                    (SELECT SUM(disaster_count) FROM social_security) as total_disasters,
                    (SELECT COUNT(*) FROM regions WHERE risk_level <= 4) as count_aman,
                    (SELECT COUNT(*) FROM regions WHERE risk_level BETWEEN 5 AND 7) as count_waspada,
                    (SELECT COUNT(*) FROM regions WHERE risk_level >= 8) as count_siaga
                    
                  FROM national_statistics n
                  ORDER BY n.id DESC
                  LIMIT 1";

        $this->db->query($query);
        $result = $this->db->single();

        // Fallback JIKA data kosong agar view tidak error
        if (!$result) {
            return [
                'inflation_rate' => 0,
                'avg_price_rice' => 0,   // Tambahan agar tidak error
                'avg_price_oil' => 0,    // Tambahan agar tidak error
                'avg_price_chili' => 0,  // Tambahan agar tidak error
                'dominant_conflict' => '-', // Tambahan agar tidak error
                'intelligence_analysis' => 'Menunggu data...', // Tambahan agar tidak error
                'emotion_index' => 50,
                'total_disasters' => 0,
                'count_aman' => 0,
                'count_waspada' => 0,
                'count_siaga' => 0,
                'overall_status' => 'AMAN',
                'ai_recommendation' => 'Data belum tersedia.',
                'last_updated' => date('Y-m-d H:i:s')
            ];
        }

        return $result;
    }

    public function getLatestDisasters()
    {
        try {
            $this->db->query("SELECT event_name, location, created_at FROM disaster_logs ORDER BY created_at DESC LIMIT 5");
            $results = $this->db->resultSet();
            return $results ? $results : [];
        } catch (PDOException $e) {
            return [];
        }
    }

    private function getStatus($score)
    {
        if ($score <= 4)
            return 'Aman';
        if ($score <= 7)
            return 'Waspada';
        return 'Siaga 1';
    }

    private function getColor($score)
    {
        if ($score <= 4)
            return '#28a745';
        if ($score <= 7)
            return '#ffc107';
        return '#dc3545';
    }
    public function getTopIssues()
    {
        // Query PostgreSQL untuk memecah array active_issues dan menghitung frekuensinya
        $query = "SELECT issue, COUNT(*) as count
                  FROM (
                      SELECT unnest(active_issues) as issue 
                      FROM social_security
                  ) sub
                  WHERE issue IS NOT NULL AND issue != ''
                  GROUP BY issue
                  ORDER BY count DESC
                  LIMIT 4"; // Ambil 4 isu teratas
        
        $this->db->query($query);
        return $this->db->resultSet();
    }
}