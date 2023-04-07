<?php if(isset($_POST["address-id"]) && count($_SESSION["basket"]) > 0) { ?>

<?php
$address_id = $_POST["address-id"];
$user_id = $_POST["user-id"];
$used_points = !$_SESSION["logged_in"] ? 0 : $_POST["points"];

// I'll not use this for now
$payment_type = $_POST["payment-type"];

CompleteOrder($_SESSION["basket"], $address_id, $user_id, $used_points);

$_SESSION["basket"] = [];
?>

<h2 class="fs-3 mt-3">Rendelését sikereset leadta!</h2>
<?php $arrival_date = date_add(new DateTime(), new DateInterval("P1W")); ?>
<p>Várható érkezés: <span class="fw-bold"><?php echo date_format($arrival_date, "Y. m. d."); ?></span></p>

<?php } ?>