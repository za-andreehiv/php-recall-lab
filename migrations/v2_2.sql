CREATE TABLE IF NOT EXISTS exercise_details (
  exercise_id INT UNSIGNED NOT NULL,
  goal TEXT NOT NULL,
  theory_text LONGTEXT NOT NULL,
  input_data MEDIUMTEXT NOT NULL,
  expected_result MEDIUMTEXT NOT NULL,
  constraints_text TEXT NOT NULL,
  self_check TEXT NOT NULL,
  hint1 TEXT NULL,
  hint2 TEXT NULL,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (exercise_id),
  CONSTRAINT fk_exercise_details_exercise FOREIGN KEY (exercise_id)
    REFERENCES course_exercises(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
