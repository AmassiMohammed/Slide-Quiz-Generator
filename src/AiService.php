<?php

namespace QuizGen;

class AiService
{
    // Google Gemini – KOSTENLOS!
    private const API_URL = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';
    private const TIMEOUT = 90;

    private string $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Sendet einen Prompt an Gemini und gibt den Text zurück.
     *
     * @throws \RuntimeException Bei API-Fehler
     */
    public function complete(string $prompt, int $maxTokens = 4096): string
    {
        $url = self::API_URL . '?key=' . urlencode($this->apiKey);

        $payload = json_encode([
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'maxOutputTokens' => $maxTokens,
                'temperature'     => 0.7,
            ]
        ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_TIMEOUT        => self::TIMEOUT,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($curlErr) {
            throw new \RuntimeException('Netzwerkfehler: ' . $curlErr);
        }

        if ($httpCode !== 200) {
            $body = json_decode($response, true);
            $msg  = $body['error']['message'] ?? "HTTP {$httpCode}";
            throw new \RuntimeException('API-Fehler: ' . $msg);
        }

        $data = json_decode($response, true);

        // Gemini Response-Format
        return $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
    }
}