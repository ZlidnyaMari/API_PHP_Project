<?php
$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');

$connection->query(
    "CREATE TABLE users (
        uuid TEXT NOT NULL
        CONSTRAINT uuid_primary_key PRIMARY KEY,
        username TEXT NOT NULL
        CONSTRAINT username_unique_key UNIQUE,
        first_name TEXT NOT NULL,
        last_name TEXT NOT NULL
        )"
); 
$connection->query(
    "CREATE TABLE posts (
        uuid TEXT NOT NULL
        CONSTRAINT uuid_primary_key PRIMARY KEY,
        autor_uuid TEXT NOT NULL
        title TEXT NOT NULL,
        'text' TEXT NOT NULL
        )"
); 
$connection->query(
    "CREATE TABLE comments (
        uuid TEXT NOT NULL
        CONSTRAINT uuid_primary_key PRIMARY KEY,
        post_uuid TEXT NOT NULL
        autor_uuid TEXT NOT NULL
        'text' TEXT NOT NULL
        )"
);
$connection->query(
    "CREATE TABLE likes (
        uuid TEXT NOT NULL 
        CONSTRAINT uuid_primary_key PRIMARY KEY,
        post_uuid TEXT NOT NULL,
        autor_uuid TEXT NOT NULL,
		FOREIGN KEY(post_uuid) REFERENCES posts(uuid),
		FOREIGN KEY(autor_uuid) REFERENCES users(uuid)
    )"
);     

