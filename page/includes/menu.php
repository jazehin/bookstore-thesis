<?php
session_start();

if (!isset($_SESSION["logged_in"])) {
    $_SESSION["logged_in"] = false;
} else if ($_SESSION["logged_in"]) {
    $_SESSION["user"] = GetUserById($_SESSION["user"]["id"]);
}

if (!isset($_SESSION["user"])) {
    $_SESSION["user"] = [];
}

if (!isset($_SESSION["basket"])) {
    $_SESSION["basket"] = [];
}

if (isset($_GET['p'])) {
    $p = $_GET['p'];
} else {
    $p = 'main';
}

$title = "Könyváruház";

switch ($p) {
    case 'main':
        if ($_SESSION["logged_in"] && $_SESSION["user"]["type"] === "administrator") {
            header("Location: /statistics");
        }

        $books = GetRecommendations($_SESSION["user"]["username"], $_SESSION["user"]["id"]);
        if ($_SESSION["logged_in"] && count($books) > 0) {
            if (isset($_GET["page"])) {
                $content = "pages/main.php";
                $title = "Főoldal";
            } else {
                header("Location: /main/1");
            }
        } else {
            header("Location: /bestsellers/1");
        }
        break;
    case 'profile':
        $content = 'pages/profile.php';
        $title = "Profil";
        break;
    case 'basket':
        $content = 'pages/basket.php';
        $title = "Kosár";
        break;
    case 'order-address':
        $content = 'pages/orderaddress.php';
        $title = "Szállítási cím választása";
        break;
    case 'order-payment':
        $content = 'pages/orderpayment.php';
        $title = "Fizetési mód választása";
        break;
    case 'order-success':
        $content = 'pages/ordersuccess.php';
        $title = "Sikeres rendelés";
        break;
    case 'add-address':
        $content = 'pages/addaddress.php';
        $title = "Szállítási cím megadása";
        break;
    case 'signout':
        $content = 'pages/signout.php';
        $title = "Kilépés...";
        break;
    case 'search':
        $content = 'pages/search.php';
        $title = "Keresés";
        break;
    case 'book':
        $content = 'pages/book.php';
        // I set the title with JS based on the #title of the page
        break;
    case 'author':
        $content = 'pages/author.php';
        // I set the title with JS based on the #title of the page
        break;
    case 'publisher':
        $content = 'pages/publisher.php';
        // I set the title with JS based on the #title of the page
        break;
    case 'new':
        $content = 'pages/new.php';
        $title = "Újdonságaink";
        break;
    case 'soon':
        $content = 'pages/soon.php';
        $title = "Hamarosan megjelenik";
        break;
    case 'bestsellers':
        $content = 'pages/bestsellers.php';
        $title = "Bestsellerek";
        break;
    case 'addbook':
        $content = 'pages/addbook.php';
        $title = "Könyv hozzáadása";
        break;
    case 'modifybook':
        $content = 'pages/modifybook.php';
        $title = "Könyv módosítása/törlése";
        break;
    case 'comments':
        $content = 'pages/comments.php';
        $title = "Kommentek";
        break;
    case 'statistics':
        $content = 'pages/statistics.php';
        $title = "Statisztikák";
        break;
    case 'random':
        $isbns = GetISBNs();
        header("Location: /books/" . $isbns[array_rand($isbns)]);
        break;
    case 'error':
        $content = 'pages/error.php';
        $title = "Hiba";
        break;
    default:
        header("Location: /");
        break;
}

?>