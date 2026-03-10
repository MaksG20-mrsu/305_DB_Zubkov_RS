import csv

DB_NAME = 'movies_rating.db'

DELETE_TABLES = '''
    DROP TABLE IF EXISTS movies;
    DROP TABLE IF EXISTS ratings;
    DROP TABLE IF EXISTS tags;
    DROP TABLE IF EXISTS users;
'''

CREATE_MOVIES_TABLE = '''
CREATE TABLE IF NOT EXISTS movies (
    id INTEGER PRIMARY KEY,
    title TEXT NOT NULL,
    year INTEGER,
    genres TEXT
);
'''

CREATE_RATINGS_TABLE = '''
CREATE TABLE IF NOT EXISTS ratings (
    id INTEGER PRIMARY KEY,
    user_id INTEGER,
    movie_id INTEGER,
    rating REAL,
    timestamp INTEGER
);
'''

CREATE_TAGS_TABLE = '''
CREATE TABLE IF NOT EXISTS tags (
    id INTEGER PRIMARY KEY,
    user_id INTEGER,
    movie_id INTEGER,
    tag TEXT,
    timestamp INTEGER
);
'''

CREATE_USERS_TABLE = '''
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY,
    name TEXT,
    email TEXT,
    gender TEXT,
    register_date TEXT,
    occupation TEXT
);
'''

sql_script = []

sql_script.append(DELETE_TABLES.strip())
sql_script.append("")
sql_script.append(CREATE_MOVIES_TABLE.strip())
sql_script.append("")
sql_script.append(CREATE_TAGS_TABLE.strip())
sql_script.append("")
sql_script.append(CREATE_RATINGS_TABLE.strip())
sql_script.append("")
sql_script.append(CREATE_USERS_TABLE.strip())
sql_script.append("")

with open('dataset/users.txt', 'r', encoding='utf-8') as f:
    for line in f:
        line = line.strip()
        if line:
            parts = line.split('|')
            if len(parts) == 6:
                user_id, name, email, gender, register_date, occupation = parts
                name = name.replace("'", "''")
                email = email.replace("'", "''")
                occupation = occupation.replace("'", "''")
                sql_script.append(f"INSERT INTO users (id, name, email, gender, register_date, occupation) VALUES ({user_id}, '{name}', '{email}', '{gender}', '{register_date}', '{occupation}');")
sql_script.append("")


with open('dataset/tags.csv', 'r', encoding='utf-8') as f:
    reader = csv.DictReader(f)
    for row in reader:
        user_id = row['userId']
        movie_id = row['movieId']
        tag = row['tag'].replace("'", "''")
        timestamp = row['timestamp']
        sql_script.append(f"INSERT INTO tags (user_id, movie_id, tag, timestamp) VALUES ({user_id}, {movie_id}, '{tag}', {timestamp});")
sql_script.append("")


with open('dataset/movies.csv', 'r', encoding='utf-8') as f:
    reader = csv.DictReader(f)
    for row in reader:
        movie_id = row['movieId']
        title = row['title'].replace("'", "''")
        genres = row['genres'].replace("'", "''")
        year = 'NULL'
        if '(' in title and ')' in title:
            year_part = title.split('(')[-1].split(')')[0]
            if year_part.isdigit() and len(year_part) == 4:
                year = year_part
                title = title.replace(f'({year_part})', '').strip() 

        sql_script.append(f"INSERT INTO movies (id, title, year, genres) VALUES ({movie_id}, '{title}', {year}, '{genres}');")
sql_script.append("")


with open('dataset/ratings.csv', 'r', encoding='utf-8') as f:
    reader = csv.DictReader(f)
    for row in reader:
        user_id = row['userId']
        movie_id = row['movieId']
        rating = row['rating']
        timestamp = row['timestamp']
        sql_script.append(f"INSERT INTO ratings (user_id, movie_id, rating, timestamp) VALUES ({user_id}, {movie_id}, {rating}, {timestamp});")


sql_script = '\n'.join(sql_script)
with open('db_init.sql', 'w', encoding='utf-8') as f:
    f.write(sql_script)