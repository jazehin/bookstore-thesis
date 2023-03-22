<?php
if (DoesBookExist($_GET["isbn"])) {
    $bookdata = GetBookByISBN($_GET["isbn"]);
    $folder = "covers/";
    $img = "no_cover.jpg";
    if (file_exists($folder . $bookdata["isbn"] . ".jpg")) {
        $img = $bookdata["isbn"] . ".jpg";
    } else if (file_exists($folder . $bookdata["isbn"] . ".jpeg")) {
        $img = $bookdata["isbn"] . ".jpeg";
    } else if (file_exists($folder . $bookdata["isbn"] . ".png")) {
        $img = $bookdata["isbn"] . ".png";
    }

    $genres = "";
    for ($i = 0; $i < count($bookdata["genres"]); $i++) {
        $genres = $genres . $bookdata["genres"][$i][1];
        if ($i < count($bookdata["genres"]) - 1)
            $genres = $genres . ', ';
    }

    $writers = "";
    for ($i = 0; $i < count($bookdata["writers"]); $i++) {
        $writers = $writers . $bookdata["writers"][$i][1];
        if ($i < count($bookdata["writers"]) - 1)
            $writers = $writers . ', ';
    }


$_SESSION["basket"]

?>

    <div class="card p-3">
        <!--
    <div class="row">
        <span class="fw-bold fs-5 lh-1">
                    <?php echo $bookdata["title"]; ?>
        </span>
        <span> · </span>
        <span class="fst-italic"><?php echo $writers ?></span>
    </div>
    -->
        <div class="row">
            <div class="col-md-4 col-lg-3">
                <div>
                    <p>
                        <span id="title" class="fw-bold fs-5 lh-1">
                            <?php echo $bookdata["title"]; ?>
                        </span>
                        <script>
                            document.title = document.getElementById("title").innerText;
                        </script>
                        <br>
                        <span class="fst-italic">
                            <?php echo $writers ?>
                        </span>
                    </p>
                </div>
                <img class="img-fluid" src="<?php echo '/' . $folder . $img ?>" alt="">
                <div class="book-info">
                    <div class="row pt-1">
                        <span class="col-5 my-auto fw-bold">Kiadó:</span>
                        <span class="col-7 my-auto">
                            <?php echo $bookdata["publisher"]; ?>
                        </span>
                    </div>

                    <div class="row pt-1">
                        <span class="col-5 my-auto fw-bold">ISBN:</span>
                        <span class="col-7 my-auto">
                            <?php echo $bookdata["isbn"]; ?>
                        </span>
                    </div>

                    <div class="row pt-1">
                        <span class="col-5 my-auto fw-bold">Műfajok:</span>
                        <span class="col-7 my-auto">
                            <?php echo $genres; ?>
                        </span>
                    </div>

                    <?php if (!empty($bookdata["weight"])) { ?>
                        <div class="row pt-1">
                            <span class="col-5 my-auto fw-bold">Súly:</span>
                            <span class="col-7 my-auto">
                                <?php echo $bookdata["weight"]; ?> g
                            </span>
                        </div>
                    <?php } ?>

                    <div class="row pt-1">
                        <span class="col-5 my-auto fw-bold">Kötéstípus:</span>
                        <span class="col-7 my-auto">
                            <?php echo $bookdata["cover"]; ?>
                        </span>
                    </div>

                    <div class="row pt-1">
                        <span class="col-5 my-auto fw-bold">Kiadás dátuma:</span>
                        <span class="col-7 my-auto">
                            <?php
                            $date = date_create($bookdata["date_published"]);
                            echo date_format($date, "Y. m. d.");
                            ?>
                        </span>
                    </div>

                    <div class="row pt-1">
                        <span class="col-5 my-auto fw-bold">Nyelv:</span>
                        <span class="col-7 my-auto">
                            <?php echo $bookdata["language"]; ?>
                        </span>
                    </div>

                    <div class="row py-1">
                        <span class="col-5 my-auto fw-bold">Oldalak száma:</span>
                        <span class="col-7 my-auto">
                            <?php echo $bookdata["pages"]; ?>
                        </span>
                    </div>



                </div>
            </div>
            <div class="col-md-8 col-lg-9">
                <span>
                    <?php echo stripslashes($bookdata["description"]); ?>
                </span>
                <div class="purchase mt-3 pt-3 d-flex justify-content-end align-items-center">
                    <div class="price my-auto">
                        <span class="bookcard-price  <?php if (!is_null($bookdata["discounted_price"]))
                            echo "text-decoration-line-through";
                        else
                            echo "fs-5"; ?>"><?php echo $bookdata["price"]; ?> Ft</span>
                        <?php if (!is_null($bookdata["discounted_price"])) { ?>
                            &nbsp;helyett&nbsp;<span class="bookcard-discounted-price text-danger fw-bold fs-5">
                                <?php echo $bookdata["discounted_price"]; ?> Ft
                            </span>
                            <span class="discount-percent fs-5">
                                (-
                                <?php echo round(((1 - ($bookdata["discounted_price"] / $bookdata["price"])) * 100), 0); ?>%)
                            </span>
                        <?php } ?>
                    </div>
                    <form action="" method="post">

                        <input type="submit" class="btn btn-brown ms-3" value="Kosárba">
                    </form>
                </div>
            </div>

        </div>


        <!--<i class="fa-solid fa-basket-shopping fa-xl"></i>-->
    </div>


<?php } else { ?>

    <p class="text-danger">Nincs könyv ilyen ISBN-nel!</p>

<?php } ?>