# QuizGen 🎯
**KI-gestützter Quiz-Generator aus PDF & Text**

Ein vollständiges Web-Projekt, das mithilfe von KI automatisch Multiple-Choice-Quizze aus hochgeladenen PDF-Dateien oder eingegebenem Text generiert.

---

## 🚀 Live Demo
> Lokal verfügbar unter: `http://localhost/Slide-Quiz-Generator/public/index.php`

---

## ✨ Features

- 📄 **PDF-Upload** – Automatische Textextraktion aus PDF-Dateien (bis 10 MB)
- ✏️ **Texteingabe** – Direkte Eingabe von beliebigem Lernstoff
- 🤖 **KI-Generierung** – Fragen werden via Groq API (LLaMA 3.1) generiert
- 🔢 **Flexible Fragenanzahl** – 10 oder 20 Fragen wählbar
- 🎚️ **Schwierigkeitsgrade** – Einfach / Mittel / Schwer
- ⌨️ **Keyboard Shortcuts** – A/B/C/D oder 1/2/3/4 + Enter zum Navigieren
- 📊 **Ergebnisauswertung** – Detaillierte Auswertung mit Erklärungen pro Frage
- 💾 **Verlauf** – Alle Quiz-Ergebnisse werden in der Datenbank gespeichert
- 📱 **Responsives Design** – Funktioniert auf Desktop und Mobile

---

## 🛠️ Tech Stack

| Bereich | Technologie |
|---|---|
| Frontend | HTML5, CSS3 (Custom Properties, CSS Grid, Flexbox) |
| Backend | PHP 8.2 |
| Datenbank | MariaDB / MySQL |
| KI-API | Groq API (LLaMA 3.1 8B) |
| PDF-Parsing | pdftotext (Poppler) + smalot/pdfparser |
| Dependency Manager | Composer (PSR-4 Autoloading) |
| Versionskontrolle | Git / GitHub |

---

## 📁 Projektstruktur

```
Slide-Quiz-Generator/
├── config/
│   └── database.php        # Datenbankkonfiguration
├── public/
│   ├── css/
│   │   └── style.css       # Komplettes Stylesheet
│   ├── index.php           # Startseite (Upload & Texteingabe)
│   ├── upload.php          # Backend: Verarbeitung & KI-Generierung
│   ├── quiz.php            # Quiz-Seite
│   ├── result.php          # Ergebnisseite
│   └── history.php         # Quiz-Verlauf
├── src/
│   ├── AiService.php       # Groq API Integration
│   ├── PdfParser.php       # PDF Textextraktion
│   └── QuizGenerator.php   # Quiz-Logik & Prompt Engineering
├── sql/
│   └── database.sql        # Datenbankstruktur
├── templates/
│   ├── header.php          # Wiederverwendbarer Header
│   └── footer.php          # Wiederverwendbarer Footer
├── .env                    # API Keys (nicht im Repository)
├── .gitignore
└── composer.json
```

---

## ⚙️ Installation

### Voraussetzungen
- PHP 8.0+
- MySQL / MariaDB
- Composer
- XAMPP (oder anderer lokaler Server)

### Setup

**1. Repository klonen:**
```bash
git clone https://github.com/AmassiMohammed/Slide-Quiz-Generator.git
cd Slide-Quiz-Generator
```

**2. Dependencies installieren:**
```bash
composer install
```

**3. Datenbank einrichten** – in phpMyAdmin das SQL ausführen:
```sql
-- Inhalt von sql/database.sql ausführen
```

**4. Umgebungsvariablen konfigurieren:**
```bash
# .env Datei erstellen
API_KEY=dein_groq_api_key_hier
```

**5. Datenbank-Zugangsdaten anpassen** in `config/database.php`:
```php
'user'     => 'root',
'password' => '',
'dbname'   => 'quizgen_db',
```

**6. Im Browser öffnen:**
```
http://localhost/Slide-Quiz-Generator/public/index.php
```

---

## 🔒 Sicherheit

- **XSS-Schutz** – alle Ausgaben mit `htmlspecialchars()` escaped
- **SQL Injection** – Prepared Statements bei allen DB-Queries
- **Datei-Validierung** – Typ- und Größenprüfung beim Upload
- **Shell-Injection** – `escapeshellarg()` beim pdftotext-Aufruf
- **API Keys** – werden in `.env` gespeichert, nicht im Repository

---

## 📸 Screenshots

> *Quiz-Generator Startseite, Quiz-Ansicht und Ergebnisseite*

---

## 👤 Autor

**Mohammed Amassi**  
Informatik-Student @ FH Technikum Wien  
GitHub: [@AmassiMohammed](https://github.com/AmassiMohammed)

---

## 📄 Lizenz

## Lizenz

Dieses Projekt wurde als persönliches Softwareprojekt von Amassi Mohammed entwickelt.

Die Idee, Konzeption und Implementierung stammen vollständig vom Autor.
Das Projekt steht in keiner offiziellen Verbindung zur FH Technikum Wien
und wurde unabhängig von Studienprojekten erstellt.
