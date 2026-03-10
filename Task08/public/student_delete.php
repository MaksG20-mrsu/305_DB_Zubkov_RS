<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db = new PDO('sqlite:../data/database.db');
        $stmt = $db->prepare("DELETE FROM Students WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        header("Location: index.php");
    } catch (PDOException $e) { die($e->getMessage()); }
}
?>
<!DOCTYPE html>
<body>
    <form method="POST">
        <input type="hidden" name="id" value="<?= $_GET['id'] ?>">
        <h3>Вы уверены, что хотите удалить студента?</h3>
        <button type="submit">Да, удалить</button>
        <a href="index.php">Отмена</a>
    </form>
</body>