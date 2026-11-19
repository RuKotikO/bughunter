PRAGMA foreign_keys=OFF;
BEGIN TRANSACTION;

CREATE TABLE Users (
    user_id INTEGER PRIMARY KEY AUTOINCREMENT,
    full_name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    role TEXT NOT NULL
);

INSERT INTO Users VALUES(1,'Кунгуров П.О.','kungurov@example.com','Администратор');
INSERT INTO Users VALUES(2,'Аксёнов Р.Н.','aksenov@example.com','Разработчик');
INSERT INTO Users VALUES(3,'Иванова А.С.','ivanova@example.com','Тестировщик');
INSERT INTO Users VALUES(4,'Петров В.В.','petrov@example.com','Заказчик');

CREATE TABLE Projects (
    project_id INTEGER PRIMARY KEY AUTOINCREMENT,
    code TEXT NOT NULL UNIQUE,
    name TEXT NOT NULL,
    lead_id INTEGER,
    FOREIGN KEY (lead_id) REFERENCES Users(user_id)
);

INSERT INTO Projects VALUES(1,'BH','Модуль учёта дефектов BugHunter',1);
INSERT INTO Projects VALUES(2,'WEB','Корпоративный портал',2);

CREATE TABLE Bugs (
    bug_id INTEGER PRIMARY KEY AUTOINCREMENT,
    project_id INTEGER NOT NULL,
    reporter_id INTEGER NOT NULL,
    assignee_id INTEGER,
    title TEXT NOT NULL,
    status TEXT NOT NULL,
    priority TEXT NOT NULL,
    FOREIGN KEY (project_id) REFERENCES Projects(project_id),
    FOREIGN KEY (reporter_id) REFERENCES Users(user_id),
    FOREIGN KEY (assignee_id) REFERENCES Users(user_id)
);

INSERT INTO Bugs VALUES(1,1,3,2,'Не работает фильтрация багов по приоритету','В работе','Высокий');
INSERT INTO Bugs VALUES(2,1,3,2,'Опечатка в заголовке окна авторизации','На проверке','Низкий');
INSERT INTO Bugs VALUES(3,2,4,2,'Ошибка 500 при загрузке аватара','Новый','Критический');

CREATE TABLE Comments (
    comment_id INTEGER PRIMARY KEY AUTOINCREMENT,
    bug_id INTEGER NOT NULL,
    author_id INTEGER NOT NULL,
    body TEXT NOT NULL,
    created_at TEXT DEFAULT (datetime('now', 'localtime')),
    FOREIGN KEY (bug_id) REFERENCES Bugs(bug_id),
    FOREIGN KEY (author_id) REFERENCES Users(user_id)
);

INSERT INTO Comments VALUES(1,1,2,'Воспроизвёл ошибку, проблема в SQL-запросе.','2026-11-18 12:00:00');
INSERT INTO Comments VALUES(2,1,3,'Жду исправления для повторного прогона тестов.','2026-11-18 12:05:00');

CREATE TABLE BugHistory (
    history_id INTEGER PRIMARY KEY AUTOINCREMENT,
    bug_id INTEGER NOT NULL,
    changed_by INTEGER NOT NULL,
    field TEXT NOT NULL,
    old_val TEXT,
    new_val TEXT,
    FOREIGN KEY (bug_id) REFERENCES Bugs(bug_id),
    FOREIGN KEY (changed_by) REFERENCES Users(user_id)
);

INSERT INTO BugHistory VALUES(1,1,2,'status','Новый','В работе');
INSERT INTO BugHistory VALUES(2,2,2,'status','В работе','На проверке');

COMMIT;
