#!/usr/bin/env php
<?php

if (!file_exists('university.db')) {
    die("Ошибка: Файл базы данных 'university.db' не найден.\n" .
        "Сначала выполните: sqlite3 university.db < create_database.sql\n");
}

// Подключаемся 
try {
    $db = new PDO('sqlite:university.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage() . "\n");
}

// Получаем текущий год
$currentYear = date('Y');

// Получаем все действующие группы
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
    die("Ошибка при получении групп: " . $e->getMessage() . "\n");
}

// Вывод заголовка
echo "================================================\n";
echo "             СПИСОК СТУДЕНТОВ\n";
echo "================================================\n\n";

// Показываем доступные группы
echo "ДОСТУПНЫЕ ГРУППЫ: " . implode(', ', $groups) . "\n\n";

// Запрашиваем ввод
echo "Введите номер группы или нажмите Enter для всех групп: ";
$input = trim(fgets(STDIN));

// Проверка ввода
$selectedGroup = null;
if ($input !== '') {
    if (!is_numeric($input)) {
        echo "Ошибка: номер группы должен быть числом!\n";
        exit(1);
    }
    
    $groupNumber = (int)$input;
    if (!in_array($groupNumber, $groups)) {
        echo "Ошибка: группы №{$groupNumber} не существует!\n";
        exit(1);
    }
    $selectedGroup = $groupNumber;
}

// Получаем студентов
try {
    $sql = "
        SELECT 
            g.number as group_number,
            g.major as major,
            s.last_name || ' ' || s.first_name as full_name,
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
    die("Ошибка при получении студентов: " . $e->getMessage() . "\n");
}

// Если нет студентов
if (empty($students)) {
    echo "\nСтудентов не найдено.\n";
    exit(0);
}

// Выводим таблицу 
echo "\n";
drawTable($students);

/**
 * Рисует таблицу 
 */
function drawTable($students) {
    $maxGroup = 6;
    $maxmajor = 1;
    $maxName = 4;
    
    foreach ($students as $student) {
        $maxGroup = max($maxGroup, strlen($student['group_number']));
        $maxmajor = max($maxmajor, mb_strlen($student['major']));
        $maxName = max($maxName, mb_strlen($student['full_name']));
    }
    
    $colGroup = $maxGroup + 2;
    $colmajor = $maxmajor + 2;
    $colName = $maxName + 2;
    $colGender = 8;
    $colBirth = 14;
    $colStudentId = 17;
    
    echo "+" . str_repeat('-', $colGroup) . 
         "+" . str_repeat('-', $colmajor) . 
         "+" . str_repeat('-', $colName) . 
         "+" . str_repeat('-', $colGender) . 
         "+" . str_repeat('-', $colBirth) . 
         "+" . str_repeat('-', $colStudentId) . "+\n";
    
    // Заголовок таблицы
    printf("| %-{$maxGroup}s | %-{$maxmajor}s | %-{$maxName}s | %-6s | %-12s | %-15s |\n",
        "Группа", "Направление", "ФИО", "Пол", "Дата рожд.", "№ билета");
    
    // Разделитель
    echo "+" . str_repeat('-', $colGroup) . 
         "+" . str_repeat('-', $colmajor) . 
         "+" . str_repeat('-', $colName) . 
         "+" . str_repeat('-', $colGender) . 
         "+" . str_repeat('-', $colBirth) . 
         "+" . str_repeat('-', $colStudentId) . "+\n";
    
    // Данные студентов
    foreach ($students as $student) {
        $birthDate = date('d.m.Y', strtotime($student['birth_date']));
        
        printf("| %-{$maxGroup}s | %-{$maxmajor}s | %-{$maxName}s | %-6s | %-12s | %-15s |\n",
            $student['group_number'],
            mb_strimwidth($student['major'], 0, $maxmajor, '...'),
            mb_strimwidth($student['full_name'], 0, $maxName, '...'),
            $student['gender'],
            $birthDate,
            $student['student_id']);
    }
    
    echo "+" . str_repeat('-', $colGroup) . 
         "+" . str_repeat('-', $colmajor) . 
         "+" . str_repeat('-', $colName) . 
         "+" . str_repeat('-', $colGender) . 
         "+" . str_repeat('-', $colBirth) . 
         "+" . str_repeat('-', $colStudentId) . "+\n";
    
    echo "\nВсего студентов: " . count($students) . "\n";
}