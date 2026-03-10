PRAGMA foreign_keys = ON;

DROP TABLE IF EXISTS Exams;
DROP TABLE IF EXISTS Subjects;
DROP TABLE IF EXISTS Students;
DROP TABLE IF EXISTS Groups;

CREATE TABLE Groups (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    number TEXT NOT NULL UNIQUE,
    major TEXT NOT NULL,
    graduation_year INTEGER NOT NULL
);

CREATE TABLE Students (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    full_name TEXT NOT NULL,
    gender TEXT NOT NULL,
    birth_date DATE NOT NULL,
    student_ticket_number TEXT NOT NULL UNIQUE,
    group_id INTEGER NOT NULL,
    FOREIGN KEY (group_id) REFERENCES Groups(id) ON DELETE RESTRICT
);

CREATE TABLE Subjects (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    major TEXT NOT NULL,
    year INTEGER NOT NULL
);

CREATE TABLE Exams (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    student_id INTEGER NOT NULL,
    subject_id INTEGER NOT NULL,
    exam_date DATE NOT NULL,
    grade INTEGER NOT NULL,
    FOREIGN KEY (student_id) REFERENCES Students(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES Subjects(id) ON DELETE RESTRICT
);

CREATE INDEX idx_students_group ON Students(group_id);
CREATE INDEX idx_groups_year ON Groups(graduation_year);

INSERT INTO Groups (number, major, graduation_year) VALUES
('101', 'ФИИТ', 2029), ('102', 'ФИИТ', 2029), ('103', 'ПМИ', 2029), ('104', 'ПИ', 2029), ('105', 'ПИ', 2029),
('201', 'ФИИТ', 2028), ('202', 'ФИИТ', 2028), ('203', 'ПМИ', 2028), ('204', 'ПИ', 2028), ('205', 'ПИ', 2028),
('301', 'ФИИТ', 2027), ('302', 'ФИИТ', 2027), ('303', 'ПМИ', 2027), ('304', 'ПИ', 2027), ('305', 'ПИ', 2027),
('401', 'ФИИТ', 2026), ('402', 'ФИИТ', 2026), ('403', 'ПМИ', 2026), ('404', 'ПИ', 2026), ('405', 'ПИ', 2026);

INSERT INTO Students (full_name, gender, birth_date, student_ticket_number, group_id) VALUES
('Петров Петр Петрович', 'Мужской', '2006-01-15', '250101', (SELECT id FROM Groups WHERE number='101')),
('Сидорова Анна Ивановна', 'Женский', '2003-05-20', '220305', (SELECT id FROM Groups WHERE number='403')),
('Зубков Роман Сергеевич', 'Мужской', '2004-03-12', '231544', (SELECT id FROM Groups WHERE number='305')),
('Иванов Максим Александрович', 'Мужской', '2004-07-21', '231553', (SELECT id FROM Groups WHERE number='305')),
('Ивенин Артём Андреевич', 'Мужской', '2004-01-15', '230274', (SELECT id FROM Groups WHERE number='305')),
('Казейкин Иван Иванович', 'Мужской', '2004-11-30', '231527', (SELECT id FROM Groups WHERE number='305')),
('Кочнев Артем Алексеевич', 'Мужской', '2004-05-05', '231554', (SELECT id FROM Groups WHERE number='305')),
('Логунов Илья Сергеевич', 'Мужской', '2004-09-14', '231551', (SELECT id FROM Groups WHERE number='305')),
('Макарова Юлия Сергеевна', 'Женский', '2004-02-28', '231540', (SELECT id FROM Groups WHERE number='305')),
('Маклаков Сергей Александрович', 'Мужской', '2003-12-10', '240021', (SELECT id FROM Groups WHERE number='305')),
('Маскинскова Наталья Сергеевна', 'Женский', '2004-08-08', '231536', (SELECT id FROM Groups WHERE number='305')),
('Мукасеев Дмитрий Александрович', 'Мужской', '2004-06-19', '230026', (SELECT id FROM Groups WHERE number='305')),
('Наумкин Владислав Валерьевич', 'Мужской', '2004-04-25', '231532', (SELECT id FROM Groups WHERE number='305')),
('Паркаев Василий Александрович', 'Мужской', '2004-10-03', '231533', (SELECT id FROM Groups WHERE number='305')),
('Полковников Дмитрий Александрович', 'Мужской', '2004-01-20', '231556', (SELECT id FROM Groups WHERE number='305')),
('Пузаков Дмитрий Александрович', 'Мужской', '2004-05-17', '231537', (SELECT id FROM Groups WHERE number='305')),
('Пшеницына Полина Алексеевна', 'Женский', '2004-09-09', '231523', (SELECT id FROM Groups WHERE number='305')),
('Пяткин Игорь Алексеевич', 'Мужской', '2004-07-07', '231525', (SELECT id FROM Groups WHERE number='305')),
('Рыбаков Евгений Геннадьевич', 'Мужской', '2004-03-30', '231550', (SELECT id FROM Groups WHERE number='305')),
('Рыжкин Владислав Дмитриевич', 'Мужской', '2004-12-12', '231538', (SELECT id FROM Groups WHERE number='305')),
('Рябченко Александра Станиславовна', 'Женский', '2004-11-15', '231518', (SELECT id FROM Groups WHERE number='305')),
('Томилин Илья Петрович', 'Мужской', '2003-05-05', '220325', (SELECT id FROM Groups WHERE number='305')),
('Тульсков Илья Андреевич', 'Мужской', '2004-08-22', '231531', (SELECT id FROM Groups WHERE number='305')),
('Фирстов Артём Александрович', 'Мужской', '2004-02-14', '231545', (SELECT id FROM Groups WHERE number='305')),
('Четайкин Владислав Александрович', 'Мужской', '2004-06-01', '240022', (SELECT id FROM Groups WHERE number='305')),
('Шарунов Максим Игоревич', 'Мужской', '2004-10-20', '231528', (SELECT id FROM Groups WHERE number='305')),
('Шушев Денис Сергеевич', 'Мужской', '2004-01-09', '231969', (SELECT id FROM Groups WHERE number='305'));

INSERT INTO Subjects (name, major, year) VALUES
('Математический анализ', 'ФИИТ', 1),
('Алгебра и геометрия', 'ФИИТ', 1),
('Программирование на C++', 'ФИИТ', 1),
('Алгоритмы и структуры данных', 'ФИИТ', 2),
('Базы данных', 'ПИ', 2),
('Веб-программирование', 'ПИ', 3),
('Машинное обучение', 'ПМИ', 3),
('Философия', 'ФИИТ', 2);