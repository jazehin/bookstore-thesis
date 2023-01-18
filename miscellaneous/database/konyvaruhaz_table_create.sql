-- int types size reference: https://dev.mysql.com/doc/refman/5.6/en/integer-types.html

USE konyvadatok;

-- tábla könyvek tárolására
CREATE TABLE konyvek (
    ISBN VARCHAR(13) NOT NULL,
    oldalszam SMALLINT UNSIGNED NOT NULL,
    kiado VARCHAR(255) NOT NULL,
    suly INT UNSIGNED NOT NULL, -- grammban adjuk meg
    konyvcim VARCHAR(255) NOT NULL,
    boritokep VARCHAR(255) NULL,
    kotestipus BIT NOT NULL, -- 0: puhakötésű, 1: keménykötésű
    kiadaseve SMALLINT UNSIGNED NOT NULL,
    nettoar SMALLINT UNSIGNED NOT NULL, -- forintban adjuk meg
    afakulcs TINYINT UNSIGNED NOT NULL, -- százalékként adjuk meg
    nyelv VARCHAR(30) NOT NULL,
    keszlet SMALLINT UNSIGNED NOT NULL,
    leiras TEXT NOT NULL,
    PRIMARY KEY (ISBN)
);

-- tábla a műfajok tárolására
CREATE TABLE mufajok (
    mufajid INT NOT NULL,
    mufajnev VARCHAR(255) NOT NULL,
    PRIMARY KEY (mufajid)
);

-- tábla a könyvek és műfajok összekötésére
CREATE TABLE konyvek_mufajok (
    id INT NOT NULL,
    ISBN VARCHAR(13) NOT NULL,
    mufajid INT NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (ISBN) REFERENCES konyvek(ISBN),
    FOREIGN KEY (mufajid) REFERENCES mufajok(mufajid)
);

-- tábla a műfajok tárolására
CREATE TABLE irok (
    iroid INT NOT NULL,
    ironev VARCHAR(255) NOT NULL,
    PRIMARY KEY (iroid)
);

-- tábla a könyvek és műfajok összekötésére
CREATE TABLE konyvek_irok (
    id INT NOT NULL,
    ISBN VARCHAR(13) NOT NULL,
    iroid INT NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (ISBN) REFERENCES konyvek(ISBN),
    FOREIGN KEY (iroid) REFERENCES irok(iroid)
);

