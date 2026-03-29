-- ══════════════════════════════════════════════════
--  QuizGen – Datenbankstruktur
--  Ausführen: mysql -u root -p < sql/database.sql
-- ══════════════════════════════════════════════════

CREATE DATABASE IF NOT EXISTS quizgen_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE quizgen_db;

-- ── Benutzer & Berechtigungen ─────────────────────
-- Passe 'deinPasswort' an (gleich wie in config/database.php)!
CREATE USER IF NOT EXISTS 'quizgen_user'@'localhost'
  IDENTIFIED BY 'deinPasswort';

GRANT ALL PRIVILEGES ON quizgen_db.* TO 'quizgen_user'@'localhost';
FLUSH PRIVILEGES;

-- ── Ergebnisse ────────────────────────────────────
CREATE TABLE IF NOT EXISTS results (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  score       TINYINT UNSIGNED                     NOT NULL COMMENT 'Prozent 0–100',
  correct     TINYINT UNSIGNED                     NOT NULL,
  total       TINYINT UNSIGNED                     NOT NULL,
  difficulty  ENUM('easy', 'medium', 'hard')       NOT NULL DEFAULT 'medium',
  created_at  DATETIME                             NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_created_at (created_at)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;