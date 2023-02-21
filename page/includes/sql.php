<?php

function GetResultSetOfSingleColumn($column, $table)
{
    $con = GetConnection();
    $sql = "SELECT DISTINCT " . $column . " FROM " . $table . ";";
    $resultset = mysqli_query($con, $sql);
    mysqli_close($con);
    return $resultset;
}

function GetArrayFromResultSet($resultset)
{
    $array = array();
    while ($row = mysqli_fetch_row($resultset)) {
        array_push($array, $row[0]);
    }

    return $array;
}

function GetPublishers()
{
    $resultset = GetResultSetOfSingleColumn("kiadonev", "konyvadatok.kiadok");
    return GetArrayFromResultSet($resultset);
}

function GetCoverTypes()
{
    $resultset = GetResultSetOfSingleColumn("kotestipusnev", "konyvadatok.kotestipusok");
    return GetArrayFromResultSet($resultset);
}

function GetLanguages()
{
    $resultset = GetResultSetOfSingleColumn("nyelvnev", "konyvadatok.nyelvek");
    return GetArrayFromResultSet($resultset);
}

function GetSerieses()
{
    $resultset = GetResultSetOfSingleColumn("sorozatnev", "konyvadatok.konyvsorozatok");
    return GetArrayFromResultSet($resultset);
}

function GetGenres()
{
    $resultset = GetResultSetOfSingleColumn("mufajnev", "konyvadatok.mufajok");
    return GetArrayFromResultSet($resultset);
}

function GetWriters()
{
    $resultset = GetResultSetOfSingleColumn("ironev", "konyvadatok.irok");
    return GetArrayFromResultSet($resultset);
}

function InsertBook($bookdata)
{
    $con = GetConnection();

    // simple data
    $isbn = "'" . mysqli_real_escape_string($con, $bookdata["isbn"]) . "'";
    $title = "'" . mysqli_real_escape_string($con, $bookdata["title"]) . "'";
    $dateOfPublishing = "'" . mysqli_real_escape_string($con, $bookdata["date-of-publishing"]) . "'";
    $stock = mysqli_real_escape_string($con, $bookdata["stock"]);
    $numberOfPages = mysqli_real_escape_string($con, $bookdata["number-of-pages"]);
    $weight = (empty($bookdata["weight"]) ? "NULL" : mysqli_real_escape_string($con, $bookdata["weight"]));
    $description = "'" . mysqli_real_escape_string($con, $bookdata["description"]) . "'";
    $price = mysqli_real_escape_string($con, $bookdata["price"]);
    $discountedPrice = (empty($bookdata["discounted-price"]) ? "NULL" : mysqli_real_escape_string($con, $bookdata["discounted-price"]));

    // foreign keys that go into the book table
    // methodology: if the value is not already stored in the current table, insert it -> then get its index

    // series (can also be empty)
    $series = 'NULL';
    if (!empty($bookdata["series"])) {
        $sql = "SELECT sorozatid FROM konyvadatok.sorozatok WHERE sorozatnev = '" . mysqli_real_escape_string($con, $bookdata["series"]) . "';";
        $rs = mysqli_query($con, $sql);
        if (mysqli_num_rows($rs) == 0) {
            $sql = "INSERT INTO konyvadatok.sorozatok (sorozatnev) VALUES ('" . mysqli_real_escape_string($con, $bookdata["series"]) . "');";
            mysqli_query($con, $sql);
        }
        $sql = "SELECT sorozatid FROM konyvadatok.sorozatok WHERE sorozatnev = '" . mysqli_real_escape_string($con, $bookdata["series"]) . "';";
        $rs = mysqli_query($con, $sql);
        $row = mysqli_fetch_row($rs);
        $series = $row[0];
    }

    // publisher
    $sql = "SELECT kiadoid FROM konyvadatok.kiadok WHERE kiadonev = '" . mysqli_real_escape_string($con, $bookdata["publisher"]) . "';";
    $rs = mysqli_query($con, $sql);
    if (mysqli_num_rows($rs) == 0) {
        $sql = "INSERT INTO konyvadatok.kiadok (kiadonev) VALUES ('" . mysqli_real_escape_string($con, $bookdata["publisher"]) . "');";
        mysqli_query($con, $sql);
    }
    $sql = "SELECT kiadoid FROM konyvadatok.kiadok WHERE kiadonev = '" . mysqli_real_escape_string($con, $bookdata["publisher"]) . "';";
    $rs = mysqli_query($con, $sql);
    $row = mysqli_fetch_row($rs);
    $publisher = $row[0];

    // covertype
    $sql = "SELECT kotestipusid FROM konyvadatok.kotestipusok WHERE kotestipusnev = '" . mysqli_real_escape_string($con, $bookdata["covertype"]) . "';";
    $rs = mysqli_query($con, $sql);
    if (mysqli_num_rows($rs) == 0) {
        $sql = "INSERT INTO konyvadatok.kotestipusok (kotestipusnev) VALUES ('" . mysqli_real_escape_string($con, $bookdata["covertype"]) . "');";
        mysqli_query($con, $sql);
    }
    $sql = "SELECT kotestipusid FROM konyvadatok.kotestipusok WHERE kotestipusnev = '" . mysqli_real_escape_string($con, $bookdata["covertype"]) . "';";
    $rs = mysqli_query($con, $sql);
    $row = mysqli_fetch_row($rs);
    $covertype = $row[0];

    // language
    $sql = "SELECT nyelvid FROM konyvadatok.nyelvek WHERE nyelvnev = '" . mysqli_real_escape_string($con, $bookdata["language"]) . "';";
    $rs = mysqli_query($con, $sql);
    if (mysqli_num_rows($rs) == 0) {
        $sql = "INSERT INTO konyvadatok.nyelvek (nyelvnev) VALUES ('" . mysqli_real_escape_string($con, $bookdata["language"]) . "');";
        mysqli_query($con, $sql);
    }
    $sql = "SELECT nyelvid FROM konyvadatok.nyelvek WHERE nyelvnev = '" . mysqli_real_escape_string($con, $bookdata["language"]) . "';";
    $rs = mysqli_query($con, $sql);
    $row = mysqli_fetch_row($rs);
    $language = $row[0];

    // insert book into table
    $sql = 'INSERT INTO konyvadatok.konyvek (isbn, oldalszam, kiadoid, suly, konyvcim, sorozatid, kotestipusid, kiadasdatuma, ar, akciosar, nyelvid, keszlet, leiras) VALUES (
        ' . $isbn . ',
        ' . $numberOfPages . ',
        ' . $publisher . ',
        ' . $weight . ',
        ' . $title . ',
        ' . $series . ',
        ' . $covertype . ',
        ' . $dateOfPublishing . ',
        ' . $price . ',
        ' . $discountedPrice . ',
        ' . $language . ',
        ' . $stock . ',
        ' . $description . '
    );';
    mysqli_query($con, $sql);

    // genres: for each genre, if it isn't already in the db, add it, then connect it to the book in the separate connecting table
    foreach ($bookdata["genres"] as $key => $value) {
        $sql = "SELECT mufajid FROM konyvadatok.mufajok WHERE mufajnev = '" . mysqli_real_escape_string($con, $value) . "';";
        $rs = mysqli_query($con, $sql);
        if (mysqli_num_rows($rs) == 0) {
            $sql = "INSERT INTO konyvadatok.mufajok (mufajnev) VALUES ('" . mysqli_real_escape_string($con, $value) . "');";
            mysqli_query($con, $sql);
        }
        $sql = "SELECT mufajid FROM konyvadatok.mufajok WHERE mufajnev = '" . mysqli_real_escape_string($con, $value) . "';";
        $rs = mysqli_query($con, $sql);
        $row = mysqli_fetch_row($rs);
        $genre = $row[0];

        $sql = 'INSERT INTO konyvadatok.konyvek_mufajok (isbn, mufajid) VALUES (' . mysqli_real_escape_string($con, $isbn) . ', ' . mysqli_real_escape_string($con, $genre) . ');';
    }

    // writers: same method as genres
    foreach ($bookdata["writers"] as $key => $value) {
        $sql = "SELECT iroid FROM konyvadatok.irok WHERE ironev = '" . mysqli_real_escape_string($con, $value) . "';";
        $rs = mysqli_query($con, $sql);
        if (mysqli_num_rows($rs) == 0) {
            $sql = "INSERT INTO konyvadatok.irok (ironev) VALUES ('" . mysqli_real_escape_string($con, $value) . "');";
            mysqli_query($con, $sql);
        }
        $sql = "SELECT iroid FROM konyvadatok.irok WHERE ironev = '" . mysqli_real_escape_string($con, $value) . "';";
        $rs = mysqli_query($con, $sql);
        $row = mysqli_fetch_row($rs);
        $writer = $row[0];

        $sql = 'INSERT INTO konyvadatok.konyvek_irok (isbn, iroid) VALUES (' . mysqli_real_escape_string($con, $isbn) . ', ' . mysqli_real_escape_string($con, $writer) . ');';
    }

    mysqli_close($con);
}

function ArrayToString($array)
{
    $string = "";
    foreach ($array as $key => $value) {
        $string += $value . ", ";
    }
    return substr($string, 0, strlen($string) - 2);
}
?>