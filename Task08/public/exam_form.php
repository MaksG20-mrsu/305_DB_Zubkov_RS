<?php
try {
    $db = new PDO('sqlite:../data/database.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) { die("Error: " . $e->getMessage()); }

$examId = $_GET['id'] ?? null;
$preSelectedStudentId = $_GET['student_id'] ?? null;
$exam = ['exam_date' => date('Y-m-d'), 'grade' => 5, 'subject_id' => '', 'student_id' => $preSelectedStudentId];

if ($examId) {
    $stmt = $db->prepare("SELECT * FROM Exams WHERE id = ?");
    $stmt->execute([$examId]);
    $exam = $stmt->fetch(PDO::FETCH_ASSOC);
}

$currentStudentId = $_POST['student_id'] ?? $exam['student_id'];

$currentGroupId = null;
if ($currentStudentId) {
    $stmt = $db->prepare("SELECT group_id, group_id as gid FROM Students WHERE id = ?");
    $stmt->execute([$currentStudentId]);
    $res = $stmt->fetch();
    if ($res) $currentGroupId = $res['gid'];
}

if (isset($_POST['group_select'])) {
    $currentGroupId = $_POST['group_select'];
}

$groups = $db->query("SELECT * FROM Groups ORDER BY number")->fetchAll(PDO::FETCH_ASSOC);

$students = [];
$subjects = [];
if ($currentGroupId) {
    $stmt = $db->prepare("SELECT * FROM Students WHERE group_id = ? ORDER BY full_name");
    $stmt->execute([$currentGroupId]);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $db->prepare("SELECT major FROM Groups WHERE id = ?");
    $stmt->execute([$currentGroupId]);
    $major = $stmt->fetchColumn();

    $stmt = $db->prepare("SELECT * FROM Subjects WHERE major = ? ORDER BY year, name");
    $stmt->execute([$major]);
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $data = [
        $_POST['student_id'],
        $_POST['subject_id'],
        $_POST['exam_date'],
        $_POST['grade']
    ];
    if ($examId) {
        $stmt = $db->prepare("UPDATE Exams SET student_id=?, subject_id=?, exam_date=?, grade=? WHERE id=?");
        $stmt->execute([...$data, $examId]);
    } else {
        $stmt = $db->prepare("INSERT INTO Exams (student_id, subject_id, exam_date, grade) VALUES (?, ?, ?, ?)");
        $stmt->execute($data);
    }
    header("Location: exams.php?student_id=" . $_POST['student_id']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head><title>Экзамен</title></head>
<body>
    <h2><?= $examId ? 'Редактирование экзамена' : 'Новый экзамен' ?></h2>

    <form method="POST" id="mainForm">
        <p>Группа: 
            <select name="group_select" onchange="document.getElementById('mainForm').submit()">
                <option value="">-- Выберите группу --</option>
                <?php foreach ($groups as $g): ?>
                    <option value="<?= $g['id'] ?>" <?= $currentGroupId == $g['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($g['number']) ?> (<?= $g['major'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </p>

        <p>Студент:
            <select name="student_id" required>
                <?php foreach ($students as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= $currentStudentId == $s['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($s['full_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>

        <p>Дисциплина:
            <select name="subject_id" required>
                <?php foreach ($subjects as $sub): ?>
                    <option value="<?= $sub['id'] ?>" <?= $exam['subject_id'] == $sub['id'] ? 'selected' : '' ?>>
                        <?= $sub['year'] ?> курс - <?= htmlspecialchars($sub['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>

        <p>Дата: <input type="date" name="exam_date" value="<?= $exam['exam_date'] ?>" required></p>
        <p>Оценка: <input type="number" name="grade" min="2" max="5" value="<?= $exam['grade'] ?>" required></p>

        <button type="submit" name="save">Сохранить</button>
        <?php if($currentStudentId): ?>
            <a href="exams.php?student_id=<?= $currentStudentId ?>">Отмена</a>
        <?php else: ?>
            <a href="index.php">Отмена</a>
        <?php endif; ?>
    </form>
</body>
</html>