-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 08, 2023 at 08:05 PM
-- Server version: 8.0.32-0ubuntu0.22.04.2
-- PHP Version: 8.1.2-1ubuntu2.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE bookstore CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci;
USE bookstore;
--
-- Database: `konyvaruhaz`
--

DELIMITER $$
--
-- Procedures
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

CREATE DEFINER=`jazehin`@`localhost` PROCEDURE `InsertBook` (`_isbn` VARCHAR(13), `_pages` int, `_publisher` TEXT, `_weight` INT, `_title` VARCHAR(255), `_series` TEXT, `_cover` TEXT, `_date_published` DATE, `_price` int, `_discounted_price` int, `_language` TEXT, `_stock` INT, `_description` TEXT, `_genres` TEXT, `_writers` TEXT)  BEGIN
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

CREATE DEFINER=`jazehin`@`localhost` PROCEDURE `UpdateBook` (`_isbn` VARCHAR(13), `_pages` int, `_publisher` TEXT, `_weight` INT, `_title` VARCHAR(255), `_series` TEXT, `_cover` TEXT, `_date_published` DATE, `_price` int, `_discounted_price` int, `_language` TEXT, `_stock` INT, `_description` TEXT, `_genres` TEXT, `_writers` TEXT)  BEGIN
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
-- Functions
--
CREATE DEFINER=`jazehin`@`localhost` FUNCTION `DoesBookExist` (`_isbn` VARCHAR(13)) RETURNS int(1) READS SQL DATA
BEGIN
	IF (SELECT title FROM books WHERE isbn = _isbn) IS NULL THEN
		RETURN FALSE;
	END IF;
    RETURN TRUE;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `address_id` int NOT NULL,
  `company` varchar(100) DEFAULT NULL,
  `county_id` int NOT NULL,
  `city` varchar(50) NOT NULL,
  `public_space` varchar(50) NOT NULL,
  `zip_code` int NOT NULL,
  `note` varchar(50) DEFAULT NULL
)

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `isbn` varchar(13) NOT NULL,
  `pages` int UNSIGNED NOT NULL,
  `publisher_id` int NOT NULL,
  `weight` int UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `series_id` int DEFAULT NULL,
  `cover_id` int NOT NULL,
  `date_published` date NOT NULL,
  `price` int UNSIGNED NOT NULL,
  `discounted_price` int UNSIGNED DEFAULT NULL,
  `language_id` int NOT NULL,
  `stock` int UNSIGNED NOT NULL,
  `description` text NOT NULL
);

--
-- Dumping data for table `books`
--

-- --------------------------------------------------------

--
-- Table structure for table `books_genres`
--

CREATE TABLE `books_genres` (
  `id` int NOT NULL,
  `isbn` varchar(13) NOT NULL,
  `genre_id` int NOT NULL
);

-- --------------------------------------------------------

--
-- Table structure for table `books_writers`
--

CREATE TABLE `books_writers` (
  `id` int NOT NULL,
  `isbn` varchar(13) NOT NULL,
  `writer_id` int NOT NULL
);

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `comment_id` int NOT NULL,
  `user_id` int NOT NULL,
  `isbn` varchar(13) NOT NULL,
  `comment_text` text NOT NULL,
  `comment_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- --------------------------------------------------------

--
-- Table structure for table `counties`
--

CREATE TABLE `counties` (
  `county_id` int NOT NULL,
  `county` varchar(22) DEFAULT NULL
);

--
-- Dumping data for table `counties`
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
-- Table structure for table `covers`
--

CREATE TABLE `covers` (
  `cover_id` int NOT NULL,
  `cover` varchar(30) DEFAULT NULL
);

--
-- Dumping data for table `covers`
--

-- --------------------------------------------------------

--
-- Table structure for table `genres`
--

CREATE TABLE `genres` (
  `genre_id` int NOT NULL,
  `genre` varchar(255) NOT NULL
);

--
-- Dumping data for table `genres`
--

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

CREATE TABLE `languages` (
  `language_id` int NOT NULL,
  `language` varchar(30) DEFAULT NULL
);

--
-- Dumping data for table `languages`
--

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `login_id` int NOT NULL,
  `user_id` int NOT NULL,
  `username` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` char(64) NOT NULL,
  `salt` char(10) NOT NULL
);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int NOT NULL,
  `user_id` int NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  `price_sum` int NOT NULL
);

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `order_id` int NOT NULL,
  `isbn` varchar(13) NOT NULL,
  `quantity` int UNSIGNED NOT NULL,
  `price` int UNSIGNED NOT NULL,
  `discounted_price` int UNSIGNED DEFAULT NULL
);

-- --------------------------------------------------------

--
-- Table structure for table `publishers`
--

CREATE TABLE `publishers` (
  `publisher_id` int NOT NULL,
  `publisher` varchar(255) DEFAULT NULL
);

--
-- Dumping data for table `publishers`
--

-- --------------------------------------------------------

--
-- Table structure for table `serieses`
--

CREATE TABLE `serieses` (
  `series_id` int NOT NULL,
  `series` varchar(255) DEFAULT NULL
);

--
-- Dumping data for table `serieses`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `type_id` int NOT NULL,
  `family_name` varchar(50) DEFAULT NULL,
  `given_name` varchar(50) DEFAULT NULL,
  `gender` enum('female','male') DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `phone_number` varchar(12) DEFAULT NULL,
  `points` int UNSIGNED NOT NULL DEFAULT '0'
);

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `type_id`, `family_name`, `given_name`, `gender`, `birthdate`, `phone_number`, `points`) VALUES
(1, 3, NULL, NULL, NULL, NULL, NULL, 0),
(2, 1, NULL, NULL, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users_addresses`
--

CREATE TABLE `users_addresses` (
  `user_id` int NOT NULL,
  `address_id` int NOT NULL
);

-- --------------------------------------------------------

--
-- Table structure for table `user_book_reviews`
--

CREATE TABLE `user_book_reviews` (
  `user_id` int NOT NULL,
  `isbn` varchar(13) NOT NULL,
  `has_bought_or_read` bit(1) NOT NULL,
  `rating` int DEFAULT NULL,
  `review` text
);

-- --------------------------------------------------------

--
-- Table structure for table `user_preferences`
--

CREATE TABLE `user_preferences` (
  `user_id` int NOT NULL,
  `genre_id` int NOT NULL
);

-- --------------------------------------------------------

--
-- Table structure for table `user_types`
--

CREATE TABLE `user_types` (
  `type_id` int NOT NULL,
  `type` varchar(30) NOT NULL
);

--
-- Dumping data for table `user_types`
--

INSERT INTO `user_types` (`type_id`, `type`) VALUES
(1, 'administrator'),
(2, 'moderator'),
(3, 'user');

-- --------------------------------------------------------

--
-- Table structure for table `writers`
--

CREATE TABLE `writers` (
  `writer_id` int NOT NULL,
  `writer` varchar(255) NOT NULL
);

--
-- Dumping data for table `writers`
--

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`address_id`),
  ADD KEY `county_id` (`county_id`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`isbn`),
  ADD KEY `publisher_id` (`publisher_id`),
  ADD KEY `series_id` (`series_id`),
  ADD KEY `language_id` (`language_id`),
  ADD KEY `cover_id` (`cover_id`);

--
-- Indexes for table `books_genres`
--
ALTER TABLE `books_genres`
  ADD PRIMARY KEY (`id`),
  ADD KEY `isbn` (`isbn`),
  ADD KEY `genre_id` (`genre_id`);

--
-- Indexes for table `books_writers`
--
ALTER TABLE `books_writers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `isbn` (`isbn`),
  ADD KEY `writer_id` (`writer_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `isbn` (`isbn`);

--
-- Indexes for table `counties`
--
ALTER TABLE `counties`
  ADD PRIMARY KEY (`county_id`);

--
-- Indexes for table `covers`
--
ALTER TABLE `covers`
  ADD PRIMARY KEY (`cover_id`);

--
-- Indexes for table `genres`
--
ALTER TABLE `genres`
  ADD PRIMARY KEY (`genre_id`);

--
-- Indexes for table `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`language_id`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`login_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`order_id`,`isbn`),
  ADD KEY `isbn` (`isbn`);

--
-- Indexes for table `publishers`
--
ALTER TABLE `publishers`
  ADD PRIMARY KEY (`publisher_id`);

--
-- Indexes for table `serieses`
--
ALTER TABLE `serieses`
  ADD PRIMARY KEY (`series_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `type_id` (`type_id`);

--
-- Indexes for table `users_addresses`
--
ALTER TABLE `users_addresses`
  ADD KEY `user_id` (`user_id`),
  ADD KEY `address_id` (`address_id`);

--
-- Indexes for table `user_book_reviews`
--
ALTER TABLE `user_book_reviews`
  ADD PRIMARY KEY (`user_id`,`isbn`),
  ADD KEY `isbn` (`isbn`);

--
-- Indexes for table `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD KEY `user_id` (`user_id`),
  ADD KEY `genre_id` (`genre_id`);

--
-- Indexes for table `user_types`
--
ALTER TABLE `user_types`
  ADD PRIMARY KEY (`type_id`);

--
-- Indexes for table `writers`
--
ALTER TABLE `writers`
  ADD PRIMARY KEY (`writer_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `address_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `books_genres`
--
ALTER TABLE `books_genres`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `books_writers`
--
ALTER TABLE `books_writers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `counties`
--
ALTER TABLE `counties`
  MODIFY `county_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `covers`
--
ALTER TABLE `covers`
  MODIFY `cover_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `genres`
--
ALTER TABLE `genres`
  MODIFY `genre_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `languages`
--
ALTER TABLE `languages`
  MODIFY `language_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `login`
--
ALTER TABLE `login`
  MODIFY `login_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `publishers`
--
ALTER TABLE `publishers`
  MODIFY `publisher_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `serieses`
--
ALTER TABLE `serieses`
  MODIFY `series_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_types`
--
ALTER TABLE `user_types`
  MODIFY `type_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `writers`
--
ALTER TABLE `writers`
  MODIFY `writer_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `addresses_ibfk_1` FOREIGN KEY (`county_id`) REFERENCES `counties` (`county_id`);

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`publisher_id`) REFERENCES `publishers` (`publisher_id`),
  ADD CONSTRAINT `books_ibfk_2` FOREIGN KEY (`series_id`) REFERENCES `serieses` (`series_id`),
  ADD CONSTRAINT `books_ibfk_3` FOREIGN KEY (`language_id`) REFERENCES `languages` (`language_id`),
  ADD CONSTRAINT `books_ibfk_4` FOREIGN KEY (`cover_id`) REFERENCES `covers` (`cover_id`);

--
-- Constraints for table `books_genres`
--
ALTER TABLE `books_genres`
  ADD CONSTRAINT `books_genres_ibfk_1` FOREIGN KEY (`isbn`) REFERENCES `books` (`isbn`),
  ADD CONSTRAINT `books_genres_ibfk_2` FOREIGN KEY (`genre_id`) REFERENCES `genres` (`genre_id`);

--
-- Constraints for table `books_writers`
--
ALTER TABLE `books_writers`
  ADD CONSTRAINT `books_writers_ibfk_1` FOREIGN KEY (`isbn`) REFERENCES `books` (`isbn`),
  ADD CONSTRAINT `books_writers_ibfk_2` FOREIGN KEY (`writer_id`) REFERENCES `writers` (`writer_id`);

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`isbn`) REFERENCES `books` (`isbn`);

--
-- Constraints for table `login`
--
ALTER TABLE `login`
  ADD CONSTRAINT `login_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`isbn`) REFERENCES `books` (`isbn`),
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`type_id`) REFERENCES `user_types` (`type_id`);

--
-- Constraints for table `users_addresses`
--
ALTER TABLE `users_addresses`
  ADD CONSTRAINT `users_addresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `users_addresses_ibfk_2` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`address_id`);

--
-- Constraints for table `user_book_reviews`
--
ALTER TABLE `user_book_reviews`
  ADD CONSTRAINT `user_book_reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `user_book_reviews_ibfk_2` FOREIGN KEY (`isbn`) REFERENCES `books` (`isbn`);

--
-- Constraints for table `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD CONSTRAINT `user_preferences_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `user_preferences_ibfk_2` FOREIGN KEY (`genre_id`) REFERENCES `genres` (`genre_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
