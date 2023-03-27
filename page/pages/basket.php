<?php
$keys = array_keys($_SESSION["basket"]);
$item_count = count($keys);
$price_sum = 0;
?>

<?php if ($item_count > 0) { // if there are items in the basket... ?>
    <div class="modal fade" id="deleteBookFromCartModal" tabindex="-1" aria-labelledby="deleteBookFromCartModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="deleteBookFromCartModalLabel">Biztosan törölni szeretné a könyvet a kosarából?
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <span>Ha meggondolná magát, újra fel kell keresnie a könyv oldalát és a kosarába kell tennie.</span>
                </div>

                <div class="modal-footer">
                    <input type="button" id="delete-button" class="btn btn-danger" data-bs-dismiss="modal" value="Törlés">
                    <input type="button" class="btn btn-secondary" data-bs-dismiss="modal" value="Mégse">
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="reachedStockLimitModal" tabindex="-1" aria-labelledby="reachedStockLimitModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="reachedStockLimitModalLabel">Információ
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <span>Nem áll rendelkezésre több példány a kiválaszott könyvből.</span>
                </div>

                <div class="modal-footer">
                    <input type="button" class="btn btn-secondary" data-bs-dismiss="modal" value="Bezárás">
                </div>
            </div>
        </div>
    </div>
    
    <h1 class="fs-3">Kosarának tartalma:</h1>
    <div id="sum-table" class="table-responsive">

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

                    $folder = "covers/";
                    $img = "no_cover.jpg";
                    if (file_exists($folder . $bookdata["isbn"] . ".jpg")) {
                       $img = $bookdata["isbn"] . ".jpg";
                    } else if (file_exists($folder . $bookdata["isbn"] . ".jpeg")) {
                        $img = $bookdata["isbn"] . ".jpeg";
                    } else if (file_exists($folder . $bookdata["isbn"] . ".png")) {
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
    </div>

    <div class="text-end">
        <a 
            <?php if ($_SESSION["logged_in"] && mysqli_num_rows(GetAddressesByUsername($_SESSION["user"]["username"])) > 0) { ?>
                href="/order-address"
            <?php } else { ?>
                href="/add-address"
            <?php } ?>
         class="btn btn-brown">Tovább a szállítási cím megadásához</a>
    </div>
<?php } else { //if it's empty... ?>
    <h1 class="fs-3">A kosara üres.</h1>
    <!-- Ezek a könyvek érdekelhetik szekció -->
<?php } ?>