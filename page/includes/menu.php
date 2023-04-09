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

switch ($p) {
    case 'main':
        if ($_SESSION["logged_in"] && $_SESSION["user"]["type"] === "administrator") {
            header("Location: /statistics");
        }

        $books = GetRecommendations($_SESSION["user"]["username"], $_SESSION["user"]["id"]);
        if ($_SESSION["logged_in"] && count($books) > 0) {
            if (isset($_GET["page"])) {
                $content = "pages/main.php";
            } else {
                header("Location: /main/1");
            }
        } else {
            header("Location: /bestsellers/1");
        }
        break;
    case 'login':
        $content = 'pages/login.php';
        break;
    case 'profile':
        $content = 'pages/profile.php';
        break;
    case 'basket':
        $content = 'pages/basket.php';
        break;
    case 'order-address':
        $content = 'pages/orderaddress.php';
        break;
    case 'order-payment':
        $content = 'pages/orderpayment.php';
        break;
    case 'order-success':
        $content = 'pages/ordersuccess.php';
        break;
    case 'add-address':
        $content = 'pages/addaddress.php';
        break;
    case 'signout':
        $content = 'pages/signout.php';
        break;
    case 'forgotten-password':
        $content = 'pages/forgottenpassword.php';
        break;
    case 'search':
        $content = 'pages/search.php';
        break;
    case 'book':
        $content = 'pages/book.php';
        break;
    case 'author':
        $content = 'pages/author.php';
        break;
    case 'publisher':
        $content = 'pages/publisher.php';
        break;
    case 'books':
        $content = 'pages/books.php';
        break;
    case 'new':
        $content = 'pages/new.php';
        break;
    case 'soon':
        $content = 'pages/soon.php';
        break;
    case 'bestsellers':
        $content = 'pages/bestsellers.php';
        break;
    case 'addbook':
        $content = 'pages/addbook.php';
        break;
    case 'modifybook':
        $content = 'pages/modifybook.php';
        break;
    case 'comments':
        $content = 'pages/comments.php';
        break;
    case 'statistics':
        $content = 'pages/statistics.php';
        break;
    case 'random':
        $isbns = GetISBNs();
        header("Location: /books/" . $isbns[array_rand($isbns)]);
        break;
    case 'error':
        $content = 'pages/error.php';
        break;
    default:
        header("Location: /");
        break;
}

?>