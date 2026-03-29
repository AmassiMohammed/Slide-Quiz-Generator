<?php

namespace QuizGen;

class QuizGenerator
{
    private AiService $ai;

    public function __construct(AiService $ai)
    {
        $this->ai = $ai;
    }

    /**
     * Generiert Quiz-Fragen aus dem gegebenen Text.
     *
     * @param  string $text            Quelltext (max. ~12 000 Zeichen)
     * @param  int    $questionCount   10 oder 20
     * @param  string $difficulty      easy | medium | hard
     * @return array                   Array von Frage-Arrays
     * @throws \RuntimeException
     */
    public function generate(string $text, int $questionCount = 10, string $difficulty = 'medium'): array
    {
        $diffLabel = match ($difficulty) {
            'easy'  => 'einfach (Grundbegriffe, Definitionen)',
            'hard'  => 'schwer (Analyse, Zusammenhänge, Details)',
            default => 'mittelschwer (Verständnis & Anwendung)',
        };

        $prompt = <<<PROMPT
Du bist ein professioneller Lerncoach. Erstelle genau {$questionCount} Multiple-Choice-Fragen auf Basis des folgenden Textes.

Schwierigkeitsgrad: {$diffLabel}

PFLICHTREGELN:
- Jede Frage hat genau 4 Antwortmöglichkeiten (A, B, C, D)
- Genau eine Antwort ist korrekt
- Fragen basieren ausschließlich auf dem Inhalt des Textes
- Antworte NUR mit einem gültigen JSON-Objekt, ohne Markdown-Code-Blöcke

EXAKTES JSON-FORMAT:
{
  "questions": [
    {
      "question": "Fragetext?",
      "options": {
        "A": "Antwort A",
        "B": "Antwort B",
        "C": "Antwort C",
        "D": "Antwort D"
      },
      "correct": "A",
      "explanation": "Kurze Erklärung warum A richtig ist."
    }
  ]
}

TEXT:
{$text}
PROMPT;

        $raw  = $this->ai->complete($prompt, 4096);
        $json = $this->parseJson($raw);

        if (empty($json['questions'])) {
            throw new \RuntimeException('Keine Fragen generiert. Bitte erneut versuchen.');
        }

        // Validieren & bereinigen
        return array_map([$this, 'sanitizeQuestion'], $json['questions']);
    }

    // ── PRIVATE ──────────────────────────────────────────────

    private function parseJson(string $raw): array
    {
        // Markdown-Backticks entfernen, falls vorhanden
        $clean = preg_replace('/^```(?:json)?\s*|\s*```$/s', '', trim($raw));
        $data  = json_decode($clean, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('KI hat kein gültiges JSON zurückgegeben.');
        }

        return $data;
    }

    private function sanitizeQuestion(array $q): array
    {
        return [
            'question'    => htmlspecialchars($q['question']    ?? '', ENT_QUOTES, 'UTF-8'),
            'options'     => [
                'A' => htmlspecialchars($q['options']['A'] ?? '', ENT_QUOTES, 'UTF-8'),
                'B' => htmlspecialchars($q['options']['B'] ?? '', ENT_QUOTES, 'UTF-8'),
                'C' => htmlspecialchars($q['options']['C'] ?? '', ENT_QUOTES, 'UTF-8'),
                'D' => htmlspecialchars($q['options']['D'] ?? '', ENT_QUOTES, 'UTF-8'),
            ],
            'correct'     => strtoupper($q['correct'] ?? 'A'),
            'explanation' => htmlspecialchars($q['explanation'] ?? '', ENT_QUOTES, 'UTF-8'),
        ];
    }
}