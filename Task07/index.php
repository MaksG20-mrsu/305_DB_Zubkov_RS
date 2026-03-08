<?php
// Проверяем существование файла базы данных
if (!file_exists('university.db')) {
    die("Ошибка: Файл базы данных 'university.db' не найден.<br>" .
        "Сначала выполните: sqlite3 university.db < create_database.sql");
}

// Подключаемся к базе данных
try {
    $dbPath = __DIR__ . '/university.db';
    $db = new PDO('sqlite:' . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

// Текущий год
$currentYear = date('Y');

// Получаем группы для фильтра
try {
    $stmt = $db->prepare("
        SELECT DISTINCT number 
        FROM groups 
        WHERE graduation_year >= :year 
        ORDER BY number
    ");
    $stmt->execute([':year' => $currentYear]);
    $groups = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    die("Ошибка при получении групп: " . $e->getMessage());
}

// Получаем выбранную группу
$selectedGroup = null;
if (isset($_GET['group']) && $_GET['group'] !== '') {
    $selectedGroup = (int)$_GET['group'];
}

// Получаем студентов
try {
    $sql = "
        SELECT 
            g.number as group_number,
            g.major as major,
            s.last_name,
            s.first_name,
            s.middle_name,
            s.gender,
            s.birth_date,
            s.student_id
        FROM students s
        JOIN groups g ON s.group_id = g.id
        WHERE g.graduation_year >= :year
    ";
    
    $params = [':year' => $currentYear];
    
    if ($selectedGroup !== null) {
        $sql .= " AND g.number = :group";
        $params[':group'] = $selectedGroup;
    }
    
    $sql .= " ORDER BY g.number, s.last_name, s.first_name";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $students = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Ошибка при получении студентов: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Список студентов - Университет</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>📚 Список студентов университета</h1>
            <p class="subtitle">База данных действующих групп (год окончания ≥ <?= $currentYear ?>)</p>
        </header>
        
        <section class="filter-section">
            <form method="GET" class="filter-form">
                <div class="form-group">
                    <label for="group">🔍 Фильтр по группе:</label>
                    <select name="group" id="group" onchange="this.form.submit()">
                        <option value="">Все группы</option>
                        <?php foreach ($groups as $group): ?>
                            <option value="<?= $group ?>" <?= $selectedGroup == $group ? 'selected' : '' ?>>
                                Группа <?= $group ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn-apply">Применить</button>
                    <?php if ($selectedGroup !== null): ?>
                        <a href="?" class="btn-reset">Сбросить фильтр</a>
                    <?php endif; ?>
                </div>
            </form>
        </section>
        
        <main>
            <?php if (empty($students)): ?>
                <div class="message info">
                    <p>🎓 Студентов не найдено</p>
                </div>
            <?php else: ?>
                <div class="table-container">
                    <table class="students-table">
                        <thead>
                            <tr>
                                <th>Группа</th>
                                <th>Направление подготовки</th>
                                <th>ФИО</th>
                                <th>Пол</th>
                                <th>Дата рождения</th>
                                <th>Номер студ. билета</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td class="group-number"><?= htmlspecialchars($student['group_number']) ?></td>
                                    <td class="major"><?= htmlspecialchars($student['major']) ?></td>
                                    <td class="student-name">
                                        <?= htmlspecialchars($student['last_name']) ?> 
                                        <?= htmlspecialchars($student['first_name']) ?>
                                        <?php if (!empty($student['middle_name'])): ?>
                                            <?= htmlspecialchars($student['middle_name']) ?>
                                        <?php endif; ?>
                                    </td>
                                    <td class="gender"><?= htmlspecialchars($student['gender']) ?></td>
                                    <td class="birth-date"><?= date('d.m.Y', strtotime($student['birth_date'])) ?></td>
                                    <td class="student-id"><?= htmlspecialchars($student['student_id']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="summary">
                    <div class="total-count">
                        <span class="label">Всего студентов:</span>
                        <span class="value"><?= count($students) ?></span>
                    </div>
                    <?php if ($selectedGroup !== null): ?>
                        <div class="group-info">
                            <span class="label">Выбрана группа:</span>
                            <span class="value"><?= $selectedGroup ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </main>
        
        <footer>
            <p>© <?= date('Y') ?> Университетская система. Лабораторная работа №7</p>
        </footer>
    </div>
</body>
</html>