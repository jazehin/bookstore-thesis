<?php if ($_SESSION["logged_in"]) { ?>

    <?php $_SESSION["user"] = GetUserById($_SESSION["user"]["id"]); ?>
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <form action="" method="post">
                        <div class="d-flex justify-content-between align-items-center">
                            <h2 class="fs-5">Felhasználói adatok</h2>
                            <input type="button" class="btn btn-link" id="edit-button" value="Szerkesztés"
                                onclick="editProfile();">
                            <input type="button" class="btn btn-link d-none" id="save-button" value="Mentés" onclick="saveProfile();">
                        </div>
                        <table class="table responsive-table">
                            <tbody>
                                <tr>
                                    <td>Felhasználónév:</td>
                                    <td>
                                        <?php echo $_SESSION["user"]["username"]; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>E-mail:</td>
                                    <td>
                                        <?php echo $_SESSION["user"]["email"]; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Teljes név:</td>
                                    <?php if (isset($_SESSION["user"]["family_name"]) && isset($_SESSION["user"]["given_name"])) { ?>
                                        <td class="data-label">
                                            <?php echo $_SESSION["user"]["family_name"] . " " . $_SESSION["user"]["given_name"]; ?>
                                        </td>
                                    <?php } else { ?>
                                        <td class="fst-italic data-label">Nincs megadva</td>
                                    <?php } ?>
                                    <td class="data-input d-none">
                                        <input type="text" name="family_name" class="form-control mb-1" id="family_name"
                                            placeholder="Vezetéknév" <?php if (isset($_SESSION["user"]["family_name"])) echo 'value="' . $_SESSION["user"]["family_name"] . '"'; ?>>
                                        <input type="text" name="given_name" class="form-control" id="given_name"
                                            placeholder="Keresztnév" <?php if (isset($_SESSION["user"]["given_name"])) echo 'value="' . $_SESSION["user"]["given_name"] . '"'; ?>>
                                        <span id="name-error" class="text-danger"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Nem:</td>
                                    <?php if (isset($_SESSION["user"]["gender"])) { ?>
                                        <td class="data-label">
                                            <?php if ($_SESSION["user"]["gender"] == "male")
                                                echo "férfi"; ?>
                                            <?php if ($_SESSION["user"]["gender"] == "female")
                                                echo "nő"; ?>
                                        </td>
                                    <?php } else { ?>
                                        <td class="fst-italic data-label">Nincs megadva</td>
                                    <?php } ?>
                                    <td class="data-input d-none">
                                    <input type="radio" id="null" name="gender" value="null" <?php if (!isset($_SESSION["user"]["gender"])) echo "checked"; ?>>
                                        <label for="null" class="form-label">Nem szeretném megadni</label><br>
                                        <input type="radio" id="male" name="gender" value="male" <?php if ($_SESSION["user"]["gender"] == "male") echo "checked"; ?>>
                                        <label for="male" class="form-label">Férfi</label><br>
                                        <input type="radio" id="female" name="gender" value="female" <?php if ($_SESSION["user"]["gender"] == "female") echo "checked"; ?>>
                                        <label for="female" class="form-label">Nő</label>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Születési dátum:</td>
                                    <?php if (isset($_SESSION["user"]["birthdate"])) { ?>
                                        <td class="data-label">
                                            <?php
                                            $dateOfBirth = date_create($_SESSION["user"]["birthdate"]);
                                            $today = date_create();
                                            $age = date_diff($dateOfBirth, $today, true)->format("%y");
                                            echo date_format($dateOfBirth, "Y. m. d.") . " (" . $age . " éves)";
                                            ?>
                                        </td>
                                    <?php } else { ?>
                                        <td class="fst-italic data-label">Nincs megadva</td>
                                    <?php } ?>
                                    <td class="data-input d-none">
                                        <input type="date" class="form-control" id="birthdate" name="birthdate" <?php if (isset($_SESSION["user"]["birthdate"])) echo 'value="' . $_SESSION["user"]["birthdate"] . '"'; ?>>
                                        <span id="birthdate-error" class="text-danger"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Telefonszám:</td>
                                    <?php if (isset($_SESSION["user"]["phone_number"])) { ?>
                                        <td class="data-label">
                                            <?php echo $_SESSION["user"]["phone_number"]; ?>
                                        </td>
                                    <?php } else { ?>
                                        <td class="fst-italic data-label">Nincs megadva</td>
                                    <?php } ?>
                                    <td class="data-input d-none">
                                        <input type="text" class="form-control" id="phone_number" name="phone_number" <?php if (isset($_SESSION["user"]["phone_number"])) echo 'value="' . $_SESSION["user"]["phone_number"] . '"'; ?>>
                                        <span id="phone-number-error" class="text-danger"></span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
            <div class="card mt-4">
                <div class="card-body">
                    <h2 class="fs-5">Szállítási címek</h2>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h2 class="fs-5">Érdeklődések</h2>
                    <?php $genres = GetGenres(); ?>
                    <?php $preferences = GetGenrePreferencesByUsername($_SESSION["user"]["username"]); ?>
                    <?php for ($i = 0; $i < count($genres); $i++) { ?>
                        <input type="button" class="pill-<?php if (!in_array($genres[$i], $preferences))
                            echo 'in'; ?>active m-1" value="<?php echo $genres[$i]; ?>"
                            onclick="changePill(this);">
                    <?php } ?>
                </div>
            </div>
            <div class="card mt-4">
                <div class="card-body">
                    <h2 class="fs-5">Hűségprogram</h2>
                </div>
            </div>
        </div>
    </div>

<?php } else { ?>

    <div class="card">
        <div class="card-body">
            <span class="fw-bold">Kérem jelentkezzen be az oldal megtekintéséhez!</span>
        </div>
    </div>

<?php } ?>