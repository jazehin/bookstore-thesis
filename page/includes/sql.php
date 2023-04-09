<?php

function GetResultSetOfSingleColumn($column, $table)
{
    $con = GetConnection();
    $sql = "SELECT DISTINCT " . $column . " FROM " . $table . ";";
    $rs = mysqli_query($con, $sql);
    mysqli_close($con);
    return $rs;
}

function GetArrayFromResultSet($rs)
{
    $array = array();
    while ($row = mysqli_fetch_row($rs)) {
        array_push($array, $row[0]);
    }
    return $array;
}

function GetPublishers()
{
    $rs = GetResultSetOfSingleColumn("publisher", "publishers");
    return GetArrayFromResultSet($rs);
}

function GetCoverTypes()
{
    $rs = GetResultSetOfSingleColumn("cover", "covers");
    return GetArrayFromResultSet($rs);
}

function GetLanguages()
{
    $rs = GetResultSetOfSingleColumn("language", "languages");
    return GetArrayFromResultSet($rs);
}

function GetSerieses()
{
    $rs = GetResultSetOfSingleColumn("series", "serieses");
    return GetArrayFromResultSet($rs);
}

function GetGenres()
{
    $rs = GetResultSetOfSingleColumn("genre", "genres");
    return GetArrayFromResultSet($rs);
}

function GetWriters()
{
    $rs = GetResultSetOfSingleColumn("writer", "writers");
    return GetArrayFromResultSet($rs);
}

function GetISBNs()
{
    $rs = GetResultSetOfSingleColumn("isbn", "books");
    return GetArrayFromResultSet($rs);
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
    $sql = "SELECT genre FROM genres INNER JOIN preferences ON genres.genre_id = preferences.genre_id WHERE preferences.user_id = (SELECT user_id FROM login WHERE username = '" . $username . "');";
    $rs = mysqli_query($con, $sql);
    $array = [];

    while ($row = mysqli_fetch_row($rs)) {
        array_push($array, $row[0]);
    }

    mysqli_close($con);
    return $array;
}

function GetAuthorsPurchasedFromByUsername($username)
{
    $con = GetConnection();
    $sql = "SELECT DISTINCT writer
            FROM writers
            INNER JOIN books_writers ON writers.writer_id = books_writers.writer_id
            INNER JOIN books ON books.isbn = books_writers.isbn
            INNER JOIN order_details ON books.isbn = order_details.isbn
            INNER JOIN orders ON order_details.order_id = orders.order_id
            INNER JOIN login ON orders.user_id = login.user_id
            WHERE username = \"" . $username . "\";";
    $rs = mysqli_query($con, $sql);
    $array = [];

    while ($row = mysqli_fetch_row($rs)) {
        array_push($array, $row[0]);
    }

    mysqli_close($con);
    return $array;
}

function GetSeriesesPurchasedFromByUsername($username)
{
    $con = GetConnection();
    $sql = "SELECT DISTINCT series
            FROM serieses
            INNER JOIN books ON books.series_id = serieses.series_id
            INNER JOIN order_details ON order_details.isbn = books.isbn
            INNER JOIN orders ON order_details.order_id = orders.order_id
            INNER JOIN login ON orders.user_id = login.user_id
            WHERE series IS NOT NULL AND username = \"" . $username . "\";";
    $rs = mysqli_query($con, $sql);
    $array = [];

    while ($row = mysqli_fetch_row($rs)) {
        array_push($array, $row[0]);
    }

    mysqli_close($con);
    return $array;
}

function GetGenresPurchasedByUsername($username)
{
    $con = GetConnection();
    $sql = "SELECT DISTINCT genre
            FROM genres
            INNER JOIN books_genres ON genres.genre_id = books_genres.genre_id
            INNER JOIN books ON books.isbn = books_genres.isbn
            INNER JOIN order_details ON books.isbn = order_details.isbn
            INNER JOIN orders ON order_details.order_id = orders.order_id
            INNER JOIN login ON orders.user_id = login.user_id
            WHERE username = \"" . $username . "\";";
    $rs = mysqli_query($con, $sql);
    $array = [];

    while ($row = mysqli_fetch_row($rs)) {
        array_push($array, $row[0]);
    }

    mysqli_close($con);
    return $array;
}

function GetReadISBNs($username) {
    $con = GetConnection();
    $sql = "SELECT DISTINCT isbn
            FROM order_details
            INNER JOIN orders ON order_details.order_id = orders.order_id
            INNER JOIN login ON orders.user_id = login.user_id
            WHERE username = \"" . $username . "\";";
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

function CompleteOrder($books, $address_id, $user_id, $used_points) {
    if (is_null($user_id)) $used_points = 0;
    $price_sum = GetPriceSum($books) - $used_points;

    $con = GetConnection();
    $sql = "SELECT CompleteOrder(?, ?, ?);";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "iii", $user_id, $address_id, $price_sum);
    mysqli_execute($stmt);
    $rs = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);

    $order_id = 0;
    while ($row = mysqli_fetch_row($rs)) {
        $order_id = $row[0];
    }

    $isbns = array_keys($books);
    $item_count = count($isbns);
    for ($i = 0; $i < $item_count; $i++) {
        $bookdata = GetBookByISBN($isbns[$i]);
        $price = 0;
        if (is_null($bookdata["discounted_price"])) {
            $price = $bookdata["price"];
        } else {
            $price = $bookdata["discounted_price"];
        }
        
        AddOrderDetail($con, $order_id, $isbns[$i], $books[$isbns[$i]], $price);

        $sql = "UPDATE books SET stock = stock - ? WHERE isbn = ?;";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "is", $books[$isbns[$i]], $isbns[$i]);
        mysqli_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    
    if (!is_null($user_id)) {
        $sql = "UPDATE users SET points = points - ? WHERE user_id = ?;";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $used_points, $user_id);
        mysqli_execute($stmt);
        mysqli_stmt_close($stmt);

        $points_to_add = floor(($price_sum - $used_points) / 10);
        $sql = "UPDATE users SET points = points + ? WHERE user_id = ?;";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $points_to_add, $user_id);
        mysqli_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    mysqli_close($con);
}

function GetPriceSum($books) {
    $price_sum = 0;
    $isbns = array_keys($books);
    $item_count = count($isbns);
    for ($i = 0; $i < $item_count; $i++) {
        $bookdata = GetBookByISBN($isbns[$i]);
        $price = 0;
        if (is_null($bookdata["discounted_price"])) {
            $price = $bookdata["price"];
        } else {
            $price = $bookdata["discounted_price"];
        }
        $price_sum += $price * $books[$isbns[$i]];
    }
    return $price_sum;
}

function AddOrderDetail($con, $order_id, $isbn, $quantity, $price) {
    $sql = "INSERT INTO order_details (order_id, isbn, quantity, price) VALUES (?, ?, ?, ?);";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "isii", $order_id, $isbn, $quantity, $price);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

function SearchBooks($query, $page, $books_per_page) {
    $offset = ($page - 1) * $books_per_page;
    $con = GetConnection();
    $sql = "SELECT DISTINCT isbn FROM books WHERE title LIKE \"%$query%\" LIMIT $offset, $books_per_page;";
    $rs = mysqli_query($con, $sql);
    mysqli_close($con);
    return GetArrayFromResultSet($rs);
}

function GetNumberOfSearchResults($query) {
    $con = GetConnection();
    $sql = "SELECT COUNT(isbn) FROM books WHERE title LIKE \"%$query%\";";
    $rs = mysqli_query($con, $sql);
    $count = 0;
    while ($row = mysqli_fetch_row($rs)) {
        $count = $row[0];
    }
    mysqli_close($con);
    return $count;
}

function GetComments($isbn, $comments_per_page, $page) {
    $offset = ($page - 1) * $comments_per_page;
    $con = GetConnection();
    $sql = "SELECT comment_id, username, comment_text, comment_date 
            FROM comments INNER JOIN login ON comments.user_id = login.user_id 
            WHERE isbn = '$isbn' 
            ORDER BY comment_date DESC 
            LIMIT $offset, $comments_per_page;";
    $rs = mysqli_query($con, $sql);
    mysqli_close($con);
    return $rs;
}

/*
function GetComments($isbn, $comments_per_page, $page) {
    $con = GetConnection();
    $sql = "CALL GetCommentsByISBN($isbn, $comments_per_page, $page)";
    $rs = mysqli_query($con, $sql);
    mysqli_close($con);
    return $rs;
}
*/
function PostComment($user_id, $isbn, $comment) {
    $con = GetConnection();
    $sql = "INSERT INTO comments (user_id, isbn, comment_text) VALUES (?, ?, ?);";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "iss", $user_id, $isbn, $comment);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($con);
}

function DeleteComment($comment_id) {
    $con = GetConnection();
    $sql = "DELETE FROM comments WHERE comment_id = ?;";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $comment_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($con);
}

function GetNumberOfComments($isbn) {
    $con = GetConnection();
    $sql = "SELECT COUNT(isbn) FROM comments WHERE isbn LIKE \"%$isbn%\";";
    $rs = mysqli_query($con, $sql);
    $count = 0;
    while ($row = mysqli_fetch_row($rs)) {
        $count = $row[0];
    }
    mysqli_close($con);
    return $count;
}

function SetRating($user_id, $isbn, $rating) {
    $con = GetConnection();
    $sql = "CALL SetRating(?, ?, ?);";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "isi", $user_id, $isbn, $rating);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($con);
}

function GetAvgRatingByISBN($isbn) {
    $con = GetConnection();
    $sql = "SELECT AVG(rating), COUNT(rating) FROM ratings WHERE isbn = ?;";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $isbn);
    mysqli_stmt_bind_result($stmt, $rating, $count);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($con);
    return [$rating, $count];
}

function GetOrdersByUserId($user_id) {
    $con = GetConnection();
    $sql = "SELECT order_date, price_sum FROM orders WHERE user_id = ? ORDER BY order_date DESC;";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}

function GetNumberOfAuthorsBooks($author) {
    $con = GetConnection();
    $sql = "SELECT COUNT(books.isbn) FROM books 
            INNER JOIN books_writers ON books.isbn = books_writers.isbn 
            INNER JOIN writers ON books_writers.writer_id = writers.writer_id 
            WHERE writer = ?;";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $author);
    mysqli_execute($stmt);
    $rs = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    $count = 0;
    while ($row = mysqli_fetch_row($rs)) {
        $count = $row[0];
    }
    mysqli_close($con);
    return $count;
}

function GetAuthorsBooks($author, $page, $books_per_page) {
    $offset = ($page - 1) * $books_per_page;
    $con = GetConnection();
    $sql = "SELECT DISTINCT books.isbn FROM books 
            INNER JOIN books_writers ON books.isbn = books_writers.isbn 
            INNER JOIN writers ON books_writers.writer_id = writers.writer_id 
            WHERE writer = ? 
            LIMIT ?, ?;";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sii", $author, $offset, $books_per_page);
    mysqli_execute($stmt);
    $rs = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($con);
    return GetArrayFromResultSet($rs);
}

function GetNumberOfPublishersBooks($publisher) {
    $con = GetConnection();
    $sql = "SELECT COUNT(isbn) FROM books 
            INNER JOIN publishers ON books.publisher_id = publishers.publisher_id 
            WHERE publisher = ?;";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $publisher);
    mysqli_execute($stmt);
    $rs = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    $count = 0;
    while ($row = mysqli_fetch_row($rs)) {
        $count = $row[0];
    }
    mysqli_close($con);
    return $count;
}

function GetPublishersBooks($publisher, $page, $books_per_page) {
    $offset = ($page - 1) * $books_per_page;
    $con = GetConnection();
    $sql = "SELECT DISTINCT books.isbn FROM books 
            INNER JOIN publishers ON books.publisher_id = publishers.publisher_id 
            WHERE publisher = ? 
            LIMIT ?, ?;";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sii", $publisher, $offset, $books_per_page);
    mysqli_execute($stmt);
    $rs = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($con);
    return GetArrayFromResultSet($rs);
}

function GetBooksForPage($page, $books_per_page, $sql, $types, $vars) {
    $con = GetConnection();
    $stmt = mysqli_prepare($con, $sql);
    if ($types !== "")
        mysqli_stmt_bind_param($stmt, $types, $vars);
    mysqli_execute($stmt);
    $rs = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($con);
    return GetArrayFromResultSet($rs);
}

function GetNumberBooksForPage($sql, $types, ...$vars) {
    $con = GetConnection();
    $stmt = mysqli_prepare($con, $sql);
    if ($types !== "")
        mysqli_stmt_bind_param($stmt, $types, $vars);
    mysqli_execute($stmt);
    $rs = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($con);
    return mysqli_num_rows($rs);
}

function GetNumberOfAllComments() {
    $con = GetConnection();
    $sql = "SELECT COUNT(comment_id) FROM comments;";
    $rs = mysqli_query($con, $sql);
    $count = 0;
    while ($row = mysqli_fetch_row($rs)) {
        $count = $row[0];
    }
    mysqli_close($con);
    return $count;
}

function GetAllComments($comments_per_page, $page) {
    $offset = ($page - 1) * $comments_per_page;
    $con = GetConnection();
    $sql = "SELECT comment_id, username, comment_text, comment_date 
            FROM comments INNER JOIN login ON comments.user_id = login.user_id 
            ORDER BY comment_date DESC 
            LIMIT $offset, $comments_per_page;";
    $rs = mysqli_query($con, $sql);
    mysqli_close($con);
    return $rs;
}

function GetMostPurchasedBooksByAge($user_id) {
    $con = GetConnection();
    $sql = "CALL GetMostPurchasedBooksByAge(?);";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $rs = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($con);

    $array = [];
    while ($row = mysqli_fetch_row($rs)) {
        array_push($array, $row[0]);
    }
    
    return $array;
}

function GetMostPurchasedBooksByGender($user_id) {
    $con = GetConnection();
    $sql = "CALL GetMostPurchasedBooksByGender(?);";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $rs = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($con);

    $array = [];
    while ($row = mysqli_fetch_row($rs)) {
        array_push($array, $row[0]);
    }
    
    return $array;
}

function GetRecommendations($username, $user_id) {
    $all_books = array_fill_keys(GetISBNs(), 0);
    $read_books = array_fill_keys(GetReadISBNs($_SESSION["user"]["username"]), 0);
    $books = array_diff_key($all_books, $read_books);

    $preferences = GetGenrePreferencesByUsername($username);
    $genres_purchased = GetGenresPurchasedByUsername($username);
    $authors_purchased_from = GetAuthorsPurchasedFromByUsername($username);
    $serieses_purchased_from = GetSeriesesPurchasedFromByUsername($username);

    $most_purchased_books_by_age = GetMostPurchasedBooksByAge($user_id);
    $most_purchased_books_by_gender = GetMostPurchasedBooksByGender($user_id);

    foreach ($books as $isbn => $value) {
        $bookdata = GetBookByISBN($isbn);

        $publication_date = strtotime($bookdata["date_published"]);
        $current_time = time();
        $is_book_available = $bookdata["stock"] > 0 && $publication_date <= $current_time;
        if (!$is_book_available) {
            continue;
        }

        for ($i = 0; $i < count($bookdata["genres"]); $i++) { 
            if (in_array($bookdata["genres"][$i][1], $preferences)) {
                $books[$isbn] += 2;
            }
        }

        for ($i = 0; $i < count($bookdata["genres"]); $i++) { 
            if (in_array($bookdata["genres"][$i][1], $genres_purchased)) {
                $books[$isbn] += 1;
            }
        }

        for ($i = 0; $i < count($bookdata["writers"]); $i++) { 
            if (in_array($bookdata["writers"][$i][1], $authors_purchased_from)) {
                $books[$isbn] += 1;
            }
        }

        if (!is_null($bookdata["series"]) && in_array($bookdata["series"], $serieses_purchased_from)) {
            $books[$isbn] += 3;
        }

        if (in_array($isbn, $most_purchased_books_by_age)) {
            $books[$isbn] += 2;
        }

        if (in_array($isbn, $most_purchased_books_by_gender)) {
            $books[$isbn] += 2;
        }
    }

    $books = array_filter($books);
    arsort($books);

    return array_keys($books);
}

function GetStatistics($view) {
    $con = GetConnection();
    $sql = "SELECT * FROM $view LIMIT 30";
    $rs = mysqli_query($con, $sql);
    return $rs;
}
?>