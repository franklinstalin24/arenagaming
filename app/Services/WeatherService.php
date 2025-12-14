<?php

namespace App\Services;

use GuzzleHttp\Client;

class WeatherService
{
    protected Client $client;
    protected string $apiKey;
    protected string $baseUrl;
    protected string $units;
    protected string $lang;

    public function __construct()
    {
        // verify=false: evita errores de certificado en entornos locales sin CA configurada.
        // Úsalo solo en desarrollo.
        $this->client = new Client(['timeout' => 5, 'verify' => false]);
        $config = config('services.weather');
        $this->apiKey = $config['key'] ?? '';
        $this->baseUrl = $config['base_url'] ?? 'https://api.openweathermap.org/data/2.5/weather';
        $this->units = $config['units'] ?? 'metric';
        $this->lang = $config['lang'] ?? 'es';
    }

    public function getCurrentByCity(string $city): array
    {
        if (empty($this->apiKey)) {
            return [
                'success' => false,
                'message' => 'Weather API key not configured',
            ];
        }

        try {
            $res = $this->client->get($this->baseUrl, [
                'query' => [
                    'q' => $city,
                    'appid' => $this->apiKey,
                    'units' => $this->units,
                    'lang'  => $this->lang,
                ],
            ]);
            $data = json_decode((string) $res->getBody(), true);

            if (!is_array($data)) {
                return ['success' => false, 'message' => 'Invalid weather response'];
            }

            $temp = $data['main']['temp'] ?? null;
            $desc = $data['weather'][0]['description'] ?? null;
            $cityName = $data['name'] ?? $city;

            return [
                'success' => true,
                'data' => [
                    'city' => $cityName,
                    'temp' => $temp,
                    'description' => $desc,
                    'raw' => $data,
                ],
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Weather API error: ' . $e->getMessage(),
            ];
        }
    }
}
