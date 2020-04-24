# hair-set-catalog

CREATE DATABASE hairset_system;

CREATE USER hairset identified BY '123456';

GRANT ALL ON hairset_system.* TO hairset;


create table users (
 id int PRIMARY KEY auto_increment NOT NULL,
 email VARCHAR(32) NOT NULL,
 name VARCHAR(50) NOT NULL,
 password VARCHAR(255) NOT NULL,
 created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 unique idx_email(email)
);


CREATE table categories (
 id int PRIMARY KEY auto_increment NOT NULL,
 name VARCHAR(50) NOT NULL,
 created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at  datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);


CREATE table styles (
 id int PRIMARY KEY auto_increment NOT NULL,
 category_id int NOT NULL,
 user_id int NOT NULL,
 picture VARCHAR(255) NOT NULL,
 body text NOT NULL,
 created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
 updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL
);


CREATE table good (
 id int PRIMARY KEY auto_increment NOT NULL,
 user_id int NOT NULL,
 style_id int NOT NULL,
 created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
 UNIQUE user_id_style_id_index(user_id, style_id) 
);


INSERT INTO categories (name, created_at, updated_at) VALUES
('巻きおろし', now(), now()),
('ゆるふわ', now(), now()),
('編みおろし', now(), now()),
('ストレート', now(), now()),
('ウェーブ', now(), now()),
('アップスタイル', now(), now()),
('ダウンスタイル', now(), now()),
('ハーフアップ', now(), now()),
('夜会', now(), now()),
('ポニーテール', now(), now()),
('ツインテール', now(), now()),
('シニヨン', now(), now()),
('編み込み', now(), now()),
('ロープ編み', now(), now()),
('ねじり編み', now(), now());