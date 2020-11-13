CREATE DATABASE IF NOT EXISTS machines_db;
USE machines_db;

CREATE TABLE users(
id          int(255) auto_increment not null,
name        varchar(50) not null,
surname     varchar(150),
email       varchar(255) not null,
password    varchar(255) not null,
created_at  datetime DEFAULT CURRENT_TIMESTAMP,
CONSTRAINT pk_users PRIMARY KEY(id)
)ENGINE=InnoDb;


CREATE TABLE machines(
id          int(255) auto_increment not null,
user_id     int(255) not null,
brand       varchar(50) not null,
model       varchar(150) not null,
manufacturer varchar(255) not null,
price    decimal(10,2) not null,
image_front_url  varchar(512),
image_lateral_url varchar(512),
image_thumbnail_url varchar(512),
created_at  datetime DEFAULT CURRENT_TIMESTAMP,
updated_at  datetime DEFAULT CURRENT_TIMESTAMP,
CONSTRAINT pk_machines PRIMARY KEY(id),
CONSTRAINT fk_machines_user FOREIGN KEY(user_id) REFERENCES users(id)
)ENGINE=InnoDb;