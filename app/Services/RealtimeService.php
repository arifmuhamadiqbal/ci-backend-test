<?php namespace App\Services;

class RealtimeService
{
    public function fetch(): array
    {
        $url = env('realtime.url');
        $client = \Config\Services::curlrequest(['timeout' => 10]);
        $res = $client->get($url);
        $data = json_decode($res->getBody(), true);

        // Pastikan bentuknya array of rows; jika tidak, sesuaikan aksesnya
        return is_array($data) ? $data : [];
    }

    public function findByKey(string $key, $value): array
    {
        $rows = $this->fetch();
        return array_values(array_filter($rows, function($row) use ($key, $value) {
            return isset($row[$key]) && (string)$row[$key] === (string)$value;
        }));
    }
}
