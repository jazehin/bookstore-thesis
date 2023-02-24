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

