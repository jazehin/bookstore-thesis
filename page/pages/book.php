<?php if (DoesBookExist($_GET["isbn"])) { ?>
    <?php
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
        $writers = $writers . '<a class="link-dark" href="/authors/' . str_replace(' ', '+', $bookdata["writers"][$i][1]) . '/1">' . $bookdata["writers"][$i][1] . '</a>';
        if ($i < count($bookdata["writers"]) - 1)
            $writers = $writers . ', ';
    }

    if (isset($_POST["post-comment"])) {
        PostComment($_SESSION["user"]["id"], $bookdata["isbn"], $_POST["comment"]);
    }

    if (isset($_POST["delete-comment"]))
        DeleteComment($_POST["comment-id"]);
    ?>

    <div class="modal fade" id="deleteCommentModal" tabindex="-1" aria-labelledby="deleteCommentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="deleteCommentModalLabel">
                        Komment törlése
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <span class="txt-danger">Figyelem! Ez a művelet nem vonható vissza!</span>
                </div>

                <form action="" method="post" class="modal-footer">
                    <input type="button" class="btn btn-secondary" data-bs-dismiss="modal" value="Mégse">
                    <input type="submit" class="btn btn-danger" name="delete-comment" value="Törlés">
                    <input type="hidden" name="comment-id" id="comment-id">
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addedToBasketModal" tabindex="-1" aria-labelledby="addedToBasketModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="addedToBasketModalLabel">
                        Információ
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <span id="info">

                    </span>
                </div>

                <div class="modal-footer">
                    <input type="button" class="btn btn-secondary" data-bs-dismiss="modal" value="Bezárás">
                    <a href="/basket" class="btn btn-brown">Kosárhoz</a>
                </div>
            </div>
        </div>
    </div>

    <div class=" p-3">
        <div class="row">
            <div class="col-md-4 col-lg-3">
                <div>
                    <p class="mb-1">
                        <span id="title" class="fw-bold fs-5 ">
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
                <?php if ($_SESSION["logged_in"]) { ?>
                <div class="rating mb-2">
                    <?php for ($i = 1; $i < 6; $i++) { ?> 
                        <i class="fa-solid fa-star rating-star" id="rating-<?php echo $i; ?>" onmouseenter="setRatingLook(this);" onmouseover="setRatingLook(this);" onmousemove="setRatingLook(this);" onclick="setRating('<?php echo $bookdata['isbn']; ?>', <?php echo $i; ?>);" style="min-width: 20px;"></i>
                    <?php } ?>
                </div>
                <?php } ?>
                <img class="img-fluid" src="<?php echo '/' . $folder . $img ?>" alt="">
                <div class="book-info">
                    <div class="row pt-1">
                        <span class="col-5 my-auto fw-bold">Kiadó:</span>
                        <span class="col-7 my-auto">
                            <a class="link-dark" href="/publishers/<?php echo str_replace(' ', '+', $bookdata["publisher"]); ?>/1">
                                <?php echo $bookdata["publisher"]; ?>
                            </a>
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
                            $date_published = date_create($bookdata["date_published"]);
                            echo date_format($date_published, "Y. m. d.");
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
                    <div class="price my-auto me-3 text-end">
                        <span class="bookcard-price d-inline-block <?php if (!is_null($bookdata["discounted_price"]))
                            echo "text-decoration-line-through";
                        else
                            echo "fs-5"; ?>"><?php echo $bookdata["price"]; ?> Ft</span>
                        <?php if (!is_null($bookdata["discounted_price"])) { ?>
                            <br class="d-md-none d-block">
                            <?php echo " helyett "; ?>
                            <span class="bookcard-discounted-price text-danger fw-bold fs-5">
                                <?php echo $bookdata["discounted_price"]; ?> Ft
                            </span>
                            <span class="discount-percent fs-5">
                                <?php echo "(-" . round(((1 - ($bookdata["discounted_price"] / $bookdata["price"])) * 100), 0) . "%)"; ?>
                            </span>
                        <?php } ?>
                        <br>
                        <span class="points d-inline-block">
                            A könyv megvásárlásával <span class="fw-bold">
                                <?php
                                if (is_null($bookdata["discounted_price"]))
                                    echo round($bookdata["price"], -1) / 10;
                                else
                                    echo round($bookdata["discounted_price"], -1) / 10;
                                ?>
                                pont
                            </span> szerezhető.
                        </span>
                    </div>
                    <?php $today = date_create(); ?>
                    <div class="d-flex flex-column ms-3 justify-content-center">
                        <input type="button" class="btn btn-brown <?php if ($today < $date_published || $bookdata["stock"] == 0)
                            echo 'disabled' ?>" value="Kosárba" data-bs-toggle="modal"
                                data-bs-target="#addedToBasketModal"
                                onclick="addToBasket(<?php echo $bookdata['isbn']; ?>, '<?php echo $bookdata['title']; ?>')">

                        <span class="">Készlet:
                            <?php echo $bookdata["stock"]; ?> db
                        </span>


                    </div>

                </div>
            </div>

        </div>

        <div class="row mt-3 pt-3">
            <span class="fs-5 fw-bold">Kommentek</span>
            <div id="comments">
                
            </div>
            <script id="script">
                loadComments(<?php echo $bookdata["isbn"]; ?>, 1);
            </script>
            <?php if ($_SESSION["logged_in"]) { ?>
                <form id="comment-on-book" method="post">
                    <label for="comment">
                        <?php if (GetNumberOfComments($bookdata["isbn"]) > 0) { ?>
                            Csatlakozzon Ön is a beszélgetéshez!
                        <?php } else { ?>
                            Ossza meg véleményét a könyvvel kapcsolatban!
                        <?php } ?>
                    </label>
                    <textarea name="comment" id="comment" class="form-control" rows="5" onkeyup="enablePostButton(this);"></textarea>
                    <div class="w-100 text-end">
                        <input type="submit" value="Posztolás" class="btn btn-brown mt-3" id="post-comment" name="post-comment" disabled>
                    </div>
                </form>
            <?php } else { ?>
                <span>Jelentkezzen be, hogy hozzászólhasson a beszélgetéshez!</span>
            <?php } ?>
        </div>
    </div>


<?php } else { ?>

    <p class="text-danger">Nincs könyv ilyen ISBN-nel!</p>

<?php } ?>