<?php
if (isset($_GET['p'])) {
    $p = $_GET['p'];
}
else {
    $p = 'main';
}

switch ($p) {
    case 'main':
        $content = 'mainpage.php';
        break;
    case 'addbook':
        $content = 'addbook.php';
        break;
    default:
        $content = 'mainpage.php';
        break;
}

?>