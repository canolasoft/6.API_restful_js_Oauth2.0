CREATE DATABASE IF NOT EXISTS base_usuarios;
CREATE TABLE IF NOT EXISTS base_usuarios.usuario (
    id INT(11) NOT NULL AUTO_INCREMENT,
    usr_name VARCHAR(100) NOT NULL,
    usr_email VARCHAR(100) UNIQUE NOT NULL,
    usr_pass VARCHAR(100) NOT NULL,
    imagen VARCHAR(100) DEFAULT NULL,
    PRIMARY KEY (id)
);
CREATE TABLE IF NOT EXISTS base_usuarios.access_token (
    token CHAR(32) NOT NULL,
    id_usuario INT(11) NOT NULL,
    fecha_creado DATETIME NOT NULL DEFAULT NOW(),
    fecha_vencimiento DATETIME NOT NULL DEFAULT (NOW() + INTERVAL 12 HOUR),
    PRIMARY KEY (token),
    FOREIGN KEY (id_usuario) REFERENCES usuario(id)
);
