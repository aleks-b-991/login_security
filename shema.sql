-- Users table for the login example (MySQL)
-- Passwords are never stored in plain text: pw_hash holds the output of
-- PHP's password_hash() (bcrypt by default, 60 characters; 255 leaves room for newer algorithms).

CREATE DATABASE IF NOT EXISTS webapp;
USE webapp;

CREATE TABLE IF NOT EXISTS users (
    id       VARCHAR(50)  NOT NULL PRIMARY KEY,
    pw_hash  VARCHAR(255) NOT NULL
);

-- To add a user, generate the hash with PHP first, for example:
--   php -r "echo password_hash('MyS3cure!Pass', PASSWORD_DEFAULT), PHP_EOL;"
-- then insert it:
--   INSERT INTO users (id, pw_hash) VALUES ('student01', '<paste the hash here>');
