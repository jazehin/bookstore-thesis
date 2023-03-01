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
    $resultset = GetResultSetOfSingleColumn("publisher", "publishers");
    return GetArrayFromResultSet($resultset);
}

function GetCoverTypes()
{
    $resultset = GetResultSetOfSingleColumn("cover", "covers");
    return GetArrayFromResultSet($resultset);
}

function GetLanguages()
{
    $resultset = GetResultSetOfSingleColumn("language", "languages");
    return GetArrayFromResultSet($resultset);
}

function GetSerieses()
{
    $resultset = GetResultSetOfSingleColumn("series", "serieses");
    return GetArrayFromResultSet($resultset);
}

function GetGenres()
{
    $resultset = GetResultSetOfSingleColumn("genre", "genres");
    return GetArrayFromResultSet($resultset);
}

function GetWriters()
{
    $resultset = GetResultSetOfSingleColumn("writer", "writers");
    return GetArrayFromResultSet($resultset);
}

function InsertBook($bookdata)
{
    $con = GetConnection();

    // simple data
    $isbn = "'" . mysqli_real_escape_string($con, $bookdata["isbn"]) . "'";
    $title = "'" . mysqli_real_escape_string($con, $bookdata["title"]) . "'";
    $dateOfPublishing = "'" . mysqli_real_escape_string($con, $bookdata["date_published"]) . "'";
    $stock = mysqli_real_escape_string($con, $bookdata["stock"]);
    $numberOfPages = mysqli_real_escape_string($con, $bookdata["pages"]);
    $weight = (empty($bookdata["weight"]) ? "NULL" : mysqli_real_escape_string($con, $bookdata["weight"]));
    $description = "'" . mysqli_real_escape_string($con, $bookdata["description"]) . "'";
    $price = mysqli_real_escape_string($con, $bookdata["price"]);
    $discountedPrice = (empty($bookdata["discounted_price"]) ? "NULL" : mysqli_real_escape_string($con, $bookdata["discounted_price"]));

    // foreign keys that go into the book table
    // methodology: if the value is not already stored in the current table, insert it -> then get its index

    // series (can also be empty)
    $series = 'NULL';
    if (!empty($bookdata["series"])) {
        $sql = "SELECT series_id FROM serieses WHERE series = '" . mysqli_real_escape_string($con, $bookdata["series"]) . "';";
        $rs = mysqli_query($con, $sql);
        if (mysqli_num_rows($rs) == 0) {
            $sql = "INSERT INTO serieses (series) VALUES ('" . mysqli_real_escape_string($con, $bookdata["series"]) . "');";
            mysqli_query($con, $sql);
        }
        $sql = "SELECT series_id FROM serieses WHERE series = '" . mysqli_real_escape_string($con, $bookdata["series"]) . "';";
        $rs = mysqli_query($con, $sql);
        $row = mysqli_fetch_row($rs);
        $series = $row[0];
    }

    // publisher
    $sql = "SELECT publisher_id FROM publishers WHERE publisher = '" . mysqli_real_escape_string($con, $bookdata["publisher"]) . "';";
    $rs = mysqli_query($con, $sql);
    if (mysqli_num_rows($rs) == 0) {
        $sql = "INSERT INTO publishers (publisher) VALUES ('" . mysqli_real_escape_string($con, $bookdata["publisher"]) . "');";
        mysqli_query($con, $sql);
    }
    $sql = "SELECT publisher_id FROM publishers WHERE publisher = '" . mysqli_real_escape_string($con, $bookdata["publisher"]) . "';";
    $rs = mysqli_query($con, $sql);
    $row = mysqli_fetch_row($rs);
    $publisher = $row[0];

    // covertype
    $sql = "SELECT cover_id FROM covers WHERE cover = '" . mysqli_real_escape_string($con, $bookdata["covertype"]) . "';";
    $rs = mysqli_query($con, $sql);
    if (mysqli_num_rows($rs) == 0) {
        $sql = "INSERT INTO covers (cover) VALUES ('" . mysqli_real_escape_string($con, $bookdata["covertype"]) . "');";
        mysqli_query($con, $sql);
    }
    $sql = "SELECT cover_id FROM covers WHERE cover = '" . mysqli_real_escape_string($con, $bookdata["covertype"]) . "';";
    $rs = mysqli_query($con, $sql);
    $row = mysqli_fetch_row($rs);
    $covertype = $row[0];

    // language
    $sql = "SELECT language_id FROM languages WHERE language = '" . mysqli_real_escape_string($con, $bookdata["language"]) . "';";
    $rs = mysqli_query($con, $sql);
    if (mysqli_num_rows($rs) == 0) {
        $sql = "INSERT INTO languages (language) VALUES ('" . mysqli_real_escape_string($con, $bookdata["language"]) . "');";
        mysqli_query($con, $sql);
    }
    $sql = "SELECT language_id FROM languages WHERE language = '" . mysqli_real_escape_string($con, $bookdata["language"]) . "';";
    $rs = mysqli_query($con, $sql);
    $row = mysqli_fetch_row($rs);
    $language = $row[0];

    // insert book into table
    $sql = 'INSERT INTO books (isbn, pages, publisher_id, weight, title, series_id, cover_id, date_published, price, discounted_price, language_id, stock, description) VALUES (
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
        $sql = "SELECT genre_id FROM genres WHERE genre = '" . mysqli_real_escape_string($con, $value) . "';";
        $rs = mysqli_query($con, $sql);
        if (mysqli_num_rows($rs) == 0) {
            $sql = "INSERT INTO genres (genre) VALUES ('" . mysqli_real_escape_string($con, $value) . "');";
            mysqli_query($con, $sql);
        }
        $sql = "SELECT genre_id FROM genres WHERE genre = '" . mysqli_real_escape_string($con, $value) . "';";
        $rs = mysqli_query($con, $sql);
        $row = mysqli_fetch_row($rs);
        $genre = $row[0];

        $sql = 'INSERT INTO books_genres (isbn, genre_id) VALUES (' . mysqli_real_escape_string($con, $isbn) . ', ' . mysqli_real_escape_string($con, $genre) . ');';
    }

    // writers: same method as genres
    foreach ($bookdata["writers"] as $key => $value) {
        $sql = "SELECT writer_id FROM writers WHERE writer = '" . mysqli_real_escape_string($con, $value) . "';";
        $rs = mysqli_query($con, $sql);
        if (mysqli_num_rows($rs) == 0) {
            $sql = "INSERT INTO writers (writer) VALUES ('" . mysqli_real_escape_string($con, $value) . "');";
            mysqli_query($con, $sql);
        }
        $sql = "SELECT writer_id FROM writers WHERE writer = '" . mysqli_real_escape_string($con, $value) . "';";
        $rs = mysqli_query($con, $sql);
        $row = mysqli_fetch_row($rs);
        $writer = $row[0];

        $sql = 'INSERT INTO books_writers (isbn, writer_id) VALUES (' . mysqli_real_escape_string($con, $isbn) . ', ' . mysqli_real_escape_string($con, $writer) . ');';
    }

    mysqli_close($con);
}

function GetBookByISBN($isbn) {
    $con = GetConnection();
    $sql = 'CALL GetBookByISBN("' . $isbn . '");';
    $rs = mysqli_query($con, $sql);
    $assoc = mysqli_fetch_assoc($rs);
    return $assoc;
}

function DoesBookExist($isbn): bool {
    $con = GetConnection();
    $sql = 'SELECT DoesBookExist("' . $isbn . '");';
    $rs = mysqli_query($con, $sql);
    return mysqli_fetch_row($rs)[0];
}

function Login($username, $password): int {
    $con = GetConnection();
    $sql = "SELECT user_id FROM login WHERE username = ? AND password = ?;";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $username, $password);
    mysqli_execute($stmt);
    mysqli_stmt_bind_result($stmt, $user_id);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($con);
    return $user_id;
}

function GetUserById($id) {
    $con = GetConnection();
    $sql = "CALL GetUserById(?);";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_execute($stmt);
    mysqli_stmt_bind_result($stmt, $username, $email, $type, $family_name, $given_name, $gender, $birthdate, $phone_number);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($con);

    $array = array(
        "id" => $id,
        "username" => $username,
        "email" => $email,
        "type" => $type,
        "family_name" => $family_name,
        "given_name" => $given_name,
        "gender" => $gender,
        "birthdate" => $birthdate,
        "phone_number" => $phone_number
    );

    return $array;
}

function ArrayToString($array)
{
    $string = "";
    foreach ($array as $key => $value) {
        $string += $value . ";";
    }
    return substr($string, 0, strlen($string) - 1);
}
?>