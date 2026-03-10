INSERT INTO users (name, email, gender, register_date, occupation) 
VALUES ('Иванов Артём', 'ivanov.artem@mail.ru', 'male', datetime('now', 'localtime'), 'student');

INSERT INTO users (name, email, gender, register_date, occupation) 
VALUES ('Соколова Мария', 'sokolova.maria@mail.ru', 'female', datetime('now', 'localtime'), 'student');

INSERT INTO users (name, email, gender, register_date, occupation) 
VALUES ('Кузнецов Егор', 'kuznetsov.egor@mail.ru', 'male', datetime('now', 'localtime'), 'student');

INSERT INTO users (name, email, gender, register_date, occupation) 
VALUES ('Васильева Анна', 'vasileva.anna@mail.ru', 'female', datetime('now', 'localtime'), 'student');

INSERT INTO users (name, email, gender, register_date, occupation) 
VALUES ('Морозов Никита', 'morozov.nikita@mail.ru', 'male', datetime('now', 'localtime'), 'student');



INSERT INTO movies (title, year)
VALUES ('Interstellar', 2014);

INSERT INTO movie_genres (movie_id, genre_id)
VALUES (
    (SELECT id FROM movies WHERE title = 'Interstellar' AND year = 2014),
    (SELECT id FROM genres WHERE name = 'Sci-Fi')
);

INSERT INTO movies (title, year)
VALUES ('Whiplash', 2014);

INSERT INTO movie_genres (movie_id, genre_id)
VALUES (
    (SELECT id FROM movies WHERE title = 'Whiplash' AND year = 2014),
    (SELECT id FROM genres WHERE name = 'Drama')
);

INSERT INTO movies (title, year)
VALUES ('Drive', 2011);

INSERT INTO movie_genres (movie_id, genre_id)
VALUES (
    (SELECT id FROM movies WHERE title = 'Drive' AND year = 2011),
    (SELECT id FROM genres WHERE name = 'Crime')
);


INSERT INTO ratings (user_id, movie_id, rating, timestamp)
VALUES (
    (SELECT id FROM users WHERE email = 'ivanov.artem@mail.ru'),
    (SELECT id FROM movies WHERE title = 'Interstellar' AND year = 2014),
    5.0,
    strftime('%s', 'now')
);

INSERT INTO ratings (user_id, movie_id, rating, timestamp)
VALUES (
    (SELECT id FROM users WHERE email = 'ivanov.artem@mail.ru'),
    (SELECT id FROM movies WHERE title = 'Whiplash' AND year = 2014),
    4.8,
    strftime('%s', 'now')
);


INSERT INTO ratings (user_id, movie_id, rating, timestamp)
VALUES (
    (SELECT id FROM users WHERE email = 'ivanov.artem@mail.ru'),
    (SELECT id FROM movies WHERE title = 'Drive' AND year = 2011),
    4.3,
    strftime('%s', 'now')
);