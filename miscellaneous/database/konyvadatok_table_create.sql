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

-- tábla könyvek tárolására
CREATE TABLE konyvek (
    ISBN VARCHAR(13) CHARACTER SET UTF8MB4 NOT NULL,
    oldalszam SMALLINT UNSIGNED NOT NULL,
    kiadoid INT NOT NULL,
    suly INT UNSIGNED NOT NULL, -- grammban adjuk meg
    konyvcim VARCHAR(255) CHARACTER SET UTF8MB4 NOT NULL,
    -- boritokep VARCHAR(255) CHARACTER SET UTF8MB4 NULL, -- új megoldás: ./covers/{ISBN}.png
    sorozatid INT NULL,
    kotestipus BIT NOT NULL, -- 0: puhakötésű, 1: keménykötésű
    kiadasdatuma DATE NOT NULL,
    ar SMALLINT UNSIGNED NOT NULL, -- forintban adjuk meg
    akcio TINYINT UNSIGNED NOT NULL DEFAULT 0, -- százalékban adjuk meg
    nyelvid INT NOT NULL,
    keszlet SMALLINT UNSIGNED NOT NULL,
    leiras TEXT NOT NULL,
    PRIMARY KEY (ISBN),
    FOREIGN key (kiadoid) REFERENCES kiadok(kiadoid),
    FOREIGN KEY (sorozatid) REFERENCES konyvsorozatok(sorozatid),
    FOREIGN KEY (nyelvid) REFERENCES nyelvek(nyelvid)
);

-- tábla a könyvek és műfajok összekötésére
CREATE TABLE konyvek_mufajok (
    id INT AUTO_INCREMENT NOT NULL,
    ISBN VARCHAR(13) CHARACTER SET UTF8MB4 NOT NULL,
    mufajid INT NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (ISBN) REFERENCES konyvek(ISBN),
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
    ISBN VARCHAR(13) CHARACTER SET UTF8MB4 NOT NULL,
    iroid INT NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (ISBN) REFERENCES konyvek(ISBN),
    FOREIGN KEY (iroid) REFERENCES irok(iroid)
);

-- nézet a legújabb könyvek lekérdezésére
CREATE VIEW legujabb_konyvek AS
SELECT * FROM konyvek
ORDER BY kiadasdatuma DESC
LIMIT 15;