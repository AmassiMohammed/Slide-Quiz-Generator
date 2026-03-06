<?php

namespace QuizGen;

class AiService
{
    private const API_URL = 'https://api.anthropic.com/v1/messages';
    private const MODEL   = 'claude-sonnet-4-20250514';
    private const TIMEOUT = 90;

    private string $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Sendet einen Prompt an Claude und gibt den Text zurück.
     *
     * @throws \RuntimeException Bei API-Fehler
     */
    public function complete(string $prompt, int $maxTokens = 4096): string
    {
        $payload = json_encode([
            'model'      => self::MODEL,
            'max_tokens' => $maxTokens,
            'messages'   => [
                ['role' => 'user', 'content' => $prompt]
            ],
        ]);

        $ch = curl_init(self::API_URL);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_TIMEOUT        => self::TIMEOUT,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'x-api-key: ' . $this->apiKey,
                'anthropic-version: 2023-06-01',
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
        return $data['content'][0]['text'] ?? '';
    }
}