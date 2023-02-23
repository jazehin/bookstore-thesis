<?php
if (isset($_GET['p'])) {
    $p = $_GET['p'];
} else {
    $p = 'main';
}

switch ($p) {
    case 'main':
        $content = 'mainpage.php';
        break;
    case 'book':
        $content = 'bookpage.php';
        break;
    case 'books':
        $content = 'books.php';
        break;
    case 'addbook':
        $content = 'addbook.php';
        break;
    default:
        header("Location: ./");
        break;
}

?>