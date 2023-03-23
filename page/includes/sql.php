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

function GetISBNs() {
    $resultset = GetResultSetOfSingleColumn("isbn", "books");
    return GetArrayFromResultSet($resultset);
}

function InsertBook($bookdata)
{
    $con = GetConnection();

    // simple data
    $isbn = mysqli_real_escape_string($con, $bookdata["isbn"]);
    $pages = mysqli_real_escape_string($con, $bookdata["pages"]);
    $publisher = (empty($bookdata["publisher"]) ? null : mysqli_real_escape_string($con, $bookdata["publisher"]));
    $weight = (empty($bookdata["weight"]) ? null : mysqli_real_escape_string($con, $bookdata["weight"]));
    $title = mysqli_real_escape_string($con, $bookdata["title"]);
    $series = (empty($bookdata["series"]) ? null : mysqli_real_escape_string($con, $bookdata["series"]));
    $cover = mysqli_real_escape_string($con, $bookdata["covertype"]);
    $datePublished = mysqli_real_escape_string($con, $bookdata["date_published"]);
    $price = mysqli_real_escape_string($con, $bookdata["price"]);
    $discountedPrice = (empty($bookdata["discounted_price"]) ? null : mysqli_real_escape_string($con, $bookdata["discounted_price"]));
    $language = mysqli_real_escape_string($con, $bookdata["language"]);
    $stock = mysqli_real_escape_string($con, $bookdata["stock"]);
    $description = mysqli_real_escape_string($con, $bookdata["description"]);
    $description = str_replace("\\r\\n", "<br>", $description);

    $genres = "";
    for ($i = 0; $i < count($bookdata["genres"]); $i++) {
        $genres = $genres . $bookdata["genres"][$i];
        if ($i < count($bookdata["genres"]) - 1)
            $genres = $genres . '@';
    }

    $writers = "";
    for ($i = 0; $i < count($bookdata["writers"]); $i++) {
        $writers = $writers . $bookdata["writers"][$i];
        if ($i < count($bookdata["writers"]) - 1)
            $writers = $writers . '@';
    }

    $genres = mysqli_real_escape_string($con, $genres);
    $writers = mysqli_real_escape_string($con, $writers);

    // prepare sta
    $sql = "CALL InsertBook(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sisissssiisisss", $isbn, $pages, $publisher, $weight, $title, $series, $cover, $datePublished, $price, $discountedPrice, $language, $stock, $description, $genres, $writers);
    mysqli_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($con);
}

function DeleteBook($isbn)
{
    $con = GetConnection();
    $sql = "CALL DeleteBook(?);";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $isbn);
    mysqli_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($con);
}

function UpdateBook($bookdata)
{
    $con = GetConnection();

    // simple data
    $isbn = mysqli_real_escape_string($con, $bookdata["isbn"]);
    $pages = mysqli_real_escape_string($con, $bookdata["pages"]);
    $publisher = (empty($bookdata["publisher"]) ? null : mysqli_real_escape_string($con, $bookdata["publisher"]));
    $weight = (empty($bookdata["weight"]) ? null : mysqli_real_escape_string($con, $bookdata["weight"]));
    $title = mysqli_real_escape_string($con, $bookdata["title"]);
    $series = (empty($bookdata["series"]) ? null : mysqli_real_escape_string($con, $bookdata["series"]));
    $cover = mysqli_real_escape_string($con, $bookdata["covertype"]);
    $datePublished = mysqli_real_escape_string($con, $bookdata["date_published"]);
    $price = mysqli_real_escape_string($con, $bookdata["price"]);
    $discountedPrice = (empty($bookdata["discounted_price"]) ? null : mysqli_real_escape_string($con, $bookdata["discounted_price"]));
    $language = mysqli_real_escape_string($con, $bookdata["language"]);
    $stock = mysqli_real_escape_string($con, $bookdata["stock"]);
    $description = mysqli_real_escape_string($con, $bookdata["description"]);
    $description = str_replace("\\r\\n", "<br>", $description);

    $genres = "";
    for ($i = 0; $i < count($bookdata["genres"]); $i++) {
        $genres = $genres . $bookdata["genres"][$i];
        if ($i < count($bookdata["genres"]) - 1)
            $genres = $genres . '@';
    }

    $writers = "";
    for ($i = 0; $i < count($bookdata["writers"]); $i++) {
        $writers = $writers . $bookdata["writers"][$i];
        if ($i < count($bookdata["writers"]) - 1)
            $writers = $writers . '@';
    }
    
    $genres = mysqli_real_escape_string($con, $genres);
    $writers = mysqli_real_escape_string($con, $writers);

    // prepare sta
    $sql = "CALL UpdateBook(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sisissssiisisss", $isbn, $pages, $publisher, $weight, $title, $series, $cover, $datePublished, $price, $discountedPrice, $language, $stock, $description, $genres, $writers);
    mysqli_execute($stmt);
    echo mysqli_stmt_error($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($con);
}

function GetBookByISBN($isbn)
{
    $con = GetConnection();
    $sql = 'CALL GetBookByISBN("' . $isbn . '");';
    $rs = mysqli_query($con, $sql);
    $assoc = mysqli_fetch_assoc($rs);
    $assoc["genres"] = GetGenresByISBN($isbn);
    $assoc["writers"] = GetWritersByISBN($isbn);
    mysqli_close($con);
    return $assoc;
}

function GetGenresByISBN($isbn)
{
    $con = GetConnection();
    $sql = 'CALL GetGenresByISBN("' . $isbn . '");';
    $rs = mysqli_query($con, $sql);
    $array = mysqli_fetch_all($rs, MYSQLI_NUM);
    mysqli_close($con);
    return $array;
}

function GetWritersByISBN($isbn)
{
    $con = GetConnection();
    $sql = 'CALL GetWritersByISBN("' . $isbn . '");';
    $rs = mysqli_query($con, $sql);
    $array = mysqli_fetch_all($rs, MYSQLI_NUM);
    mysqli_close($con);
    return $array;
}

function DoesBookExist($isbn): bool
{
    $con = GetConnection();
    $sql = 'SELECT DoesBookExist("' . $isbn . '");';
    $rs = mysqli_query($con, $sql);
    return mysqli_fetch_row($rs)[0];
}

function Login($username, $password): int|null
{
    $con = GetConnection();
    $username = mysqli_real_escape_string($con, $username);
    $password = mysqli_real_escape_string($con, $password);
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

function SignUp($username, $password, $email, $salt)
{
    $con = GetConnection();
    $username = mysqli_real_escape_string($con, $username);
    $password = mysqli_real_escape_string($con, $password);
    $email = mysqli_real_escape_string($con, $email);
    $salt = mysqli_real_escape_string($con, $salt);

    $sql = "CALL SignUp(?, ?, ?, ?);";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssss", $username, $password, $email, $salt);
    mysqli_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($con);
}


function EmailAlreadyExists($email) {
    $con = GetConnection();
    $sql = "SELECT user_id FROM login WHERE email = ?;";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_execute($stmt);
    mysqli_stmt_bind_result($stmt, $user_id);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($con);
    return !is_null($user_id);
}

function UsernameAlreadyExists($username) {
    $con = GetConnection();
    $sql = "SELECT user_id FROM login WHERE username = ?;";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_execute($stmt);
    mysqli_stmt_bind_result($stmt, $user_id);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($con);
    return !is_null($user_id);
}

function GetSalt($username) {
    $con = GetConnection();
    $sql = "SELECT salt FROM login WHERE username = ?;";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_execute($stmt);
    mysqli_stmt_bind_result($stmt, $salt);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($con);
    return $salt;
}

function GetUserById($id)
{
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
        $string += $value . ", ";
    }
    return substr($string, 0, strlen($string) - 2);
}
?>