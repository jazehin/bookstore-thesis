<?php if (count($_SESSION["basket"]) != 0) /* If the basket is not empty */ { ?>

    <?php if ($_SESSION["logged_in"]) { ?>

        <?php $addresses = GetAddressesByUsername($_SESSION["user"]["username"]); ?>

        <h1 class="fs-3">Válasszon szállítási címet</h1>

        <form action="/order-payment" method="post">
            <div id="addresses">
                <?php $username = $_SESSION["user"]["username"]; ?>
                <?php $i = 1; ?>
                <?php while ($row = mysqli_fetch_row($addresses)) { ?>
                    <div class="address my-2">
                        <input type="radio" name="address" id="address-<?php echo $i; ?>" value="<?php echo $i; ?>"
                            onclick="onAddressChoose(<?php echo $i; ?>);">
                        <label for="address-<?php echo $i; ?>">
                            <?php if (isset($row[1])) { ?>
                                (<span id="company-<?php echo $i; ?>"><?php echo $row[1] ?></span>)
                            <?php } ?>
                            <span id="county-<?php echo $i; ?>"><?php echo "$row[2]"; ?></span><span class="d-none" id="county-code-<?php echo $i; ?>"><?php echo GetCountyIdByCountyName("$row[2]"); ?></span> megye,
                            <span id="zip-code-<?php echo $i; ?>"><?php echo "$row[5]"; ?></span>.
                            <span id="city-<?php echo $i; ?>"><?php echo "$row[3]"; ?></span>,
                            <span id="public-space-<?php echo $i; ?>"><?php echo "$row[4]"; ?></span>
                            <?php if (isset($row[6])) { ?>
                                (Megj.: <span id="note-<?php echo $i; ?>"><?php echo "$row[6]" ?></span>)
                            <?php } ?>
                            <?php $i = $i + 1; ?>
                        </label>
                    </div>
                <?php } ?>
            </div>

            <div class="d-none">
                <input type="text" name="company" id="company">
                <input type="text" name="county" id="county">
                <input type="text" name="city" id="city">
                <input type="text" name="public-space" id="public-space">
                <input type="text" name="zip-code" id="zip-code">
                <input type="text" name="note" id="note">
            </div>

            <div class="buttons mt-3">
                <a href="/add-address" class="btn btn-brown">Új cím megadása</a>
                <input type="submit" class="btn btn-brown disabled" id="submit" value="Tovább a fizetéshez">
            </div>
        </form>

    <?php } else { ?>
        <script>
            window.location.href = `${window.location.origin}/add-address`;
        </script>
    <?php } ?>

<?php } else { ?>
    <script>
        window.location.href = window.location.origin;
    </script>
<?php } ?>