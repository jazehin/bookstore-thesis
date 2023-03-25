<?php
include("./../../includes/db_con.php");
include("./../../includes/sql.php");

$isbn = $_GET["isbn"];

if (DoesBookExist($isbn)) {
    $bookdata = GetBookByISBN($isbn);
} else {
    return null;
}

$separator = '#';
$listSeparator = '@';

$joinedData = $bookdata["isbn"] . $separator;
$joinedData = $joinedData . $bookdata["title"] . $separator;
$joinedData = $joinedData . $bookdata["series"] . $separator;
$joinedData = $joinedData . $bookdata["date_published"] . $separator;
$joinedData = $joinedData . $bookdata["stock"] . $separator;
$joinedData = $joinedData . $bookdata["pages"] . $separator;
$joinedData = $joinedData . $bookdata["weight"] . $separator;
$joinedData = $joinedData . $bookdata["publisher"] . $separator;
$joinedData = $joinedData . $bookdata["cover"] . $separator;
$joinedData = $joinedData . $bookdata["language"] . $separator;
$joinedData = $joinedData . $bookdata["description"] . $separator;

for ($i=0; $i < count($bookdata["genres"]); $i++) { 
    $joinedData = $joinedData . $bookdata["genres"][$i][1];
    if ($i < count($bookdata["genres"]) - 1)
        $joinedData = $joinedData . $listSeparator;
    else 
        $joinedData = $joinedData . $separator;
}

for ($i=0; $i < count($bookdata["writers"]); $i++) { 
    $joinedData = $joinedData . $bookdata["writers"][$i][1];
    if ($i < count($bookdata["writers"]) - 1)
        $joinedData = $joinedData . $listSeparator;
    else 
        $joinedData = $joinedData . $separator;
}

$joinedData = $joinedData . $bookdata["price"] . $separator;
$joinedData = $joinedData . $bookdata["discounted_price"];

echo $joinedData;