# 6.API_restful_js_Oauth2.0

# Seguridad: Login con API (Token/KEY OAuth 2.0)

Este proyecto es un ejemplo práctico de implementación de un sistema de **autenticación segura** utilizando **Tokens de acceso** y el estándar **OAuth 2.0**, combinado con **Bootstrap**, **JavaScript** y **Programación Orientada a Objetos (POO) en PHP**.

📖 Puedes encontrar la teoría utilizada en este ejemplo en el siguiente enlace:
<br>
<a href="https://oauth.net/2/" target="_blank">Enlace 1</a>
<br>
<a href="https://docs.google.com/document/d/1lhBlLzDBTsm5zI9kWFjIm0B3sWAmhhWgpqg-IGfepcA/edit?tab=t.0#heading=h.7qu730h0wku2" target="_blank">Enlace 2</a>

---

## 🔒 Descripción del proyecto
La aplicación permite a los usuarios autenticarse mediante **API REST**, generando un **token único** que actúa como llave de acceso a los recursos protegidos.  
El sistema implementa un flujo básico de **OAuth 2.0** para sesiones temporales, con expiración automática del token.

---

## 📦 Tecnologías utilizadas
- <a href="https://docs.google.com/document/d/1lhBlLzDBTsm5zI9kWFjIm0B3sWAmhhWgpqg-IGfepcA/edit?tab=t.0#heading=h.jxadv55jyth" target="_blank">**Bootstrap**</a> para el diseño responsivo.
- <a href="https://docs.google.com/document/d/1lhBlLzDBTsm5zI9kWFjIm0B3sWAmhhWgpqg-IGfepcA/edit?tab=t.0#heading=h.xl3ce7m9tn28" target="_blank">**JavaScript**</a> para la interacción dinámica 
- **PHP (POO)** → lógica de autenticación y emisión de tokens.
- **MySQL** → almacenamiento seguro de usuarios y tokens.
- **OAuth 2.0** → estándar de autorización.

---

## 🗄️ Estructura de base de datos

```sql
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

