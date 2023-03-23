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
    case 'books':
        $content = 'pages/books.php';
        break;
    case 'addbook':
        $content = 'pages/addbook.php';
        break;
    case 'modifybook':
        $content = 'pages/modifybook.php';
        break;
    default:
        header("Location: /");
        break;
}

?>