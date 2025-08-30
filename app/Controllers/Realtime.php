<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Controller;

class Realtime extends Controller
{
    use ResponseTrait;

    private $url;

    public function __construct()
    {
        $this->url = getenv('realtime.url'); // ambil dari .env
    }

    /**
     * Ambil dan parse data dari API realtime
     */
    private function getRealtimeData()
    {
        $response = file_get_contents($this->url);
        if (!$response) {
            return null;
        }

        $data = json_decode($response, true);
        if (!$data || !isset($data['DATA'])) {
            return null;
        }

        $rows = explode("\n", trim($data['DATA']));
        $result = [];

        foreach ($rows as $row) {
            $cols = explode("|", $row);
            if (count($cols) < 3) continue;

            $result[] = [
                "nim"  => $cols[0],
                "nama" => $cols[1],
                "ymd"  => $cols[2],
            ];
        }

        return $result;
    }

    /**
     * Cari berdasarkan NIM
     */
    public function byNim($nim)
    {
        $data = $this->getRealtimeData();
        if (!$data) return $this->fail("Gagal ambil data realtime");

        $found = array_values(array_filter($data, fn($r) => $r['nim'] == $nim));
        return $this->respond($found);
    }

    /**
     * Cari berdasarkan Nama (LIKE / contains)
     */
    public function byName($name)
    {
        $data = $this->getRealtimeData();
        if (!$data) return $this->fail("Gagal ambil data realtime");

        $found = array_values(array_filter($data, fn($r) => stripos($r['nama'], $name) !== false));
        return $this->respond($found);
    }

    /**
     * Cari berdasarkan Tanggal YMD (exact match)
     */
    public function byYmd($ymd)
    {
        $data = $this->getRealtimeData();
        if (!$data) return $this->fail("Gagal ambil data realtime");

        $found = array_values(array_filter($data, fn($r) => $r['ymd'] == $ymd));
        return $this->respond($found);
    }
}
