<?php

$_SESSION["user"] = GetUserById($_SESSION["user"]["user_id"]);

$company = $_POST["company"];
$county = $_POST["county"];
$city = $_POST["city"];
$public_space = $_POST["public-space"];
$zip_code = $_POST["zip-code"];
$note = $_POST["note"];

$address_id = GetAddressId($company, $county, $city, $public_space, $zip_code, $note);

$user_id = $_SESSION["logged_in"] ? $_SESSION["user"]["id"] : null;

?>

<form action="/order-success" method="post">
    <div class="d-none">
        <input type="text" name="address-id" id="address-id" value="<?php echo $address_id; ?>">
        <input type="text" name="user-id" id="user-id" value="<?php echo $user_id; ?>">
    </div>

    <h1 class="fs-3">Válasszon fizetési módot:</h1>
    <input type="radio" name="payment-type" id="cash-on-delivery" value="cash-on-delivery" onclick="onAddressChoose();" checked>
    <label for="cash-on-delivery">Utánvételes fizetés (az egyetlen működő opció a pillanatban)</label><br>
    <?php if ($_SESSION["logged_in"] && $_SESSION["user"]["points"] > 0) { ?>
    <h2 class="fs-3">Válassza ki, hány pontot szeretne felhasználni:</h2>
    <input type="range" min="0" max="<?php echo $_SESSION["user"]["points"]; ?>" value="0" class="form-range" id="myRange">
    <?php } ?>
    <input type="submit" class="btn btn-brown mt-2" value="Rendelés véglegesítése">
</form>