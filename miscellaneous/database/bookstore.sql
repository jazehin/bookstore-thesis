-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Gép: localhost:3306
-- Létrehozás ideje: 2023. Már 24. 15:01
-- Kiszolgáló verziója: 8.0.32-0ubuntu0.22.04.2
-- PHP verzió: 8.1.2-1ubuntu2.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Adatbázis: `bookstore`
--

DROP DATABASE IF EXISTS bookstore;
CREATE DATABASE IF NOT EXISTS bookstore CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci;
USE bookstore;

DELIMITER $$
--
-- Eljárások
--
CREATE DEFINER=`jazehin`@`localhost` PROCEDURE `DeleteBook` (`_isbn` VARCHAR(13))  BEGIN
	DELETE FROM books_genres WHERE isbn = _isbn;
    DELETE FROM books_writers WHERE isbn = _isbn;
    DELETE FROM books WHERE isbn = _isbn;
END$$

CREATE DEFINER=`jazehin`@`localhost` PROCEDURE `GetBookByISBN` (IN `_isbn` VARCHAR(13))  BEGIN
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
END$$

CREATE DEFINER=`jazehin`@`localhost` PROCEDURE `GetGenresByISBN` (IN `_isbn` VARCHAR(13))  BEGIN
    SELECT 
		genres.genre_id,
        genre
	FROM
		genres 
        INNER JOIN books_genres ON genres.genre_id = books_genres.genre_id
	WHERE
		isbn = _isbn;
END$$

CREATE DEFINER=`jazehin`@`localhost` PROCEDURE `GetKommentek` (IN `_isbn` VARCHAR(13))  BEGIN
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
END$$

CREATE DEFINER=`jazehin`@`localhost` PROCEDURE `GetUserById` (`_user_id` INT)  BEGIN
    SELECT
        username,
        email,
        type,
        family_name,
        given_name,
        gender,
        birthdate,
        phone_number
    FROM
        users
        NATURAL JOIN login
        NATURAL JOIN user_types
    WHERE user_id = _user_id;
END$$

CREATE DEFINER=`jazehin`@`localhost` PROCEDURE `GetWritersByISBN` (IN `_isbn` VARCHAR(13))  BEGIN
    SELECT 
		writers.writer_id,
        writer
	FROM
		writers 
        INNER JOIN books_writers ON writers.writer_id = books_writers.writer_id
	WHERE
		isbn = _isbn;
END$$

CREATE DEFINER=`jazehin`@`localhost` PROCEDURE `InsertBook` (`_isbn` VARCHAR(13), `_pages` INT, `_publisher` TEXT, `_weight` INT, `_title` VARCHAR(255), `_series` TEXT, `_cover` TEXT, `_date_published` DATE, `_price` INT, `_discounted_price` INT, `_language` TEXT, `_stock` INT, `_description` TEXT, `_genres` TEXT, `_writers` TEXT)  BEGIN
	DECLARE delimiter_string CHAR(1) DEFAULT '@';
    DECLARE _genre, _writer TEXT;
    DECLARE i, begin, length INT;
    
    IF (SELECT publisher_id FROM publishers WHERE publisher = _publisher) IS NULL THEN
		INSERT INTO publishers (publisher) VALUES (_publisher);
	END IF;
    
    IF _series IS NOT NULL AND (SELECT series_id FROM serieses WHERE series = _series) IS NULL THEN
		INSERT INTO serieses (series) VALUES (_series);
	END IF;
    
    IF (SELECT cover_id FROM covers WHERE cover = _cover) IS NULL THEN
		INSERT INTO covers (cover) VALUES (_cover);
	END IF;
    
    IF (SELECT language_id FROM languages WHERE language = _language) IS NULL THEN
		INSERT INTO languages (language) VALUES (_language);
	END IF;
    
    INSERT INTO books (
		isbn,
        pages,
        publisher_id,
        weight,
        title,
        series_id,
        cover_id,
        date_published,
        price,
        discounted_price,
        language_id,
        stock,
        description
    ) VALUES ( 
		_isbn,
		_pages,
        (SELECT publisher_id FROM publishers WHERE publisher = _publisher),
        _weight,
        _title,
        (SELECT series_id FROM serieses WHERE series = _series),
        (SELECT cover_id FROM covers WHERE cover = _cover),
		_date_published,
        _price,
        _discounted_price,
        (SELECT language_id FROM languages WHERE language = _language),
        _stock,
        _description
	);
        
	SET i = 1;
    SET begin = 1;
    WHILE i <= LENGTH(_genres) DO
		IF SUBSTRING(_genres, i, 1) = delimiter_string THEN
			SET length = i - begin;
            SET _genre = substring(_genres, begin, length);
            IF (SELECT genre_id FROM genres WHERE genre = _genre) IS NULL THEN
				INSERT INTO genres (genre) VALUES (_genre);
			END IF;
            INSERT INTO books_genres (isbn, genre_id) VALUES (_isbn, (SELECT genre_id FROM genres WHERE genre = _genre));
            SET begin = i + 1;
		END IF;
        set i = i + 1;
    END WHILE;
    SET length = i - begin;
    SET _genre = substring(_genres, begin, length);
    IF (SELECT genre_id FROM genres WHERE genre = _genre) IS NULL THEN
		INSERT INTO genres (genre) VALUES (_genre);
	END IF;
    INSERT INTO books_genres (isbn, genre_id) VALUES (_isbn, (SELECT genre_id FROM genres WHERE genre = _genre));
    
    SET i = 1;
    SET begin = 1;
    WHILE i <= LENGTH(_writers) DO
		IF SUBSTRING(_writers, i, 1) = delimiter_string THEN
			SET length = i - begin;
            SET _writer = substring(_writers, begin, length);
            IF (SELECT writer_id FROM writers WHERE writer = _writer) IS NULL THEN
				INSERT INTO writers (writer) VALUES (_writer);
			END IF;
            INSERT INTO books_writers (isbn, writer_id) VALUES (_isbn, (SELECT writer_id FROM writers WHERE writer = _writer));
            SET begin = i + 1;
		END IF;
        set i = i + 1;
    END WHILE;
    SET length = i - begin;
    SET _writer = substring(_writers, begin, length);
    IF (SELECT writer_id FROM writers WHERE writer = _writer) IS NULL THEN
		INSERT INTO writers (writer) VALUES (_writer);
	END IF;
    INSERT INTO books_writers (isbn, writer_id) VALUES (_isbn, (SELECT writer_id FROM writers WHERE writer = _writer));
END$$

CREATE DEFINER=`jazehin`@`localhost` PROCEDURE `RemovePreference` (`_username` VARCHAR(20), `_genre` VARCHAR(255))  BEGIN
	DECLARE _user_id INT DEFAULT (SELECT user_id FROM login WHERE username = _username);
    DECLARE _genre_id INT DEFAULT (SELECT genre_id FROM genres WHERE genre = _genre);
    DELETE FROM user_preferences WHERE user_id = _user_id AND genre_id = _genre_id;
END$$

CREATE DEFINER=`jazehin`@`localhost` PROCEDURE `SetPreference` (`_username` VARCHAR(20), `_genre` VARCHAR(255))  BEGIN
	DECLARE _user_id INT DEFAULT (SELECT user_id FROM login WHERE username = _username);
    DECLARE _genre_id INT DEFAULT (SELECT genre_id FROM genres WHERE genre = _genre);
	INSERT INTO user_preferences (user_id, genre_id) VALUES (_user_id, _genre_id);
END$$

CREATE DEFINER=`jazehin`@`localhost` PROCEDURE `SignUp` (`_username` VARCHAR(20), `_password` CHAR(64), `_email` VARCHAR(255), `_salt` CHAR(10))  BEGIN
	DECLARE id INT DEFAULT 0;
    INSERT INTO users (type_id) VALUES (3);
    SET id = last_insert_id();
    INSERT INTO login (user_id, username, email, password, salt) VALUES (id, _username, _email, _password, _salt);
END$$

CREATE DEFINER=`jazehin`@`localhost` PROCEDURE `UpdateBook` (`_isbn` VARCHAR(13), `_pages` INT, `_publisher` TEXT, `_weight` INT, `_title` VARCHAR(255), `_series` TEXT, `_cover` TEXT, `_date_published` DATE, `_price` INT, `_discounted_price` INT, `_language` TEXT, `_stock` INT, `_description` TEXT, `_genres` TEXT, `_writers` TEXT)  BEGIN
	DECLARE delimiter_string CHAR(1) DEFAULT '@';
    DECLARE _genre, _writer TEXT;
    DECLARE i, begin, length INT;

	DELETE FROM books_genres WHERE isbn = _isbn;
    DELETE FROM books_writers WHERE isbn = _isbn;
    
    SET i = 1;
    SET begin = 1;
    WHILE i <= LENGTH(_genres) DO
		IF SUBSTRING(_genres, i, 1) = delimiter_string THEN
			SET length = i - begin;
            SET _genre = substring(_genres, begin, length);
            IF (SELECT genre_id FROM genres WHERE genre = _genre) IS NULL THEN
				INSERT INTO genres (genre) VALUES (_genre);
			END IF;
            INSERT INTO books_genres (isbn, genre_id) VALUES (_isbn, (SELECT genre_id FROM genres WHERE genre = _genre));
            SET begin = i + 1;
		END IF;
        set i = i + 1;
    END WHILE;
    SET length = i - begin;
    SET _genre = substring(_genres, begin, length);
    IF (SELECT genre_id FROM genres WHERE genre = _genre) IS NULL THEN
		INSERT INTO genres (genre) VALUES (_genre);
	END IF;
    INSERT INTO books_genres (isbn, genre_id) VALUES (_isbn, (SELECT genre_id FROM genres WHERE genre = _genre));
    
    SET i = 1;
    SET begin = 1;
    WHILE i <= LENGTH(_writers) DO
		IF SUBSTRING(_writers, i, 1) = delimiter_string THEN
			SET length = i - begin;
            SET _writer = substring(_writers, begin, length);
            IF (SELECT writer_id FROM writers WHERE writer = _writer) IS NULL THEN
				INSERT INTO writers (writer) VALUES (_writer);
			END IF;
            INSERT INTO books_writers (isbn, writer_id) VALUES (_isbn, (SELECT writer_id FROM writers WHERE writer = _writer));
            SET begin = i + 1;
		END IF;
        set i = i + 1;
    END WHILE;
    SET length = i - begin;
    SET _writer = substring(_writers, begin, length);
    IF (SELECT writer_id FROM writers WHERE writer = _writer) IS NULL THEN
		INSERT INTO writers (writer) VALUES (_writer);
	END IF;
    INSERT INTO books_writers (isbn, writer_id) VALUES (_isbn, (SELECT writer_id FROM writers WHERE writer = _writer));
    
    IF (SELECT publisher_id FROM publishers WHERE publisher = _publisher) IS NULL THEN
		INSERT INTO publishers (publisher) VALUES (_publisher);
	END IF;
    
    IF _series IS NOT NULL AND (SELECT series_id FROM serieses WHERE series = _series) IS NULL THEN
		INSERT INTO serieses (series) VALUES (_series);
	END IF;
    
    IF (SELECT cover_id FROM covers WHERE cover = _cover) IS NULL THEN
		INSERT INTO covers (cover) VALUES (_cover);
	END IF;
    
    IF (SELECT language_id FROM languages WHERE language = _language) IS NULL THEN
		INSERT INTO languages (language) VALUES (_language);
	END IF;
    
    UPDATE books SET 
		pages = _pages,
        publisher_id = (SELECT publisher_id FROM publishers WHERE publisher = _publisher),
        weight = _weight,
        title = _title,
        series_id = (SELECT series_id FROM serieses WHERE series = _series),
        cover_id = (SELECT cover_id FROM covers WHERE cover = _cover),
        date_published = _date_published,
        price = _price,
        discounted_price = _discounted_price,
        language_id = (SELECT language_id FROM languages WHERE language = _language),
        stock = _stock,
        description = _description
	WHERE 
		isbn = _isbn;
END$$

--
-- Függvények
--
CREATE DEFINER=`jazehin`@`localhost` FUNCTION `DoesBookExist` (`_isbn` VARCHAR(13) CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci) RETURNS INT READS SQL DATA
BEGIN
	IF (SELECT title FROM books WHERE isbn = _isbn) IS NULL THEN
		RETURN FALSE;
	END IF;
    RETURN TRUE;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `addresses`
--

CREATE TABLE `addresses` (
  `address_id` int NOT NULL,
  `company` varchar(100) COLLATE utf8mb4_hungarian_ci DEFAULT NULL,
  `county_id` int NOT NULL,
  `city` varchar(50) COLLATE utf8mb4_hungarian_ci NOT NULL,
  `public_space` varchar(50) COLLATE utf8mb4_hungarian_ci NOT NULL,
  `zip_code` int NOT NULL,
  `note` varchar(50) COLLATE utf8mb4_hungarian_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `books`
--

CREATE TABLE `books` (
  `isbn` varchar(13) CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci NOT NULL,
  `pages` int UNSIGNED NOT NULL,
  `publisher_id` int NOT NULL,
  `weight` int UNSIGNED DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_hungarian_ci NOT NULL,
  `series_id` int DEFAULT NULL,
  `cover_id` int NOT NULL,
  `date_published` date NOT NULL,
  `price` int UNSIGNED NOT NULL,
  `discounted_price` int UNSIGNED DEFAULT NULL,
  `language_id` int NOT NULL,
  `stock` int UNSIGNED NOT NULL,
  `description` text COLLATE utf8mb4_hungarian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `books_genres`
--

CREATE TABLE `books_genres` (
  `isbn` varchar(13) COLLATE utf8mb4_hungarian_ci NOT NULL,
  `genre_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `books_writers`
--

CREATE TABLE `books_writers` (
  `isbn` varchar(13) COLLATE utf8mb4_hungarian_ci NOT NULL,
  `writer_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `comments`
--

CREATE TABLE `comments` (
  `comment_id` int NOT NULL,
  `user_id` int NOT NULL,
  `isbn` varchar(13) COLLATE utf8mb4_hungarian_ci NOT NULL,
  `comment_text` text COLLATE utf8mb4_hungarian_ci NOT NULL,
  `comment_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `counties`
--

CREATE TABLE `counties` (
  `county_id` int NOT NULL,
  `county` varchar(22) COLLATE utf8mb4_hungarian_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- A tábla adatainak kiíratása `counties`
--

INSERT INTO `counties` (`county_id`, `county`) VALUES
(1, 'Bács-Kiskun'),
(2, 'Baranya'),
(3, 'Békés'),
(4, 'Borsod-Abaúj-Zemplén'),
(5, 'Csongrád-Csanád'),
(6, 'Fejér'),
(7, 'Győr-Moson-Sopron'),
(8, 'Hajdú-Bihar'),
(9, 'Heves'),
(10, 'Jász-Nagykun-Szolnok'),
(11, 'Komárom-Esztergom'),
(12, 'Nógrád'),
(13, 'Pest'),
(14, 'Somogy'),
(15, 'Szabolcs-Szatmár-Bereg'),
(16, 'Tolna'),
(17, 'Vas'),
(18, 'Veszprém'),
(19, 'Zala');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `covers`
--

CREATE TABLE `covers` (
  `cover_id` int NOT NULL,
  `cover` varchar(30) COLLATE utf8mb4_hungarian_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `genres`
--

CREATE TABLE `genres` (
  `genre_id` int NOT NULL,
  `genre` varchar(255) COLLATE utf8mb4_hungarian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `languages`
--

CREATE TABLE `languages` (
  `language_id` int NOT NULL,
  `language` varchar(30) COLLATE utf8mb4_hungarian_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `login`
--

CREATE TABLE `login` (
  `login_id` int NOT NULL,
  `user_id` int NOT NULL,
  `username` varchar(20) COLLATE utf8mb4_hungarian_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_hungarian_ci NOT NULL,
  `password` char(64) COLLATE utf8mb4_hungarian_ci NOT NULL,
  `salt` char(10) COLLATE utf8mb4_hungarian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- A tábla adatainak kiíratása `login`
--

INSERT INTO `login` (`login_id`, `user_id`, `username`, `email`, `password`, `salt`) VALUES
(1, 1, 'jazehin', 'jazehin@outlook.com', 'e0efd41c1949f1d76a8abd8d0dfe84b694e3cdeb56c9d0bb60d32919f86c46de', 'KPEsolIEGj'),
(2, 2, 'admin', 'admin@admin.com', '895d3acc59990382039c82306d7eacaf73bf49c445c690619652894c4045cb46', '80rSmKia$w');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `orders`
--

CREATE TABLE `orders` (
  `order_id` int NOT NULL,
  `user_id` int NULL,
  `address_id` int NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `price_sum` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `order_details`
--

CREATE TABLE `order_details` (
  `order_id` int NOT NULL,
  `isbn` varchar(13) COLLATE utf8mb4_hungarian_ci NOT NULL,
  `quantity` int UNSIGNED NOT NULL,
  `price` int UNSIGNED NOT NULL,
  `discounted_price` int UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `publishers`
--

CREATE TABLE `publishers` (
  `publisher_id` int NOT NULL,
  `publisher` varchar(255) COLLATE utf8mb4_hungarian_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `serieses`
--

CREATE TABLE `serieses` (
  `series_id` int NOT NULL,
  `series` varchar(255) COLLATE utf8mb4_hungarian_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- A tábla adatainak kiíratása `serieses`
--

INSERT INTO `serieses` (`series_id`, `series`) VALUES
(3, 'dfbdfbfbdfb'),
(4, 'Sori'),
(5, 'Heartstopper');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `type_id` int NOT NULL,
  `family_name` varchar(50) COLLATE utf8mb4_hungarian_ci DEFAULT NULL,
  `given_name` varchar(50) COLLATE utf8mb4_hungarian_ci DEFAULT NULL,
  `gender` enum('female','male') COLLATE utf8mb4_hungarian_ci DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `phone_number` varchar(12) COLLATE utf8mb4_hungarian_ci DEFAULT NULL,
  `points` int UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- A tábla adatainak kiíratása `users`
--

INSERT INTO `users` (`user_id`, `type_id`, `family_name`, `given_name`, `gender`, `birthdate`, `phone_number`, `points`) VALUES
(1, 1, 'Kiss', 'Bence', 'male', '2003-03-08', '06205111156', 0),
(2, 3, NULL, NULL, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `users_addresses`
--

CREATE TABLE `users_addresses` (
  `user_id` int NOT NULL,
  `address_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `user_book_reviews`
--

CREATE TABLE `user_book_reviews` (
  `user_id` int NOT NULL,
  `isbn` varchar(13) COLLATE utf8mb4_hungarian_ci NOT NULL,
  `has_bought_or_read` bit(1) NOT NULL,
  `rating` int DEFAULT NULL,
  `review` text COLLATE utf8mb4_hungarian_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `user_preferences`
--

CREATE TABLE `user_preferences` (
  `user_id` int NOT NULL,
  `genre_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `user_types`
--

CREATE TABLE `user_types` (
  `type_id` int NOT NULL,
  `type` varchar(30) COLLATE utf8mb4_hungarian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- A tábla adatainak kiíratása `user_types`
--

INSERT INTO `user_types` (`type_id`, `type`) VALUES
(1, 'administrator'),
(2, 'moderator'),
(3, 'user');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `writers`
--

CREATE TABLE `writers` (
  `writer_id` int NOT NULL,
  `writer` varchar(255) COLLATE utf8mb4_hungarian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- A tábla adatainak kiíratása `writers`
--

INSERT INTO `writers` (`writer_id`, `writer`) VALUES
(13, 'író1'),
(14, 'író2'),
(15, 'gbdgb'),
(16, 'író3'),
(17, 'ír'),
(18, 'í'),
(19, 'jk rowling'),
(20, 'hc andersen'),
(21, 'avram latosca'),
(22, 'Tina Bremer-Olszewski'),
(23, 'Adam Silvera'),
(24, 'Alice Oseman'),
(25, 'Nagy Kriszta Léna'),
(26, 'Jo Nesbo'),
(27, 'Naomi Alderman');

--
-- Indexek a kiírt táblákhoz
--

--
-- A tábla indexei `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`address_id`),
  ADD KEY `county_id` (`county_id`);

--
-- A tábla indexei `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`isbn`),
  ADD KEY `publisher_id` (`publisher_id`),
  ADD KEY `series_id` (`series_id`),
  ADD KEY `language_id` (`language_id`),
  ADD KEY `cover_id` (`cover_id`);

--
-- A tábla indexei `books_genres`
--
ALTER TABLE `books_genres`
  ADD KEY `isbn` (`isbn`),
  ADD KEY `genre_id` (`genre_id`);

--
-- A tábla indexei `books_writers`
--
ALTER TABLE `books_writers`
  ADD KEY `isbn` (`isbn`),
  ADD KEY `writer_id` (`writer_id`);

--
-- A tábla indexei `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `isbn` (`isbn`);

--
-- A tábla indexei `counties`
--
ALTER TABLE `counties`
  ADD PRIMARY KEY (`county_id`);

--
-- A tábla indexei `covers`
--
ALTER TABLE `covers`
  ADD PRIMARY KEY (`cover_id`);

--
-- A tábla indexei `genres`
--
ALTER TABLE `genres`
  ADD PRIMARY KEY (`genre_id`);

--
-- A tábla indexei `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`language_id`);

--
-- A tábla indexei `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`login_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- A tábla indexei `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `address_id` (`address_id`), 
  ADD KEY `user_id` (`user_id`);

--
-- A tábla indexei `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`order_id`,`isbn`),
  ADD KEY `isbn` (`isbn`);

--
-- A tábla indexei `publishers`
--
ALTER TABLE `publishers`
  ADD PRIMARY KEY (`publisher_id`);

--
-- A tábla indexei `serieses`
--
ALTER TABLE `serieses`
  ADD PRIMARY KEY (`series_id`);

--
-- A tábla indexei `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `type_id` (`type_id`);

--
-- A tábla indexei `users_addresses`
--
ALTER TABLE `users_addresses`
  ADD KEY `user_id` (`user_id`),
  ADD KEY `address_id` (`address_id`);

--
-- A tábla indexei `user_book_reviews`
--
ALTER TABLE `user_book_reviews`
  ADD PRIMARY KEY (`user_id`,`isbn`),
  ADD KEY `isbn` (`isbn`);

--
-- A tábla indexei `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD KEY `user_id` (`user_id`),
  ADD KEY `genre_id` (`genre_id`);

--
-- A tábla indexei `user_types`
--
ALTER TABLE `user_types`
  ADD PRIMARY KEY (`type_id`);

--
-- A tábla indexei `writers`
--
ALTER TABLE `writers`
  ADD PRIMARY KEY (`writer_id`);

--
-- A kiírt táblák AUTO_INCREMENT értéke
--

--
-- AUTO_INCREMENT a táblához `addresses`
--
ALTER TABLE `addresses`
  MODIFY `address_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `counties`
--
ALTER TABLE `counties`
  MODIFY `county_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT a táblához `covers`
--
ALTER TABLE `covers`
  MODIFY `cover_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `genres`
--
ALTER TABLE `genres`
  MODIFY `genre_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `languages`
--
ALTER TABLE `languages`
  MODIFY `language_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `login`
--
ALTER TABLE `login`
  MODIFY `login_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT a táblához `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `publishers`
--
ALTER TABLE `publishers`
  MODIFY `publisher_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `serieses`
--
ALTER TABLE `serieses`
  MODIFY `series_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT a táblához `user_types`
--
ALTER TABLE `user_types`
  MODIFY `type_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT a táblához `writers`
--
ALTER TABLE `writers`
  MODIFY `writer_id` int NOT NULL AUTO_INCREMENT;

--
-- Megkötések a kiírt táblákhoz
--

--
-- Megkötések a táblához `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `addresses_ibfk_1` FOREIGN KEY (`county_id`) REFERENCES `counties` (`county_id`);

--
-- Megkötések a táblához `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`publisher_id`) REFERENCES `publishers` (`publisher_id`),
  ADD CONSTRAINT `books_ibfk_2` FOREIGN KEY (`series_id`) REFERENCES `serieses` (`series_id`),
  ADD CONSTRAINT `books_ibfk_3` FOREIGN KEY (`language_id`) REFERENCES `languages` (`language_id`),
  ADD CONSTRAINT `books_ibfk_4` FOREIGN KEY (`cover_id`) REFERENCES `covers` (`cover_id`);

--
-- Megkötések a táblához `books_genres`
--
ALTER TABLE `books_genres`
  ADD CONSTRAINT `books_genres_ibfk_1` FOREIGN KEY (`isbn`) REFERENCES `books` (`isbn`),
  ADD CONSTRAINT `books_genres_ibfk_2` FOREIGN KEY (`genre_id`) REFERENCES `genres` (`genre_id`);

--
-- Megkötések a táblához `books_writers`
--
ALTER TABLE `books_writers`
  ADD CONSTRAINT `books_writers_ibfk_1` FOREIGN KEY (`isbn`) REFERENCES `books` (`isbn`),
  ADD CONSTRAINT `books_writers_ibfk_2` FOREIGN KEY (`writer_id`) REFERENCES `writers` (`writer_id`);

--
-- Megkötések a táblához `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`isbn`) REFERENCES `books` (`isbn`);

--
-- Megkötések a táblához `login`
--
ALTER TABLE `login`
  ADD CONSTRAINT `login_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Megkötések a táblához `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`address_id`);

--
-- Megkötések a táblához `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`isbn`) REFERENCES `books` (`isbn`),
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- Megkötések a táblához `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`type_id`) REFERENCES `user_types` (`type_id`);

--
-- Megkötések a táblához `users_addresses`
--
ALTER TABLE `users_addresses`
  ADD CONSTRAINT `users_addresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `users_addresses_ibfk_2` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`address_id`);

--
-- Megkötések a táblához `user_book_reviews`
--
ALTER TABLE `user_book_reviews`
  ADD CONSTRAINT `user_book_reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `user_book_reviews_ibfk_2` FOREIGN KEY (`isbn`) REFERENCES `books` (`isbn`);

--
-- Megkötések a táblához `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD CONSTRAINT `user_preferences_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `user_preferences_ibfk_2` FOREIGN KEY (`genre_id`) REFERENCES `genres` (`genre_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;