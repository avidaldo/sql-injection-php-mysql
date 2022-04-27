DROP DATABASE IF EXISTS pruebas;
CREATE DATABASE pruebas;
USE pruebas;

CREATE TABLE users (
    id INT PRIMARY KEY auto_increment,
    email VARCHAR(45) NOT NULL,
    clave VARCHAR(45) NOT NULL
);

INSERT INTO users(email, clave) VALUES ('user@dom.com','1234');
INSERT INTO users(email, clave) VALUES ('user@dom.com','1234');
INSERT INTO users(email, clave) VALUES ('user3@dom.com','1234');
INSERT INTO users(email, clave) VALUES ('user4@dom.com','1234');