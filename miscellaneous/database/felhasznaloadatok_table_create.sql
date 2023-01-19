-- int types size reference: https://dev.mysql.com/doc/refman/5.6/en/integer-types.html

USE felhasznaloadatok;

-- tábla a felhaszálói adatok tárolására
CREATE TABLE felhasznalok (
    felhasznaloid INT NOT NULL,
    felhasznalonev VARCHAR(20) NOT NULL,
    vezeteknev VARCHAR(50) NULL,
    keresztnev VARCHAR(50) NULL,
    nem ENUM('nő', 'férfi', 'na') NOT NULL DEFAULT 'na',
    szuldatum DATE NULL,
    PRIMARY KEY (felhasznaloid)
);

-- tábla a belépési adatok tárolására (európai szabvány: GDPR)
CREATE TABLE belepesiadatok (
    belepesid INT NOT NULL
    felhasznaloid INT NOT NULL,
    email VARCHAR(255) NOT NULL,
    jelszo VARCHAR(255) NOT NULL,
    FOREIGN KEY (felhasznaloid) REFERENCES felhasznalok(felhasznaloid),
    PRIMARY KEY (belepesid)
);

