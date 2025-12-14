<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Services\WeatherService;

class ExternalController extends Controller
{
    public function weather(Request $request, WeatherService $weatherService)
    {
        $city = $request->query('city', 'Quito');
        $result = $weatherService->getCurrentByCity($city);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Error obteniendo el clima',
            ], 500);
        }

        $data = $result['data'];
        $text = sprintf(
            'Clima actual de %s: %s°C, %s.',
            $data['city'],
            number_format((float) $data['temp'], 0),
            $data['description']
        );

        return response()->json([
            'success' => true,
            'data' => $data,
            'text' => $text,
        ]);
    }
}
