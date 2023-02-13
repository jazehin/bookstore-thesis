USE felhasznaloadatok;

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

