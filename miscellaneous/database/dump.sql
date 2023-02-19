-- int types size reference: https://dev.mysql.com/doc/refman/5.6/en/integer-types.html

DROP DATABASE IF EXISTS rendelesadatok;
DROP DATABASE IF EXISTS felhasznaloadatok;
DROP DATABASE IF EXISTS konyvadatok;

CREATE DATABASE IF NOT EXISTS rendelesadatok;
CREATE DATABASE IF NOT EXISTS felhasznaloadatok;
CREATE DATABASE IF NOT EXISTS konyvadatok;

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
LIMIT 15;USE felhasznaloadatok;

-- tábla a felhasználótípusok tárolására
CREATE TABLE felhasznalotipusok (
    tipusid INT AUTO_INCREMENT NOT NULL,
    tipusnev VARCHAR(30) CHARACTER SET UTF8MB4 NOT NULL,
    PRIMARY KEY (tipusid)
);

-- beillesztem az előző táblába a felhasználó típusokat
INSERT INTO felhasznalotipusok (tipusnev) VALUES
("adminisztrátor"),
("moderátor"),
("felhasználó");

-- tábla a felhaszálói adatok tárolására
CREATE TABLE felhasznalok (
    felhasznaloid INT AUTO_INCREMENT NOT NULL,
    felhasznalonev VARCHAR(20) CHARACTER SET UTF8MB4 NOT NULL,
    tipusid INT NOT NULL,
    vezeteknev VARCHAR(50) CHARACTER SET UTF8MB4 NULL,
    keresztnev VARCHAR(50) CHARACTER SET UTF8MB4 NULL,
    nem ENUM('nő', 'férfi', 'na') NOT NULL DEFAULT 'na',
    szuldatum DATE NULL,
    telszam VARCHAR(12) NULL,
    PRIMARY KEY (felhasznaloid),
    FOREIGN KEY (tipusid) REFERENCES felhasznalotipusok(tipusid)
);

-- tábla az értékelések tárolására
CREATE TABLE ertekelesek (
    felhasznaloid INT NOT NULL,
    isbn VARCHAR(13) CHARACTER SET UTF8MB4 NOT NULL,
    ertekeles TINYINT NOT NULL,
    PRIMARY KEY (felhasznaloid, isbn),
    FOREIGN KEY (felhasznaloid) REFERENCES felhasznalok(felhasznaloid),
    FOREIGN KEY (isbn) REFERENCES konyvadatok.konyvek(isbn)
);

-- tábla a szállítási címek tárolására
CREATE TABLE szallitasicimek (
    szallitasicimid INT AUTO_INCREMENT NOT NULL,
    cegnev VARCHAR(100) NULL,
    megye ENUM('Bács-Kiskun','Baranya','Békés','Borsod-Abaúj-Zemplén','Csongrád-Csanád','Fejér','Győr-Moson-Sopron','Hajdú-Bihar','Heves','Jász-Nagykun-Szolnok','Komárom-Esztergom','Nógrád','Pest','Somogy','Szabolcs-Szatmár-Bereg','Tolna','Vas','Veszprém','Zala') NOT NULL,
    varos VARCHAR(50) NOT NULL,
    kozterulet VARCHAR(50) NOT NULL,
    megjegyzes VARCHAR(50) NULL,
    PRIMARY KEY (szallitasicimid)
);

-- tábla a belépési adatok tárolására (európai szabvány: GDPR)
CREATE TABLE belepesiadatok (
    belepesid INT AUTO_INCREMENT NOT NULL,
    felhasznaloid INT NOT NULL,
    email VARCHAR(255) CHARACTER SET UTF8MB4 NOT NULL,
    jelszo CHAR(64) CHARACTER SET UTF8MB4 NOT NULL,
    PRIMARY KEY (belepesid),
    FOREIGN KEY (felhasznaloid) REFERENCES felhasznalok(felhasznaloid)
);

-- tábla a kommentek tárolására
CREATE TABLE kommentek (
    kommentid INT AUTO_INCREMENT NOT NULL,
    felhasznaloid INT NOT NULL, -- ki írta?
    isbn VARCHAR(13) CHARACTER SET UTF8MB4 NOT NULL, -- melyik könyv oldala alá írta?
    szoveg TEXT CHARACTER SET UTF8MB4 NOT NULL, -- mit írt?
    datum TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(), -- mikor írta? (ez alapján lesz rendezve)
    PRIMARY KEY (kommentid),
    FOREIGN KEY (felhasznaloid) REFERENCES felhasznalok(felhasznaloid),
    FOREIGN KEY (isbn) REFERENCES konyvadatok.konyvek(isbn)
)

-- nézet a kommentek lekérdezésére
DELIMITER //
CREATE PROCEDURE GetKommentek(isbn VARCHAR(13))
BEGIN
    SELECT felhasznalok.felhasznaloid AS fid, felhasznalok.felhasznalonev AS fnev, kommentek.szoveg AS szoveg, kommentek.datum AS datum 
    FROM kommentek INNER JOIN felhasznalok ON kommentek.felhasznaloid = felhasznalok.felhasznaloid
    WHERE kommentek.isbn = isbn
    ORDER BY datum DESC;
END
// DELIMITER ;

USE rendelesadatok;

-- tábla a kosarak tárolására
-- CREATE TABLE kosarak (
--     kosarid INT AUTO_INCREMENT NOT NULL,
--     felhasznaloid INT NOT NULL,
--     PRIMARY KEY (kosarid, felhasznaloid),
--     FOREIGN KEY (felhasznaloid) REFERENCES felhasznaloadatok.felhasznalok(felhasznaloid)
-- );

-- tábla a rendelés összesítésére
CREATE TABLE rendelesek (
    rendelesid INT AUTO_INCREMENT NOT NULL,
    felhasznaloid INT NOT NULL,
    ido TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    osszesar INT NOT NULL,
    PRIMARY KEY (rendelesid),
    FOREIGN KEY (felhasznaloid) REFERENCES felhasznaloadatok.felhasznalok(felhasznaloid)
);

-- tábla a kosarak tartalmának tárolására
CREATE TABLE rendelesreszletek (
    rendelesid INT NOT NULL,
    isbn VARCHAR(13) CHARACTER SET UTF8MB4 NOT NULL,
    mennyiseg INT UNSIGNED NOT NULL,
    eredetiar INT NOT NULL,
    akciosar INT NULL,
    PRIMARY KEY (rendelesid, isbn),
    FOREIGN KEY (isbn) REFERENCES konyvadatok.konyvek(isbn)
);

-- nézet egy adott felhasználó kosarának tartalmának lekérdezésére
DELIMITER //
CREATE PROCEDURE GetReszletesKosar(felhasznaloid INT)
BEGIN
    SELECT 
        rendelesreszletek.isbn AS isbn,
        konyvek.konyvcim AS cim,
        rendelesreszletek.mennyiseg AS mennyiseg, 
        (rendelesreszletek.mennyiseg * konyvek.nettoar) AS ar
    FROM rendelesadatok.rendelesreszletek INNER JOIN konyvadatok.konyvek ON rendelesreszletek.isbn = konyvek.isbn
    WHERE felhasznaloid = kosartartalmak.felhasznaloid; 
END
// DELIMITER ;

