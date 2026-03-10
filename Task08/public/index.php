<?php
try {
    $db = new PDO('sqlite:../data/database.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения: " . htmlspecialchars($e->getMessage()));
}

$groupsStmt = $db->query("SELECT id, number FROM Groups WHERE graduation_year >= strftime('%Y', 'now') ORDER BY number");
$groups = $groupsStmt->fetchAll(PDO::FETCH_ASSOC);

$selectedGroupId = $_GET['group_id'] ?? null;

$query = "
    SELECT s.id, g.number as group_number, g.major, s.full_name, s.gender, s.birth_date, s.student_ticket_number 
    FROM Students s 
    JOIN Groups g ON s.group_id = g.id 
    WHERE g.graduation_year >= strftime('%Y', 'now')
";
$params = [];

if ($selectedGroupId) {
    $query .= " AND g.id = ?";
    $params[] = $selectedGroupId;
}

$query .= " ORDER BY g.number, s.full_name";
$stmt = $db->prepare($query);
$stmt->execute($params);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Список студентов</title>
    <style>
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .btn { padding: 5px 10px; text-decoration: none; border-radius: 4px; border: 1px solid #ccc; background: #eee; color: #333; }
        .btn-primary { background: #007bff; color: white; border-color: #007bff; }
        .btn-danger { background: #dc3545; color: white; border-color: #dc3545; }
        .actions { white-space: nowrap; }
    </style>
</head>
<body>

<h1>Список студентов</h1>

<form method="GET">
    <label>Фильтр по группе:</label>
    <select name="group_id" onchange="this.form.submit()">
        <option value="">Все группы</option>
        <?php foreach ($groups as $group): ?>
            <option value="<?= $group['id'] ?>" <?= $selectedGroupId == $group['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($group['number']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>

<table>
    <thead>
        <tr>
            <th>Группа</th>
            <th>ФИО</th>
            <th>Пол</th>
            <th>Дата рождения</th>
            <th>№ Зачетки</th>
            <th>Действия</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($students as $student): ?>
        <tr>
            <td><?= htmlspecialchars($student['group_number']) ?></td>
            <td><?= htmlspecialchars($student['full_name']) ?></td>
            <td><?= htmlspecialchars($student['gender']) ?></td>
            <td><?= htmlspecialchars($student['birth_date']) ?></td>
            <td><?= htmlspecialchars($student['student_ticket_number']) ?></td>
            <td class="actions">
                <a href="exams.php?student_id=<?= $student['id'] ?>" class="btn">Экзамены</a>
                <a href="student_form.php?id=<?= $student['id'] ?>" class="btn">Ред.</a>
                <a href="student_delete.php?id=<?= $student['id'] ?>" class="btn btn-danger">Удал.</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div style="margin-top: 20px;">
    <a href="student_form.php" class="btn btn-primary">Добавить студента</a>
</div>

</body>
</html>