<?php if (count($_SESSION["basket"]) != 0) /* If the basket is not empty */{ ?>

    <?php  ?>

    <?php $counties = GetCounties(); ?>

    <div class="row">
        <form id="new-address-form" action="/order-payment" method="post">
            <span class="text-danger">* Kötelező mező</span><br>
            <label for="company" class="form-label mt-2">Cég:</label>
            <input type="text" name="company" class="form-control" id="company" placeholder="Példa Kft.">
            <label for="county" class="form-label mt-2">Megye: <span class="text-danger">*</span>
            </label>
            <select name="county" class="form-control" id="county">
                <?php while ($row = mysqli_fetch_row($counties)) { ?>
                    <option value="<?php echo $row[0] ?>"><?php echo $row[1]; ?></option>
                <?php } ?>
            </select>
            <label for="city" class="form-label mt-2">Város: <span class="text-danger">*</span></label>
            <input type="text" name="city" class="form-control" id="city" placeholder="Példaváros" onkeyup="validateAddress();">
            <p id="city-error" class="text-danger"></p>
            <label for="public-space" class="form-label mt-2">Közterület: <span class="text-danger">*</span></label>
            <input type="text" name="public-space" class="form-control" id="public-space" placeholder="Példa utca 23." onkeyup="validateAddress();">
            <p id="public-space-error" class="text-danger"></p>
            <label for="zip-code" class="form-label mt-2">Irányítószám: <span class="text-danger">*</span></label>
            <input type="text" name="zip-code" class="form-control" id="zip-code" placeholder="1234" onkeyup="validateAddress();">
            <p id="zip-code-error" class="text-danger"></p>
            <label for="note" class="form-label mt-2">Megjegyzés:</label>
            <input type="text" name="note" class="form-control" id="note">
            <input type="submit" value="Folytatás" class="form-control btn btn-brown mt-2 disabled" id="submit-button">
        </form>
    </div>

<?php } else { ?>
    <script>
        window.location.href = window.location.origin;
    </script>
<?php } ?>