USE konyvadatok;

-- tábla a könyvsorozatok tárolására
CREATE TABLE konyvsorozatok (
    sorozatid INT AUTO_INCREMENT NOT NULL,
    sorozatnev VARCHAR(255) CHARACTER SET UTF8MB4,
    PRIMARY KEY (sorozatid)
);

-- tábla a kiadók tárolására
CREATE TABLE kiadok (
    kiadoid INT AUTO_INCREMENT NOT NULL,
    kiadonev VARCHAR(255) CHARACTER SET UTF8MB4,
    PRIMARY KEY (kiadoid)
);

-- tábla a nyelvek tárolására
CREATE TABLE nyelvek (
    nyelvid INT AUTO_INCREMENT NOT NULL,
    nyelvnev VARCHAR(30) CHARACTER SET UTF8MB4,
    PRIMARY KEY (nyelvid)
);

-- tábla a műfajok tárolására
CREATE TABLE mufajok (
    mufajid INT AUTO_INCREMENT NOT NULL,
    mufajnev VARCHAR(255) CHARACTER SET UTF8MB4 NOT NULL,
    PRIMARY KEY (mufajid)
);

-- tábla a kötéstípusok tárolására
CREATE TABLE kotestipusok (
    kotestipusid INT AUTO_INCREMENT NOT NULL,
    kotestipusnev VARCHAR(30) CHARACTER SET UTF8MB4,
    PRIMARY KEY (kotestipusid)
);

-- tábla könyvek tárolására
CREATE TABLE konyvek (
    isbn VARCHAR(13) CHARACTER SET UTF8MB4 NOT NULL,
    oldalszam SMALLINT UNSIGNED NOT NULL,
    kiadoid INT NOT NULL,
    suly INT UNSIGNED NULL, -- grammban adjuk meg
    konyvcim VARCHAR(255) CHARACTER SET UTF8MB4 NOT NULL,
    -- boritokep VARCHAR(255) CHARACTER SET UTF8MB4 NULL, -- új megoldás: ./covers/{isbn}.png
    sorozatid INT NULL,
    kotestipusid INT NOT NULL,
    kiadasdatuma DATE NOT NULL,
    ar SMALLINT UNSIGNED NOT NULL, -- forintban adjuk meg
    akciosar SMALLINT UNSIGNED NULL, -- forintban adjuk meg
    nyelvid INT NOT NULL,
    keszlet SMALLINT UNSIGNED NOT NULL,
    leiras TEXT NOT NULL,
    PRIMARY KEY (isbn),
    FOREIGN key (kiadoid) REFERENCES kiadok(kiadoid),
    FOREIGN KEY (sorozatid) REFERENCES konyvsorozatok(sorozatid),
    FOREIGN KEY (nyelvid) REFERENCES nyelvek(nyelvid),
    FOREIGN KEY (kotestipusid) REFERENCES kotestipusok(kotestipusid)
);

-- tábla a könyvek és műfajok összekötésére
CREATE TABLE konyvek_mufajok (
    id INT AUTO_INCREMENT NOT NULL,
    isbn VARCHAR(13) CHARACTER SET UTF8MB4 NOT NULL,
    mufajid INT NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (isbn) REFERENCES konyvek(isbn),
    FOREIGN KEY (mufajid) REFERENCES mufajok(mufajid)
);

-- tábla a műfajok tárolására
CREATE TABLE irok (
    iroid INT AUTO_INCREMENT NOT NULL,
    ironev VARCHAR(255) CHARACTER SET UTF8MB4 NOT NULL,
    PRIMARY KEY (iroid)
);

-- tábla a könyvek és műfajok összekötésére
CREATE TABLE konyvek_irok (
    id INT AUTO_INCREMENT NOT NULL,
    isbn VARCHAR(13) CHARACTER SET UTF8MB4 NOT NULL,
    iroid INT NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (isbn) REFERENCES konyvek(isbn),
    FOREIGN KEY (iroid) REFERENCES irok(iroid)
);

-- nézet a legújabb könyvek lekérdezésére
CREATE VIEW legujabb_konyvek AS
SELECT * FROM konyvek
ORDER BY kiadasdatuma DESC
LIMIT 15;

-- tárolt eljárás egy könyv adatainak lekérésére ISBN alapján
DEIMITER //
CREATE PROCEDURE `GetBookByISBN`(IN _isbn VARCHAR(13))
BEGIN
    SELECT 
		isbn,
		oldalszam,
		kiadonev AS kiado,
        suly, 
        konyvcim, 
        sorozatnev AS sorozat, 
        kotestipusnev AS kotestipus, 
        kiadasdatuma,
        ar,
        akciosar,
        nyelvnev AS nyelv,
        keszlet,
        leiras
	FROM
		konyvadatok.konyvek 
        INNER JOIN konyvadatok.kiadok ON konyvek.kiadoid = kiadok.kiadoid
        LEFT JOIN konyvadatok.konyvsorozatok ON konyvek.sorozatid = konyvsorozatok.sorozatid -- lehet hogy nem sorozat része!
        INNER JOIN konyvadatok.kotestipusok ON konyvek.kotestipusid = kotestipusok.kotestipusid
        INNER JOIN konyvadatok.nyelvek ON konyvek.nyelvid = nyelvek.nyelvid
	WHERE
		isbn = _isbn;
END //
DELIMITER ;

-- TODO: tárolt eljárások egy könyv íróinak és műfajainak lekérdezésére