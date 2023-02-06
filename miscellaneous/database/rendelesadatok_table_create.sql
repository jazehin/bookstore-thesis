USE rendelesadatok;

-- tábla a kosarak tárolására
-- CREATE TABLE kosarak (
--     kosarid INT AUTO_INCREMENT NOT NULL,
--     felhasznaloid INT NOT NULL,
--     PRIMARY KEY (kosarid, felhasznaloid),
--     FOREIGN KEY (felhasznaloid) REFERENCES felhasznaloadatok.felhasznalok(felhasznaloid)
-- );

-- tábla a kosarak tartalmának tárolására
CREATE TABLE kosartartalmak (
    tartalomid INT AUTO_INCREMENT NOT NULL,
    felhasznaloid INT NOT NULL,
    ISBN VARCHAR(13) CHARACTER SET UTF8MB4 NOT NULL,
    mennyiseg INT UNSIGNED NOT NULL,
    PRIMARY KEY (tartalomid),
    FOREIGN KEY (felhasznaloid) REFERENCES felhasznaloadatok.felhasznalok(felhasznaloid),
    FOREIGN KEY (ISBN) REFERENCES konyvadatok.konyvek(ISBN)
);

-- nézet egy adott felhasználó kosarának tartalmának lekérdezésére
DELIMITER //
CREATE PROCEDURE GetKosar(felhasznaloid INT)
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

-- tábla a szállítási címeknek