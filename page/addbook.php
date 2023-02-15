<?php
$writers = array();
$genres = array();
$publishers = GetPublishers();
$series = GetSerieses();
$languages = GetLanguages();
$covertypes = GetCoverTypes();
?>


<form class="card my-3 p-3" action="./" method="post" enctype="multipart/form-data">
    <h1 class="fs-2">Könyv hozzáadása</h1>
    <p><span class="text-danger">*</span> kötelező mező</p>

    <div class="row">
        <div class="col-sm-4 mb-3">
            <label for="isbn" class="form-label">ISBN: <span class="text-danger">*</span></label>
            <input type="text" name="isbn" id="isbn" class="form-control" maxlength="13" pattern="[0-9]{13}" required>
        </div>
        <div class="col-sm-4 mb-3">
            <label for="title" class="form-label">Könyvcím: <span class="text-danger">*</span></label>
            <input type="text" name="title" id="title" class="form-control" maxlength="255" required>
        </div>
        <div class="col-sm-4 mb-3">
            <label for="series" class="form-label">Könyvsorozat:</label>
            <input type="text" name="series" id="series" list="serieses" class="form-control">
            <datalist id="serieses">
                <?php for ($i=0; $i < count($series); $i++) { ?>
                    <option value="<?php echo $series[$i]; ?>">
                <?php } ?>
            </datalist>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4 mb-3">
            <label for="date-of-publishing" class="form-label">Kiadás dátuma: <span class="text-danger">*</span></label>
            <input type="date" name="date-of-publishing" id="date-of-publishing" class="form-control" required>
        </div>
        <div class="col-sm-4 mb-3">
            <label for="number-of-pages" class="form-label">Oldalszám: <span class="text-danger">*</span></label>
            <input type="number" name="number-of-pages" id="number-of-pages" class="form-control" required>
        </div>
        <div class="col-sm-4 mb-3">
            <label for="weight" class="form-label">Súly: <span class="text-danger">*</span></label>
            <div class="input-group">
                <input type="number" name="weight" id="weight" class="form-control" required>
                <span class="input-group-text" id="basic-addon2">gramm</span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4 mb-3">
            <label for="publisher" class="form-label">Kiadó: <span class="text-danger">*</span></label>
            <input type="text" name="publisher" id="publisher" list="publishers" class="form-control" required>
            <datalist id="publishers">
                <?php for ($i=0; $i < count($publishers); $i++) { ?>
                    <option value="<?php echo $publishers[$i]; ?>">
                <?php } ?>
            </datalist>
        </div>
        <div class="col-sm-4 mb-3">
            <label for="covertype" class="form-label">Kötéstípus: <span class="text-danger">*</span></label>
            <input type="text" name="covertype" id="covertype" list="covertypes" class="form-control" required>
            <datalist id="covertypes">
                <?php for ($i=0; $i < count($covertypes); $i++) { ?>
                    <option value="<?php echo $covertypes[$i]; ?>">
                <?php } ?>
            </datalist>
        </div>
        <div class="col-sm-4 mb-3">
            <label for="language" class="form-label">Nyelv: <span class="text-danger">*</span></label>
            <input type="text" name="language" id="language" list="languages" class="form-control" required>
            <datalist id="languages">
                <?php for ($i=0; $i < count($languages); $i++) { ?>
                    <option value="<?php echo $languages[$i]; ?>">
                <?php } ?>
            </datalist>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <label for="genre-1" class="form-label">Műfaj(ok): <span class="text-danger">*</span><br><span class="small">Nyomja meg a + gombot további mezők felvételéhez és a - gombot az eltávolításukhoz.</span></label>
            <div class="row" id="genre-fields">
                <div class="col-12 mb-3">
                    <datalist id="genres">
                        <option value="thriller"></option>
                    </datalist>
                    <input type="text" class="form-control genre-field" name="genre-1" id="genre-1" list="genres" required>
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
        </div>
        <div class="col-sm-6">
            <label for="writer-1" class="form-label">Író(k): <span class="text-danger">*</span><br><span class="small">Nyomja meg a + gombot további mezők felvételéhez és a - gombot az eltávolításukhoz.</span></label>
            <div class="row" id="writer-fields">
                <div class="col-12 mb-3">
                    <datalist id="writers">
                        
                    </datalist>
                    <input type="text" class="form-control writer-field" name="writer-1" id="writer-1" list="writers" required>
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
        </div>
    </div>

    <input type="submit" class="form-control" value="Felvétel">
</form>