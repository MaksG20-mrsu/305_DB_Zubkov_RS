<?php
try {
    $db = new PDO('sqlite:../data/database.db');
} catch (PDOException $e) { die("Error: " . $e->getMessage()); }

$studentId = $_GET['student_id'] ?? die("Не указан ID студента");

$stmt = $db->prepare("SELECT full_name FROM Students WHERE id = ?");
$stmt->execute([$studentId]);
$student = $stmt->fetch();

$stmt = $db->prepare("
    SELECT e.id, sub.name as subject, e.exam_date, e.grade 
    FROM Exams e 
    JOIN Subjects sub ON e.subject_id = sub.id 
    WHERE e.student_id = ? 
    ORDER BY e.exam_date
");
$stmt->execute([$studentId]);
$exams = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ru">
<head><title>Экзамены</title></head>
<body>
    <h2>Результаты экзаменов: <?= htmlspecialchars($student['full_name']) ?></h2>
    <table border="1" cellpadding="5">
        <tr><th>Дата</th><th>Дисциплина</th><th>Оценка</th><th>Действия</th></tr>
        <?php foreach ($exams as $exam): ?>
        <tr>
            <td><?= $exam['exam_date'] ?></td>
            <td><?= htmlspecialchars($exam['subject']) ?></td>
            <td><?= $exam['grade'] ?></td>
            <td>
                <a href="exam_form.php?id=<?= $exam['id'] ?>&student_id=<?= $studentId ?>">Ред.</a>
                <a href="exam_delete.php?id=<?= $exam['id'] ?>&student_id=<?= $studentId ?>">Удал.</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <br>
    <a href="exam_form.php?student_id=<?= $studentId ?>">Добавить экзамен</a>
    <br><br>
    <a href="index.php">Вернуться к списку студентов</a>
</body>
</html>