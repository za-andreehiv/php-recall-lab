<?php
declare(strict_types=1);

function course_overview(int $userId): array
{
    $stmt = db()->prepare("SELECT
        (SELECT COUNT(*) FROM course_exercises) total,
        (SELECT COUNT(*) FROM user_exercise_progress WHERE user_id=? AND best_status='passed') accepted,
        (SELECT COUNT(*) FROM user_exercise_progress WHERE user_id=? AND best_status='needs_work') needs_work,
        (SELECT COUNT(*) FROM mistake_cards WHERE user_id=? AND active=1 AND due_at<=NOW()) due_mistakes");
    $stmt->execute([$userId,$userId,$userId]);
    return $stmt->fetch() ?: ['total'=>0,'accepted'=>0,'needs_work'=>0,'due_mistakes'=>0];
}

function course_sections_with_progress(int $userId): array
{
    $stmt = db()->prepare("SELECT s.*,
        COUNT(DISTINCT e.id) exercise_count,
        COUNT(DISTINCT CASE WHEN p.best_status='passed' THEN e.id END) accepted_count,
        COUNT(DISTINCT CASE WHEN p.best_status='needs_work' THEN e.id END) needs_work_count
      FROM course_sections s
      LEFT JOIN course_lessons l ON l.section_id=s.id
      LEFT JOIN course_exercises e ON e.lesson_id=l.id
      LEFT JOIN user_exercise_progress p ON p.exercise_id=e.id AND p.user_id=?
      GROUP BY s.id ORDER BY s.sort_order");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

function course_section_unlocked(int $userId, int $sectionId): bool
{
    $stmt = db()->prepare('SELECT sort_order FROM course_sections WHERE id=?');
    $stmt->execute([$sectionId]);
    $order = (int)$stmt->fetchColumn();
    if ($order <= 1) {
        return true;
    }

    $stmt = db()->prepare("SELECT COUNT(DISTINCT e.id) total,
        COUNT(DISTINCT CASE WHEN p.best_status='passed' THEN e.id END) accepted
      FROM course_sections s
      JOIN course_lessons l ON l.section_id=s.id
      JOIN course_exercises e ON e.lesson_id=l.id
      LEFT JOIN user_exercise_progress p ON p.exercise_id=e.id AND p.user_id=?
      WHERE s.sort_order=?");
    $stmt->execute([$userId,$order-1]);
    $row = $stmt->fetch();
    $total = (int)($row['total'] ?? 0);
    $accepted = (int)($row['accepted'] ?? 0);
    return $total > 0 && $accepted >= (int)ceil($total * 0.8);
}

function course_exercise_unlocked(int $userId, int $exerciseId): bool
{
    $stmt = db()->prepare('SELECT e.sort_order,l.section_id FROM course_exercises e
        JOIN course_lessons l ON l.id=e.lesson_id WHERE e.id=?');
    $stmt->execute([$exerciseId]);
    $exercise = $stmt->fetch();
    if (!$exercise || !course_section_unlocked($userId,(int)$exercise['section_id'])) {
        return false;
    }

    $batch = intdiv((int)$exercise['sort_order'] - 1, 10);
    if ($batch === 0) {
        return true;
    }
    $from = ($batch - 1) * 10 + 1;
    $to = $from + 9;
    $stmt = db()->prepare("SELECT COUNT(DISTINCT CASE WHEN p.best_status='passed' THEN e.id END)
      FROM course_exercises e
      JOIN course_lessons l ON l.id=e.lesson_id
      LEFT JOIN user_exercise_progress p ON p.exercise_id=e.id AND p.user_id=?
      WHERE l.section_id=? AND e.sort_order BETWEEN ? AND ?");
    $stmt->execute([$userId,$exercise['section_id'],$from,$to]);
    return (int)$stmt->fetchColumn() >= 8;
}

function next_course_exercise(int $userId): ?array
{
    $sections = course_sections_with_progress($userId);
    foreach ($sections as $section) {
        if (!course_section_unlocked($userId,(int)$section['id'])) {
            break;
        }
        $stmt = db()->prepare("SELECT e.id FROM course_exercises e
          JOIN course_lessons l ON l.id=e.lesson_id
          LEFT JOIN user_exercise_progress p ON p.exercise_id=e.id AND p.user_id=?
          WHERE l.section_id=? AND COALESCE(p.best_status,'not_started')<>'passed'
          ORDER BY e.sort_order LIMIT 30");
        $stmt->execute([$userId,$section['id']]);
        foreach ($stmt->fetchAll() as $row) {
            if (course_exercise_unlocked($userId,(int)$row['id'])) {
                $load = db()->prepare("SELECT e.*,l.title lesson_title,l.concept_summary,s.id section_id,s.code section_code,s.title section_title
                  FROM course_exercises e JOIN course_lessons l ON l.id=e.lesson_id
                  JOIN course_sections s ON s.id=l.section_id WHERE e.id=?");
                $load->execute([$row['id']]);
                return $load->fetch() ?: null;
            }
        }
    }
    return null;
}
