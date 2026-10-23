CREATE TABLE IF NOT EXISTS course_sections (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(20) NOT NULL UNIQUE,
  title VARCHAR(180) NOT NULL,
  description TEXT NULL,
  sort_order SMALLINT UNSIGNED NOT NULL,
  source_url VARCHAR(500) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- SPLIT --
CREATE TABLE IF NOT EXISTS course_lessons (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  section_id INT UNSIGNED NOT NULL,
  slug VARCHAR(120) NOT NULL UNIQUE,
  title VARCHAR(180) NOT NULL,
  concept_summary TEXT NOT NULL,
  sort_order SMALLINT UNSIGNED NOT NULL,
  source_url VARCHAR(500) NULL,
  CONSTRAINT fk_course_lessons_section FOREIGN KEY (section_id) REFERENCES course_sections(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- SPLIT --
CREATE TABLE IF NOT EXISTS course_exercises (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  lesson_id INT UNSIGNED NOT NULL,
  code VARCHAR(30) NOT NULL UNIQUE,
  exercise_type ENUM('php','sql','theory','project') NOT NULL DEFAULT 'php',
  title VARCHAR(220) NOT NULL,
  prompt TEXT NOT NULL,
  starter_code MEDIUMTEXT NULL,
  checker_config LONGTEXT NOT NULL,
  difficulty TINYINT UNSIGNED NOT NULL DEFAULT 1,
  sort_order SMALLINT UNSIGNED NOT NULL,
  source_url VARCHAR(500) NULL,
  KEY idx_course_exercises_lesson (lesson_id, sort_order),
  CONSTRAINT fk_course_exercises_lesson FOREIGN KEY (lesson_id) REFERENCES course_lessons(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- SPLIT --
CREATE TABLE IF NOT EXISTS exercise_submissions (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  exercise_id INT UNSIGNED NOT NULL,
  answer MEDIUMTEXT NOT NULL,
  status ENUM('passed','needs_work','manual_accepted') NOT NULL,
  result_json LONGTEXT NOT NULL,
  error_fingerprint CHAR(64) NULL,
  manual_note TEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_submission_user_exercise (user_id, exercise_id, created_at),
  CONSTRAINT fk_submission_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_submission_exercise FOREIGN KEY (exercise_id) REFERENCES course_exercises(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- SPLIT --
CREATE TABLE IF NOT EXISTS user_exercise_progress (
  user_id INT UNSIGNED NOT NULL,
  exercise_id INT UNSIGNED NOT NULL,
  best_status ENUM('not_started','needs_work','passed') NOT NULL DEFAULT 'not_started',
  attempts SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  last_attempt_at DATETIME NULL,
  accepted_at DATETIME NULL,
  PRIMARY KEY (user_id, exercise_id),
  KEY idx_exercise_progress_status (user_id, best_status),
  CONSTRAINT fk_exercise_progress_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_exercise_progress_exercise FOREIGN KEY (exercise_id) REFERENCES course_exercises(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- SPLIT --
CREATE TABLE IF NOT EXISTS mistake_cards (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  exercise_id INT UNSIGNED NOT NULL,
  error_key CHAR(64) NOT NULL,
  question TEXT NOT NULL,
  answer TEXT NOT NULL,
  step_index TINYINT UNSIGNED NOT NULL DEFAULT 0,
  due_at DATETIME NOT NULL,
  last_grade TINYINT UNSIGNED NULL,
  last_reviewed_at DATETIME NULL,
  active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_mistake_user_error (user_id, error_key),
  KEY idx_mistake_due (user_id, active, due_at),
  CONSTRAINT fk_mistake_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_mistake_exercise FOREIGN KEY (exercise_id) REFERENCES course_exercises(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
