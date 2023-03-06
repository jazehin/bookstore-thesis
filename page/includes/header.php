<?php
include("includes/db_con.php");
include("includes/sql.php");

session_start();
$is_logged_in = isset($_SESSION["logged_in"]) && $_SESSION["logged_in"];

$genres = GetGenres();
?>

<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Könyváruház</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN"
        crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://kit.fontawesome.com/fea0ed64d7.js" crossorigin="anonymous"></script>

    <link rel="stylesheet" type="text/css" href="css/style.css">
    <script src="js/index.js"></script>
</head>

<body>
    <header class="navbar" style="background-color: rgba(225, 211, 180, 1);">
        <div class="container">
            <div class="row align-items-center w-100 mx-auto">
                <div class="col-auto d-inline d-lg-none ps-0">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar"
                        aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>
                <div class="col-lg-auto col-auto">
                    <a class="navbar-brand" href="/main">Könyváruház</a>
                </div>
                <div class="col px-3 d-none d-lg-block">
                    <form class="d-flex" role="search" method="get">
                        <input class="flex-fill form-control me-2" type="search" placeholder="Keresés"
                            aria-label="Search">
                        <input class="btn btn-brown" type="submit" value="Keresés">
                        <a class="btn btn-brown text-nowrap ms-2 d-none d-lg-block">Részletes
                            keresés</a>
                    </form>
                </div>
                <div class="col-lg-auto col px-0 text-end">
                    <a class="btn border-0" <?php if ($is_logged_in) { ?> data-bs-toggle="modal"
                            data-bs-target="#loggedInModal" <?php } else { ?> data-bs-toggle="modal"
                            data-bs-target="#loginModal" <?php } ?>>
                        <i class="fa-solid fa-circle-user fs-2"></i>
                    </a>
                    <a class="btn border-0" href="/basket">
                        <i class="fa-solid fa-basket-shopping fs-2"></i>
                    </a>

                </div>
            </div>
        </div>
        <div class="container d-block d-lg-none pt-2">
            <form class="d-flex" role="search" method="get">
                <input class="flex-fill form-control me-2" type="search" placeholder="Keresés" aria-label="Search">
                <input class="btn btn-brown" type="submit" value="Keresés">
            </form>
        </div>
    </header>
    <!--
    <nav class="navbar">
        <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvas" aria-labelledby="offcanvasLabel"
            style="background-color: rgba(225, 211, 180, 1);">
            <div class="offcanvas-header">
                <a class="navbar-brand" id="offcanvasLabel" href="/">Könyváruház</a>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="/books/9789635841523">Könyv</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            Admin
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/addbook">Könyv hozzáadása</a></li>
                            <li><a class="dropdown-item" href="#">Könyv módosítása</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    -->
    <nav class="navbar navbar-expand-lg py-0 mb-3" style="background-color: rgba(225, 211, 180, 0.5);">
        <div class="container ps-3">
            <div class="collapse navbar-collapse py-1" id="navbar">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="/books/9789635841523">Könyv</a>
                    </li>
                    <?php if ($is_logged_in && $_SESSION["user"]["type"] === "administrator") { ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            Admin
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/addbook">Könyv hozzáadása</a></li>
                            <li><a class="dropdown-item" href="/modifybook">Könyv módosítása</a></li>
                        </ul>
                    </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="loginModalLabel">Belépés</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <span id="login-error" class="text-danger d-none"></span>
                    <label for="login-username" class="form-label">Felhasználónév:</label>
                    <input type="text" name="username" id="login-username" class="form-control">
                    <label for="login-password" class="form-label mt-2">Jelszó:</label>
                    <input type="password" name="password" id="login-password" class="form-control">
                    <div class="mt-2">
                        <a href="/forgotten">Elfelejtette jelszavát?</a>
                    </div>
                    <div class="mt-2">
                        <span>Még nincs fiókja? </span>
                        <a href="" data-bs-toggle="modal" data-bs-target="#signupModal">Regisztráljon most!</a>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="button" class="btn btn-secondary" data-bs-dismiss="modal" value="Mégse">
                    <input type="button" value="Belépés" class="btn-brown btn" onclick="login();">
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="signupModal" tabindex="-1" aria-labelledby="signupModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="signupModalLabel">Regisztráció</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="/signup" method="post" autocomplete="on">
                    <div class="modal-body">
                        <label for="signup_redirect_uri" class="d-none">URI:</label>
                        <input type="text" name="signup_redirect_uri" id="signup_redirect_uri" class="d-none"
                            value="<?php echo $uri; ?>">
                        <label for="signup-email" class="form-label">E-mail cím:</label>
                        <input type="text" name="email" id="signup-email" class="form-control">
                        <label for="signup-username" class="form-label mt-2">Felhasználónév:</label>
                        <input type="text" name="username" id="signup-username" class="form-control">
                        <label for="signup-password" class="form-label mt-2">Jelszó:</label>
                        <input type="password" name="password" id="signup-password" class="form-control">
                        <div class="mt-2">
                            <span>Már van fiókja? </span>
                            <a href="" data-bs-toggle="modal" data-bs-target="#loginModal">Jelentkezzen be!</a>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="button" class="btn btn-secondary" data-bs-dismiss="modal" value="Mégse">
                        <input type="submit" value="Regisztráció" class="btn-brown btn">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="loggedInModal" tabindex="-1" aria-labelledby="loggedInModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="loggedInModalLabel">Üdvözlöm,
                        <?php if ($is_logged_in)
                            echo $_SESSION["user"]["username"]; ?>
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <span>Köszönjük, hogy minket választ!</span>
                </div>

                <div class="modal-footer">
                    <a href="/profile" class="btn btn-brown">Profil megtekintése</a>
                    <a href="/signout" class="btn btn-danger">Kijelentkezés</a>
                    <input type="button" class="btn btn-secondary" data-bs-dismiss="modal" value="Bezárás">
                </div>
            </div>
        </div>
    </div>

    <main class="container">