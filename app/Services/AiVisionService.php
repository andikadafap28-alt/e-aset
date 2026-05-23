<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Exception;

class AiVisionService
{
    public function extractStockCard(UploadedFile $image)
    {
        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) {
            throw new Exception('GEMINI_API_KEY is not set in .env');
        }

        $base64Image = base64_encode(file_get_contents($image->getRealPath()));
        $mimeType = $image->getMimeType();

        $prompt = 'Ekstrak data dari gambar kartu stok ini. Temukan baris transaksi dan kembalikan HANYA dalam format JSON array of objects. Key yang harus ada: "tanggal" (format YYYY-MM-DD), "jenis_transaksi" (masuk/keluar), "jumlah" (integer), dan "keterangan" (string). Jangan tambahkan teks markdown atau penjelasan apapun selain JSON mentah.';

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                        [
                            'inline_data' => [
                                'mime_type' => $mimeType,
                                'data' => $base64Image
                            ]
                        ]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.1,
                'topK' => 32,
                'topP' => 1,
            ]
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}", $payload);

        if (!$response->successful()) {
            throw new Exception('Failed to connect to Gemini API: ' . $response->body());
        }

        $result = $response->json();
        
        if (!isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            throw new Exception('Invalid response format from Gemini API');
        }

        $text = $result['candidates'][0]['content']['parts'][0]['text'];
        
        // Clean markdown blocks if any
        $text = str_replace(['```json', '```'], '', $text);
        
        $json = json_decode(trim($text), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('AI did not return valid JSON. Output was: ' . $text);
        }

        return $json;
    }
}
