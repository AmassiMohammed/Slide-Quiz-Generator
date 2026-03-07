<?php

namespace QuizGen;

class AiService
{
    // Groq – Kostenlos & sehr schnell!
    private const API_URL = 'https://api.groq.com/openai/v1/chat/completions';
    private const MODEL   = 'llama-3.1-8b-instant'; // Kostenlos
    private const TIMEOUT = 60;

    private string $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Sendet einen Prompt an Groq und gibt den Text zurück.
     *
     * @throws \RuntimeException Bei API-Fehler
     */
    public function complete(string $prompt, int $maxTokens = 4096): string
    {
        $payload = json_encode([
            'model'       => self::MODEL,
            'max_tokens'  => $maxTokens,
            'temperature' => 0.7,
            'messages'    => [
                [
                    'role'    => 'system',
                    'content' => 'Du bist ein professioneller Lerncoach. Antworte immer nur mit validem JSON, ohne Markdown-Backticks oder zusätzlichen Text.'
                ],
                [
                    'role'    => 'user',
                    'content' => $prompt
                ]
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
                'Authorization: Bearer ' . $this->apiKey,
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

        // Groq verwendet OpenAI-Format
        return $data['choices'][0]['message']['content'] ?? '';
    }
}