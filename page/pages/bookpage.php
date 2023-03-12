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
    $genres = substr($genres, 0, strlen($genres) - 2);

    $writers = "";
    for ($i = 0; $i < count($bookdata["writers"]); $i++) {
        $writers = $writers . $bookdata["writers"][$i][1];
        if ($i < count($bookdata["writers"]) - 1)
            $writers = $writers . ', ';
    }
    $writers = substr($writers, 0, strlen($writers) - 2);




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
                        <span class="fw-bold fs-5 lh-1">
                            <?php echo $bookdata["title"]; ?>
                        </span>
                        <span> · </span>
                        <span class="fst-italic">
                            <?php echo $writers ?>
                        </span><br>
                        <span>
                            <?php echo $genres; ?>
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
                <div class="book-content">
                    <span class="fw-bold">Leírás:</span><br>
                    <span>
                        <?php echo str_replace("\n", "<br>", $bookdata["description"]); ?>
                    </span>
                </div>
            </div>
        </div>


        <!--<i class="fa-solid fa-basket-shopping fa-xl"></i>-->
    </div>


<?php } else { ?>

    <p class="text-danger">Nincs könyv ilyen ISBN-nel!</p>

<?php } ?>

