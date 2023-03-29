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

function GetISBNs()
{
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
    $sql = "SELECT * FROM books WHERE isbn = '" . $isbn . "';";
    $rs = mysqli_query($con, $sql);
    $count = mysqli_num_rows($rs);
    return $count > 0;
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


function EmailAlreadyExists($email)
{
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

function UsernameAlreadyExists($username)
{
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

function GetSalt($username)
{
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
    mysqli_stmt_bind_result($stmt, $username, $email, $type, $family_name, $given_name, $gender, $birthdate, $phone_number, $points);
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
        "phone_number" => $phone_number,
        "points" => $points
    );

    return $array;
}

function SetPreference($username, $genre, $add)
{
    $con = GetConnection();
    if ($add)
        $sql = "CALL SetPreference(?, ?);";
    else
        $sql = "CALL RemovePreference(?, ?);";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $username, $genre);
    mysqli_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($con);
}

function GetGenrePreferencesByUsername($username)
{
    $con = GetConnection();
    $sql = "SELECT genre FROM genres INNER JOIN user_preferences ON genres.genre_id = user_preferences.genre_id WHERE user_preferences.user_id = (SELECT user_id FROM login WHERE username = '" . $username . "');";
    $rs = mysqli_query($con, $sql);
    $array = [];

    while ($row = mysqli_fetch_row($rs)) {
        array_push($array, $row[0]);
    }

    mysqli_close($con);
    return $array;
}

function SaveUserData($username, $family_name, $given_name, $gender, $birthdate, $phone_number)
{
    $con = GetConnection();
    $sql = "UPDATE users SET family_name = ?, given_name = ?, gender = ?, birthdate = ?, phone_number = ? WHERE user_id = (SELECT user_id FROM login WHERE username = ?);";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssssss", $family_name, $given_name, $gender, $birthdate, $phone_number, $username);
    mysqli_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($con);
}

function GetCounties()
{
    $con = GetConnection();
    $sql = "SELECT county_id, county FROM counties;";
    $rs = mysqli_query($con, $sql);
    mysqli_close($con);
    return $rs;
}

function SaveAddress($username, $company, $county, $city, $public_space, $zip_code, $note) {
    $con = GetConnection();
    $sql = "CALL SaveAddress(?, ?, ?, ?, ?, ?, ?);";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssissss", $username, $company, $county, $city, $public_space, $zip_code, $note);
    mysqli_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($con);
}

function GetAddressesByUsername($username) {
    $con = GetConnection();
    $sql = "SELECT addresses.address_id, addresses.company, counties.county, addresses.city, addresses.public_space, addresses.zip_code, addresses.note FROM addresses 
            INNER JOIN users_addresses ON addresses.address_id = users_addresses.address_id 
            INNER JOIN users ON users_addresses.user_id = users.user_id 
            INNER JOIN login ON users.user_id = login.user_id 
            INNER JOIN counties ON addresses.county_id = counties.county_id 
            WHERE login.username = '" . $username . "';";
    $rs = mysqli_query($con, $sql);
    mysqli_close($con);
    return $rs;
}

function DeleteAddressCon($address_id, $username) {
    $con = GetConnection();
    $sql = "DELETE FROM users_addresses WHERE user_id = (SELECT user_id FROM login WHERE username = '$username') AND address_id = $address_id;";
    mysqli_query($con, $sql);
    mysqli_close($con);
}

function GetAddressId($company, $county, $city, $public_space, $zip_code, $note) {
    $con = GetConnection();
    $sql = "SELECT GetAddressId(?, ?, ?, ?, ?, ?);";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sissss", $company, $county, $city, $public_space, $zip_code, $note);
    mysqli_stmt_bind_result($stmt, $id);
    mysqli_execute($stmt);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($con);
    return $id;
}

function GetCountyIdByCountyName($county) {
    $con = GetConnection();
    $sql = "SELECT county_id FROM counties WHERE county = ?;";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $county);
    mysqli_stmt_bind_result($stmt, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($con);
    return $id;
}
?>