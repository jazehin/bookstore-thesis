<?php

// get lists for datalists
$writers = GetWriters();
$genres = GetGenres();
$publishers = GetPublishers();
$serieses = GetSerieses();
$languages = GetLanguages();
$covertypes = GetCoverTypes();

// declare an array for $_POST
$bookdata = array(
    "isbn" => "",
    "title" => "",
    "series" => "",
    "date-of-publishing" => "",
    "stock" => "",
    "number-of-pages" => "",
    "weight" => "",
    "publisher" => "",
    "covertype" => "",
    "language" => "",
    "description" => "",
    "genres" => array(),
    "writers" => array(),
    "price" => "",
    "discounted-price" => ""
);

// declare an array for error messages to be displayed
$errors = array(
    "isbn" => "",
    "title" => "",
    "series" => "",
    "date-of-publishing" => "",
    "stock" => "",
    "number-of-pages" => "",
    "weight" => "",
    "publisher" => "",
    "covertype" => "",
    "language" => "",
    "description" => "",
    "genres" => "",
    "writers" => "",
    "price" => "",
    "discounted-price" => "",
    "cover" => ""
);

// boolean to control whether to display the $_POST variables in the input fields or not
$display = false;

// boolean to display a "successfully added" message
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // checking ISBN
    $bookdata["isbn"] = $_POST["isbn"];

    $isIsbnValid = (strlen($bookdata["isbn"]) == 10 || strlen($bookdata["isbn"]) == 13) && preg_match("/^\d+$/", $bookdata["isbn"]);

    if (!$isIsbnValid) {
        $errors["isbn"] = "Kérem adjon meg egy helyes ISBN-t! Az ISBN 10 vagy 13 karakter hosszú és csak számokat tartalmaz!";
    }

    // checking book title 
    $bookdata["title"] = $_POST["title"];

    $isTitleValid = !empty($bookdata["title"]);

    if (!$isTitleValid) {
        $errors["title"] = "Kérem adja meg a könyv címét!";
    }

    // checking the date of publishing
    $bookdata["date-of-publishing"] = $_POST["date-of-publishing"];

    $isPublishingDateValid = !empty($bookdata["date-of-publishing"]);

    if (!$isPublishingDateValid) {
        $errors["date-of-publishing"] = "Kérem adjon meg egy dátumot!";
    }

    // checking the stock
    $bookdata["stock"] = intval($_POST["stock"]);

    $isStockValid = $bookdata["stock"] >= 0 && strval($bookdata["stock"]) == $_POST["stock"];

    if (!$isStockValid) {
        $errors["stock"] = "Kérem 0-t, vagy nagyobb számot adjon meg készletnek!";
    }

    // checking the number of pages
    $bookdata["number-of-pages"] = intval($_POST["number-of-pages"]);

    $isNumberOfPagesValid = $bookdata["number-of-pages"] > 0 && strval($bookdata["number-of-pages"]) == $_POST["number-of-pages"];

    if (!$isNumberOfPagesValid) {
        $errors["number-of-pages"] = "Kérem pozitív számot adjon meg az oldalak számának!";
    }

    // checking weight
    $bookdata["weight"] = intval($_POST["weight"]);

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

    // checking price
    $bookdata["price"] = intval($_POST["price"]);

    $isPriceValid = $bookdata["price"] > 0 && strval($bookdata["price"]) == $_POST["price"];

    if (!$isPriceValid) {
        $errors["price"] = "Kérem pozitív számot adjon meg a könyv árának!";
    }

    // checking discounted price
    $bookdata["discounted-price"] = intval($_POST["discounted-price"]);

    $isDiscountedPriceValid = empty($_POST['discounted-price']) || (strval($bookdata["discounted-price"]) == $_POST["discounted-price"] && $bookdata["discounted-price"] >= 0);

    if (!$isDiscountedPriceValid) {
        $errors["discounted-price"] = "Kérem pozitív számot adjon meg a könyv akciós árának!";
    }

    // checking cover
    $targetFile = "";
    if ($isIsbnValid) {
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

        // Check if $uploadOk is set to 0 by an error
        if (empty($errors["cover"])) {
            if (!move_uploaded_file($_FILES["cover"]["tmp_name"], $targetFile)) {
                $errors["cover"] = "Nem sikerült feltölteni a fájlt.";
            }
        }
    } else {
        $errors["cover"] = "A borító feltöltéséhez szükség van a könyv ISBN-ére!";
    }

    if (!array_filter($errors)) {
        // there are no errors
        InsertBook($bookdata);

        $display = false;
        $success = true;
    } else {
        // delete uploaded cover
        if (file_exists($targetFile)) {
            unlink($targetFile);
        }

        // if there are errors, show the values, otherwise hide them 
        $display = true;
        $success = false;
    }
}


// TODO: on failed form submission load the erroneus data back into the form
// TODO: add genres and writes to db
?>



<form class="card my-3 p-3" action="./addbook" method="post" enctype="multipart/form-data"
    style="background-color: rgba(225, 211, 180, 0.2);">
    <h1 class="fs-2">Könyv hozzáadása</h1>
    <p><span class="text-danger">*</span> kötelező mező</p>
    <?php if ($success) { ?>
        <p class="text-success">Könyv hozzáadása sikerült!</p>
    <?php } ?>
    <div class="row">
        <div class="col-sm-4 mb-3">
            <label for="isbn" class="form-label">ISBN: <span class="text-danger">*</span></label>
            <input type="text" name="isbn" id="isbn" class="form-control">
            <?php if (!empty($errors["isbn"])) { ?>
                <p class="text-danger">
                    <?php echo $errors["isbn"]; ?>
                </p>
            <?php } ?>
        </div>
        <div class="col-sm-4 mb-3">
            <label for="title" class="form-label">Könyvcím: <span class="text-danger">*</span></label>
            <input type="text" name="title" id="title" class="form-control">
            <?php if (!empty($errors["title"])) { ?>
                <p class="text-danger">
                    <?php echo $errors["title"]; ?>
                </p>
            <?php } ?>
        </div>
        <div class="col-sm-4 mb-3">
            <label for="series" class="form-label">Könyvsorozat:</label>
            <input type="text" name="series" id="series" list="serieses" class="form-control">
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
            <label for="date-of-publishing" class="form-label">Kiadás dátuma: <span class="text-danger">*</span></label>
            <input type="date" name="date-of-publishing" id="date-of-publishing" class="form-control">
            <?php if (!empty($errors["date-of-publishing"])) { ?>
                <p class="text-danger">
                    <?php echo $errors["date-of-publishing"]; ?>
                </p>
            <?php } ?>
        </div>
        <div class="col-sm-3 mb-3">
            <label for="stock" class="form-label">Készlet: <span class="text-danger">*</span></label>
            <div class="input-group">
                <input type="text" name="stock" id="stock" class="form-control">
                <span class="input-group-text">db</span>
            </div>
            <?php if (!empty($errors["stock"])) { ?>
                <p class="text-danger">
                    <?php echo $errors["stock"]; ?>
                </p>
            <?php } ?>
        </div>
        <div class="col-sm-3 mb-3">
            <label for="number-of-pages" class="form-label">Oldalszám: <span class="text-danger">*</span></label>
            <input type="text" name="number-of-pages" id="number-of-pages" class="form-control">
            <?php if (!empty($errors["number-of-pages"])) { ?>
                <p class="text-danger">
                    <?php echo $errors["number-of-pages"]; ?>
                </p>
            <?php } ?>
        </div>
        <div class="col-sm-3 mb-3">
            <label for="weight" class="form-label">Súly:</label>
            <div class="input-group">
                <input type="text" name="weight" id="weight" class="form-control">
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
            <input type="text" name="publisher" id="publisher" list="publishers" class="form-control">
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
            <input type="text" name="covertype" id="covertype" list="covertypes" class="form-control">
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
            <input type="text" name="language" id="language" list="languages" class="form-control">
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
            <textarea name="description" id="description" class="form-control" rows="7"></textarea>
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
                    <input type="text" class="form-control genre-field" name="genre-1" id="genre-1" list="genres">
                </div>
            </div>
            <div class="row">
                <div class="col-6 mb-3">
                    <input type="button" class="form-control" value="+" onclick="AddField('genre')">
                </div>
                <div class="col-6 mb-3">
                    <input type="button" class="form-control" value="-" onclick="RemoveField('genre')">
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
                <datalist id="genres">
                    <?php for ($i = 0; $i < count($writers); $i++) { ?>
                        <option value="<?php echo $writers[$i]; ?>">
                        <?php } ?>
                </datalist>
                <div class="col-12 mb-3">
                    <input type="text" class="form-control writer-field" name="writer-1" id="writer-1" list="writers">
                </div>
            </div>
            <div class="row">
                <div class="col-6 mb-3">
                    <input type="button" class="form-control" value="+" onclick="AddField('writer')">
                </div>
                <div class="col-6 mb-3">
                    <input type="button" class="form-control" value="-" onclick="RemoveField('writer')">
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
                <input type="text" name="price" id="price" class="form-control">
                <span class="input-group-text">Ft</span>
            </div>
            <?php if (!empty($errors["price"])) { ?>
                <p class="text-danger">
                    <?php echo $errors["price"]; ?>
                </p>
            <?php } ?>
        </div>
        <div class="col-sm-4 mb-3">
            <label for="discounted-price" class="form-label">Akciós ár:</label>
            <div class="input-group">
                <input type="text" name="discounted-price" id="discounted-price" class="form-control">
                <span class="input-group-text">Ft</span>
            </div>
            <?php if (!empty($errors["discounted-price"])) { ?>
                <p class="text-danger">
                    <?php echo $errors["discounted-price"]; ?>
                </p>
            <?php } ?>
        </div>
        <div class="col-sm-4 mb-3">
            <label for="cover" class="form-label">Borítókép:</label>
            <input type="file" name="cover" id="cover" class="form-control">
            <?php if (!empty($errors["cover"])) { ?>
                <p class="text-danger">
                    <?php echo $errors["cover"]; ?>
                </p>
            <?php } ?>
        </div>
    </div>

    <input type="submit" class="form-control" value="Felvétel">
</form>