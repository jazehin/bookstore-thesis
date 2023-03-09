<?php if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] && $_SESSION["user"]["type"] === "administrator") { ?>

    <?php

    // get lists for datalists
    $writers = GetWriters();
    $publishers = GetPublishers();
    $serieses = GetSerieses();
    $languages = GetLanguages();
    $covertypes = GetCoverTypes();

    // declare an array for $_POST
    $bookdata = array(
        "isbn" => "",
        "title" => "",
        "series" => "",
        "date_published" => "",
        "stock" => "",
        "pages" => "",
        "weight" => "",
        "publisher" => "",
        "covertype" => "",
        "language" => "",
        "description" => "",
        "genres" => array(""),
        "writers" => array(""),
        "price" => "",
        "discounted_price" => ""
    );

    // declare an array for error messages to be displayed
    $errors = array(
        "isbn" => "",
        "title" => "",
        "series" => "",
        "date_published" => "",
        "stock" => "",
        "pages" => "",
        "weight" => "",
        "publisher" => "",
        "covertype" => "",
        "language" => "",
        "description" => "",
        "genres" => "",
        "writers" => "",
        "price" => "",
        "discounted_price" => "",
        "cover" => ""
    );

    // boolean to control whether to display the $_POST variables in the input disabled fields or not
    $display = false;

    // boolean to display a "successfully added" message
    $deleted = false;
    $modified = false;

    if (isset($_POST["isbn"])) {
        $bookdata["isbn"] = $_POST["isbn"];
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST["delete"])) {
            DeleteBook($bookdata["isbn"]);
            $deleted = true;
        } else {
            // there's no need for checking ISBN

            // checking book title 
            $bookdata["title"] = $_POST["title"];

            $isTitleValid = !empty($bookdata["title"]);

            if (!$isTitleValid) {
                $errors["title"] = "Kérem adja meg a könyv címét!";
            }

            // store series
            $bookdata["series"] = $_POST["series"];

            // checking the date of publishing
            $bookdata["date_published"] = $_POST["date_published"];

            $isPublishingDateValid = !empty($bookdata["date_published"]);

            if (!$isPublishingDateValid) {
                $errors["date_published"] = "Kérem adjon meg egy dátumot!";
            }

            // checking the stock
            $bookdata["stock"] = (strlen($_POST["stock"]) == 0 ? NULL : intval($_POST["stock"]));

            $isStockValid = strlen($_POST["stock"]) > 0 && $bookdata["stock"] >= 0 && strval($bookdata["stock"]) == $_POST["stock"];

            if (!$isStockValid) {
                $errors["stock"] = "Kérem 0-t, vagy nagyobb számot adjon meg készletnek!";
            }

            // checking the number of pages
            $bookdata["pages"] = (intval($_POST["pages"]) == 0 ? NULL : intval($_POST["pages"]));

            $isNumberOfPagesValid = $bookdata["pages"] > 0 && strval($bookdata["pages"]) == $_POST["pages"];

            if (!$isNumberOfPagesValid) {
                $errors["pages"] = "Kérem pozitív számot adjon meg az oldalak számának!";
            }

            // checking weight
            $bookdata["weight"] = (intval($_POST["weight"]) == 0 ? NULL : intval($_POST["weight"]));

            $isWeightValid = empty($_POST['weight']) || (strval($bookdata["weight"]) == $_POST["weight"] && $bookdata["weight"] >= 0);

            if (!$isWeightValid) {
                $errors["weight"] = "Kérem érvényes súlyt adjon meg!";
            }

            // checking publisher
            $bookdata["publisher"] = $_POST["publisher"];

            $isPublisherValid = !empty($bookdata["publisher"]);

            if (!$isPublisherValid) {
                $errors["publisher"] = "Kérem adjon meg egy kiadót!";
            }

            // checking covertype
            $bookdata["covertype"] = $_POST["covertype"];

            $isCovertypeValid = !empty($bookdata["covertype"]);

            if (!$isCovertypeValid) {
                $errors["covertype"] = "Kérem adjon meg egy kötéstípust!";
            }

            // checking language
            $bookdata["language"] = $_POST["language"];

            $isLanguageValid = !empty($bookdata["language"]);

            if (!$isLanguageValid) {
                $errors["language"] = "Kérem adjon meg egy nyelvet!";
            }

            // checking description
            $bookdata["description"] = $_POST["description"];

            $isDescriptionValid = !empty($bookdata["description"]);

            if (!$isDescriptionValid) {
                $errors["description"] = "Kérem írjon leírást a könyvhöz!";
            }

            // checking genres
            $bookdata["genres"] = array();
            $genreCount = 1;
            while (isset($_POST["genre-" . $genreCount + 1])) {
                $genreCount++;
            }

            for ($i = 1; $i <= $genreCount; $i++) {
                $genre = $_POST["genre-" . $i];
                array_push($bookdata["genres"], $genre);

                if (empty($errors["genres"])) {
                    $isGenreValid = !empty($genre);

                    if (!$isGenreValid) {
                        $errors["genres"] = "Kérem adjon meg annyi műfajt ahány mező van!";
                    }
                }
            }

            // checking writers
            $bookdata["writers"] = array();
            $writerCount = 1;
            while (isset($_POST["writer-" . $writerCount + 1])) {
                $writerCount++;
            }

            for ($i = 1; $i <= $writerCount; $i++) {
                $writer = $_POST["writer-" . $i];
                array_push($bookdata["writers"], $writer);

                if (empty($errors["writers"])) {
                    $isWriterValid = !empty($writer);

                    if (!$isWriterValid) {
                        $errors["writers"] = "Kérem adjon meg annyi írót ahány mező van!";
                    }
                }
            }
            print_r($bookdata["writers"]);
            print_r($bookdata["genres"]);

            // checking price
            $bookdata["price"] = (intval($_POST["price"]) == 0 ? NULL : intval($_POST["price"]));

            $isPriceValid = $bookdata["price"] > 0 && strval($bookdata["price"]) == $_POST["price"];

            if (!$isPriceValid) {
                $errors["price"] = "Kérem pozitív számot adjon meg a könyv árának!";
            }

            // checking discounted price
            $bookdata["discounted_price"] = "";
            if (!isset($_POST["discounted_price"]) || $_POST["discounted_price"] === "") {
                $bookdata["discounted_price"] = null;
            } else if ($_POST["discounted_price"] == "0" || $_POST["discounted_price"] == "-0") {
                $bookdata["discounted_price"] = 0;
            } else {
                if (is_numeric($_POST["discounted_price"])) {
                    $bookdata["discounted_price"] = intval($_POST["discounted_price"]);
                } else {
                    $bookdata["discounted_price"] = $_POST["discounted_price"];
                }
            }

            $isDiscountedPriceValid = is_null($bookdata["discounted_price"]) || (is_numeric($bookdata["discounted_price"]) && $bookdata["discounted_price"] > 0);

            if (!$isDiscountedPriceValid) {
                $errors["discounted_price"] = "Kérem pozitív számot adjon meg a könyv akciós árának, vagy hagyja üresen!";
            }

            // checking cover
            $targetFile = "";
            if (!empty($_FILES["cover"]["name"])) {
                $targetDirectory = "covers/";
                $imageFileType = strtolower(pathinfo(basename($_FILES["cover"]["name"]), PATHINFO_EXTENSION));
                $targetFile = $targetDirectory . $bookdata['isbn'] . '.' . $imageFileType;

                // delete if file exists already
                if (file_exists($targetFile)) {
                    unlink($targetFile);
                }

                // check if the provided file is an image
                $check = getimagesize($_FILES["cover"]["tmp_name"]);
                if ($check === false) {
                    $errors["cover"] = "A fájlnak képnek kell lennie!";
                }

                // check file size (ok: <10MB)
                if ($_FILES["cover"]["size"] > 1e+7) {
                    $errorMessage = "A kép nem lehet nagyobb 10MB-nál!";
                    if (empty($errors["cover"])) {
                        $errors["cover"] = $errorMessage;
                    } else {
                        $errors["cover"] = $errors["cover"] . '<br>' . $errorMessage;
                    }
                }

                // only allow png, jpg, jpeg files
                $isAllowedFileType = $imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg";
                if (!$isAllowedFileType) {
                    $errorMessage = "A kép csak PNG, JPG és JPEG fájlformátumú lehet!";
                    if (empty($errors["cover"])) {
                        $errors["cover"] = $errorMessage;
                    } else {
                        $errors["cover"] = $errors["cover"] . '<br>' . $errorMessage;
                    }
                }

                // check if there are errors
                if (empty($errors["cover"])) {
                    if (!move_uploaded_file($_FILES["cover"]["tmp_name"], $targetFile)) {
                        $errors["cover"] = "Nem sikerült feltölteni a fájlt.";
                    }
                }
            }

            if (!array_filter($errors)) {
                // there are no errors
                UpdateBook($bookdata);
                $modified = true;
                $display = false;
            } else {
                // delete uploaded cover
                if (file_exists($targetFile)) {
                    unlink($targetFile);
                }

                // if there are errors, show the values, otherwise hide them 
                $display = true;
            }
        }
    }
    ?>



    <form class="card p-3" action="./modifybook" method="post" autocomplete="off" enctype="multipart/form-data">
        <h1 class="fs-2">Könyv módosítása</h1>
        <p><span class="text-danger">*</span> kötelező mező</p>

        <?php if ($modified) { ?>
            <p class="text-success">A könyv módosítása sikerült!</p>
        <?php } ?>

        <?php if ($deleted) { ?>
            <p class="text-danger">A könyv törlése sikerült!</p>
        <?php } ?>

        <div class="row">
            <div class="col-sm-4 mb-3">
                <label for="isbn" class="form-label">ISBN: <span class="text-danger">*</span></label>
                <input type="text" name="isbn" id="isbn" class="form-control" onkeyup="loadBookDataByIsbn(this.value);"
                    maxlength="13" value="<?php if ($display)
                        echo $bookdata["isbn"]; ?>">
                <?php if (!empty($errors["isbn"])) { ?>
                    <p class="text-danger">
                        <?php echo $errors["isbn"]; ?>
                    </p>
                <?php } ?>
            </div>
            <div class="col-sm-4 mb-3">
                <label for="title" class="form-label">Könyvcím: <span class="text-danger">*</span></label>
                <input disabled type="text" name="title" id="title" class="form-control" value="<?php if ($display)
                    echo $bookdata["title"]; ?>">
                <?php if (!empty($errors["title"])) { ?>
                    <p class="text-danger">
                        <?php echo $errors["title"]; ?>
                    </p>
                <?php } ?>
            </div>
            <div class="col-sm-4 mb-3">
                <label for="series" class="form-label">Könyvsorozat:</label>
                <input disabled type="text" name="series" id="series" list="serieses" class="form-control" value="<?php if ($display)
                    echo $bookdata["series"]; ?>">
                <datalist id="serieses">
                    <?php for ($i = 0; $i < count($serieses); $i++) { ?>
                        <option value="<?php echo $serieses[$i]; ?>">
                        <?php } ?>
                </datalist>
                <?php if (!empty($errors["series"])) { ?>
                    <p class="text-danger">
                        <?php echo $errors["series"]; ?>
                    </p>
                <?php } ?>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-3 mb-3">
                <label for="date_published" class="form-label">Kiadás dátuma: <span class="text-danger">*</span></label>
                <input disabled type="date" name="date_published" id="date_published" class="form-control" value="<?php if ($display)
                    echo $bookdata["date_published"]; ?>">
                <?php if (!empty($errors["date_published"])) { ?>
                    <p class="text-danger">
                        <?php echo $errors["date_published"]; ?>
                    </p>
                <?php } ?>
            </div>
            <div class="col-sm-3 mb-3">
                <label for="stock" class="form-label">Készlet: <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input disabled type="text" name="stock" id="stock" class="form-control" value="<?php if ($display)
                        echo $bookdata["stock"]; ?>">
                    <span class="input-group-text">db</span>
                </div>
                <?php if (!empty($errors["stock"])) { ?>
                    <p class="text-danger">
                        <?php echo $errors["stock"]; ?>
                    </p>
                <?php } ?>
            </div>
            <div class="col-sm-3 mb-3">
                <label for="pages" class="form-label">Oldalszám: <span class="text-danger">*</span></label>
                <input disabled type="text" name="pages" id="pages" class="form-control" value="<?php if ($display)
                    echo $bookdata["pages"]; ?>">
                <?php if (!empty($errors["pages"])) { ?>
                    <p class="text-danger">
                        <?php echo $errors["pages"]; ?>
                    </p>
                <?php } ?>
            </div>
            <div class="col-sm-3 mb-3">
                <label for="weight" class="form-label">Súly:</label>
                <div class="input-group">
                    <input disabled type="text" name="weight" id="weight" class="form-control" value="<?php if ($display)
                        echo $bookdata["weight"]; ?>">
                    <span class="input-group-text">gramm</span>
                </div>
                <?php if (!empty($errors["weight"])) { ?>
                    <p class="text-danger">
                        <?php echo $errors["weight"]; ?>
                    </p>
                <?php } ?>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-4 mb-3">
                <label for="publisher" class="form-label">Kiadó: <span class="text-danger">*</span></label>
                <input disabled type="text" name="publisher" id="publisher" list="publishers" class="form-control" value="<?php if ($display)
                    echo $bookdata["publisher"]; ?>">
                <datalist id="publishers">
                    <?php for ($i = 0; $i < count($publishers); $i++) { ?>
                        <option value="<?php echo $publishers[$i]; ?>">
                        <?php } ?>
                </datalist>
                <?php if (!empty($errors["publisher"])) { ?>
                    <p class="text-danger">
                        <?php echo $errors["publisher"]; ?>
                    </p>
                <?php } ?>
            </div>
            <div class="col-sm-4 mb-3">
                <label for="covertype" class="form-label">Kötéstípus: <span class="text-danger">*</span></label>
                <input disabled type="text" name="covertype" id="covertype" list="covertypes" class="form-control" value="<?php if ($display)
                    echo $bookdata["covertype"]; ?>">
                <datalist id="covertypes">
                    <?php for ($i = 0; $i < count($covertypes); $i++) { ?>
                        <option value="<?php echo $covertypes[$i]; ?>">
                        <?php } ?>
                </datalist>
                <?php if (!empty($errors["covertype"])) { ?>
                    <p class="text-danger">
                        <?php echo $errors["covertype"]; ?>
                    </p>
                <?php } ?>
            </div>
            <div class="col-sm-4 mb-3">
                <label for="language" class="form-label">Nyelv: <span class="text-danger">*</span></label>
                <input disabled type="text" name="language" id="language" list="languages" class="form-control" value="<?php if ($display)
                    echo $bookdata["language"]; ?>">
                <datalist id="languages">
                    <?php for ($i = 0; $i < count($languages); $i++) { ?>
                        <option value="<?php echo $languages[$i]; ?>">
                        <?php } ?>
                </datalist>
                <?php if (!empty($errors["language"])) { ?>
                    <p class="text-danger">
                        <?php echo $errors["language"]; ?>
                    </p>
                <?php } ?>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 mb-3">
                <label for="description" class="form-label">Leírás: <span class="text-danger">*</span></label>
                <textarea disabled name="description" id="description" class="form-control" rows="7"><?php if ($display)
                    echo $bookdata["description"]; ?></textarea>
            </div>
            <?php if (!empty($errors["description"])) { ?>
                <p class="text-danger">
                    <?php echo $errors["description"]; ?>
                </p>
            <?php } ?>
        </div>

        <div class="row">
            <div class="col-sm-6">
                <label for="genre-1" class="form-label">Műfaj(ok): <span class="text-danger">*</span><br><span
                        class="small">Nyomja meg a + gombot további mezők felvételéhez és a - gombot az
                        eltávolításukhoz.</span></label>
                <div class="row" id="genre-fields">
                    <datalist id="genres">
                        <?php for ($i = 0; $i < count($genres); $i++) { ?>
                            <option value="<?php echo $genres[$i]; ?>">
                            <?php } ?>
                    </datalist>
                    <div class="col-12 mb-3">
                        <input disabled type="text" class="form-control genre-field" name="genre-1" id="genre-1"
                            list="genres" value="<?php if ($display)
                                echo $bookdata["genres"][0]; ?>">
                    </div>
                    <?php for ($i = 1; $i < count($bookdata["genres"]); $i++) { ?>
                        <div class="col-12 mb-3">
                            <input disabled type="text" class="form-control genre-field" name="genre-<?php echo $i + 1; ?>"
                                id="genre-<?php echo $i + 1; ?>" list="genres" value="<?php if ($display)
                                         echo $bookdata["genres"][$i]; ?>">
                        </div>
                    <?php } ?>
                </div>
                <div class="row">
                    <div class="col-6 mb-3">
                        <input disabled type="button" class="btn-brown form-control form-button" value="+"
                            onclick="AddField('genre')">
                    </div>
                    <div class="col-6 mb-3">
                        <input disabled type="button" class="btn-brown form-control form-button" value="-"
                            onclick="RemoveField('genre')">
                    </div>
                </div>
                <?php if (!empty($errors["genres"])) { ?>
                    <p class="text-danger">
                        <?php echo $errors["genres"]; ?>
                    </p>
                <?php } ?>
            </div>
            <div class="col-sm-6">
                <label for="writer-1" class="form-label">Író(k): <span class="text-danger">*</span><br><span
                        class="small">Nyomja meg a + gombot további mezők felvételéhez és a - gombot az
                        eltávolításukhoz.</span></label>
                <div class="row" id="writer-fields">
                    <datalist id="writers">
                        <?php for ($i = 0; $i < count($writers); $i++) { ?>
                            <option value="<?php echo $writers[$i]; ?>">
                            <?php } ?>
                    </datalist>
                    <div class="col-12 mb-3">
                        <input disabled type="text" class="form-control writer-field" name="writer-1" id="writer-1"
                            list="writers" value="<?php if ($display)
                                echo $bookdata["writers"][0]; ?>">
                    </div>
                    <?php for ($i = 1; $i < count($bookdata["writers"]); $i++) { ?>
                        <div class="col-12 mb-3">
                            <input disabled type="text" class="form-control writer-field" name="writer-<?php echo $i + 1; ?>"
                                id="writer-<?php echo $i + 1; ?>" list="writers" value="<?php if ($display)
                                         echo $bookdata["writers"][$i]; ?>">
                        </div>
                    <?php } ?>
                </div>
                <div class="row">
                    <div class="col-6 mb-3">
                        <input disabled type="button" class="btn-brown form-control form-button" value="+"
                            onclick="AddField('writer')">
                    </div>
                    <div class="col-6 mb-3">
                        <input disabled type="button" class="btn-brown form-control form-button" value="-"
                            onclick="RemoveField('writer')">
                    </div>
                </div>
                <?php if (!empty($errors["writers"])) { ?>
                    <p class="text-danger">
                        <?php echo $errors["writers"]; ?>
                    </p>
                <?php } ?>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-4 mb-3">
                <label for="price" class="form-label">Ár: <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input disabled type="text" name="price" id="price" class="form-control" value="<?php if ($display)
                        echo $bookdata["price"]; ?>">
                    <span class="input-group-text">Ft</span>
                </div>
                <?php if (!empty($errors["price"])) { ?>
                    <p class="text-danger">
                        <?php echo $errors["price"]; ?>
                    </p>
                <?php } ?>
            </div>
            <div class="col-sm-4 mb-3">
                <label for="discounted_price" class="form-label">Akciós ár:</label>
                <div class="input-group">
                    <input disabled type="text" name="discounted_price" id="discounted_price" class="form-control" value="<?php if ($display)
                        echo $bookdata["discounted_price"]; ?>">
                    <span class="input-group-text">Ft</span>
                </div>
                <?php if (!empty($errors["discounted_price"])) { ?>
                    <p class="text-danger">
                        <?php echo $errors["discounted_price"]; ?>
                    </p>
                <?php } ?>
            </div>
            <div class="col-sm-4 mb-3">
                <label for="cover" class="form-label">Borítókép:</label>
                <input disabled type="file" name="cover" id="cover" class="form-control">
                <?php if (!empty($errors["cover"])) { ?>
                    <p class="text-danger">
                        <?php echo $errors["cover"]; ?>
                    </p>
                <?php } ?>
            </div>
        </div>

        <div class="row">
            <div class="col-6">
                <input disabled type="button" class="btn btn-danger form-control form-button" value="Törlés"
                    data-bs-toggle="modal" data-bs-target="#deleteBookModal">
            </div>
            <div class="col-6">

                <input disabled type="submit" name="modify" class="btn-brown form-control form-button" value="Mentés">
            </div>
        </div>

        <div class="modal fade" id="deleteBookModal" tabindex="-1" aria-labelledby="deleteBookModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="deleteBookModalLabel">
                            Könyv törlése
                        </h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <span class="txt-danger">Figyelem! Ez a művelet nem vonható vissza!</span>
                    </div>

                    <div class="modal-footer">
                        <input type="button" class="btn btn-secondary" data-bs-dismiss="modal" value="Mégse">
                        <input type="submit" value="Törlés" name="delete" class="btn btn-danger">
                    </div>
                </div>
            </div>
        </div>

    </form>

<?php } else { ?>

    <p class="text-danger">Nincs jogosultsága az oldal megtekintéséhez!</p>

<?php } ?>