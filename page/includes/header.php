<?php
include("includes/db_con.php");
include("includes/sql.php");

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

    <link rel="stylesheet" type="text/css" href="css/mystyle.css">
    <script src="js/myscript.js"></script>
</head>

<body>
    <header class="navbar" style="background-color: rgba(225, 211, 180, 1);">
        <div class="container">
            <div class="row align-items-center w-100 mx-auto">
                <div class="col-auto d-inline d-lg-none ps-0">
                    <button type="button" class="navbar-toggler" data-bs-toggle="offcanvas" data-bs-target="#offcanvas"
                        aria-controls="offcanvas">
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
                        <input class="btn" type="submit" value="Keresés"
                            style="background-color: rgba(139, 94, 60, 1); color: white;">
                        <a class="btn text-nowrap ms-2 d-none d-lg-block"
                            style="background-color: rgba(139, 94, 60, 1); color: white;">Részletes
                            keresés</a>
                    </form>
                </div>
                <div class="col-lg-auto col px-0 text-end">
                        <a class="btn border-0" data-bs-toggle="modal" data-bs-target="#login">
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
                <input class="btn" type="submit" value="Keresés"
                    style="background-color: rgba(139, 94, 60, 1); color: white;">
            </form>
        </div>
    </header>
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

    <div class="modal fade" id="login" tabindex="-1" aria-labelledby="loginLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <main class="container">