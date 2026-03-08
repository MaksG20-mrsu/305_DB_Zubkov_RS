<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db = new PDO('sqlite:../data/database.db');
        $stmt = $db->prepare("DELETE FROM Exams WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        header("Location: exams.php?student_id=" . $_POST['student_id']);
    } catch (PDOException $e) { die($e->getMessage()); }
}
?>
<!DOCTYPE html>
<body>
    <form method="POST">
        <input type="hidden" name="id" value="<?= $_GET['id'] ?>">
        <input type="hidden" name="student_id" value="<?= $_GET['student_id'] ?>">
        <h3>Удалить запись об экзамене?</h3>
        <button type="submit">Да</button>
        <a href="exams.php?student_id=<?= $_GET['student_id'] ?>">Отмена</a>
    </form>
</body>