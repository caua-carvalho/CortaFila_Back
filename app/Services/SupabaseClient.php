<?php

namespace App\Services;

use App\Utils\Debug;

class SupabaseClient
{
    private string $url;
    private string $key;

    public function __construct()
    {
        $this->url = rtrim($_ENV['SUPABASE_URL'] ?? '', '/');
        $this->key = $_ENV['SUPABASE_KEY'] ?? '';

        if (!$this->url || !$this->key) {
            throw new \Exception("Supabase URL ou KEY não configurados no .env");
        }
    }

    private function request(string $method, string $endpoint, array $body = []): array
    {
        $fullUrl = "{$this->url}/rest/v1/{$endpoint}";

        Debug::log([
            "type"   => "SUPABASE_REQUEST",
            "method" => strtoupper($method),
            "url"    => $fullUrl,
            "body"   => $body
        ]);

        $curl = curl_init();

        $options = [
            CURLOPT_URL            => $fullUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                "apikey: {$this->key}",
                "Authorization: Bearer {$this->key}",
                "Content-Type: application/json",
                "Prefer: return=representation"
            ],
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2
        ];

        if (!empty($body)) {
            $options[CURLOPT_POSTFIELDS] = json_encode($body);
        }

        curl_setopt_array($curl, $options);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error    = curl_error($curl);

        curl_close($curl);

        $json = json_decode($response, true);

        Debug::log([
            "type"          => "SUPABASE_RESPONSE",
            "status"        => $httpCode,
            "response_raw"  => $response,
            "response_json" => $json,
            "curl_error"    => $error
        ]);

        return [
            'status' => $httpCode,
            'error'  => $error ?: null,
            'data'   => $json
        ];
    }

    public function select(string $table, array $query = []): array
    {
        $endpoint = $table;

        if (!empty($query)) {
            $endpoint .= '?' . http_build_query($query);
        }

        return $this->request('GET', $endpoint);
    }

    public function insert(string $table, array $data): array
    {
        return $this->request('POST', $table, $data);
    }

    public function rpc(string $func, array $params = []): array
    {
        return $this->request('POST', "rpc/{$func}", $params);
    }

    public function update(string $table, array $data, string $filter): array
    {
        return $this->request('PATCH', "{$table}?{$filter}", $data);
    }

    public function delete(string $table, string $filter): array
    {
        return $this->request('DELETE', "{$table}?{$filter}");
    }
}
