<?php if (!$is_logged_in) { ?>

    <div class="card">
        <div class="card-body">
            <span class="fw-bold">Kérem jelentkezzen be az oldal megtekintéséhez!</span>
        </div>
    </div>

<?php } else { ?>

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h2 class="fs-3">
                        <?php echo $_SESSION["user"]["username"]; ?> felhasználó adatai
                    </h2>
                    <dl>
                        <dd></dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

<?php } ?>