-- int types size reference: https://dev.mysql.com/doc/refman/5.6/en/integer-types.html

DROP DATABASE IF EXISTS konyvaruhaz;
CREATE DATABASE IF NOT EXISTS konyvaruhaz;
USE konyvaruhaz;

CREATE TABLE serieses (
    series_id INT AUTO_INCREMENT NOT NULL,
    series VARCHAR(255) CHARACTER SET UTF8MB4,
    PRIMARY KEY (series_id)
);

CREATE TABLE publishers (
    publisher_id INT AUTO_INCREMENT NOT NULL,
    publisher VARCHAR(255) CHARACTER SET UTF8MB4,
    PRIMARY KEY (publisher_id)
);

CREATE TABLE languages (
    language_id INT AUTO_INCREMENT NOT NULL,
    language VARCHAR(30) CHARACTER SET UTF8MB4,
    PRIMARY KEY (language_id)
);

-- table for storing cover types
CREATE TABLE covers (
    cover_id INT AUTO_INCREMENT NOT NULL,
    cover VARCHAR(30) CHARACTER SET UTF8MB4,
    PRIMARY KEY (cover_id)
);

CREATE TABLE genres (
    genre_id INT AUTO_INCREMENT NOT NULL,
    genre VARCHAR(255) CHARACTER SET UTF8MB4 NOT NULL,
    PRIMARY KEY (genre_id)
);

CREATE TABLE writers (
    writer_id INT AUTO_INCREMENT NOT NULL,
    writer VARCHAR(255) CHARACTER SET UTF8MB4 NOT NULL,
    PRIMARY KEY (writer_id)
);

CREATE TABLE books (
    isbn VARCHAR(13) CHARACTER SET UTF8MB4 NOT NULL,
    pages SMALLINT UNSIGNED NOT NULL,
    publisher_id INT NOT NULL,
    weight INT UNSIGNED NULL, -- in gramms
    title VARCHAR(255) CHARACTER SET UTF8MB4 NOT NULL,
    series_id INT NULL,
    cover_id INT NOT NULL,
    date_published DATE NOT NULL,
    price SMALLINT UNSIGNED NOT NULL, -- in HUF
    discounted_price SMALLINT UNSIGNED NULL, -- in HUF
    language_id INT NOT NULL,
    stock SMALLINT UNSIGNED NOT NULL,
    description TEXT NOT NULL,
    PRIMARY KEY (isbn),
    FOREIGN key (publisher_id) REFERENCES publishers(publisher_id),
    FOREIGN KEY (series_id) REFERENCES serieses(series_id),
    FOREIGN KEY (language_id) REFERENCES languages(language_id),
    FOREIGN KEY (cover_id) REFERENCES covers(cover_id)
);

CREATE TABLE books_genres (
    id INT AUTO_INCREMENT NOT NULL,
    isbn VARCHAR(13) CHARACTER SET UTF8MB4 NOT NULL,
    genre_id INT NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (isbn) REFERENCES books(isbn),
    FOREIGN KEY (genre_id) REFERENCES genres(genre_id)
);

CREATE TABLE books_writers (
    id INT AUTO_INCREMENT NOT NULL,
    isbn VARCHAR(13) CHARACTER SET UTF8MB4 NOT NULL,
    writer_id INT NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (isbn) REFERENCES books(isbn),
    FOREIGN KEY (writer_id) REFERENCES writers(writer_id)
);

DELIMITER //
CREATE PROCEDURE `GetBookByISBN`(IN _isbn VARCHAR(13))
BEGIN
    SELECT 
		isbn,
		pages,
		publisher,
        weight, 
        title, 
        series, 
        cover, 
        date_published,
        price,
        discounted_price,
        language,
        stock,
        description
	FROM
		books 
        INNER JOIN publishers ON books.publisher_id = publishers.publisher_id
        LEFT JOIN serieses ON books.series_id = serieses.series_id -- lehet hogy nem sorozat része!
        INNER JOIN covers ON books.cover_id = covers.cover_id
        INNER JOIN languages ON books.language_id = languages.language_id
	WHERE
		isbn = _isbn;
END //
DELIMITER ;

DELIMITER //
CREATE PROCEDURE `GetWritersByISBN`(IN _isbn VARCHAR(13))
BEGIN
    SELECT 
		writer_id,
        writer
	FROM
		writers 
        INNER JOIN books_writers ON writers.writer_id = books_writers.writer_id
	WHERE
		isbn = _isbn;
END //
DELIMITER ;

DELIMITER //
CREATE PROCEDURE `GetGenresByISBN`(IN _isbn VARCHAR(13))
BEGIN
    SELECT 
		genre_id,
        genre
	FROM
		genres 
        INNER JOIN books_genres ON genres.genre_id = books_genres.genre_id
	WHERE
		isbn = _isbn;
END //
DELIMITER ;

CREATE TABLE user_types (
    type_id INT AUTO_INCREMENT NOT NULL,
    type VARCHAR(30) CHARACTER SET UTF8MB4 NOT NULL,
    PRIMARY KEY (type_id)
);

INSERT INTO user_types (type) VALUES
("adminisztrátor"),
("moderátor"),
("felhasználó");

CREATE TABLE users (
    user_id INT AUTO_INCREMENT NOT NULL,
    username VARCHAR(20) CHARACTER SET UTF8MB4 NOT NULL,
    type_id INT NOT NULL,
    family_name VARCHAR(50) CHARACTER SET UTF8MB4 NULL,
    given_name VARCHAR(50) CHARACTER SET UTF8MB4 NULL,
    gender ENUM('female', 'male') NULL DEFAULT NULL,
    birthdate DATE NULL,
    phone_number VARCHAR(12) NULL,
    PRIMARY KEY (user_id),
    FOREIGN KEY (type_id) REFERENCES user_types(type_id)
);

CREATE TABLE user_book_reviews (
    user_id INT NOT NULL,
    isbn VARCHAR(13) CHARACTER SET UTF8MB4 NOT NULL,
    has_bought_or_read BIT NOT NULL,
    rating TINYINT NULL,
    review TEXT NULL,
    PRIMARY KEY (user_id, isbn),
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (isbn) REFERENCES books(isbn)
);

CREATE TABLE counties (
    county_id TINYINT AUTO_INCREMENT NOT NULL,
    county VARCHAR(22),
    PRIMARY KEY (county_id)
);

INSERT INTO counties (county) VALUES
('Bács-Kiskun'),
('Baranya'),
('Békés'),
('Borsod-Abaúj-Zemplén'),
('Csongrád-Csanád'),
('Fejér'),
('Győr-Moson-Sopron'),
('Hajdú-Bihar'),
('Heves'),
('Jász-Nagykun-Szolnok'),
('Komárom-Esztergom'),
('Nógrád'),
('Pest'),
('Somogy'),
('Szabolcs-Szatmár-Bereg'),
('Tolna'),
('Vas'),
('Veszprém'),
('Zala');

CREATE TABLE addresses (
    address_id INT AUTO_INCREMENT NOT NULL,
    company VARCHAR(100) NULL,
    county_id TINYINT NOT NULL,
    city VARCHAR(50) NOT NULL,
    public_space VARCHAR(50) NOT NULL,
    zip_code TINYINT NOT NULL,
    note VARCHAR(50) NULL,
    PRIMARY KEY (address_id)
);

-- TODO: make connections

CREATE TABLE login (
    login_id INT AUTO_INCREMENT NOT NULL,
    user_id INT NOT NULL,
    email VARCHAR(255) CHARACTER SET UTF8MB4 NOT NULL,
    password CHAR(64) CHARACTER SET UTF8MB4 NOT NULL,
    PRIMARY KEY (login_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE comments (
    comment_id INT AUTO_INCREMENT NOT NULL,
    user_id INT NOT NULL,
    isbn VARCHAR(13) CHARACTER SET UTF8MB4 NOT NULL,
    comment_text TEXT CHARACTER SET UTF8MB4 NOT NULL, 
    comment_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    PRIMARY KEY (comment_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (isbn) REFERENCES books(isbn)
);

DELIMITER //
CREATE PROCEDURE GetKommentek(IN _isbn VARCHAR(13))
BEGIN
    SELECT 
        user_id,
        username,
        comment_text,
        comment_date
    FROM 
        comments 
        INNER JOIN users ON comments.user_id = users.user_id
    WHERE isbn = _isbn
    ORDER BY comment_date DESC;
END
// DELIMITER ;

CREATE TABLE orders (
    order_id INT AUTO_INCREMENT NOT NULL,
    user_id INT NOT NULL,
    order_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    price_sum INT NOT NULL,
    PRIMARY KEY (order_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE order_details (
    order_id INT NOT NULL,
    isbn VARCHAR(13) CHARACTER SET UTF8MB4 NOT NULL,
    quantity INT UNSIGNED NOT NULL,
    price SMALLINT UNSIGNED NOT NULL,
    discounted_price SMALLINT UNSIGNED NULL,
    PRIMARY KEY (order_id, isbn),
    FOREIGN KEY (isbn) REFERENCES books(isbn),
    FOREIGN KEY (order_id) REFERENCES orders(order_id)
);