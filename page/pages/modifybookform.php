<?php
include("./../includes/db_con.php");
include("./../includes/sql.php");

$display = false;


$isbn = $_GET["isbn"];

if (DoesBookExist($isbn)) {
    $bookdata = GetBookByISBN($isbn);
    $display = true;
}

// FIXME

?>

<script src="js/addbook_field_control.js"></script>

<div class="row">
    <div class="col-sm-4 mb-3">
        <label for="isbn" class="form-label">ISBN: <span class="text-danger">*</span></label>
        <input type="text" name="isbn" id="isbn" class="form-control" onkeyup="loadBookData(this.value);" value="<?php if ($display)
            echo $bookdata["isbn"]; ?>">
        <?php if (!empty($errors["isbn"])) { ?>
            <p class="text-danger">
                <?php echo $errors["isbn"]; ?>
            </p>
        <?php } ?>
    </div>
    <div class="col-sm-4 mb-3">
        <label for="title" class="form-label">Könyvcím: <span class="text-danger">*</span></label>
        <input type="text" name="title" id="title" class="form-control" value="<?php if ($display)
            echo $bookdata["title"]; ?>">
        <?php if (!empty($errors["title"])) { ?>
            <p class="text-danger">
                <?php echo $errors["title"]; ?>
            </p>
        <?php } ?>
    </div>
    <div class="col-sm-4 mb-3">
        <label for="series" class="form-label">Könyvsorozat:</label>
        <input type="text" name="series" id="series" list="serieses" class="form-control" value="<?php if ($display)
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
        <input type="date" name="date_published" id="date_published" class="form-control" value="<?php if ($display)
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
            <input type="text" name="stock" id="stock" class="form-control" value="<?php if ($display)
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
        <input type="text" name="pages" id="pages" class="form-control" value="<?php if ($display)
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
            <input type="text" name="weight" id="weight" class="form-control" value="<?php if ($display)
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
        <input type="text" name="publisher" id="publisher" list="publishers" class="form-control" value="<?php if ($display)
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
        <input type="text" name="covertype" id="covertype" list="covertypes" class="form-control" value="<?php if ($display)
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
        <input type="text" name="language" id="language" list="languages" class="form-control" value="<?php if ($display)
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
        <textarea name="description" id="description" class="form-control" rows="7"><?php if ($display)
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
                <input type="text" class="form-control genre-field" name="genre-1" id="genre-1" list="genres" value="<?php if ($display)
                    echo $bookdata["genres"][0]; ?>">
            </div>
            <?php for ($i = 1; $i < count($bookdata["genres"]); $i++) { ?>
                <div class="col-12 mb-3">
                    <input type="text" class="form-control genre-field" name="genre-<?php echo $i + 1; ?>"
                        id="genre-<?php echo $i + 1; ?>" list="genres" value="<?php if ($display)
                                 echo $bookdata["genres"][$i]; ?>">
                </div>
            <?php } ?>
        </div>
        <div class="row">
            <div class="col-6 mb-3">
                <input type="button" class="btn-brown form-control" value="+" onclick="AddField('genre')">
            </div>
            <div class="col-6 mb-3">
                <input type="button" class="btn-brown form-control" value="-" onclick="RemoveField('genre')">
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
                <input type="text" class="form-control writer-field" name="writer-1" id="writer-1" list="writers" value="<?php if ($display)
                    echo $bookdata["writers"][0]; ?>">
            </div>
            <?php for ($i = 1; $i < count($bookdata["writers"]); $i++) { ?>
                <div class="col-12 mb-3">
                    <input type="text" class="form-control writer-field" name="writer-<?php echo $i + 1; ?>"
                        id="writer-<?php echo $i + 1; ?>" list="writers" value="<?php if ($display)
                                 echo $bookdata["writers"][$i]; ?>">
                </div>
            <?php } ?>
        </div>
        <div class="row">
            <div class="col-6 mb-3">
                <input type="button" class="btn-brown form-control" value="+" onclick="AddField('writer')">
            </div>
            <div class="col-6 mb-3">
                <input type="button" class="btn-brown form-control" value="-" onclick="RemoveField('writer')">
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
            <input type="text" name="price" id="price" class="form-control" value="<?php if ($display)
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
            <input type="text" name="discounted_price" id="discounted_price" class="form-control" value="<?php if ($display)
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
        <input type="file" name="cover" id="cover" class="form-control">
        <?php if (!empty($errors["cover"])) { ?>
            <p class="text-danger">
                <?php echo $errors["cover"]; ?>
            </p>
        <?php } ?>
    </div>
</div>

<input type="submit" class="btn-brown form-control" value="Mentés">