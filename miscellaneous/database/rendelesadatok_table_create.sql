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
    ISBN VARCHAR(13) CHARACTER SET UTF8MB4 NOT NULL,

    mennyiseg INT UNSIGNED NOT NULL,
    eredetiar INT NOT NULL,
    akciosar INT NULL,
    PRIMARY KEY (rendelesid, ISBN),
    FOREIGN KEY (felhasznaloid) REFERENCES felhasznaloadatok.felhasznalok(felhasznaloid),
    FOREIGN KEY (ISBN) REFERENCES konyvadatok.konyvek(ISBN)
);

-- nézet egy adott felhasználó kosarának tartalmának lekérdezésére
DELIMITER //
CREATE PROCEDURE GetReszletesKosar(felhasznaloid INT)
BEGIN
    SELECT 
        kosartartalmak.ISBN AS ISBN,
        konyvek.konyvcim AS cim,
        kosartartalmak.mennyiseg AS mennyiseg, 
        (kosartartalmak.mennyiseg * konyvek.nettoar * (1 + konyvek.afakulcs)) AS ar
    FROM rendelesadatok.kosartartalmak INNER JOIN konyvadatok.konyvek ON kosartartalmak.ISBN = konyvek.ISBN
    WHERE felhasznaloid = kosartartalmak.felhasznaloid; 
END
// DELIMITER ;
