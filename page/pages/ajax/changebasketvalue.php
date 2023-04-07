<?php
include("./../../includes/db_con.php");
include("./../../includes/sql.php");
session_start();

$bookdata = GetBookByISBN($_GET["isbn"]);
$value = $_GET["value"];

if ($value == -1 && $_SESSION["basket"][$bookdata["isbn"]] == 1) {
    unset($_SESSION["basket"][$bookdata["isbn"]]);
} else if ($value == 1 && $_SESSION["basket"][$bookdata["isbn"]] == $bookdata["stock"]) {
    //...
} else {
    $_SESSION["basket"][$bookdata["isbn"]] += $value;
}


$keys = array_keys($_SESSION["basket"]);
$item_count = count($keys);
$price_sum = 0;
?>

<?php if ($item_count == 0) { ?>
    <h1 class="fs-3">A kosara üres.</h1>
<?php } else { ?>
    <h1 class="fs-3">Kosarának tartalma:</h1>
<table class="table">
            <thead class="table-head-brown text-center-phones">
                <tr>
                    <th class="col">Könyv</th>
                    <th class="col">Darabszám</th>
                    <th class="col-md col-3">Ár</th>
                </tr>
            </thead>
            <tbody class="text-center-phones align-middle">
                <?php for ($i = 0; $i < $item_count; $i++) { ?>

                    <?php
                    $bookdata = GetBookByISBN($keys[$i]);

                    $relative_path = "./../../";
                    $folder = "covers/";
                    $img = "no_cover.jpg";
                    if (file_exists($relative_path . $folder . $bookdata["isbn"] . ".jpg")) {
                       $img = $bookdata["isbn"] . ".jpg";
                    } else if (file_exists($relative_path . $folder . $bookdata["isbn"] . ".jpeg")) {
                        $img = $bookdata["isbn"] . ".jpeg";
                    } else if (file_exists($relative_path . $folder . $bookdata["isbn"] . ".png")) {
                        $img = $bookdata["isbn"] . ".png";
                    }
                    ?>

                    <tr>
                        
                        <td>
                            <a href="/books/<?php echo $bookdata["isbn"]; ?>" class="text-decoration-none">
                                <img class="img-basket m-2" src="<?php echo $folder . $img ?>" alt="<?php echo $folder . $img ?>"><br class="d-lg-none">
                                <span><?php echo $bookdata["title"]; ?></span>
                            </a>
                        </td>
                        <td>
                            <div class="btn-group" role="group" aria-label="Basic example">
                                <input type="button" value="-" class="btn btn-light" 
                                <?php if ($_SESSION["basket"][$bookdata["isbn"]] == 1) { ?>
                                    data-bs-toggle="modal" data-bs-target="#deleteBookFromCartModal" onclick="updateModal(<?php echo $bookdata['isbn']; ?>);"
                                <?php } else { ?>
                                    onclick="changeBasketValue(<?php echo $bookdata['isbn']; ?>, -1);"
                                <?php } ?>
                                >
                                <input type="button" value="<?php echo $_SESSION["basket"][$keys[$i]]; ?>" class="btn btn-light disabled">
                                <input type="button" value="+" class="btn btn-light" 
                                <?php if ($_SESSION["basket"][$bookdata["isbn"]] == $bookdata["stock"]) { ?>
                                    data-bs-toggle="modal" data-bs-target="#reachedStockLimitModal"
                                <?php } else { ?>
                                    onclick="changeBasketValue(<?php echo $bookdata['isbn']; ?>, 1);">
                                <?php } ?>
                            </div>
                        </td>
                        <td>
                            <?php
                            $price = 0;
                            if (is_null($bookdata["discounted_price"])) {
                                $price = $bookdata["price"];
                            } else {
                                $price = $bookdata["discounted_price"];
                            }

                            $price *= $_SESSION["basket"][$keys[$i]];

                            $price_sum = $price_sum + $price;

                            echo $price . " Ft";
                            ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
            <tfoot class="table-foot-brown">
                <td></td>
                <td>Összesen:</td>
                <td class="fw-bold"><?php echo round($price_sum, -1) . " Ft"; ?></td>
            </tfoot>
        </table>
        <div class="text-end">
            <a 
                <?php if ($_SESSION["logged_in"] && mysqli_num_rows(GetAddressesByUsername($_SESSION["user"]["username"])) > 0) { ?>
                    href="/order-address"
                <?php } else { ?>
                    href="/add-address"
                <?php } ?>
             class="btn btn-brown">Tovább a szállítási cím megadásához</a>
        </div>
        <?php } ?>