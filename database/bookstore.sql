-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Gép: localhost:3306
-- Létrehozás ideje: 2023. Ápr 11. 20:27
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

CREATE DEFINER=`jazehin`@`localhost` PROCEDURE `GetMostPurchasedBooksByAge` (IN `_user_id` INT)  BEGIN
	DECLARE age INT DEFAULT TIMESTAMPDIFF(YEAR, (SELECT users.birthdate FROM users WHERE users.user_id = _user_id), CURDATE());
    SELECT order_details.isbn, SUM(order_details.quantity)
    FROM order_details
    INNER JOIN orders ON orders.order_id = order_details.order_id
    INNER JOIN users ON users.user_id = orders.user_id
    WHERE ABS(TIMESTAMPDIFF(YEAR, users.birthdate, CURDATE()) - age) <= 5 AND users.user_id <> _user_id
    GROUP BY isbn
    HAVING SUM(order_details.quantity) > 0
    ORDER BY SUM(order_details.quantity) DESC
    LIMIT 30;
END$$

CREATE DEFINER=`jazehin`@`localhost` PROCEDURE `GetMostPurchasedBooksByGender` (`_user_id` INT)  BEGIN
	DECLARE gender VARCHAR(10) DEFAULT (SELECT users.gender FROM users WHERE users.user_id = _user_id);
    SELECT order_details.isbn, SUM(order_details.quantity)
    FROM order_details
    INNER JOIN orders ON orders.order_id = order_details.order_id
    INNER JOIN users ON users.user_id = orders.user_id
    WHERE users.gender = gender AND users.user_id <> _user_id
    GROUP BY isbn
    HAVING SUM(order_details.quantity) > 0
    ORDER BY SUM(order_details.quantity) DESC
    LIMIT 30;
END$$

CREATE DEFINER=`jazehin`@`localhost` PROCEDURE `GetUserById` (IN `_user_id` INT)  BEGIN
    SELECT
        username,
        email,
        type,
        family_name,
        given_name,
        gender,
        birthdate,
        phone_number,
        points
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

CREATE DEFINER=`jazehin`@`localhost` PROCEDURE `RemovePreference` (IN `_username` VARCHAR(20), IN `_genre` VARCHAR(255))  BEGIN
	DECLARE _user_id INT DEFAULT (SELECT user_id FROM login WHERE username = _username);
    DECLARE _genre_id INT DEFAULT (SELECT genre_id FROM genres WHERE genre = _genre);
    DELETE FROM preferences WHERE user_id = _user_id AND genre_id = _genre_id;
END$$

CREATE DEFINER=`jazehin`@`localhost` PROCEDURE `SaveAddress` (IN `_username` VARCHAR(20), IN `_company` VARCHAR(100), IN `_county_id` INT, IN `_city` VARCHAR(50), IN `_public_space` VARCHAR(50), IN `_zip_code` CHAR(4), IN `_note` VARCHAR(50))  BEGIN
	DECLARE id INT DEFAULT 0;
    SET id = (SELECT address_id FROM addresses WHERE company = _company AND county_id = _county_id AND city = _city AND public_space = _public_space AND zip_code = _zip_code AND note = _note); 
    
    IF (id IS NULL) THEN
		INSERT INTO addresses (company, county_id, city, public_space, zip_code, note) VALUES (_company, _county_id, _city, _public_space, _zip_code, _note);
		SET id = last_insert_id();
    END IF;
    
    INSERT INTO users_addresses (user_id, address_id) VALUES ((SELECT user_id FROM login WHERE username = _username), id);
END$$

CREATE DEFINER=`jazehin`@`localhost` PROCEDURE `SetPreference` (IN `_username` VARCHAR(20), IN `_genre` VARCHAR(255))  BEGIN
	DECLARE _user_id INT DEFAULT (SELECT user_id FROM login WHERE username = _username);
    DECLARE _genre_id INT DEFAULT (SELECT genre_id FROM genres WHERE genre = _genre);
	INSERT INTO preferences (user_id, genre_id) VALUES (_user_id, _genre_id);
END$$

CREATE DEFINER=`jazehin`@`localhost` PROCEDURE `SetRating` (`_user_id` INT, `_isbn` VARCHAR(13), `_rating` INT)  BEGIN
	IF ((SELECT rating FROM ratings WHERE user_id = _user_id AND isbn = _isbn) IS NULL) THEN
    	INSERT INTO ratings (user_id, isbn, rating) VALUES (_user_id, _isbn, _rating);
    ELSE
		UPDATE ratings SET rating = _rating WHERE user_id = _user_id AND isbn = _isbn;
    END IF;
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
CREATE DEFINER=`jazehin`@`localhost` FUNCTION `CompleteOrder` (`_user_id` INT, `_address_id` INT, `_price_sum` INT) RETURNS INT BEGIN
	INSERT INTO orders (user_id, address_id, price_sum) VALUES (_user_id, _address_id, _price_sum);
    RETURN last_insert_id();
END$$

CREATE DEFINER=`jazehin`@`localhost` FUNCTION `GetAddressId` (`_company` VARCHAR(100), `_county_id` INT, `_city` VARCHAR(50), `_public_space` VARCHAR(50), `_zip_code` CHAR(4), `_note` VARCHAR(50)) RETURNS INT BEGIN
	DECLARE id INT DEFAULT (SELECT address_id FROM addresses WHERE company = _company AND county_id = _county_id AND city = _city AND public_space = _public_space AND zip_code = _zip_code AND note = _note); 
   
    IF (id IS NULL) THEN
		INSERT INTO addresses (company, county_id, city, public_space, zip_code, note) VALUES (_company, _county_id, _city, _public_space, _zip_code, _note);
		SET id = last_insert_id();
    END IF;
                                
    RETURN id;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `addresses`
--

CREATE TABLE `addresses` (
  `address_id` int NOT NULL,
  `company` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci DEFAULT NULL,
  `county_id` int NOT NULL,
  `city` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci NOT NULL,
  `public_space` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci NOT NULL,
  `zip_code` char(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci NOT NULL,
  `note` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- A tábla adatainak kiíratása `addresses`
--

INSERT INTO `addresses` (`address_id`, `company`, `county_id`, `city`, `public_space`, `zip_code`, `note`) VALUES
(20, 'Példa Kft.', 13, 'Budapest', 'Példa tér 34.', '1234', 'adja oda a portásnak a csomagot'),
(21, NULL, 2, 'Pécs', 'Példa út 6.', '7621', NULL),
(23, '', 2, 'Pécs', 'Példa út 6.', '7621', '');

-- --------------------------------------------------------

--
-- A nézet helyettes szerkezete `bestsellers`
-- (Lásd alább az aktuális nézetet)
--
CREATE TABLE `bestsellers` (
`isbn` varchar(13)
,`sum(order_details.quantity)` decimal(32,0)
);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `books`
--

CREATE TABLE `books` (
  `isbn` varchar(13) CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci NOT NULL,
  `pages` int UNSIGNED NOT NULL,
  `publisher_id` int NOT NULL,
  `weight` int UNSIGNED DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci NOT NULL,
  `series_id` int DEFAULT NULL,
  `cover_id` int NOT NULL,
  `date_published` date NOT NULL,
  `price` int UNSIGNED NOT NULL,
  `discounted_price` int UNSIGNED DEFAULT NULL,
  `language_id` int NOT NULL,
  `stock` int UNSIGNED NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- A tábla adatainak kiíratása `books`
--

INSERT INTO `books` (`isbn`, `pages`, `publisher_id`, `weight`, `title`, `series_id`, `cover_id`, `date_published`, `price`, `discounted_price`, `language_id`, `stock`, `description`) VALUES
('9786155716539', 120, 4, NULL, 'Ervin és az őrtündérek', NULL, 3, '2023-03-31', 2990, 2392, 1, 3, 'Ervin rettenetesen unatkozik, ám egy pillanat alatt feje tetejére áll az élete, amikor véletlenül összezsugorítja egy ügyetlen tündér. A nagyszájú Almát követve a fiú elképzelhetetlen kalandokba keveredik a Balaton partján. De milyenek a valódi tündérek és mi közük van az emberekhez? Az Ervin és az őrtündérek első részében egy olyan világ tárul fel, amelyben a vagány tündéreknek nincs szükségük szárnyakra, simán megülnek egy pókot és harcba szállnak egymással is, ha kell.'),
('9789632916064', 88, 8, 250, 'Az Univerzum mindenben támogat - Jóskártya + könyv', NULL, 3, '2022-06-18', 4990, 4490, 1, 21, 'Ez az inspiráló, 52 lapos kártyacsomag spirituális útmutatást nyújt számodra, hogy erőt meríts, amikor lemerültél, támogatást kapj és szinkronba kerülj, amikor elveszettnek érzed magad, biztonságra lelj a bizonytalansággal szemben, öröm járjon át, bármilyen körülmények között is legyél. Most már a te kezedben van. Fogadd el a kártyák által nyújtott útmutatást, gyakorolj türelmet és békét, és bízz benne, hogy az Univerzum mindenben támogat.<br>Gabrielle Bernstein és Micaela Ezra varázslatos kártyáit a magyar kiadásban külön ajándékkal szeretnénk a kezedbe adni. Bizony, ez itt a Csodák ideje, kedves Csodagyáros, ahogy Gabby is szólít minket! Meggyőződésünk, hogy a kártyákkal való munkát tökéletesen kiegészítik azok a fontos gondolatok, amelyeket Gabby Miracles now (Csodák ideje) című könyvéből gyűjtöttünk össze számodra. Olyan részeket válogattunk, amelyek leginkább támogatnak abban, ami a kártyák fő célja: hogy mi magunk teremtsük a csodát. Hogy miként? Leginkább azzal, hogy belső gazdagságunkra támaszkodva a boldogságot választjuk.<br><br>Gabrielle Bernstein, a New York Times bestseller szerzője, Oprah Winfrey SuperSoul Sunday című műsorában, mint a jövő nemzedék egyik új eszmei vezetője. Rendszeresen látható a Dr. Oz című tévéműsorban. www.gabriellebernstein.com<br><br>A csomag tartalma:<br>- Az Univerzum mindenben támogat - 52 lapos kártyacsomag<br>- Csodák ideje - szemelvényeket tartalmazó könyv<br>- 1 db organzatasak'),
('9789633244418', 811, 10, 648, 'Harry Potter és a Főnix Rendje', 8, 2, '2021-06-19', 4290, 4000, 1, 9, 'Harry Potter nem hitte volna, hogy egyszer ő fogja megvédeni basáskodó unokatestvérét, Dudley-t. Ám amikor fényes nappal dementorok támadnak kettőjükre, ez történik. De számos más vészjósló esemény is mutatja, hogy a varázsvilág békéjét sötét erők fenyegetik.<br>Harry nincs egyedül az ellenük vívott küzdelemben: a Főnix Rendje egy titkos főhadiszálláson szervezi a Sötét Nagyúr elleni harcot, ami minden fronton zajlik. Harry például kénytelen különórákat venni Piton professzortól, hogy ki tudja védeni Voldemort erőszakos behatolásait a tudatába.'),
('9789634037798', 256, 3, 240, 'Dusty - Egy életre szóló barátság', NULL, 2, '2023-01-20', 2490, NULL, 1, 20, 'Paul egy nap szorult helyzetbe kerül. Tudja, hogy semmi esélye nincs az öt utcagyerekkel szemben, akik a pénzét akarják. Ám hirtelen megjelenik egy kutya, és elijeszti a támadókat. Ettől kezdve Paul és Dusty elválaszthatatlan barátok lesznek. A fiú érzi, hogy a kutya fél valamitől. De vajon mitől? Paul előtt lassan, lépésről-lépésre feltárul egy titok. Tudja, hogy Dusty ártatlan, de vajon be tudja majd bizonyítani?'),
('9789635073948', 209, 1, 400, 'Köss békét bárkivel', NULL, 1, '2023-04-17', 5690, NULL, 1, 0, 'Az egyszerű, apró nézeteltérésektől a legfájdalmasabb, akár évek óta fennálló konfliktusos helyzetek megoldásáig - mindenre van lehetőség!<br><br>A Köss békét bárkivel az első olyan könyv, amely megmutatja az olvasóknak, hogyan lehet gyorsan megoldani bármilyen konfliktust, függetlenül attól, milyen régóta tart, vagy hány ember érintett benne.<br>Dr. David J. Lieberman, az emberi viselkedés és a személyközi kapcsolatok elismert szakértője ebben a könyvében valós, életszerű példákat, technikákat és pszichológiai stratégiákat kínál, melyeknek köszönhetően elérheted, hogy a másik ember túllépjen a sérelmen, a dühön, a fájdalmon, és hajlandó legyen békét kötni.<br><br>A könyvből megtudhatod, hogyan...<br>- vess véget bármilyen családi viszálynak,<br>- vehetsz rá bárkit, hogy bocsánatot kérjen,<br>- indíts újra baráti és más kapcsolatokat,<br>- kezeld a passzív-agresszív embereket,<br>- kaphatod meg bárkitől a megérdemelt tiszteletet,<br>- javíthatsz drasztikusan bármilyen kapcsolaton,<br>- nyerj megbocsátást bárkitől bármiért, és<br>- hangolj bárkit a saját gondolkodásmódodra.<br>Ismerd meg a legnagyobb pszichológiai titkokat, amelyek segítségével bármilyen konfliktusnak vagy vitának véget vethetsz. Tudd meg lépésről lépésre, pontosan mit kell mondanod és tenned, hogy bármilyen helyzetben békét teremts.<br><br>A hatalom, hogy véget vess bármilyen vitának, konfliktusnak vagy viszálynak, mostantól a tiéd!<br><br>\\\"Bármilyen kapcsolatban vagy helyzetben - legyen az szakmai vagy személyes - nem számít, mennyire rosszul állnak a dolgok, gyorsabban és könnyebben összehozhatod az embereket, mint azt valaha is gondoltad volna.\\\"'),
('9789635652334', 328, 2, 510, 'Az embereket szeresd, ne a tárgyakat - A minimalizmus dicsérete', NULL, 1, '2023-02-07', 5500, 5225, 1, 28, 'Képzeljünk el egy életet, amelyben mindenből kevesebb van: kevesebb holmi, kevesebb stressz, kevesebb adósság, kevesebb elégedetlenség - kevesebb zavaró tényező.<br><br>Most pedig képzeljünk el egy olyan életet, amelyben mindenből több van: több idő, több értékes kapcsolat, több támogatás, több elégedettség. Amit most elképzeltünk, nem más, mint a tudatos élet. Ennek eléréséhez azonban el kell engednünk néhány dolgot, amely az útjában áll.<br>Joshua Fields Millburn és Ryan Nicodemus - a Netflix szupersztárjai, a Minimalisták - legújabb könyvükben útmutatót kínálnak ahhoz, hogyan élhetünk teljesebb és tartalmasabb életet. Az általuk hirdetett minimalista életforma - túlmutatva a lomtalanítás témakörén - lehetővé teszi, hogy átértékeljük és meggyógyítsuk az alapvető kapcsolatainkat: a tárgyakhoz, az igazsághoz, önmagunkhoz, a pénzhez, az értékrendünkhöz, a kreativitáshoz és az embertársainkhoz fűződő viszonyunkat. Mert ha már az életünkben mindenből kevesebb van, helyet tudunk megtakarítani.<br><br>A SZERZŐKRŐL<br>Joshua Fields Millburn és Ryan Nicodemus, akiket közönségük a Minimalisták (The Minimalists) néven ismer, weboldalukon, könyveiken, podcastjukon és filmjeiken keresztül több mint 20 millió embernek segítenek abban, hogy kevesebbel éljenek tartalmas életet. Tevékenységüket méltatta a The New York Times, a The New Yorker, a The Wall Street Journal és a Time magazin is, előadásokat tartottak többek között a Harvardon, az Apple-nél és a Google-nél. Dokumentumfilmjeik, a Minimalizmus: Egy dokumentumfilm a fontos dolgokról (Minimalism: A Documentary About the Important Things) és a Minimalisták:A kevesebb most több (The Minimalists: Less Is Now) nagy sikert arattak a Netflixen. Mindketten az ohiói Daytonban nőttek fel, jelenleg Los Angelesben élnek.'),
('9789635800193', 525, 6, 410, 'A szív iránytűje', NULL, 5, '2022-07-07', 4990, 3990, 1, 18, 'London, 1865<br><br>A szabadszellemű Lady Margaret Montagu Douglas Scott a nőktől alkalmazkodást elváró társadalom ellen lázadva fittyet hány a korlátoknak és a számára elrendezett házasságnak.De a szülei, Buccleuch hercege és hercegnéje, Viktória királynő bizalmas barátai kénytelenek eltűrni a lányuk impulzív természete miatti társadalmi megrovást, és a jó társaság kiveti magából Lady Margaretet.<br><br>Margaret azonban erőt merít hozzá hasonlóan szabadszellemű társaiból, köztük Viktória királynő lányából, Louise hercegnőből, és elindul az önmegismerés rögös útján, melynek során eljut Írországba, Amerikába, majd visszatér Angliába, hogy olyan életet harcoljon ki magának, amit mindig is élnie kellett volna, és megtalálja a szerelmet, ami rá vár.<br><br>A hercegné Marguerite Kaye-vel, a szerzőtársával regényesen dolgozza fel üknagynénje, Lady Margaret Montagu Douglas Scott életét a saját családtörténetében végzett kutatások és az önmaga nem mindennapi élettörténete alapján. Történelmi részletekben gazdag, a viktoriánus udvar szalonjaiban, és a nagy ír és skót vidéki kastélyokban játszódó regény, A szív iránytűje egy lélegzetelállító romantikus történet arról a merészségről, amivel egy fiatal nő mindennel szembeszállva a szíve parancsát követi.<br><br>\\\\\\\"Sarah Ferguson yorki hercegné letehetetlen regény írt egy fiatal nőről, aki a viktoriánus kor gúzsba kötő társadalmi szokásai ellen lázad, hogy rátaláljon a saját útjára, és meglelje a szerelmet. \\\\\\\"Jeffrey Archer'),
('9789635801633', 400, 6, 400, '2gether 4.', 7, 5, '2023-10-29', 4690, NULL, 1, 0, 'Tine egy jóképű hallgató az egyetemen, míg Sarawat az egyetem egyik legnépszerűbb sráca, aki a foci és a zenei klubban is részt vesz.<br><br>Tine-t egy nap Green üldözi kezdi a szerelmével, aki azonban nem viszonozza az érzéseit. Ezért végül Tine könyörögni kezd Sarawatnak, hogy álrandizzon vele, hogy lerázhassa Greent.<br><br>Ahogy azonban az idő múlik, a tettetés kezd valósággá válni. Ám mielőtt eljutnának a \\\"boldogan éltek, míg meg nem haltak\\\" végkifejletig, még sok minden vár rájuk, a szerelemre ébredéstől egészen addig, amíg már nem tudják sem önmaguk, sem egymás előtt eltitkolni igazi érzéseiket. És amíg már valahogy nem is akarják.<br><br><br>A 2gether napjaink egyik legnépszerűbb, és legszórakoztatóbb BL regénye és TV sorozata.<br>Egy olyan szerelem története, ahol az ellentétek vonzzák egymást.'),
('9789635801657', 400, 6, 400, '2gether 5.', 7, 5, '2024-01-29', 4690, NULL, 1, 0, 'Tine egy jóképű hallgató az egyetemen, míg Sarawat az egyetem egyik legnépszerűbb sráca, aki a foci és a zenei klubban is részt vesz.<br><br>Tine-t egy nap Green üldözi kezdi a szerelmével, aki azonban nem viszonozza az érzéseit. Ezért végül Tine könyörögni kezd Sarawatnak, hogy álrandizzon vele, hogy lerázhassa Greent.<br><br>Ahogy azonban az idő múlik, a tettetés kezd valósággá válni. Ám mielőtt eljutnának a \\\"boldogan éltek, míg meg nem haltak\\\" végkifejletig, még sok minden vár rájuk, a szerelemre ébredéstől egészen addig, amíg már nem tudják sem önmaguk, sem egymás előtt eltitkolni igazi érzéseiket. És amíg már valahogy nem is akarják.<br><br><br>A 2gether napjaink egyik legnépszerűbb, és legszórakoztatóbb BL regénye és TV sorozata.<br>Egy olyan szerelem története, ahol az ellentétek vonzzák egymást.'),
('9789635873487', 40, 5, 212, 'Boribon a játszótéren', 6, 4, '2023-01-19', 2790, NULL, 1, 17, 'Irány a játszótér! Boribon magával viszi pajtását, a kis kék mackót is. De vajon egy pici mackó is annyira fogja élvezni a nagy hintát, csúszdát, homokozót, mint Boribon? A kedves mese rávilágít, hogy a nagymackóknak bizony oda kell figyelniük a kismackókra. Boribon kreatív megoldásának köszönhetően pedig minden akadály elhárul a közös játék elől - tarts velük te is!'),
('9789635974252', 299, 7, 354, 'All Your Perfects - Minden tökéletesed', NULL, 3, '2022-07-18', 3899, 3704, 1, 8, 'Egy rég elfelejtett ígéret képes lehet megmenteni a kapcsolatot?<br><br>Quinn és Graham tökéletes szerelmét veszélybe sodorja a tökéletlen házasságuk. Az évek során felgyülemlett emlékek, a hibák és a titkok már eltaszítják őket egymástól. Az egyetlen dolog, ami megmentheti őket, éppen az lehet, ami a szakadék szélére sodorta őket.<br><br>A Minden tökéletesed egy elgondolkodtató regény egy párkapcsolati gondokkal küzdő párról, akiknek múltbéli ígéreteken múlik a jövője.<br>Szívszaggató, letehetetlen olvasmány.<br><br>Az év legfelkavaróbb szerelmi története - lehet-e együtt tökéletesen boldog két tökéletlen ember?<br><br>Add át magad a sodrásának!<br><br>\\\"Minden egyes szó a lelkem legmélyéig hatolt, és míg a múlt fejezetei mosolyt csaltak az arcomra, addig a jelenben a főszereplőkkel együtt sírtam.\\\"<br>- pveronika, moly.hu<br><br>6 CSILLAG. (...) Még mindig cikáznak a gondolataim.<br>Ez a történet nagyon mély.\\\" - Angie\\\'s Dreamy Reads, goodreads.com<br><br>Szereted az érzéki, de tartalmas könyveket?<br>Vidd haza nyugodtan, tetszeni fog!<br><br>Fiatal nőknek, felső korhatár nélkül!'),
('9789636041250', 476, 9, 380, 'Az angyalos ház és más történetek', NULL, 3, '2022-10-01', 4399, 4000, 1, 1, 'Az ember mindig abba szeret bele, akibe nem kellene, vagy nem szabadna. Talán éppen azért, mert nem szabadna.<br>Egy csendes budai utcában omladozó ház áll, homlokzatán két szépséges kőangyallal. Ám úgy tűnik, a házat még ők sem képesek megóvni a lebontástól, ezért a környék lakói összefognak, hogy megmentsék. Vezetőjüket, Zsuzsát szoros szálak fűzik az épülethez: régen rendszeresen látogatta a csaknem százéves Hilda nénit, aki elmesélte neki az életét.<br>Hilda szavai életre keltik a 1910-es éveket, az angyalos ház fénykorát. Hilda és barátnői együtt élik át a századelőn a felnőtté válás és az első szerelem élményét.<br>Az angyalos ház azonban titkokat is rejt: az egyik emeleti lakásba új lakó költözik, akinél esténként különös vendégek fordulnak meg. A lányok oldalát furdalja a kíváncsiság, és amikor nyomozni kezdenek, akaratukon kívül még a kor politikai cselszövéseibe is belekeverednek.<br>A ház története a kötet végére illesztett két novellában folytatódik. A Karácsony a ligetben egy félszeg, édes-bús, tragédiába torkolló szerelmet idéz, a Rozmaring pedig a némafilmek korának Amerikájába, színpadokra és filmstúdiókba vezeti a regény egyik szereplőjét és az olvasót.'),
('9789636140861', 589, 10, 474, 'Harry Potter és a Félvér Herceg', 8, 3, '2020-03-13', 4990, NULL, 1, 14, 'A Voldemort elleni harc állása aggasztó; a baljós jeleket már a muglikormány is észleli. Szaporodnak a rejtélyes halálesetek, katasztrófák. Harry azt gyanítja, hogy esküdt ellensége, Draco Malfoy is a halálfalók jelét viseli. Az élet azonban háborús időkben sem csak harcból áll. A Weasley-ikrek üzleti tevékenysége egyre kiterjedtebb. Szerelmek szövődnek a felsőbb évesek között, a Roxfort házai pedig továbbra is versengenek egymással. Harry Dumbledore segítségével igyekszik minél alaposabban megismerni Voldemort múltját, ifjúságát, hogy rátaláljon a Sötét Nagyúr sebezhető pontjára.');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `books_genres`
--

CREATE TABLE `books_genres` (
  `isbn` varchar(13) CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci NOT NULL,
  `genre_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- A tábla adatainak kiíratása `books_genres`
--

INSERT INTO `books_genres` (`isbn`, `genre_id`) VALUES
('9789635073948', 1),
('9789635073948', 2),
('9789635652334', 1),
('9789635652334', 2),
('9789634037798', 3),
('9789634037798', 4),
('9786155716539', 3),
('9786155716539', 4),
('9789635873487', 3),
('9789635873487', 5),
('9789635801657', 4),
('9789635801657', 6),
('9789635801633', 4),
('9789635801633', 6),
('9789635974252', 4),
('9789635974252', 7),
('9789632916064', 8),
('9789636041250', 9),
('9789636041250', 10),
('9789635800193', 9),
('9789635800193', 11),
('9789633244418', 3),
('9789633244418', 4),
('9789636140861', 3),
('9789636140861', 4);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `books_writers`
--

CREATE TABLE `books_writers` (
  `isbn` varchar(13) CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci NOT NULL,
  `writer_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- A tábla adatainak kiíratása `books_writers`
--

INSERT INTO `books_writers` (`isbn`, `writer_id`) VALUES
('9789635073948', 28),
('9789635652334', 29),
('9789635652334', 30),
('9789634037798', 31),
('9786155716539', 32),
('9789635873487', 33),
('9789635801657', 34),
('9789635801633', 34),
('9789635974252', 35),
('9789632916064', 36),
('9789632916064', 37),
('9789636041250', 38),
('9789635800193', 39),
('9789633244418', 40),
('9789636140861', 40);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `comments`
--

CREATE TABLE `comments` (
  `comment_id` int NOT NULL,
  `user_id` int NOT NULL,
  `isbn` varchar(13) CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci NOT NULL,
  `comment_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci NOT NULL,
  `comment_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- A tábla adatainak kiíratása `comments`
--

INSERT INTO `comments` (`comment_id`, `user_id`, `isbn`, `comment_text`, `comment_date`) VALUES
(3, 1, '9789635073948', 'első poszt', '2023-03-31 11:14:32'),
(7, 1, '9789635800193', 'Hello World', '2023-04-10 15:54:44');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `counties`
--

CREATE TABLE `counties` (
  `county_id` int NOT NULL,
  `county` varchar(22) CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci DEFAULT NULL
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
  `cover` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- A tábla adatainak kiíratása `covers`
--

INSERT INTO `covers` (`cover_id`, `cover`) VALUES
(1, 'keménytáblás'),
(2, 'kartonált'),
(3, 'puhatáblás'),
(4, 'keménykötésű'),
(5, 'kartonkötés');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `genres`
--

CREATE TABLE `genres` (
  `genre_id` int NOT NULL,
  `genre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- A tábla adatainak kiíratása `genres`
--

INSERT INTO `genres` (`genre_id`, `genre`) VALUES
(1, 'életmód'),
(2, 'egészség'),
(3, 'ifjúsági'),
(4, 'szórakoztató'),
(5, 'mese'),
(6, 'lektűr'),
(7, 'erotikus'),
(8, 'ezotéria'),
(9, 'szépirodalom'),
(10, 'regény'),
(11, 'történelmi');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `languages`
--

CREATE TABLE `languages` (
  `language_id` int NOT NULL,
  `language` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- A tábla adatainak kiíratása `languages`
--

INSERT INTO `languages` (`language_id`, `language`) VALUES
(1, 'magyar');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `login`
--

CREATE TABLE `login` (
  `login_id` int NOT NULL,
  `user_id` int NOT NULL,
  `username` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci NOT NULL,
  `password` char(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci NOT NULL,
  `salt` char(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- A tábla adatainak kiíratása `login`
--

INSERT INTO `login` (`login_id`, `user_id`, `username`, `email`, `password`, `salt`) VALUES
(1, 1, 'jazehin', 'jazehin@outlook.com', 'e0efd41c1949f1d76a8abd8d0dfe84b694e3cdeb56c9d0bb60d32919f86c46de', 'KPEsolIEGj'),
(2, 2, 'admin', 'admin@admin.com', '895d3acc59990382039c82306d7eacaf73bf49c445c690619652894c4045cb46', '80rSmKia$w'),
(9, 9, 'modi', 'mod@mod.com', '1feda0cdeb4f4aae0bb63cf93c28635068c3ccf7cedb06928f61fd7f38850ea1', 'ktVARyBXCi');

-- --------------------------------------------------------

--
-- A nézet helyettes szerkezete `new_books`
-- (Lásd alább az aktuális nézetet)
--
CREATE TABLE `new_books` (
`isbn` varchar(13)
);

-- --------------------------------------------------------

--
-- A nézet helyettes szerkezete `not_yet_published`
-- (Lásd alább az aktuális nézetet)
--
CREATE TABLE `not_yet_published` (
`isbn` varchar(13)
);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `orders`
--

CREATE TABLE `orders` (
  `order_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `address_id` int NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `price_sum` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- A tábla adatainak kiíratása `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `address_id`, `order_date`, `price_sum`) VALUES
(27, 1, 23, '2023-04-11 18:24:58', 16608);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `order_details`
--

CREATE TABLE `order_details` (
  `order_id` int NOT NULL,
  `isbn` varchar(13) CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci NOT NULL,
  `quantity` int UNSIGNED NOT NULL,
  `price` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- A tábla adatainak kiíratása `order_details`
--

INSERT INTO `order_details` (`order_id`, `isbn`, `quantity`, `price`) VALUES
(27, '9789632916064', 2, 4490),
(27, '9789636041250', 1, 4000),
(27, '9789636140861', 1, 4990);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `preferences`
--

CREATE TABLE `preferences` (
  `user_id` int NOT NULL,
  `genre_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- A tábla adatainak kiíratása `preferences`
--

INSERT INTO `preferences` (`user_id`, `genre_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 9);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `publishers`
--

CREATE TABLE `publishers` (
  `publisher_id` int NOT NULL,
  `publisher` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- A tábla adatainak kiíratása `publishers`
--

INSERT INTO `publishers` (`publisher_id`, `publisher`) VALUES
(1, 'ÉDESVÍZ KIADÓ'),
(2, 'HVG Könyvek'),
(3, 'MANÓ KÖNYVEK'),
(4, 'DAS könyvek'),
(5, 'Pagony Kiadó Kft.'),
(6, 'MŰVELT NÉP KÖNYVKIADÓ'),
(7, 'KÖNYVMOLYKÉPZŐ KIADÓ KFT.'),
(8, 'BIOENERGETIC KIADÓ KFT.'),
(9, 'Libri Könyvkiadó Kft.'),
(10, 'Animus Kiadó');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `ratings`
--

CREATE TABLE `ratings` (
  `user_id` int NOT NULL,
  `isbn` varchar(13) CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci NOT NULL,
  `rating` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- A tábla adatainak kiíratása `ratings`
--

INSERT INTO `ratings` (`user_id`, `isbn`, `rating`) VALUES
(1, '9789635073948', 5),
(2, '9789635073948', 4);

-- --------------------------------------------------------

--
-- A nézet helyettes szerkezete `sales_by_genres`
-- (Lásd alább az aktuális nézetet)
--
CREATE TABLE `sales_by_genres` (
`genre` varchar(255)
,`SUM(quantity * order_details.price)` decimal(42,0)
,`SUM(quantity)` decimal(32,0)
);

-- --------------------------------------------------------

--
-- A nézet helyettes szerkezete `sales_by_publishers`
-- (Lásd alább az aktuális nézetet)
--
CREATE TABLE `sales_by_publishers` (
`publisher` varchar(255)
,`SUM(quantity * order_details.price)` decimal(42,0)
,`SUM(quantity)` decimal(32,0)
);

-- --------------------------------------------------------

--
-- A nézet helyettes szerkezete `sales_by_writers`
-- (Lásd alább az aktuális nézetet)
--
CREATE TABLE `sales_by_writers` (
`SUM(quantity * order_details.price)` decimal(42,0)
,`SUM(quantity)` decimal(32,0)
,`writer` varchar(255)
);

-- --------------------------------------------------------

--
-- A nézet helyettes szerkezete `sales_of_last_month`
-- (Lásd alább az aktuális nézetet)
--
CREATE TABLE `sales_of_last_month` (
`isbn` varchar(13)
,`sum(order_details.quantity * order_details.price)` decimal(42,0)
,`sum(order_details.quantity)` decimal(32,0)
,`title` varchar(255)
);

-- --------------------------------------------------------

--
-- A nézet helyettes szerkezete `sales_of_last_quarter`
-- (Lásd alább az aktuális nézetet)
--
CREATE TABLE `sales_of_last_quarter` (
`isbn` varchar(13)
,`SUM(order_details.quantity * order_details.price)` decimal(42,0)
,`SUM(order_details.quantity)` decimal(32,0)
,`title` varchar(255)
);

-- --------------------------------------------------------

--
-- A nézet helyettes szerkezete `sales_of_last_week`
-- (Lásd alább az aktuális nézetet)
--
CREATE TABLE `sales_of_last_week` (
`isbn` varchar(13)
,`SUM(order_details.quantity * order_details.price)` decimal(42,0)
,`SUM(order_details.quantity)` decimal(32,0)
,`title` varchar(255)
);

-- --------------------------------------------------------

--
-- A nézet helyettes szerkezete `sales_of_last_year`
-- (Lásd alább az aktuális nézetet)
--
CREATE TABLE `sales_of_last_year` (
`isbn` varchar(13)
,`SUM(order_details.quantity * order_details.price)` decimal(42,0)
,`SUM(order_details.quantity)` decimal(32,0)
,`title` varchar(255)
);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `serieses`
--

CREATE TABLE `serieses` (
  `series_id` int NOT NULL,
  `series` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- A tábla adatainak kiíratása `serieses`
--

INSERT INTO `serieses` (`series_id`, `series`) VALUES
(6, 'Boribon'),
(7, '2gether'),
(8, 'Harry Potter');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `type_id` int NOT NULL,
  `family_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci DEFAULT NULL,
  `given_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci DEFAULT NULL,
  `gender` enum('female','male') CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `phone_number` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci DEFAULT NULL,
  `points` int UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- A tábla adatainak kiíratása `users`
--

INSERT INTO `users` (`user_id`, `type_id`, `family_name`, `given_name`, `gender`, `birthdate`, `phone_number`, `points`) VALUES
(1, 3, NULL, NULL, 'male', '2003-03-08', NULL, 3527),
(2, 1, NULL, NULL, NULL, NULL, NULL, 0),
(9, 2, NULL, NULL, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `users_addresses`
--

CREATE TABLE `users_addresses` (
  `user_id` int NOT NULL,
  `address_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- A tábla adatainak kiíratása `users_addresses`
--

INSERT INTO `users_addresses` (`user_id`, `address_id`) VALUES
(1, 20),
(1, 21);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `user_types`
--

CREATE TABLE `user_types` (
  `type_id` int NOT NULL,
  `type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci NOT NULL
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
  `writer` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_hungarian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- A tábla adatainak kiíratása `writers`
--

INSERT INTO `writers` (`writer_id`, `writer`) VALUES
(28, 'Dr. David J. Lieberman'),
(29, 'Joshua Fields Millburn'),
(30, 'Ryan Nicodemus'),
(31, 'Jan Andersen'),
(32, 'Trogmayer Éva'),
(33, 'Marék Veronika'),
(34, 'JittiRain'),
(35, 'Colleen Hoover'),
(36, 'Gabrielle Bernstein'),
(37, 'Micaela Ezra'),
(38, 'Fábián Janka'),
(39, 'Sarah Ferguson yorki hercegné'),
(40, 'J. K. Rowling');

-- --------------------------------------------------------

--
-- Nézet szerkezete `bestsellers`
--
DROP TABLE IF EXISTS `bestsellers`;

CREATE ALGORITHM=UNDEFINED DEFINER=`jazehin`@`localhost` SQL SECURITY DEFINER VIEW `bestsellers`  AS SELECT `books`.`isbn` AS `isbn`, sum(`order_details`.`quantity`) AS `sum(order_details.quantity)` FROM (`books` join `order_details` on((`books`.`isbn` = `order_details`.`isbn`))) GROUP BY `books`.`isbn` ORDER BY sum(`order_details`.`quantity`) DESC ;

-- --------------------------------------------------------

--
-- Nézet szerkezete `new_books`
--
DROP TABLE IF EXISTS `new_books`;

CREATE ALGORITHM=UNDEFINED DEFINER=`jazehin`@`localhost` SQL SECURITY DEFINER VIEW `new_books`  AS SELECT `books`.`isbn` AS `isbn` FROM `books` WHERE ((`books`.`date_published` <= curdate()) AND (`books`.`date_published` >= (curdate() - interval 1 month))) ORDER BY `books`.`date_published` DESC ;

-- --------------------------------------------------------

--
-- Nézet szerkezete `not_yet_published`
--
DROP TABLE IF EXISTS `not_yet_published`;

CREATE ALGORITHM=UNDEFINED DEFINER=`jazehin`@`localhost` SQL SECURITY DEFINER VIEW `not_yet_published`  AS SELECT `books`.`isbn` AS `isbn` FROM `books` WHERE (`books`.`date_published` > curdate()) ORDER BY `books`.`date_published` ASC ;

-- --------------------------------------------------------

--
-- Nézet szerkezete `sales_by_genres`
--
DROP TABLE IF EXISTS `sales_by_genres`;

CREATE ALGORITHM=UNDEFINED DEFINER=`jazehin`@`localhost` SQL SECURITY DEFINER VIEW `sales_by_genres`  AS SELECT `genres`.`genre` AS `genre`, sum(`order_details`.`quantity`) AS `SUM(quantity)`, sum((`order_details`.`quantity` * `order_details`.`price`)) AS `SUM(quantity * order_details.price)` FROM (((`genres` join `books_genres` on((`genres`.`genre_id` = `books_genres`.`genre_id`))) join `books` on((`books`.`isbn` = `books_genres`.`isbn`))) join `order_details` on((`order_details`.`isbn` = `books`.`isbn`))) GROUP BY `genres`.`genre` HAVING (sum((`order_details`.`quantity` * `order_details`.`price`)) > 0) ORDER BY sum((`order_details`.`quantity` * `order_details`.`price`)) DESC ;

-- --------------------------------------------------------

--
-- Nézet szerkezete `sales_by_publishers`
--
DROP TABLE IF EXISTS `sales_by_publishers`;

CREATE ALGORITHM=UNDEFINED DEFINER=`jazehin`@`localhost` SQL SECURITY DEFINER VIEW `sales_by_publishers`  AS SELECT `publishers`.`publisher` AS `publisher`, sum(`order_details`.`quantity`) AS `SUM(quantity)`, sum((`order_details`.`quantity` * `order_details`.`price`)) AS `SUM(quantity * order_details.price)` FROM ((`publishers` join `books` on((`publishers`.`publisher_id` = `books`.`publisher_id`))) join `order_details` on((`order_details`.`isbn` = `books`.`isbn`))) WHERE (`publishers`.`publisher` is not null) GROUP BY `publishers`.`publisher` HAVING (sum((`order_details`.`quantity` * `order_details`.`price`)) > 0) ORDER BY sum((`order_details`.`quantity` * `order_details`.`price`)) DESC ;

-- --------------------------------------------------------

--
-- Nézet szerkezete `sales_by_writers`
--
DROP TABLE IF EXISTS `sales_by_writers`;

CREATE ALGORITHM=UNDEFINED DEFINER=`jazehin`@`localhost` SQL SECURITY DEFINER VIEW `sales_by_writers`  AS SELECT `writers`.`writer` AS `writer`, sum(`order_details`.`quantity`) AS `SUM(quantity)`, sum((`order_details`.`quantity` * `order_details`.`price`)) AS `SUM(quantity * order_details.price)` FROM (((`writers` join `books_writers` on((`writers`.`writer_id` = `books_writers`.`writer_id`))) join `books` on((`books`.`isbn` = `books_writers`.`isbn`))) join `order_details` on((`order_details`.`isbn` = `books`.`isbn`))) GROUP BY `writers`.`writer` HAVING (sum((`order_details`.`quantity` * `order_details`.`price`)) > 0) ORDER BY sum((`order_details`.`quantity` * `order_details`.`price`)) DESC ;

-- --------------------------------------------------------

--
-- Nézet szerkezete `sales_of_last_month`
--
DROP TABLE IF EXISTS `sales_of_last_month`;

CREATE ALGORITHM=UNDEFINED DEFINER=`jazehin`@`localhost` SQL SECURITY DEFINER VIEW `sales_of_last_month`  AS SELECT `books`.`isbn` AS `isbn`, `books`.`title` AS `title`, sum(`order_details`.`quantity`) AS `sum(order_details.quantity)`, sum((`order_details`.`quantity` * `order_details`.`price`)) AS `sum(order_details.quantity * order_details.price)` FROM ((`books` join `order_details` on((`order_details`.`isbn` = `books`.`isbn`))) join `orders` on((`order_details`.`order_id` = `orders`.`order_id`))) WHERE (`orders`.`order_date` >= (curdate() - interval 1 month)) GROUP BY `books`.`isbn` HAVING (sum((`order_details`.`quantity` * `order_details`.`price`)) > 0) ORDER BY sum((`order_details`.`quantity` * `order_details`.`price`)) DESC ;

-- --------------------------------------------------------

--
-- Nézet szerkezete `sales_of_last_quarter`
--
DROP TABLE IF EXISTS `sales_of_last_quarter`;

CREATE ALGORITHM=UNDEFINED DEFINER=`jazehin`@`localhost` SQL SECURITY DEFINER VIEW `sales_of_last_quarter`  AS SELECT `books`.`isbn` AS `isbn`, `books`.`title` AS `title`, sum(`order_details`.`quantity`) AS `SUM(order_details.quantity)`, sum((`order_details`.`quantity` * `order_details`.`price`)) AS `SUM(order_details.quantity * order_details.price)` FROM ((`books` join `order_details` on((`order_details`.`isbn` = `books`.`isbn`))) join `orders` on((`order_details`.`order_id` = `orders`.`order_id`))) WHERE (`orders`.`order_date` >= (curdate() - interval 3 month)) GROUP BY `books`.`isbn` HAVING (sum((`order_details`.`quantity` * `order_details`.`price`)) > 0) ORDER BY sum((`order_details`.`quantity` * `order_details`.`price`)) DESC ;

-- --------------------------------------------------------

--
-- Nézet szerkezete `sales_of_last_week`
--
DROP TABLE IF EXISTS `sales_of_last_week`;

CREATE ALGORITHM=UNDEFINED DEFINER=`jazehin`@`localhost` SQL SECURITY DEFINER VIEW `sales_of_last_week`  AS SELECT `books`.`isbn` AS `isbn`, `books`.`title` AS `title`, sum(`order_details`.`quantity`) AS `SUM(order_details.quantity)`, sum((`order_details`.`quantity` * `order_details`.`price`)) AS `SUM(order_details.quantity * order_details.price)` FROM ((`books` join `order_details` on((`order_details`.`isbn` = `books`.`isbn`))) join `orders` on((`order_details`.`order_id` = `orders`.`order_id`))) WHERE (`orders`.`order_date` >= (curdate() - interval 7 day)) GROUP BY `books`.`isbn` HAVING (sum((`order_details`.`quantity` * `order_details`.`price`)) > 0) ORDER BY sum((`order_details`.`quantity` * `order_details`.`price`)) DESC ;

-- --------------------------------------------------------

--
-- Nézet szerkezete `sales_of_last_year`
--
DROP TABLE IF EXISTS `sales_of_last_year`;

CREATE ALGORITHM=UNDEFINED DEFINER=`jazehin`@`localhost` SQL SECURITY DEFINER VIEW `sales_of_last_year`  AS SELECT `books`.`isbn` AS `isbn`, `books`.`title` AS `title`, sum(`order_details`.`quantity`) AS `SUM(order_details.quantity)`, sum((`order_details`.`quantity` * `order_details`.`price`)) AS `SUM(order_details.quantity * order_details.price)` FROM ((`books` join `order_details` on((`order_details`.`isbn` = `books`.`isbn`))) join `orders` on((`order_details`.`order_id` = `orders`.`order_id`))) WHERE (`orders`.`order_date` >= (curdate() - interval 1 year)) GROUP BY `books`.`isbn` HAVING (sum(`order_details`.`quantity`) > 0) ORDER BY sum(`order_details`.`quantity`) DESC ;

--
-- Indexek a kiírt táblákhoz
--

--
-- A tábla indexei `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`address_id`),
  ADD KEY `county_id` (`county_id`);
ALTER TABLE `addresses` ADD FULLTEXT KEY `city` (`city`);

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
-- A tábla indexei `preferences`
--
ALTER TABLE `preferences`
  ADD KEY `user_id` (`user_id`),
  ADD KEY `genre_id` (`genre_id`);

--
-- A tábla indexei `publishers`
--
ALTER TABLE `publishers`
  ADD PRIMARY KEY (`publisher_id`);

--
-- A tábla indexei `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`user_id`,`isbn`),
  ADD KEY `isbn` (`isbn`);

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
  MODIFY `address_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT a táblához `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT a táblához `counties`
--
ALTER TABLE `counties`
  MODIFY `county_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT a táblához `covers`
--
ALTER TABLE `covers`
  MODIFY `cover_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT a táblához `genres`
--
ALTER TABLE `genres`
  MODIFY `genre_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT a táblához `languages`
--
ALTER TABLE `languages`
  MODIFY `language_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT a táblához `login`
--
ALTER TABLE `login`
  MODIFY `login_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT a táblához `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT a táblához `publishers`
--
ALTER TABLE `publishers`
  MODIFY `publisher_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT a táblához `serieses`
--
ALTER TABLE `serieses`
  MODIFY `series_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT a táblához `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT a táblához `user_types`
--
ALTER TABLE `user_types`
  MODIFY `type_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT a táblához `writers`
--
ALTER TABLE `writers`
  MODIFY `writer_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

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
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`address_id`);

--
-- Megkötések a táblához `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`isbn`) REFERENCES `books` (`isbn`),
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- Megkötések a táblához `preferences`
--
ALTER TABLE `preferences`
  ADD CONSTRAINT `preferences_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `preferences_ibfk_2` FOREIGN KEY (`genre_id`) REFERENCES `genres` (`genre_id`);

--
-- Megkötések a táblához `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`isbn`) REFERENCES `books` (`isbn`);

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
