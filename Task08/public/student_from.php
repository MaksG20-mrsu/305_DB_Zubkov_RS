<?php
try {
    $db = new PDO('sqlite:../data/database.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) { die("Error: " . $e->getMessage()); }

$id = $_GET['id'] ?? null;
$student = ['full_name' => '', 'gender' => 'Мужской', 'birth_date' => '', 'student_ticket_number' => '', 'group_id' => ''];

if ($id) {
    $stmt = $db->prepare("SELECT * FROM Students WHERE id = ?");
    $stmt->execute([$id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$student) die("Студент не найден");
}

$groups = $db->query("SELECT id, number FROM Groups ORDER BY number")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        $_POST['full_name'],
        $_POST['gender'],
        $_POST['birth_date'],
        $_POST['student_ticket_number'],
        $_POST['group_id']
    ];

    if ($id) {
        $stmt = $db->prepare("UPDATE Students SET full_name=?, gender=?, birth_date=?, student_ticket_number=?, group_id=? WHERE id=?");
        $stmt->execute([...$data, $id]);
    } else {
        $stmt = $db->prepare("INSERT INTO Students (full_name, gender, birth_date, student_ticket_number, group_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute($data);
    }
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head><title><?= $id ? 'Редактирование' : 'Новый студент' ?></title></head>
<body>
    <h2><?= $id ? 'Редактирование студента' : 'Добавление студента' ?></h2>
    <form method="POST">
        <p>ФИО: <input type="text" name="full_name" value="<?= htmlspecialchars($student['full_name']) ?>" required></p>
        <p>Группа: 
            <select name="group_id" required>
                <?php foreach ($groups as $g): ?>
                    <option value="<?= $g['id'] ?>" <?= $student['group_id'] == $g['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($g['number']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        <p>Пол: 
            <label><input type="radio" name="gender" value="Мужской" <?= $student['gender'] == 'Мужской' ? 'checked' : '' ?>> М</label>
            <label><input type="radio" name="gender" value="Женский" <?= $student['gender'] == 'Женский' ? 'checked' : '' ?>> Ж</label>
        </p>
        <p>Дата рождения: <input type="date" name="birth_date" value="<?= $student['birth_date'] ?>" required></p>
        <p>№ Зачетки: <input type="text" name="student_ticket_number" value="<?= htmlspecialchars($student['student_ticket_number']) ?>" required></p>
        <button type="submit">Сохранить</button>
        <a href="index.php">Отмена</a>
    </form>
</body>
</html>