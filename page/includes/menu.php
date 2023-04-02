<?php
if (isset($_GET['p'])) {
    $p = $_GET['p'];
} else {
    $p = 'main';
}

switch ($p) {
    case 'main':
        $content = 'pages/mainpage.php';
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
        $content = 'pages/bookpage.php';
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
    case 'addbook':
        $content = 'pages/addbook.php';
        break;
    case 'modifybook':
        $content = 'pages/modifybook.php';
        break;
    case 'random':
        $isbns = GetISBNs();
        header("Location: /books/" . $isbns[array_rand($isbns)]);
        break;
    default:
        header("Location: /");
        break;
}

?>